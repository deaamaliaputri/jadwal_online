<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DataPribadi\KeluargaRepository;
use App\Domain\Repositories\DataPribadi\PendudukLainRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\MultifungsiRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakMultifungsi extends Controller
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
        MultifungsiRepository $multifungsiRepository,
        PribadiRepository $pribadiRepository,
        NonPendudukRepository $nonPendudukRepository,
        PejabatRepository $pejabatRepository,
        LogoRepository $logoRepository,
        AlamatRepository $alamatRepository,
        DesaRepository $desaRepository,
        KodeAdministrasiRepository $kodeAdministrasiRepository,
        PendudukLainRepository $pendudukLainRepository,
        KeluargaRepository $keluargaRepository,
        OrganisasiRepository $organisasiRepository
    )
    {
        $this->multifungsi = $multifungsiRepository;
        $this->pribadi = $pribadiRepository;
        $this->nonpenduduk = $nonPendudukRepository;
        $this->pejabat = $pejabatRepository;
        $this->logo = $logoRepository;
        $this->alamat = $alamatRepository;
        $this->desa = $desaRepository;
        $this->kodeadministrasi = $kodeAdministrasiRepository;
        $this->penduduklain = $pendudukLainRepository;
        $this->keluarga = $keluargaRepository;
        $this->organisasi = $organisasiRepository;
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

        $pdf->SetFont('Times-Roman', '', 14);
        $desa = $this->desa->find(session('desa'));
        $multifungsi = $this->multifungsi->find($id);
        $jeniskodeadministrasi = $this->multifungsi->cekkodejenisadministrasi($multifungsi->jenis_pelayanan_id);
        $alamat = $this->alamat->cekalamatperdasarkandesa(session('organisasi'));
        $kodeadministrasi = $this->kodeadministrasi->cekkodeadminbysession();
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();
        $logogambar = $this->logo->getLogokabupatencetak($desa->id);
        //kabupaten
        if ($desa->kecamatan->kabupaten->status == 1) {
            $status = 'KABUPATEN';
            $kabupaten = $desa->kecamatan->kabupaten->kabupaten;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $status = 'KOTA';
            $kabupaten = $desa->kecamatan->kabupaten->kabupaten;
        }
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
        $pdf->SetX(40);
        $pdf->Cell(0, 0, 'PEMERINTAH ' . $status . ' ' . strtoupper($kabupaten), 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Times-Roman', '', 13);
        $pdf->SetX(40);
        $pdf->Cell(0, 0, $statuskecamatan . ' ' . strtoupper($kecamatan), 0, 0, 'C');
        $pdf->Ln(5);
        if ($logogambar != null) {
            $pdf->SetFont('Times-Roman', 'B', 13);
            $pdf->Image('app/logo/' . $logogambar->logo, 20, 10, 20, 25);
        }
        $pdf->SetX(40);
        $pdf->SetFont('Times-Roman', 'B', 18);
        $pdf->Cell(0, 0, $statusdesa . ' ' . strtoupper($namadesa), 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Times-Roman', 'B', 18);
        $pdf->SetX(40);
        $pdf->SetFont('Times-Roman', '', 10);
        if ($alamat != null) {
            if ($alamat->faxmile != 0) {
                $pdf->Cell(0, 0, 'Alamat: ' . $alamat->alamat . ' Telp. ' . $alamat->telepon . ' Fax. ' . $alamat->faxmile, 0, 0, 'C');
            }
            if ($alamat->faxmile == 0) {
                $pdf->Cell(0, 0, 'Alamat: ' . $alamat->alamat . ' Telp. ' . $alamat->telepon, 0, 0, 'C');
            }
            $pdf->Ln(5);
            $pdf->SetFont('Times-Roman', '', 10);
            $pdf->SetX(40);
            $pdf->Cell(0, 0, 'email: ' . $alamat->email . ' website: ' . $alamat->website, 0, 0, 'C');

        }

        $pdf->Ln(5);
        $pdf->SetFont('Times-Roman', 'BU', 8);
        if ($kodeadministrasi == null)
            $kodeadministrasinama = '';
        else {
            $pdf->SetX(40);

            $kodeadministrasinama = $kodeadministrasi->kode;
        }
        $pdf->SetFont('Times-Roman', 'BU', 9);
        if ($kodeadministrasinama != null) {
            $pdf->Cell(0, 0, strtoupper($namadesa) . '-' . strtoupper($kodeadministrasinama), 0, '', 'C');
        } else {
            $pdf->SetX(40);

            $pdf->Cell(0, 0, strtoupper($namadesa), 0, '', 'C');
        }
        $pdf->Ln(20);
        $pdf->SetFont('arial', 'BU', 14);
        $pdf->SetX(25);
        if ($multifungsi->is_penduduk_layan != null) {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN' . ' ' . strtoupper($multifungsi->is_penduduk_layan), 0, '', 'C');

        } else {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN', 0, '', 'C');

        }
        $pdf->Ln(5);
        $pdf->SetFont('arial', '', 10);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($multifungsi->tgl, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($multifungsi->tgl, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($multifungsi->tgl, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $multifungsi->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $multifungsi->tahun, 0, '', 'C');

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

    public function Multifungsi($id)
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
        $pdf->SetTitle('Surat Multifungsi');
        $this->Kop($pdf, $id);
        $pdf->SetY(80);
        $desa = $this->desa->find(session('desa'));
        $multifungsi = $this->multifungsi->find($id);
        if ($multifungsi->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($multifungsi->penandatangan);
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

        $pdf->SetX(19);
        $pdf->Cell(0, -15, 'Yang bertanda tangan di bawah ini:', 0, '', 'L');
        $pdf->Ln(5);
        if ($pejabat == null) {
            $namalengkappejabat = '';
            $namajabatan = '';
        } else {
            if ($pejabat->titel_belakang != '') {
                if ($pejabat->titel_depan != '') {
                    $namalengkappejabat = $pejabat->titel_depan . ' ' . $pejabat->nama . ', ' . $pejabat->titel_belakang;
                }
                if ($pejabat->titel_depan == '') {
                    $namalengkappejabat = $pejabat->titel_depan . '' . $pejabat->nama . ', ' . $pejabat->titel_belakang;
                }
            }
            if ($pejabat->titel_belakang == '') {
                if ($pejabat->titel_depan != '') {
                    $namalengkappejabat = $pejabat->titel_depan . ' ' . $pejabat->nama . '' . $pejabat->titel_belakang;
                }
                if ($pejabat->titel_depan == '') {
                    $namalengkappejabat = $pejabat->titel_depan . '' . $pejabat->nama . '' . $pejabat->titel_belakang;
                }
            }

            if ($multifungsi->penandatangan == 'Jabatan Struktural') {
                $pejabatstruktural2 = $this->pejabat->find($multifungsi->jabatan_lainnya);
                if ($pejabatstruktural2->keterangan != '') {

                    $namajabatan = $pejabatstruktural2->keterangan . ' ' . $pejabatstruktural2->jabatan;
                }
                if ($pejabatstruktural2->keterangan == '') {
                    $namajabatan = $pejabatstruktural2->jabatan;
                }
            }
            if ($multifungsi->penandatangan != 'Jabatan Struktural') {

                if ($pejabat->keterangan != '') {
                    $namajabatan = $pejabat->keterangan . ' ' . $pejabat->jabatan;
                }
                if ($pejabat->keterangan == '') {
                    $namajabatan = $pejabat->jabatan;
                }
            }
        }
        $pdf->SetX(19);
        $pdf->Cell(25, -15, 'a.     Nama ', 0, '', 'L');
        $pdf->Cell(19);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(5, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(119, -15, '' . $namalengkappejabat, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);

        $pdf->SetX(19);
        $pdf->Cell(25, -15, 'b.     Jabatan ', 0, '', 'L');
        $pdf->Cell(19);
        if ($multifungsi->penandatangan == 'Jabatan Struktural') {
            $pdf->Cell(5, -15, ':     ', 0, '', 'L');

            $pdf->Cell(119, -15, $namajabatan . ' ' . $statusdesa1 . ' ' . $namadesa, 0, '', 'L');
        }
        if ($multifungsi->penandatangan != 'Jabatan Struktural') {
            $pdf->Cell(5, -15, ':     ', 0, '', 'L');
            $pdf->Cell(119, -15, $namajabatan . ' ' . $namadesa, 0, '', 'L');
        }
        $pdf->Ln(5);
        $pdf->SetX(19);
        $pdf->Cell(0, -15, 'dengan ini menerangkan bahwa:', 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(19);
        $pdf->Cell(7.5, -15, '1.', 0, '', 'L');
        $pdf->Cell(0, -15, 'Orang sebagaimana tersebut: ', 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $multifungsi->nik_penduduk, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(24, -15, 'Nama Lengkap ', 0, '', 'L');
        if ($multifungsi->is_jenis_layan == 1) {
            $penduduklain = $this->penduduklain->cekpenduduklaincetak($multifungsi->pribadi->id);
            $keluarga = $this->keluarga->cekalamat($multifungsi->pribadi->id);
            if ($multifungsi->pribadi->titel_belakang != '') {
                $namalengkap = $multifungsi->pribadi->titel_depan . ' ' . $multifungsi->pribadi->nama . ', ' . $multifungsi->pribadi->titel_belakang;
            }
            if ($multifungsi->pribadi->titel_belakang == '') {
                $namalengkap = $multifungsi->pribadi->titel_depan . ' ' . $multifungsi->pribadi->nama . '' . $multifungsi->pribadi->titel_belakang;
            }
            $hari = substr($multifungsi->pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($multifungsi->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($multifungsi->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($multifungsi->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($multifungsi->pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $multifungsi->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $jk = $multifungsi->pribadi->jk->jk;
            if ($multifungsi->pribadi->gol_darah_id != 13) {
                $golongandarah = $multifungsi->pribadi->golongan_darah->golongan_darah;
            }
            if ($multifungsi->pribadi->gol_darah_id == 13) {
                $golongandarah = '--';
            }
            $agama = $multifungsi->pribadi->agama->agama;
            $perkawinanan = $multifungsi->pribadi->perkawinan->kawin;
            if ($multifungsi->pribadi->pekerjaan_id == 89) {
                $pekerjaan = $multifungsi->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $multifungsi->pribadi->pekerjaan->pekerjaan;
            }
            if ($penduduklain != null) {
                $kewarganegaraan = 'Kewarganegaraan ' . $penduduklain->penduduk_lain;
            } else {
                $kewarganegaraan = 'Warga Negara Indonesia';
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        if ($multifungsi->is_jenis_layan == 2) {

            if ($multifungsi->non_penduduk->titel_belakang != '') {

                $namalengkap = $multifungsi->non_penduduk->titel_depan . ' ' . $multifungsi->non_penduduk->nama . ', ' . $multifungsi->non_penduduk->titel_belakang;
            }
            if ($multifungsi->non_penduduk->titel_belakang == '') {

                $namalengkap = $multifungsi->non_penduduk->titel_depan . ' ' . $multifungsi->non_penduduk->nama . ' ' . $multifungsi->non_penduduk->titel_belakang;
            }
            $hari = substr($multifungsi->non_penduduk->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($multifungsi->non_penduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($multifungsi->non_penduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($multifungsi->non_penduduk->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($multifungsi->non_penduduk->tanggal_lahir, 6, 4);
            $tempatlahir = $multifungsi->non_penduduk->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $jk = $multifungsi->non_penduduk->jk->jk;
            if ($multifungsi->non_penduduk->gol_darah_id != 13) {
                $golongandarah = $multifungsi->non_penduduk->golongan_darah->golongan_darah;
            }
            if ($multifungsi->non_penduduk->gol_darah_id == 13) {
                $golongandarah = '--';
            }
            $agama = $multifungsi->non_penduduk->agama->agama;
            $perkawinanan = $multifungsi->non_penduduk->perkawinan->kawin;
            if ($multifungsi->non_penduduk->pekerjaan_id == 89) {
                $pekerjaan = $multifungsi->non_penduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $multifungsi->non_penduduk->pekerjaan->pekerjaan;
            }
            $kewarganegaraan = 'Warga Negara Indonesia';
            $alamat = $multifungsi->non_penduduk->alamat . ' RT. ' . $multifungsi->non_penduduk->alamat_rt . ' RW. ' . $multifungsi->non_penduduk->alamat_rw;
            //kabupaten
            if ($multifungsi->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $multifungsi->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($multifungsi->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $multifungsi->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($multifungsi->non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $multifungsi->non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($multifungsi->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $multifungsi->non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($multifungsi->non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $multifungsi->non_penduduk->desa->desa;
            }
            if ($multifungsi->non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $multifungsi->non_penduduk->desa->desa;
            }
            if ($multifungsi->non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $multifungsi->non_penduduk->desa->desa;
            }
            if ($multifungsi->non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $multifungsi->non_penduduk->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
        }
        if ($multifungsi->dasar_keterangan_jenis == '') {
            $keterangan = ', menurut data, catatan dan keterangan yang bersangkutan';
        } else {
            if ($multifungsi->dasar_keterangan_jenis != 'Redaksi Bebas') {
                $keterangan = ', menurut ' . $multifungsi->dasar_keterangan_jenis;
            }
            if ($multifungsi->dasar_keterangan_jenis == 'Redaksi Bebas') {
                $keterangan = ',';
            }
        }
        $pdf->Cell(11);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(5.6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, -15, '' . $namalengkap, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Jenis Kelamin ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $jk, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Golongan Darah ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $golongandarah, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Agama', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Status Perkawinan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $perkawinanan, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Pekerjaan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Kewarganegaraan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $kewarganegaraan, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Alamat', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetWidths([5, 170]);
        $pdf->Ln(-5);
        $pdf->Cell(54);
        $pdf->Row2(['', $alamatlengkap]);
        $pdf->SetWidths([5, 170]);
        $pdf->Ln(2);
        $pdf->SetX(22);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetAligns(['', 'J']);

        $pdf->Row2(['', 'penduduk ' . ($statusdesa1) . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten . $keterangan . ' ' . $multifungsi->redaksi_1]);
        $pdf->Ln(6);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetWidths([8, 170]);
        $pdf->SetAligns(['', 'J']);

        if ($multifungsi->redaksi_2 != 1 && $multifungsi->redaksi_3 != 1) {
            $pdf->SetX(19);
            $pdf->SetAligns(['', 'J']);
            $pdf->Row2(['2', $multifungsi->redaksi_2]);
            $pdf->SetWidths([8, 170]);
            $pdf->Ln(5);
            $pdf->SetAligns(['', 'J']);
            $pdf->SetX(19);
            $pdf->Row2(['3', $multifungsi->redaksi_3]);
            $pdf->SetWidths([8, 170]);
            $pdf->Ln(5);
            $pdf->SetAligns(['', 'J']);
            $pdf->SetX(19);
            $pdf->Row2(['4', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan mengurus: ' . $multifungsi->penggunaan_surat]);
        }
        if ($multifungsi->redaksi_2 != 1 && $multifungsi->redaksi_3 == 1) {
            $pdf->SetX(19);
            $pdf->Row2(['2', $multifungsi->redaksi_2]);
            $pdf->SetWidths([8, 170]);
            $pdf->Ln(5);
            $pdf->SetAligns(['', 'J']);
            $pdf->SetX(19);
            $pdf->Row2(['3', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan mengurus: ' . $multifungsi->penggunaan_surat]);
            $pdf->SetWidths([8, 170]);
            $pdf->Ln(2);
            $pdf->SetAligns(['', 'J']);
        }
        if ($multifungsi->redaksi_2 == 1 && $multifungsi->redaksi_3 == 1) {
            $pdf->SetX(19);
            $pdf->Row2(['2.    ', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan mengurus: ' . $multifungsi->penggunaan_surat]);
            $pdf->SetWidths([8, 170]);
            $pdf->Ln(-5);
            $pdf->SetAligns(['', 'J']);
        }
        $pdf->Ln(5);
        $pdf->SetX(11);
        $pdf->Row2(['', 'Demikian Surat Keterangan ini dibuat untuk dipergunakan seperlunya.']);
        $pdf->Ln(10);
        $pdf->Cell(5);

        if ($multifungsi->pejabat_kecamatan_id == 1) {
            $pdf->SetX(30);

            if ($desa->kecamatan->kabupaten->status == 1) {
                $statuskecamatan2 = 'CAMAT';
                $kabupaten3 = $desa->kecamatan->kecamatan;
            }
            if ($desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatan2 = 'KEPALA DISTRIK';
                $kabupaten3 = $desa->kecamatan->kecamatan;
            }

            $pdf->Cell(50, 8, 'Mengetahui:', 0, '', 'C');
            $pdf->SetX(30);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(50, 16, $statuskecamatan2 . ' ' . strtoupper($kabupaten3) . ',', 0, '', 'C');
            $pdf->SetFont('Arial', 'U', 10);
            $pdf->Cell(-50, 70, '', 0, '', 'C');
            $pdf->SetFont('Arial', '', 10);


        }
        $pdf->SetX(120);
        $pdf->Cell(0, 10, $namadesa . ', ' . $multifungsi->tanggal, 0, '', 'C');
        $pdf->Ln(5);
        if ($multifungsi->penandatangan == 'Atasnama Pimpinan' || $multifungsi->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
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
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetX(120);
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
        if ($multifungsi->penandatangan == 'Jabatan Struktural') {

            $pejabatstruktural = $this->pejabat->find($multifungsi->jabatan_lainnya);


            $pdf->Ln(4);
            $pdf->SetX(120);
            $pdf->Cell(0, 10, 'u.b.', 0, '', 'C');
            $pdf->Ln(4);
            $pdf->SetX(120);
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
        if ($multifungsi->penandatangan != 'Atasnama Pimpinan' && $multifungsi->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($multifungsi->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($multifungsi->penandatangan);
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
            if ($multifungsi->penandatangan == 'Pimpinan Organisasi' && $multifungsi->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($multifungsi->penandatangan);
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
        $pdf->Ln(25);

        if ($pejabat != null) {
            $pdf->SetX(120);
            $pdf->SetFont('Arial', 'BU', 10);
            if ($pejabat->titel_belakang != '' && $pejabat->titel_depan != '') {
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan != '') {
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama, 0, '', 'C');
            } else if ($pejabat->titel_belakang != '' && $pejabat->titel_depan == '') {
                $pdf->Cell(0, 10, $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan == '') {
                $pdf->Cell(0, 10, $pejabat->nama, 0, '', 'C');
            }
            $pdf->SetFont('Arial', '', 10);
            $pdf->Ln(4);
            $pdf->SetX(120);
            $pdf->Cell(0, 10, $pejabat->pangkat, 0, '', 'C');
            $pdf->Ln(5);

            if ($pejabat->nip != '') {
                $pdf->SetX(120);
                $pdf->Cell(0, 10, 'NIP.' . $pejabat->nip, 0, '', 'C');
            }
        }
        $tanggal = date('d-m-y');
        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }
        $pdf->Output('cetak-data-multifungsi-' . $tanggal . '.pdf', 'I');
        exit;
    }
}