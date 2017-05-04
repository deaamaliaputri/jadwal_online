<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DataPribadi\KeluargaRepository;
use App\Domain\Repositories\DataPribadi\OrangTuaRepository;
use App\Domain\Repositories\DataPribadi\PendudukLainRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\KeramaianRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Penduduk\RincianNonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakKeramaian extends Controller
{
    protected $crud;
    protected $paginate;
    protected $akun;
    protected $kelompok;
    protected $jenis;
    protected $objek;
    protected $rincian;
//330 210
    public $kertas_pjg = 297; // portrait
    public $kertas_lbr = 210; // landscape
    public $kertas_pjg1 = 320; // portrait khusus refrensi

    public $font = 'Tahoma';
    public $field_font_size = 9;
    public $row_font_size = 8;

    public $butuh = false; // jika perlu fungsi AddPage()
    protected $padding_column = 5;
    protected $default_font_size = 8;
    protected $line = 0;

    public function __construct(
        KeramaianRepository $keramaianRepository,
        PribadiRepository $pribadiRepository,
        NonPendudukRepository $nonPendudukRepository,
        PejabatRepository $pejabatRepository,
        LogoRepository $logoRepository,
        AlamatRepository $alamatRepository,
        DesaRepository $desaRepository,
        KodeAdministrasiRepository $kodeAdministrasiRepository,
        PendudukLainRepository $pendudukLainRepository,
        KeluargaRepository $keluargaRepository,
        OrangTuaRepository $orangTuaRepository,
        RincianNonPendudukRepository $rincianNonPendudukRepository,
        OrganisasiRepository $organisasiRepository
    )
    {
        $this->keramaian = $keramaianRepository;
        $this->pribadi = $pribadiRepository;
        $this->nonpenduduk = $nonPendudukRepository;
        $this->pejabat = $pejabatRepository;
        $this->logo = $logoRepository;
        $this->alamat = $alamatRepository;
        $this->desa = $desaRepository;
        $this->kodeadministrasi = $kodeAdministrasiRepository;
        $this->penduduklain = $pendudukLainRepository;
        $this->keluarga = $keluargaRepository;
        $this->orangtua = $orangTuaRepository;
        $this->organisasi = $organisasiRepository;
        $this->rinciannonpenduduk = $rincianNonPendudukRepository;
        $this->middleware('auth');

    }

    function Headers($pdf)
    {
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        //Put the watermark
        $pdf->SetFont('Arial', 'B', 55);
        $pdf->SetTextColor(128);
        $pdf->RotatedText(35, 190, 'Versi Ujicoba', 24);
    }

    function RotatedText($x, $y, $pdff, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $pdff->Text($x, $y, $pdff);
        $this->Rotate(0);
    }

    function Kop($pdf, $id)
    {
        $pdf->AddFont('Times-Roman', '', 'times.php');
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        $desa = $this->desa->find(session('desa'));
        $keramaian = $this->keramaian->find($id);

        //kecamatan
        if ($desa->kecamatan->status == 1) {
            $statuskecamatan = 'KECAMATAN';
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $statuskecamatan = 'DISTRIK';
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        //desa
        if ($desa->status == 1) {
            $statusdesa = 'KELURAHAN';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 2) {
            $statusdesa = 'DESA';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 3) {
            $statusdesa = 'KAMPUNG';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 4) {
            $statusdesa = 'NEGERI';
            $namadesa = $desa->desa;
        }
        $hari3 = substr($keramaian->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($keramaian->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($keramaian->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($keramaian->tanggal, 3, 2)];
        }
        $tahun3 = substr($keramaian->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;

        $pdf->SetX(145);
        $pdf->Cell(0, 0, $namadesa . ', ' . $tempatlahir3, 0, '', '');

        if ($keramaian->jenis_ijin != 'Ijin Lainnya') {
            $keramaianjenis = $keramaian->jenis_ijin;
        }
        if ($keramaian->jenis_ijin == 'Ijin Lainnya') {
            $keramaianjenis = $keramaian->jenis_ijin_lainnya;
        }
        $pdf->Ln(10);
        $pdf->Cell(0, 0, 'Perihal  : Permohonan ' . $keramaianjenis, 0, 0, 'L');
        $pdf->SetX(145);
        $pdf->Cell(0, 0, 'Kepada :', 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetX(138);
        $pdf->Cell(0, 0, 'Yth. Kapolsek ' . $kecamatan, 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetX(145);
        $pdf->Cell(0, 0, 'di-', 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetX(150);
        $pdf->SetFont('arial', 'BU', 10);
        $pdf->Cell(0, 0, strtoupper($kecamatan), 0, 0, 'L');
        $pdf->SetFont('arial', '', 10);

    }

    function repeatColumn($pdf, $orientasi = '', $column = '', $height = 29.7)
    {

        $height_of_cell = $height; // mm
        if ($orientasi == 'P') {
            $page_height = $this->kertas_pjg; // orientasi kertas Potrait
        } else if ($orientasi == 'K') {
            $page_height = $this->kertas_pjg1; // orientasi kertas Portait
        } else if ($orientasi == 'L') {
            $page_height = $this->kertas_lbr; // orientasi kertas Landscape
        }
        $space_bottom = $page_height - $pdf->GetY(); // space bottom on page
        if ($height_of_cell > $space_bottom) {
            $this->$column($pdf);
        }

        $this->line = $space_bottom;

//        echo $space_bottom . ' + ';
    }

    public function Keramaian($id)
    {
//        array(215, 330)

        $pdf = new PdfClass('P', 'mm', array(215, 330));
        $pdf->is_header = false;
        $pdf->set_widths = 80;
        $pdf->set_footer = 29;
        $pdf->orientasi = 'P';
        $pdf->AddFont('Arial', '', 'arial.php');

        //Disable automatic page break
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('Surat Keramaian');
        $this->Kop($pdf, $id);
        $pdf->SetY(45);
        $desa = $this->desa->find(session('desa'));
        $keramaian = $this->keramaian->find($id);
        $sampaihari = $this->keramaian->ceksampaikapan($keramaian->id);
        $jeniskodeadministrasi = $this->keramaian->cekkodejenisadministrasi($keramaian->jenis_pelayanan_id);
        $kodeadministrasi = $this->kodeadministrasi->cekkodeadminbysession();
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();

        if ($keramaian->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($keramaian->penandatangan);
        }
        //kabupaten
        if ($desa->kecamatan->kabupaten->status == 1) {
            $status = 'KABUPATEN';
            $status1 = 'Kabupaten';
            $kabupaten = $desa->kecamatan->kabupaten->kabupaten;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $status = 'KOTA';
            $status1 = 'Kota';
            $kabupaten = $desa->kecamatan->kabupaten->kabupaten;
        }
        //kecamatan
        if ($desa->kecamatan->status == 1) {
            $statuskecamatan = 'KECAMATAN';
            $statuskecamatan1 = 'Kecamatan';
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $statuskecamatan = 'DISTRIK';
            $statuskecamatan1 = 'Distrik';
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        //desa
        if ($desa->status == 1) {
            $statusdesa = 'KELURAHAN';
            $statusdesa1 = 'Kelurahan';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 2) {
            $statusdesa = 'DESA';
            $statusdesa1 = 'Desa';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 3) {
            $statusdesa = 'KAMPUNG';
            $statusdesa1 = 'Kampung';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 4) {
            $statusdesa = 'NEGERI';
            $statusdesa1 = 'Negeri';
            $namadesa = $desa->desa;
        }
        $pdf->SetFont('Arial', '', 10);

        $pdf->Ln(15);
        $pdf->SetX(27);
        $pdf->Cell(0, -15, 'Yang bertanda tangan di bawah ini, saya:', 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $keramaian->nik, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Nama Lengkap ', 0, '', 'L');

        // jika penduduk
        if ($keramaian->jenis_penduduk == 1) {
            $penduduklain = $this->penduduklain->cekpenduduklaincetak($keramaian->pribadi->id);
            $keluarga = $this->keluarga->cekalamat($keramaian->pribadi->id);
            if ($keramaian->pribadi->titel_belakang != '') {
                $namalengkap = $keramaian->pribadi->titel_depan . ' ' . $keramaian->pribadi->nama . ', ' . $keramaian->pribadi->titel_belakang;
            }
            if ($keramaian->pribadi->titel_belakang == '') {
                $namalengkap = $keramaian->pribadi->titel_depan . ' ' . $keramaian->pribadi->nama . '' . $keramaian->pribadi->titel_belakang;
            }
            $hari = substr($keramaian->pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($keramaian->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($keramaian->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($keramaian->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($keramaian->pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $keramaian->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $jk = $keramaian->pribadi->jk->jk;
            if ($keramaian->pribadi->gol_darah_id != 13) {
                $golongandarah = $keramaian->pribadi->golongan_darah->golongan_darah;
            }
            if ($keramaian->pribadi->gol_darah_id == 13) {
                $golongandarah = '--';
            }
            $agama = $keramaian->pribadi->agama->agama;
            $perkawinanan = $keramaian->pribadi->perkawinan->kawin;
            if ($keramaian->pribadi->pekerjaan_id == 89) {
                $pekerjaan = $keramaian->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $keramaian->pribadi->pekerjaan->pekerjaan;
            }
            if ($penduduklain != null) {
                $kewarganegaraan = 'Kewarganegaraan ' . $penduduklain->penduduk_lain;
            } else {
                $kewarganegaraan = 'Warga Negara Indonesia';
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        if ($keramaian->jenis_penduduk == 2) {
            $penduduklain = $this->rinciannonpenduduk->cekkewarganegaraan($keramaian->penduduk_id);

            if ($keramaian->non_penduduk->titel_belakang != '') {
                $namalengkap = $keramaian->non_penduduk->titel_depan . ' ' . $keramaian->non_penduduk->nama . ', ' . $keramaian->non_penduduk->titel_belakang;
            }
            if ($keramaian->non_penduduk->titel_belakang == '') {
                $namalengkap = $keramaian->non_penduduk->titel_depan . ' ' . $keramaian->non_penduduk->nama . '' . $keramaian->non_penduduk->titel_belakang;
            }
            $hari = substr($keramaian->non_penduduk->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($keramaian->non_penduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($keramaian->non_penduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($keramaian->non_penduduk->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($keramaian->non_penduduk->tanggal_lahir, 6, 4);
            $tempatlahir = $keramaian->non_penduduk->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $jk = $keramaian->non_penduduk->jk->jk;
            if ($keramaian->non_penduduk->gol_darah_id != 13) {
                $golongandarah = $keramaian->non_penduduk->golongan_darah->golongan_darah;
            }
            if ($keramaian->non_penduduk->gol_darah_id == 13) {
                $golongandarah = '--';
            }
            $agama = $keramaian->non_penduduk->agama->agama;
            $perkawinanan = $keramaian->non_penduduk->perkawinan->kawin;
            if ($keramaian->non_penduduk->pekerjaan_id == 89) {
                $pekerjaan = $keramaian->non_penduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $keramaian->non_penduduk->pekerjaan->pekerjaan;
            }
            if ($penduduklain != null) {
                $kewarganegaraan = 'Kewarganegaraan ' . $penduduklain->rincian_non_penduduk;
            } else {
                $kewarganegaraan = 'Warga Negara Indonesia';
            }
            $alamat = $keramaian->non_penduduk->alamat . ' RT. ' . $keramaian->non_penduduk->alamat_rt . ' RW. ' . $keramaian->non_penduduk->alamat_rw;
            if ($keramaian->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $keramaian->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($keramaian->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $keramaian->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($keramaian->non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $keramaian->non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($keramaian->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $keramaian->non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($keramaian->non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $keramaian->non_penduduk->desa->desa;
            }
            if ($keramaian->non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $keramaian->non_penduduk->desa->desa;
            }
            if ($keramaian->non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $keramaian->non_penduduk->desa->desa;
            }
            if ($keramaian->non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $keramaian->non_penduduk->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;

        }
        if ($keramaian->dasar_keterangan_jenis == '') {
            $keterangan = 'menurut data, catatan dan keterangan yang bersangkutan';
        } else {
            $keterangan = 'menurut ' . $keramaian->dasar_keterangan_jenis;
        }
        $pdf->Cell(11);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(120, -15, ':    ' . strtoupper($namalengkap), 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Jenis Kelamin ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $jk, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Golongan Darah ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $golongandarah, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Agama', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Status Perkawinan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $perkawinanan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Pekerjaan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Kewarganegaraan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $kewarganegaraan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Alamat', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetWidths([5, 170]);
        $pdf->Ln(-5);
        $pdf->Cell(54);
        $pdf->Row2(['', $alamatlengkap]);
        $pdf->SetWidths([5, 176]);
        $pdf->Ln(4);
        $pdf->SetX(22);
        if ($keramaian->jenis_ijin != 'Ijin Lainnya') {
            $keramaianjenis = $keramaian->jenis_ijin;
        }
        if ($keramaian->jenis_ijin == 'Ijin Lainnya') {
            $keramaianjenis = $keramaian->jenis_ijin_lainnya;
        }

        $pdf->Row2(['', 'Dengan ini mengajukan permohonan ' . $keramaianjenis . ' dalam rangka :']);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, 0, 'a.  Jenis Kegiatan', 0, '', 'L');
        $pdf->Cell(11);
        if ($keramaian->jenis_kegiatan == 'Lainnya') {
            $jeniskegiatan = $keramaian->jenis_kegiatan_lainnya;
        } else {
            $jeniskegiatan = $keramaian->jenis_kegiatan;
        }
        $pdf->Cell(120, 0, ':     ' . $jeniskegiatan, 0, '', 'L');
        $waktuawal = substr($keramaian->waktu_mulai, 0, 2);
        $waktuakhir = substr($keramaian->waktu_akhir, 0, 2);
        $pdf->Ln(4);
        if ($waktuakhir < $waktuawal) {
            $waktuawalpenjumlahan = 24 - $waktuawal;
            $jumlahwaktu = $waktuawalpenjumlahan + $waktuakhir;
        } else {
            $jumlahwaktu = \Carbon\Carbon::createFromFormat('H:i', ($keramaian->waktu_mulai))->diffInHours(\Carbon\Carbon::createFromFormat('H:i', ($keramaian->waktu_akhir)));

        }
        if (substr($sampaihari, 0, 1) == 0) {
            $hasillengkap = $jumlahwaktu . ' Jam';
        } else {
            $hasillengkap = $sampaihari . ' ' . $jumlahwaktu . ' Jam';
        }
        $pdf->SetX(27);
        $pdf->Cell(25, 0, 'b.  Lamanya', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, 0, ':     ' . $hasillengkap, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, 0, 'c.  Jenis Hiburan', 0, '', 'L');
        $pdf->Cell(11);
        if ($keramaian->jenis_hiburan == 'Lainnya') {
            $jenishiburan = $keramaian->jenis_hiburan_lainnya;
        } else {
            $jenishiburan = $keramaian->jenis_hiburan;
        }
        $pdf->Cell(120, 0, ':     ' . $jenishiburan, 0, '', 'L');
        $pdf->Ln(4);
        if ($sampaihari != "0 Hari") {
            $pdf->SetX(27);
            $pdf->Cell(25, 0, 'd.  Pada Hari', 0, '', 'L');
            $pdf->Cell(11);
            $datetime = \DateTime::createFromFormat('d/m/Y', $keramaian->tanggal_mulai);
            $dayForDate = $datetime->format('D');
            if ($dayForDate == 'Sun') {
                $hariindo = 'Minggu';
            }
            if ($dayForDate == 'Mon') {
                $hariindo = 'Senin';
            }
            if ($dayForDate == 'Tue') {
                $hariindo = 'Selasa';
            }
            if ($dayForDate == 'Wed') {
                $hariindo = 'Rabu';
            }
            if ($dayForDate == 'Thu') {
                $hariindo = 'Kamis';
            }
            if ($dayForDate == 'Fri') {
                $hariindo = 'Jum`at';
            }
            if ($dayForDate == 'Sat') {
                $hariindo = 'Sabtu';
            }
            if ($keramaian->tanggal_mulai != $keramaian->tanggal_akhir) {
                $datetime1 = \DateTime::createFromFormat('d/m/Y', $keramaian->tanggal_akhir);
                $dayForDate1 = $datetime1->format('D');
                if ($dayForDate1 == 'Sun') {
                    $hariindo1 = ' s/d Minggu';
                }
                if ($dayForDate1 == 'Mon') {
                    $hariindo1 = ' s/d Senin';
                }
                if ($dayForDate1 == 'Tue') {
                    $hariindo1 = ' s/d Selasa';
                }
                if ($dayForDate1 == 'Wed') {
                    $hariindo1 = ' s/d Rabu';
                }
                if ($dayForDate1 == 'Thu') {
                    $hariindo1 = ' s/d Kamis';
                }
                if ($dayForDate1 == 'Fri') {
                    $hariindo1 = ' s/d Jum`at';
                }
                if ($dayForDate1 == 'Sat') {
                    $hariindo1 = ' s/d Sabtu';
                }
                $pdf->Cell(120, 0, ':     ' . $hariindo . $hariindo1, 0, '', 'L');

            } else {
                $pdf->Cell(120, 0, ':     ' . $hariindo, 0, '', 'L');
            }
            $pdf->Ln(4);
            $pdf->SetX(27);
            $pdf->Cell(25, 0, 'e.  Dari Tanggal', 0, '', 'L');

            //dari tanggal merubaha huruf

            $hari1 = substr($keramaian->tanggal_mulai, 0, 2);
            if (substr($keramaian->tanggal_mulai, 3, 2) <= 9) {
                $bulan1 = $indo[substr($keramaian->tanggal_mulai, 4, 1)];
            } else {
                $bulan1 = $indo[substr($keramaian->tanggal_mulai, 3, 2)];
            }
            $tahun1 = substr($keramaian->tanggal_mulai, 6, 4);
            $tanggal_mulai = $hari1 . ' ' . $bulan1 . ' ' . $tahun1;

            //sampai tanggal merubaha huruf

            $hari2 = substr($keramaian->tanggal_akhir, 0, 2);
            if (substr($keramaian->tanggal_akhir, 3, 2) <= 9) {
                $bulan2 = $indo[substr($keramaian->tanggal_akhir, 4, 1)];
            } else {
                $bulan2 = $indo[substr($keramaian->tanggal_akhir, 3, 2)];
            }
            $tahun2 = substr($keramaian->tanggal_akhir, 6, 4);
            $tanggal_akhir = $hari2 . ' ' . $bulan2 . ' ' . $tahun2;

            $pdf->Cell(11);
            $pdf->Cell(120, 0, ':     ' . $tanggal_mulai, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(27);
            $pdf->Cell(25, 0, 'f.   Sampai dengan', 0, '', 'L');
            $pdf->Cell(11);
            $pdf->Cell(120, 0, ':     ' . $tanggal_akhir, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(27);
            $pdf->Cell(25, 0, 'g.  Tempat Kegiatan', 0, '', 'L');
        }
        if ($sampaihari == "0 Hari") {
            $pdf->SetX(27);
            $pdf->Cell(25, 0, 'd.  Dari Tanggal', 0, '', 'L');

            //dari tanggal merubaha huruf

            $hari1 = substr($keramaian->tanggal_mulai, 0, 2);
            if (substr($keramaian->tanggal_mulai, 3, 2) <= 9) {
                $bulan1 = $indo[substr($keramaian->tanggal_mulai, 4, 1)];
            } else {
                $bulan1 = $indo[substr($keramaian->tanggal_mulai, 3, 2)];
            }
            $tahun1 = substr($keramaian->tanggal_mulai, 6, 4);
            $tanggal_mulai = $hari1 . ' ' . $bulan1 . ' ' . $tahun1;

            //sampai tanggal merubaha huruf

            $hari2 = substr($keramaian->tanggal_akhir, 0, 2);
            if (substr($keramaian->tanggal_akhir, 3, 2) <= 9) {
                $bulan2 = $indo[substr($keramaian->tanggal_akhir, 4, 1)];
            } else {
                $bulan2 = $indo[substr($keramaian->tanggal_akhir, 3, 2)];
            }
            $tahun2 = substr($keramaian->tanggal_akhir, 6, 4);
            $tanggal_akhir = $hari2 . ' ' . $bulan2 . ' ' . $tahun2;
            $cekwaktu = $this->kodeadministrasi->cekwaktuadministrasi();
            if ($cekwaktu != null) {
                $waktu = $cekwaktu->kode;
            }
            if ($cekwaktu == null) {
                $waktu = '';
            }
            $pdf->Cell(11);
            $pdf->Cell(120, 0, ':     ' . $tanggal_mulai . ' Jam:  ' . $keramaian->waktu_mulai . ' ' . $waktu, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(27);
            $pdf->Cell(25, 0, 'e.  Sampai dengan', 0, '', 'L');
            $pdf->Cell(11);
            $pdf->Cell(120, 0, ':     ' . $tanggal_akhir . ' Jam:  ' . $keramaian->waktu_akhir . ' ' . $waktu, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(27);
            $pdf->Cell(25, 0, 'f.  Tempat Kegiatan', 0, '', 'L');
        }
        $pdf->Cell(11);
        $pdf->Cell(120, 0, ':     ' . $keramaian->tempat_kegiatan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->Cell(11);
        $pdf->SetX(27);
        if ($sampaihari == "0 Hari") {
            $pdf->Cell(25, 0, 'g.  Alamat', 0, '', 'L');
        }
        if ($sampaihari != "0 Hari") {
            $pdf->Cell(25, 0, 'h.  Alamat', 0, '', 'L');
        }
        $pdf->Cell(11);
        $pdf->Cell(120, 0, ':     ' . $keramaian->alamat, 0, '', 'L');
        $pdf->SetWidths([6, 170]);
        $pdf->Ln(2);
        $pdf->Cell(53);
        $pdf->Row2(['', $alamatlengkap]);
        $pdf->SetWidths([5, 176]);
        $pdf->Ln(4);
        $pdf->SetX(22);
        $pdf->SetAligns(['', 'J']);
        $pdf->Row2(['', 'Sebagai bahan pertimbangan dalam memberikan ijin sebagaimana dimaksud, kami sanggup :']);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->SetAligns(['', 'J']);
        $pdf->Row2(['1.', 'Untuk menjaga ketertiban dan keamanan selama pelaksanaan kegiatan;']);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->SetAligns(['', 'J']);
        $pdf->Row2(['2.', 'Mentaati tata tertib lingkungan, adat istiadat, dan peraturan perundang-undangan yang berlaku:']);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->SetAligns(['', 'J']);
        $pdf->Row2(['3.', 'Tidak menyelenggarakan kegiatan yang bersifat melanggar peraturan perundang-undangan baik secara tersembunyi dan/atau terang-terangan;']);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->SetAligns(['', 'J']);
        $pdf->Row2(['4.', 'Untuk dilakukan pemberhentian secara paksa apabila dalam pelaksanaan kegiatan dimaksud mengganggu ketertiban dan keamanan masyarakat, dan kelancaran lalu lintas.']);
        $pdf->Ln(10);
        $pdf->SetWidths([170]);
        $pdf->SetX(27);
        $pdf->SetAligns(['', 'J']);
        $pdf->Row2(['Demikian permohonan Ijin Keramaian ini kami buat dengan sebenarnya dan penuh tanggung jawab untuk dapatnya dikabulkan.']);

        $pdf->ln(10);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($keramaian->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($keramaian->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($keramaian->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetX(25);
        $pdf->Cell(55, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $keramaian->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $keramaian->tahun, 0, '', 'C');
        $pdf->Ln(5);
        $pdf->SetX(25);
        $pdf->Cell(55, 0, 'Mengetahui :', 0, '', 'C');
        $pdf->Ln(5);
        if ($keramaian->penandatangan == 'Atasnama Pimpinan' || $keramaian->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(25);
            if ($pejabatpimpinan != null) {
                if ($pejabatpimpinan->keterangan != '') {
                    $keteraganjabatan = $pejabatpimpinan->keterangan . ' ';
                }
                if ($pejabatpimpinan->keterangan == '') {
                    $keteraganjabatan = '';
                }
                $pdf->Cell(50, 0, $an . ' ' . $keteraganjabatan . strtoupper($pejabatpimpinan->jabatan) . ' ' . strtoupper($namadesa) . ',', 0, '', 'C');

            } else {
                $pdf->Cell(50, 0, $an . ' ' . strtoupper($namadesa), 0, '', 'C');

            }
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetX(25);
            if ($pejabat != null) {
                $idpejabat = 'Sekretaris Organisasi';
                $pejabatsekre = $this->pejabat->cekjabatan($idpejabat);
                if ($pejabatsekre != null) {
                    if ($pejabatsekre->keterangan != '') {
                        $keteraganjabatan2 = $pejabatsekre->keterangan . ' ';
                    }
                    if ($pejabatsekre->keterangan == '') {
                        $keteraganjabatan2 = '';
                    }
                    $pdf->Cell(50, 0, $keteraganjabatan2 . $pejabatsekre->jabatan . ',', 0, '', 'C');
                }
            }

        }
        if ($keramaian->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($keramaian->jabatan_lainnya);

            $pdf->Ln(4);
            $pdf->SetX(25);
            $pdf->Cell(50, 0, 'u.b.', 0, '', 'C');
            $pdf->Ln(4);
            $pdf->SetX(25);
            if ($pejabatstruktural != null) {
                if ($pejabat->keterangan != '') {
                    $keteraganjabatan3 = $pejabat->keterangan . ' ';
                }
                if ($pejabat->keterangan == '') {
                    $keteraganjabatan3 = '';
                }
                $pdf->Cell(50, 0, $keteraganjabatan3 . $pejabat->jabatan . ',', 0, '', 'C');
            }
        }
        if ($keramaian->penandatangan != 'Atasnama Pimpinan' && $keramaian->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(25);
            if ($keramaian->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($keramaian->penandatangan);
                if ($pejabatsekretaris != null) {
                    if ($pejabatsekretaris->keterangan != '') {
                        $keteraganjabatan4 = $pejabatsekretaris->keterangan . ' ';
                    }
                    if ($pejabatsekretaris->keterangan == '') {
                        $keteraganjabatan4 = '';
                    }
                    $pdf->Cell(50, 0, $keteraganjabatan4 . strtoupper($pejabatsekretaris->jabatan . ','), 0, '', 'C');
                }
            }
            if ($keramaian->penandatangan == 'Pimpinan Organisasi' && $keramaian->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($keramaian->penandatangan);
                if ($pejabatsekretaris != null) {
                    if ($pejabatsekretaris->keterangan != '') {
                        $keteraganjabatan5 = $pejabatsekretaris->keterangan . ' ';
                    }
                    if ($pejabatsekretaris->keterangan == '') {
                        $keteraganjabatan5 = '';
                    }
                    $pdf->Cell(50, 0, $keteraganjabatan5 . strtoupper($pejabatsekretaris->jabatan . ' ' . $namadesa . ','), 0, '', 'C');
                }
            }

        }
        $pdf->Ln(20);

        if ($pejabat != null) {
            $pdf->SetX(120);
            $pdf->SetFont('Arial', 'BU', 10);
            if ($pejabat->titel_belakang != '' && $pejabat->titel_depan != '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan != '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama, 0, '', 'C');
            } else if ($pejabat->titel_belakang != '' && $pejabat->titel_depan == '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan == '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, $pejabat->nama, 0, '', 'C');
            }
            $pdf->SetFont('Arial', '', 10);
            $pdf->Ln(4);
            $pdf->SetX(-320);
            $pdf->Cell(0, 10, $pejabat->pangkat, 0, '', 'C');
            $pdf->Ln(5);

            if ($pejabat->nip != '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, 'NIP.' . $pejabat->nip, 0, '', 'C');
            }
        }
//pemohon

        $pdf->SetX(150);
        $pdf->Cell(50, -75, 'Hormat Saya,', 0, '', 'C');
        $pdf->Ln(2);
        $pdf->SetX(150);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -72, 'PEMOHON,', 0, '', 'C');
        $pdf->Ln(30);
        $pdf->SetX(150);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -70, $namalengkap, 0, '', 'C');
        $pdf->SetX(30);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(-20);
        $pdf->Cell(0, 0, 'Menyetujui dan Merekomendasi:', 0, '', 'C');
        $pdf->Ln(7);
        $pdf->SetFont('Arial', 'B', 10);

        if ($keramaian->pejabat_camat_id == 1) {
            $pdf->SetX(15);

            $pdf->Cell(50, 0, 'KAPOLSEK ' . strtoupper($kecamatan) . ',', 0, '', 'C');
        }
        if ($keramaian->pejabat_camat_id == 1) {
            $pdf->SetX(85);

            $pdf->Cell(50, 0, 'DAN RAMIL ' . strtoupper($kecamatan) . ',', 0, '', 'C');
        }
        if ($keramaian->pejabat_camat_id == 1) {
            $pdf->SetX(155);

            $pdf->Cell(50, 0, 'CAMAT ' . strtoupper($kecamatan) . ',', 0, '', 'C');
        }

        $tanggal = date('d-m-y');
        $waktu = date('H:i:s');
        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

        $pdf->Output('cetak-data-keramaian' . $tanggal . '.pdf', 'I');
        exit;
    }
}