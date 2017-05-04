<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DataPribadi\DokumenPendudukRepository;
use App\Domain\Repositories\DataPribadi\KeluargaRepository;
use App\Domain\Repositories\DataPribadi\OrangTuaRepository;
use App\Domain\Repositories\DataPribadi\PendudukLainRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\NikahRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Penduduk\RincianNonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakNikah extends Controller
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
        NikahRepository $nikahRepository,
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
        DokumenPendudukRepository $dokumenPendudukRepository,
        OrganisasiRepository $organisasiRepository
    )
    {
        $this->Nikah = $nikahRepository;
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
        $this->dokumen = $dokumenPendudukRepository;
        $this->rincian = $rincianNonPendudukRepository;

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

    public function Nikah($id)
    {
//        array(215, 330)

        $pdf = new PdfClass('P', 'mm', 'A5');
        $pdf->is_header = false;
        $pdf->set_widths = 80;
        $pdf->set_footer = 29;
        $pdf->orientasi = 'P';
        $pdf->AddFont('Arial', '', 'arial.php');

        //Disable automatic page break
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 10, 8);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('Wali Nikah');
        $pdf->AddPage();
//        $this->Kop($pdf, $id);
//        $pdf->SetY(50);
        $desa = $this->desa->find(session('desa'));
        $cekwaktu = $this->kodeadministrasi->cekwaktuadministrasi();
        $nikah = $this->Nikah->find($id);
        if ($nikah->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($nikah->penandatangan);
        }
        //kabupaten
        if ($desa->kecamatan->kabupaten->status == 1) {
            $status1 = 'Kabupaten';
            $kabupaten = $desa->kecamatan->kabupaten->kabupaten;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $status1 = 'Kota';
            $kabupaten = $desa->kecamatan->kabupaten->kabupaten;
        }
        //kecamatan
        if ($desa->kecamatan->status == 1) {
            $statuskecamatan1 = 'Kecamatan';
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $statuskecamatan1 = 'Distrik';
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        //desa
        if ($desa->status == 1) {
            $statusdesa1 = 'Kelurahan';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 2) {
            $statusdesa1 = 'Desa';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 3) {
            $statusdesa1 = 'Kampung';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 4) {
            $statusdesa1 = 'Negeri';
            $namadesa = $desa->desa;
        }
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $statusdesa1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $namadesa, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $statuskecamatan1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $kecamatan, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $status1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $kabupaten, 0, '', 'L');
        $jeniskodeadministrasi = $this->Nikah->cekkodejenisadministrasi($nikah->jenis_pelayanan_id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();

        $pdf->Ln(10);
        $pdf->SetFont('arial', 'BU', 9);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, 'SURAT KETERANGAN WALI NIKAH', 0, '', 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 8);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($nikah->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($nikah->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($nikah->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }

        $pdf->SetX(15);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $nikah->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $nikah->tahun, 0, '', 'C');
        $pdf->Ln(15);
        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Yang bertanda tangan di bawah ini:', 0, '', '');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, 'Nama Lengkap ', 0, '', 'L');

        // jika penduduk

        if ($nikah->jenis_penduduk == 1) {
            $keluarga = $this->keluarga->cekalamat($nikah->pribadi->id);
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($nikah->pribadi->id);
            $orangtuabapak = $orangtuabapak2->nama;
            if ($nikah->pribadi->titel_belakang != '') {
                if ($nikah->pribadi->titel_depan != '') {
                    $namalengkap = $nikah->pribadi->titel_depan . ' ' . $nikah->pribadi->nama . ', ' . $nikah->pribadi->titel_belakang;
                }
                if ($nikah->pribadi->titel_depan == '') {
                    $namalengkap = $nikah->pribadi->nama . ', ' . $nikah->pribadi->titel_belakang;
                }
            }
            if ($nikah->pribadi->titel_belakang == '') {
                if ($nikah->pribadi->titel_depan != '') {
                    $namalengkap = $nikah->pribadi->titel_depan . ' ' . $nikah->pribadi->nama . '' . $nikah->pribadi->titel_belakang;
                }
                if ($nikah->pribadi->titel_depan == '') {
                    $namalengkap =$nikah->pribadi->nama . '' . $nikah->pribadi->titel_belakang;
                }
            }
            $hari = substr($nikah->pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($nikah->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($nikah->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($nikah->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($nikah->pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $nikah->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $nikah->pribadi->agama->agama;

            if ($nikah->pribadi->pekerjaan_id == 89) {
                $pekerjaan = $nikah->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $nikah->pribadi->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        //jika non penduduk
        if ($nikah->jenis_penduduk == 2) {
            if ($nikah->non_penduduk->titel_belakang != '') {
                if ($nikah->non_penduduk->titel_depan != '') {
                    $namalengkap = $nikah->non_penduduk->titel_depan . ' ' . $nikah->non_penduduk->nama . ', ' . $nikah->non_penduduk->titel_belakang;
                }
                if ($nikah->non_penduduk->titel_depan == '' || $nikah->non_penduduk->titel_depan == null) {
                    $namalengkap = $nikah->non_penduduk->nama . ', ' . $nikah->non_penduduk->titel_belakang;
                }
            }
            if ($nikah->non_penduduk->titel_belakang == '') {
                if ($nikah->non_penduduk->titel_depan != '') {
                    $namalengkap = $nikah->non_penduduk->titel_depan . ' ' . $nikah->non_penduduk->nama . '' . $nikah->non_penduduk->titel_belakang;
                }
                if ($nikah->non_penduduk->titel_depan == '' || $nikah->non_penduduk->titel_depan == null) {
                    $namalengkap = $nikah->non_penduduk->nama . '' . $nikah->non_penduduk->titel_belakang;
                }
            }
            $hari = substr($nikah->non_penduduk->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($nikah->non_penduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($nikah->non_penduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($nikah->non_penduduk->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($nikah->non_penduduk->tanggal_lahir, 6, 4);
            $tempatlahir = $nikah->non_penduduk->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $nikah->non_penduduk->agama->agama;
            if ($nikah->non_penduduk->pekerjaan_id == 89) {
                $pekerjaan = $nikah->non_penduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $nikah->non_penduduk->pekerjaan->pekerjaan;
            }
            $alamat = $nikah->non_penduduk->alamat . ' RT. ' . $nikah->non_penduduk->alamat_rt . ' RW. ' . $nikah->non_penduduk->alamat_rw;
            //kabupaten
            if ($nikah->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $nikah->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($nikah->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $nikah->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($nikah->non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $nikah->non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($nikah->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $nikah->non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($nikah->non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $nikah->non_penduduk->desa->desa;
            }
            if ($nikah->non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $nikah->non_penduduk->desa->desa;
            }
            if ($nikah->non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $nikah->non_penduduk->desa->desa;
            }
            if ($nikah->non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $nikah->non_penduduk->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $orangtuabapak = '';
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Bin', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $orangtuabapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);

        // mempelai wanita

        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'adalah sebagai '.$nikah->status_wali.' dari Mempelai Perempuan:', 0, '', '');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Nama Lengkap ', 0, '', 'L');

        // jika penduduk

        if ($nikah->jenis_pengantin == 1) {
            $keluarga = $this->keluarga->cekalamat($nikah->pribadipengantin->id);
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($nikah->pribadipengantin->id);
            $orangtuabapak = $orangtuabapak2->nama;
            if ($nikah->pribadipengantin->titel_belakang != '') {
                if ($nikah->pribadipengantin->titel_depan != '') {
                    $namalengkap = $nikah->pribadipengantin->titel_depan . ' ' . $nikah->pribadipengantin->nama . ', ' . $nikah->pribadipengantin->titel_belakang;
                }
                if ($nikah->pribadipengantin->titel_depan == '') {
                    $namalengkap = $nikah->pribadipengantin->nama . ', ' . $nikah->pribadipengantin->titel_belakang;
                }
            }
            if ($nikah->pribadipengantin->titel_belakang == '') {
                if ($nikah->pribadipengantin->titel_depan != '') {
                    $namalengkap = $nikah->pribadipengantin->titel_depan . ' ' . $nikah->pribadipengantin->nama . '' . $nikah->pribadipengantin->titel_belakang;
                }
                if ($nikah->pribadipengantin->titel_depan == '') {
                    $namalengkap =$nikah->pribadipengantin->nama . '' . $nikah->pribadipengantin->titel_belakang;
                }
            }
            $hari = substr($nikah->pribadipengantin->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($nikah->pribadipengantin->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($nikah->pribadipengantin->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($nikah->pribadipengantin->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($nikah->pribadipengantin->tanggal_lahir, 6, 4);
            $tempatlahir = $nikah->pribadipengantin->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $nikah->pribadipengantin->agama->agama;

            if ($nikah->pribadipengantin->pekerjaan_id == 89) {
                $pekerjaan = $nikah->pribadipengantin->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $nikah->pribadipengantin->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        //jika non penduduk
        if ($nikah->jenis_pengantin == 2) {
            if ($nikah->non_pendudukpengantin->titel_belakang != '') {
                if ($nikah->non_pendudukpengantin->titel_depan != '') {
                    $namalengkap = $nikah->non_pendudukpengantin->titel_depan . ' ' . $nikah->non_pendudukpengantin->nama . ', ' . $nikah->non_pendudukpengantin->titel_belakang;
                }
                if ($nikah->non_pendudukpengantin->titel_depan == '' || $nikah->non_pendudukpengantin->titel_depan == null) {
                    $namalengkap = $nikah->non_pendudukpengantin->nama . ', ' . $nikah->non_pendudukpengantin->titel_belakang;
                }
            }
            if ($nikah->non_pendudukpengantin->titel_belakang == '') {
                if ($nikah->non_pendudukpengantin->titel_depan != '') {
                    $namalengkap = $nikah->non_pendudukpengantin->titel_depan . ' ' . $nikah->non_pendudukpengantin->nama . '' . $nikah->non_pendudukpengantin->titel_belakang;
                }
                if ($nikah->non_pendudukpengantin->titel_depan == '' || $nikah->non_pendudukpengantin->titel_depan == null) {
                    $namalengkap = $nikah->non_pendudukpengantin->nama . '' . $nikah->non_pendudukpengantin->titel_belakang;
                }
            }
            $hari = substr($nikah->non_pendudukpengantin->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($nikah->non_pendudukpengantin->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($nikah->non_pendudukpengantin->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($nikah->non_pendudukpengantin->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($nikah->non_pendudukpengantin->tanggal_lahir, 6, 4);
            $tempatlahir = $nikah->non_pendudukpengantin->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $nikah->non_pendudukpengantin->agama->agama;
            if ($nikah->non_pendudukpengantin->pekerjaan_id == 89) {
                $pekerjaan = $nikah->non_pendudukpengantin->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $nikah->non_pendudukpengantin->pekerjaan->pekerjaan;
            }
            $alamat = $nikah->non_pendudukpengantin->alamat . ' RT. ' . $nikah->non_pendudukpengantin->alamat_rt . ' RW. ' . $nikah->non_pendudukpengantin->alamat_rw;
            //kabupaten
            if ($nikah->non_pendudukpengantin->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $nikah->non_pendudukpengantin->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($nikah->non_pendudukpengantin->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $nikah->non_pendudukpengantin->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($nikah->non_pendudukpengantin->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $nikah->non_pendudukpengantin->desa->kecamatan->kecamatan;
            }
            if ($nikah->non_pendudukpengantin->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $nikah->non_pendudukpengantin->desa->kecamatan->kecamatan;
            }
            //desa
            if ($nikah->non_pendudukpengantin->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $nikah->non_pendudukpengantin->desa->desa;
            }
            if ($nikah->non_pendudukpengantin->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $nikah->non_pendudukpengantin->desa->desa;
            }
            if ($nikah->non_pendudukpengantin->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $nikah->non_pendudukpengantin->desa->desa;
            }
            if ($nikah->non_pendudukpengantin->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $nikah->non_pendudukpengantin->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $orangtuabapak = '';
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
//        $pdf->Ln(4);
//        $pdf->SetFont('Arial', '', 8);
//        $pdf->SetX(14);
//        $pdf->Cell(25, 0, 'Bin', 0, '', 'L');
//        $pdf->SetFont('Arial', 'B', 8);
//        $pdf->Cell(5);
//        $pdf->Cell(120, 0, ':     ' . $orangtuabapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);
        $pdf->Ln(2);
        if($nikah->alasan_wali == ''){
            $alasan = '--';
        }
        if($nikah->alasan_wali != ''){
            $alasan = $nikah->alasan_wali;
        }
        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Alasan Wali Hakim (bila wali hakim) : '.$alasan, 0, '', '');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Hubungan Wali              :     '.$nikah->hubungan_wali, 0, '', '');
        $pdf->Ln(7);
        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Demikian Surat Keterangan ini dibuat dengan sesungguhnya.', 0, '', '');
        $hari3 = substr($nikah->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($nikah->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($nikah->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($nikah->tanggal, 3, 2)];
        }
        $tahun3 = substr($nikah->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;
        $pdf->ln(14);
        $pdf->SetX(14);


            $pdf->Cell(50, 8, 'Pejabat Agama', 0, '', 'C');
            $pdf->SetX(14);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(50, 16, '(Pembantu PPN)', 0, '', 'C');
            $pdf->SetFont('Arial', 'BU', 8);
        if ($nikah->pejabat_nikah_id->titel_belakang != '' && $nikah->pejabat_nikah_id->titel_depan != '') {
            $namapejabatnikah =$nikah->pejabat_nikah_id->titel_depan . ' ' . $nikah->pejabat_nikah_id->nama . ', ' . $nikah->pejabat_nikah_id->titel_belakang;
        } else if ($nikah->pejabat_nikah_id->titel_belakang == '' && $nikah->pejabat_nikah_id->titel_depan != '') {
            $namapejabatnikah =$nikah->pejabat_nikah_id->titel_depan . ' ' . $nikah->pejabat_nikah_id->nama;
        } else if ($nikah->pejabat_nikah_id->titel_belakang != '' && $nikah->pejabat_nikah_id->titel_depan == '') {
            $namapejabatnikah =$nikah->pejabat_nikah_id->nama . ', ' . $nikah->pejabat_nikah_id->titel_belakang;
        } else if ($nikah->pejabat_nikah_id->titel_belakang == '' && $nikah->pejabat_nikah_id->titel_depan == '') {
            $namapejabatnikah = $nikah->pejabat_nikah_id->nama;
        }

        $pdf->Cell(-50, 70, $namapejabatnikah, 0, '', 'C');
            $pdf->SetFont('Arial', '', 8);



        $pdf->SetX(90);
        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(4);
        if ($nikah->penandatangan == 'Atasnama Pimpinan' || $nikah->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(90);
            if ($pejabatpimpinan != null) {

                //if keteragan pejabatpimpinan
                if ($pejabatpimpinan->keterangan != '') {
                    $keteranganjabatanpimpinan = $pejabatpimpinan->keterangan . ' ';
                }
                if ($pejabatpimpinan->keterangan == '') {
                    $keteranganjabatanpimpinan = '';
                }
                $pdf->Cell(0, 10, $an . ' ' . $keteranganjabatanpimpinan . strtoupper($pejabatpimpinan->jabatan) . ' ' . strtoupper($namadesa) . ',', 0, '', 'C');

            } else {
                $pdf->Cell(0, 10, $an . ' ' . strtoupper($namadesa), 0, '', 'C');

            }
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(90);
            if ($pejabat != null) {
                $idpejabat = 'Sekretaris Organisasi';
                $pejabatsekre = $this->pejabat->cekjabatan($idpejabat);
                if ($pejabatsekre != null) {
                    if ($pejabatsekre->keterangan != '') {
                        $keteranganjabatansekretaris = $pejabatsekre->keterangan . ' ';
                    }
                    if ($pejabatsekre->keterangan == '') {
                        $keteranganjabatansekretaris = '';
                    }

                    $pdf->Cell(0, 10, $keteranganjabatansekretaris . $pejabatsekre->jabatan . ',', 0, '', 'C');
                }
            }
        }
        if ($nikah->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($nikah->jabatan_lainnya);

            $pdf->Ln(4);
            $pdf->SetX(90);
            $pdf->Cell(0, 8, 'u.b.', 0, '', 'C');
            $pdf->Ln(2);
            $pdf->SetX(90);
            if ($pejabatstruktural != null) {
                if ($pejabat->keterangan != '') {
                    $keteranganjabatanpejabat = $pejabat->keterangan . ' ';
                }
                if ($pejabat->keterangan == '') {
                    $keteranganjabatanpejabat = '';
                }

                $pdf->Cell(0, 10, $keteranganjabatanpejabat . $pejabat->jabatan . ',', 0, '', 'C');
            }
        }
        if ($nikah->penandatangan != 'Atasnama Pimpinan' && $nikah->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(90);
            if ($nikah->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($nikah->penandatangan);
                if ($pejabatsekretaris != null) {
                    if ($pejabatsekretaris->keterangan != '') {
                        $keteranganjabatansekretaris2 = $pejabatsekretaris->keterangan . ' ';
                    }
                    if ($pejabatsekretaris->keterangan == '') {
                        $keteranganjabatansekretaris2 = '';
                    }
                    $pdf->Cell(0, 10, $keteranganjabatansekretaris2 . strtoupper($pejabatsekretaris->jabatan . ','), 0, '', 'C');
                }
            }
            if ($nikah->penandatangan == 'Pimpinan Organisasi' && $nikah->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($nikah->penandatangan);
                if ($pejabatsekretaris != null) {
                    if ($pejabatsekretaris->keterangan != '') {
                        $keteranganjabatansekretaris3 = $pejabatsekretaris->keterangan . ' ';
                    }
                    if ($pejabatsekretaris->keterangan == '') {
                        $keteranganjabatansekretaris3 = '';
                    }

                    $pdf->Cell(0, 10, $keteranganjabatansekretaris3 . strtoupper($pejabatsekretaris->jabatan . ' ' . $namadesa . ','), 0, '', 'C');
                }
            }

        }
        $pdf->Ln(18);

        if ($pejabat != null) {
            $pdf->SetX(90);
            $pdf->SetFont('Arial', 'BU', 8);
            if ($pejabat->titel_belakang != '' && $pejabat->titel_depan != '') {
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan != '') {
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama, 0, '', 'C');
            } else if ($pejabat->titel_belakang != '' && $pejabat->titel_depan == '') {
                $pdf->Cell(0, 10, $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan == '') {
                $pdf->Cell(0, 10, $pejabat->nama, 0, '', 'C');
            }
            $pdf->SetFont('Arial', '', 8);
            $pdf->Ln(4);
            $pdf->SetX(90);
            $pdf->Cell(0, 10, $pejabat->pangkat, 0, '', 'C');
            $pdf->Ln(4);

            if ($pejabat->nip != '') {
                $pdf->SetX(90);
                $pdf->Cell(0, 10, 'NIP.' . $pejabat->nip, 0, '', 'C');
            }
        }

        $tanggal = date('d-m-y');
        $organisasi = $this->organisasi->find(session('organisasi'));
//
        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }
//
        $pdf->Output('cetak-data-Nikah-simduk' . $tanggal . '.pdf', 'I');
        exit;
    }
}