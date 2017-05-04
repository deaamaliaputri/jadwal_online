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
use App\Domain\Repositories\Pelayanan\KematianRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Penduduk\RincianNonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakKematianSiak extends Controller
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
        KematianRepository $kematianRepository,
        PribadiRepository $pribadiRepository,
        NonPendudukRepository $nonPendudukRepository,
        PejabatRepository $pejabatRepository,
        LogoRepository $logoRepository,
        AlamatRepository $alamatRepository,
        DesaRepository $desaRepository,
        KodeAdministrasiRepository $kodeAdministrasiRepository,
        PendudukLainRepository $pendudukLainRepository,
        KeluargaRepository $keluargaRepository,
        DokumenPendudukRepository $dokumenPendudukRepository,
        RincianNonPendudukRepository $rincianNonPendudukRepository,
        OrangTuaRepository $orangTuaRepository,
        OrganisasiRepository $organisasiRepository

    )
    {
        $this->kematian = $kematianRepository;
        $this->pribadi = $pribadiRepository;
        $this->nonpenduduk = $nonPendudukRepository;
        $this->pejabat = $pejabatRepository;
        $this->logo = $logoRepository;
        $this->alamat = $alamatRepository;
        $this->desa = $desaRepository;
        $this->kodeadministrasi = $kodeAdministrasiRepository;
        $this->penduduklain = $pendudukLainRepository;
        $this->keluarga = $keluargaRepository;
        $this->dokumenpenduduk = $dokumenPendudukRepository;
        $this->rinciannonpenduduk = $rincianNonPendudukRepository;
        $this->organisasi = $organisasiRepository;
        $this->orangtua = $orangTuaRepository;
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
        $pdf->SetFont('Times-Roman', 'B', 10);
        $desa = $this->desa->find(session('desa'));
        $kematian = $this->kematian->find($id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();

        if ($kematian->ibu_bayi == 1) {
            $keluarga = $this->keluarga->cekalamat($kematian->ibu_penduduk_id);
        } else if ($kematian->bapak_bayi == 1) {
            $keluarga = $this->keluarga->cekalamat($kematian->bapak_penduduk_id);
        } else {
            $keluarga = null;
        }
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

        $pdf->SetFont('Times-Roman', 'B', 10);
        $pdf->ln(-5);
        $pdf->SetX(160);
        $pdf->Cell(35, 10, 'Kode . F-2.29', 1, 0, 'C');
        $pdf->ln(13);
        $pdf->SetFont('Times-Roman', '', 7);
        $pdf->Cell(0, 0, strtoupper('Pemerintah Desa/Kelurahan'), 0, 0, '');
        $pdf->SetX(50);
        $pdf->SetFont('Times-Roman', '', 8);

        $pdf->Cell(0, 0, ':  ' . strtoupper($statusdesa . ' ' . $namadesa), 0, 0, '');
        $pdf->SetX(120);
        $pdf->Cell(0, 0, strtoupper('Ket   :   Lembar 1  :   UPTD/Instansi Pelaksana'), 0, 0, '');
        $pdf->ln(4);
        $pdf->Cell(0, 0, strtoupper('Kecamatan'), 0, 0, '');
        $pdf->SetX(120);
        $pdf->Cell(0, -3, strtoupper('               Lembar 2  :   Untuk yang bersangkutan'), 0, 0, '');
        $pdf->SetX(50);
        $pdf->Cell(0, 0, ':  ' . strtoupper($statuskecamatan . ' ' . $kecamatan), 0, 0, '');
        $pdf->ln(4);
        $pdf->Cell(0, 0, strtoupper('Kabupaten/Kota'), 0, 0, '');
        $pdf->SetX(120);
        $pdf->Cell(0, -6, strtoupper('               Lembar 3  :   Desa/Kelurahan'), 0, 0, '');
        $pdf->SetX(120);
        $pdf->Cell(0, -1, strtoupper('               Lembar 4  :   Kecamatan'), 0, 0, '');
        $pdf->SetX(50);
        $pdf->Cell(0, 0, ':  ' . strtoupper($status . ' ' . $kabupaten), 0, 0, '');
        $pdf->ln(2);
        $pdf->Cell(0, 5, strtoupper('Kode Wilayah '), 0, 0, '');
        $pdf->SetX(50);
        $kodedesa = substr($desa->kode_desa, 0, 1);
        $kodedesa2 = substr($desa->kode_desa, 1, 1);
        $kodedesa3 = substr($desa->kode_desa, 2, 1);
        $kodedesa4 = substr($desa->kode_desa, 3, 1);
        $kodekecamatan = substr($desa->kecamatan->kode_kec, 0, 1);
        $kodekecamatan2 = substr($desa->kecamatan->kode_kec, 1, 1);
        $kodekabupaten = substr($desa->kecamatan->kabupaten->kode_kab, 0, 1);
        $kodekabupaten2 = substr($desa->kecamatan->kabupaten->kode_kab, 1, 1);
        $kodeprovinsi = substr($desa->kecamatan->kabupaten->provinsi->kode_prov, 0, 1);
        $kodeprovinsi2 = substr($desa->kecamatan->kabupaten->provinsi->kode_prov, 1, 1);
        $pdf->Cell(3, 5, ':', 0, 0, '');
        $pdf->Cell(5, 5, $kodeprovinsi, 1, '', 'L');
        $pdf->Cell(5, 5, $kodeprovinsi2, 1, '', 'L');
        $pdf->Cell(5, 5, $kodekabupaten, 1, '', 'L');
        $pdf->Cell(5, 5, $kodekabupaten2, 1, '', 'L');
        $pdf->Cell(5, 5, $kodekecamatan, 1, '', 'L');
        $pdf->Cell(5, 5, $kodekecamatan2, 1, '', 'L');
        $pdf->Cell(5, 5, $kodedesa, 1, '', 'L');
        $pdf->Cell(5, 5, $kodedesa2, 1, '', 'L');
        $pdf->Cell(5, 5, $kodedesa3, 1, '', 'L');
        $pdf->Cell(5, 5, $kodedesa4, 1, '', 'L');
        $pdf->ln(8);
        $pdf->SetFont('Times-Roman', 'B', 12);
        $pdf->Cell(0, 0, ' SURAT KETERANGAN KEMATIAN', 0, 0, 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Times-Roman', '', 8);
        if ($keluarga != null) {
            $totalkatanamakk = strlen($keluarga->nama);
            $kurangnamakk = 26;
            $pdf->Cell(0, 4, strtoupper('Nama Kepala Keluarga     :'), 0, 0, '');
            $pdf->SetX(53);
            for ($i = 0; $i <= $kurangnamakk; $i++) {
                $hasil = substr($keluarga->nama, $i, $totalkatanamakk);
                $tampil = substr($hasil, 0, 1);
                $widd = 5;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            $pdf->Ln(5);
            $totalkatakk = strlen($keluarga->nomor_kk);
            $kurangkk = 15;
            $pdf->Cell(0, 4, strtoupper('Nomor Kartu Keluarga    :'), 0, 0, '');
            $pdf->SetX(53);
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keluarga->nomor_kk, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 5;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
        }
        if ($keluarga == null) {
            $kurangnamakk = 26;
            $pdf->Cell(0, 4, strtoupper('Nama Kepala Keluarga     :'), 0, 0, '');
            $pdf->SetX(53);
            for ($i = 0; $i <= $kurangnamakk; $i++) {
                $pdf->Cell(5, 4, '', 1, '', 'L');

            }
            $pdf->Ln(5);
            $kurangkk = 15;
            $pdf->Cell(0, 4, strtoupper('Nomor Kartu Keluarga    :'), 0, 0, '');
            $pdf->SetX(53);
            for ($i = 0; $i <= $kurangkk; $i++) {
                $pdf->Cell(5, 4, '', 1, '', 'L');

            }
        }

    }

    function Kopatas($pdf, $id)
    {
        $pdf->AddFont('Times-Roman', '', 'times.php');
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        $pdf->AddPage();
        $pdf->SetFont('Times-Roman', 'B', 10);
        $desa = $this->desa->find(session('desa'));
        $kematian = $this->kematian->find($id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();

        if ($kematian->ibu_bayi == 1) {
            $keluarga = $this->keluarga->cekalamat($kematian->ibu_penduduk_id);
        } else if ($kematian->bapak_bayi == 1) {
            $keluarga = $this->keluarga->cekalamat($kematian->bapak_penduduk_id);
        } else {
            $keluarga = null;
        }
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

        $pdf->SetFont('Times-Roman', 'B', 10);
        $pdf->ln(-5);
        $pdf->SetX(160);
        $pdf->Cell(35, 10, 'Kode . F-2.28', 1, 0, 'C');
        $pdf->ln(13);
        $pdf->SetFont('Times-Roman', '', 7);
        $pdf->Cell(0, 0, strtoupper('Pemerintah Desa/Kelurahan'), 0, 0, '');
        $pdf->SetX(50);
        $pdf->SetFont('Times-Roman', '', 8);

        $pdf->Cell(0, 0, ':  ' . strtoupper($statusdesa . ' ' . $namadesa), 0, 0, '');
        $pdf->ln(4);
        $pdf->Cell(0, 0, strtoupper('Kecamatan'), 0, 0, '');
        $pdf->SetX(50);
        $pdf->Cell(0, 0, ':  ' . strtoupper($statuskecamatan . ' ' . $kecamatan), 0, 0, '');
        $pdf->ln(4);
        $pdf->Cell(0, 0, strtoupper('Kabupaten/Kota'), 0, 0, '');
        $pdf->SetX(50);
        $pdf->Cell(0, 0, ':  ' . strtoupper($status . ' ' . $kabupaten), 0, 0, '');
        $pdf->Ln(15);

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

    public function Kematian($id)
    {
//        array(215, 330)

        $pdf = new PdfClass('p', 'mm', array(210, 340));
        $pdf->is_header = false;
        $pdf->set_widths = 80;
        $pdf->set_footer = 29;
        $pdf->orientasi = 'p';
        $pdf->AddFont('Arial', '', 'arial.php');

        //Disable automatic page break
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('Surat kematian Siak');
        $this->Kopatas($pdf, $id);


        $desa = $this->desa->find(session('desa'));
        $kematian = $this->kematian->find($id);
        if ($kematian->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($kematian->penandatangan);
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
        $pdf->SetX(10);
        $pdf->Cell(190, 280, '', 1, '', 'L');

        $pdf->Ln(4);
//tanggal lahir


        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetX(14);
        $pdf->Cell(0, -10, 'FORMULIR PELAPORAN KEMATIAN', 0, '', 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(14);
        $pdf->Cell(5, -15, 'Yang bertanda tangan di bawah ini:', 0, '', 'L');
//        ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//        pelapor Kematian


        if ($kematian->pelapor_penduduk == 1) {
            $pelaporlist = $this->pribadi->find($kematian->pelapor_penduduk_id);

            $namapelaporpenduduk = $pelaporlist->nama;
            $tempatlahirpelapor = $pelaporlist->tempat_lahir;


            $hari5 = substr($pelaporlist->tanggal_lahir, 0, 2);
            $indo5 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($pelaporlist->tanggal_lahir, 3, 2) <= 9) {
                $bulan5 = $indo5[substr($pelaporlist->tanggal_lahir, 4, 1)];
            } else {
                $bulan5 = $indo5[substr($pelaporlist->tanggal_lahir, 3, 2)];
            }
            $tahun5 = substr($pelaporlist->tanggal_lahir, 6, 4);
            $tanggallahirpelapor = $hari5 . ' ' . $bulan5 . ' ' . $tahun5;
            $umurpelapor = date('Y') - $tahun5 . ' Tahun';


            if ($pelaporlist->pekerjaan_id == 89) {
                $pekerjaanpelapor = $pelaporlist->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanpelapor = $pelaporlist->pekerjaan->pekerjaan;
            }
            $keluarga = $this->keluarga->cekalamat($pelaporlist->id);
            $alamatpelapor = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatpelaporlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        if ($kematian->pelapor_penduduk == 2) {
            $pelaporlist = $this->nonpenduduk->find($kematian->pelapor_penduduk_id);

            $namapelaporpenduduk = $pelaporlist->nama;
            $tempatlahirpelapor = $pelaporlist->tempat_lahir;
            $hari5 = substr($pelaporlist->tanggal_lahir, 0, 2);
            $indo5 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($pelaporlist->tanggal_lahir, 3, 2) <= 9) {
                $bulan5 = $indo5[substr($pelaporlist->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo5[substr($pelaporlist->tanggal_lahir, 3, 2)];
            }
            $tahun5 = substr($pelaporlist->tanggal_lahir, 6, 4);
            $tanggallahirpelapor = $hari5 . ' ' . $bulan5 . ' ' . $tahun5;
            $umurpelapor = date('Y') - $tahun5 . ' Tahun';
            if ($pelaporlist->pekerjaan_id == 89) {
                $pekerjaanpelapor = $pelaporlist->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanpelapor = $pelaporlist->pekerjaan->pekerjaan;
            }
            $alamatpelapor = $pelaporlist->alamat . ' RT. ' . $pelaporlist->alamat_rt . ' RW. ' . $pelaporlist->alamat_rw;
            if ($pelaporlist->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $pelaporlist->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($pelaporlist->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $pelaporlist->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($pelaporlist->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $pelaporlist->desa->kecamatan->kecamatan;
            }
            if ($pelaporlist->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $pelaporlist->desa->kecamatan->kecamatan;
            }
            //desa
            if ($pelaporlist->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $pelaporlist->desa->desa;
            }
            if ($pelaporlist->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $pelaporlist->desa->desa;
            }
            if ($pelaporlist->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $pelaporlist->desa->desa;
            }
            if ($pelaporlist->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $pelaporlist->desa->desa;
            }
            $alamatpelaporlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;

        }


        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(7);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Nama Lengkap', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(120, -15, $namapelaporpenduduk, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kematian->pelapor_nik, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Umur', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $umurpelapor, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Pekerjaan', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $pekerjaanpelapor, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Alamat', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetWidths([8, 90]);
        $pdf->SetAligns(['', 'J']);
        $pdf->Ln(-9);
        $pdf->SetX(48);
        $pdf->Row3(['', $alamatpelapor]);
        $pdf->Ln(4);
        $pdf->Ln(-5);
        $pdf->SetX(48);
        $pdf->Row3(['', $alamatpelaporlengkap]);
        $pdf->Ln(13);
        $pdf->SetX(14);
        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(25, -15, 'Hubungan dengan yang mati    : ' . $kematian->shdrt->shdrt, 5, '', 'L');
        $pdf->SetFont('Arial', '', 8);

        $pdf->Ln(6);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Nama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        if ($kematian->jenis_penduduk == 1) {
            if ($kematian->pribadi->titel_belakang != '') {
                if ($kematian->pribadi->titel_depan != '') {
                    $namaJenaza = $kematian->pribadi->titel_depan . ' ' . $kematian->pribadi->nama . ', ' . $kematian->pribadi->titel_belakang;
                }
                if ($kematian->pribadi->titel_depan == '') {
                    $namaJenaza = $kematian->pribadi->titel_depan . '' . $kematian->pribadi->nama . ', ' . $kematian->pribadi->titel_belakang;
                }
            }
            if ($kematian->pribadi->titel_belakang == '') {
                if ($kematian->pribadi->titel_depan != '') {
                    $namaJenaza = $kematian->pribadi->titel_depan . ' ' . $kematian->pribadi->nama . '' . $kematian->pribadi->titel_belakang;
                }
                if ($kematian->pribadi->titel_depan == '') {
                    $namaJenaza = $kematian->pribadi->titel_depan . '' . $kematian->pribadi->nama . '' . $kematian->pribadi->titel_belakang;
                }
            }
            $jeniskelaminjenazah = $kematian->pribadi->jk->jk;
            $agamajenazah = $kematian->pribadi->agama->agama;
            $umurjenazah = substr($kematian->pribadi->tanggal_lahir, 6, 4);
            $keluarga = $this->keluarga->cekalamat($kematian->pribadi->id);
            $alamatjenazah = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkapjenazah = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
            $hari5 = substr($kematian->pribadi->tanggal_lahir, 0, 2);
            $indo5 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($kematian->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan5 = $indo5[substr($kematian->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan5 = $indo5[substr($kematian->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun5 = substr($kematian->pribadi->tanggal_lahir, 6, 4);
            $tanggallahirkematian = $hari5 . ' ' . $bulan5 . ' ' . $tahun5;
            $tempatlahirjenazah = $kematian->pribadi->tempat_lahir;
            $kodeprovinsi = $kematian->desa->kecamatan->kabupaten->provinsi->kode_prov;
            $kodekabupaten = $kematian->desa->kecamatan->kabupaten->kode_kab;
        }
        if ($kematian->jenis_penduduk == 2) {
            if ($kematian->non_penduduk->titel_belakang != '') {
                if ($kematian->non_penduduk->titel_depan != '') {
                    $namaJenaza = $kematian->non_penduduk->titel_depan . ' ' . $kematian->non_penduduk->nama . ', ' . $kematian->non_penduduk->titel_belakang;
                }
                if ($kematian->non_penduduk->titel_depan == '') {
                    $namaJenaza = $kematian->non_penduduk->titel_depan . '' . $kematian->non_penduduk->nama . ', ' . $kematian->non_penduduk->titel_belakang;
                }
            }
            if ($kematian->non_penduduk->titel_belakang == '') {
                if ($kematian->non_penduduk->titel_depan != '') {
                    $namaJenaza = $kematian->non_penduduk->titel_depan . ' ' . $kematian->non_penduduk->nama . '' . $kematian->non_penduduk->titel_belakang;
                }
                if ($kematian->non_penduduk->titel_depan == '') {
                    $namaJenaza = $kematian->non_penduduk->titel_depan . '' . $kematian->non_penduduk->nama . '' . $kematian->non_penduduk->titel_belakang;
                }
            }
            $jeniskelaminjenazah = $kematian->non_penduduk->jk->jk;
            $umurjenazah = substr($kematian->non_penduduk->tanggal_lahir, 6, 4);
            $agamajenazah = $kematian->non_penduduk->agama->agama;
            $kodeprovinsi = $kematian->non_penduduk->desa->kecamatan->kabupaten->provinsi->kode_prov;
            $kodekabupaten = $kematian->non_penduduk->desa->kecamatan->kabupaten->kode_kab;
            if ($kematian->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $kematian->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($kematian->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $kematian->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($kematian->non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $kematian->non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($kematian->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $kematian->non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($kematian->non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $kematian->non_penduduk->desa->desa;
            }
            if ($kematian->non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $kematian->non_penduduk->desa->desa;
            }
            if ($kematian->non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $kematian->non_penduduk->desa->desa;
            }
            if ($kematian->non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $kematian->non_penduduk->desa->desa;
            }

            $alamatjenazah = $kematian->non_penduduk->alamat . ' RT. ' . $kematian->non_penduduk->alamat_rt . ' RW. ' . $kematian->non_penduduk->alamat_rw;
            $alamatlengkapjenazah = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $hari5 = substr($kematian->non_penduduk->tanggal_lahir, 0, 2);
            $indo5 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($kematian->non_penduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan5 = $indo5[substr($kematian->non_penduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan5 = $indo5[substr($kematian->non_penduduk->tanggal_lahir, 3, 2)];
            }
            $tahun5 = substr($kematian->non_penduduk->tanggal_lahir, 6, 4);
            $tanggallahirkematian = $hari5 . ' ' . $bulan5 . ' ' . $tahun5;
            $tempatlahirjenazah = $kematian->non_penduduk->tempat_lahir;
        }
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(120, -15, $namaJenaza, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);

        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kematian->nik, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Jenis Kelamin', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $jeniskelaminjenazah, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Tanggal Lahir/Umur', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $tahunkematian = date("Y") - $umurjenazah;
        $pdf->Cell(120, -15, $tanggallahirkematian . ', ' . $tahunkematian . ' Tahun', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Agama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(120, -15, $agamajenazah, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Alamat', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(120, -15, $alamatjenazah, 0, '', 'L');
        $pdf->SetWidths([5, 170]);
        $pdf->Ln(-6);
        $pdf->Cell(41);
        $pdf->Row2(['', $alamatlengkapjenazah]);
        $pdf->Ln(13);
//        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//        Meninggal Dunia


        $pdf->SetX(14);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, -15, 'Telah meninggal dunia pada :', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(6);
        $datetime = \DateTime::createFromFormat('d/m/Y', $kematian->tanggal_kematian);
        $dayForDate = $datetime->format('D');
        if ($dayForDate == 'Sun') {
            $hariindo = strtoupper('Minggu');
        }
        if ($dayForDate == 'Mon') {
            $hariindo = strtoupper('Senin');
        }
        if ($dayForDate == 'Tue') {
            $hariindo = strtoupper('Selasa');
        }
        if ($dayForDate == 'Wed') {
            $hariindo = strtoupper('Rabu');
        }
        if ($dayForDate == 'Thu') {
            $hariindo = strtoupper('Kamis');
        }
        if ($dayForDate == 'Fri') {
            $hariindo = strtoupper('Jum`at');
        }
        if ($dayForDate == 'Sat') {
            $hariindo = strtoupper('Sabtu');
        }

        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Hari', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(120, -15, $hariindo, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $hari = substr($kematian->tanggal_kematian, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($kematian->tanggal_kematian, 3, 2) <= 9) {
            $bulan = $indo[substr($kematian->tanggal_kematian, 4, 1)];
        } else {
            $bulan = $indo[substr($kematian->tanggal_kematian, 3, 2)];
        }
        $tahun = substr($kematian->tanggal_kematian, 6, 4);

        $tanggal_kematiancetak = $hari . ' ' . $bulan . ' ' . $tahun;
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Tanggal', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $tanggal_kematiancetak, 0, '', 'L');
        $pdf->Ln(4);
        $cekwaktu = $this->kodeadministrasi->cekwaktuadministrasi();
        if ($cekwaktu != null) {
            $waktubagian = ' ' . $cekwaktu->kode;
        }
        if ($cekwaktu == null) {
            $waktubagian = '';
        }

        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Waktu', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kematian->waktu_mati . $waktubagian, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Usia ke-', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, date('Y') - $umurjenazah . ' Tahun', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Tempat Kematian', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kematian->tempat_kematian, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Sebab Kematian', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kematian->sebab_kematian, 0, '', 'L');

        $pdf->Ln(10);
        $pdf->SetX(85);
        $hari3 = substr($kematian->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($kematian->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($kematian->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($kematian->tanggal, 3, 2)];
        }
        $tahun3 = substr($kematian->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;

        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(85);

        $pdf->Cell(0, 10, 'Pelapor', 0, '', 'C');

        $pdf->Ln(17);

        $pdf->SetX(85);
        $pdf->SetFont('Arial', 'BU', 7);
        if ($pelaporlist->titel_belakang != '' && $pelaporlist->titel_depan != '') {
            $pdf->Cell(0, 10, '( ' . $pelaporlist->titel_depan . ' ' . $pelaporlist->nama . ', ' . $pelaporlist->titel_belakang . ' )', 0, '', 'C');
        } else if ($pelaporlist->titel_belakang == '' && $pelaporlist->titel_depan != '') {
            $pdf->Cell(0, 10, '( ' . $pelaporlist->titel_depan . ' ' . $pelaporlist->nama . ' )', 0, '', 'C');
        } else if ($pelaporlist->titel_belakang != '' && $pelaporlist->titel_depan == '') {
            $pdf->Cell(0, 10, '( ' . $pelaporlist->nama . ', ' . $pelaporlist->titel_belakang . ' )', 0, '', 'C');
        } else if ($pelaporlist->titel_belakang == '' && $pelaporlist->titel_depan == '') {
            $pdf->Cell(0, 10, '( ' . $pelaporlist->nama . ' )', 0, '', 'C');
        }
        $tanggal = date('d-m-y');
        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

//batas page atas dan bawah
//=========================================================================================================================================================================================


        $this->Kop($pdf, $id);
        $pdf->SetY(51);

        $kematian = $this->kematian->find($id);
        $jeniskodeadministrasi = $this->kematian->cekkodejenisadministrasi($kematian->jenis_pelayanan_id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();
        $cekwaktu = $this->kodeadministrasi->cekwaktuadministrasi();

        if ($kematian->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($kematian->penandatangan);
        }
        //tempat lahir bayi

//        $kabupatenkematian = $kematian->desa_lahir->kecamatan->kabupaten->kabupaten;

//==============================================================================================================================================================================

        // Jenazah
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 70, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'JENAZAH', 0, '', 'L');
        // nama Jenazah
        $pdf->Ln(0);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, strtoupper('1.   NIK                                                '), 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatanama = strlen($kematian->nik);

        $kurangnama = 15;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangnama; $i++) {
            $hasil = substr($kematian->nik, $i, $totalkatanama);
            $tampil = substr($hasil, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        if ($kematian->jenis_penduduk == 1) {

        }
        // nama Jenazah
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, strtoupper('2.   Nama Lengkap'), 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatanama = strlen($namaJenaza);

        $kurangnama = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangnama; $i++) {
            $hasil = substr($namaJenaza, $i, $totalkatanama);
            $tampil = substr($hasil, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        if ($jeniskelaminjenazah == 'Laki-Laki') {
            $jeniskelamin = '1';
        }
        if ($jeniskelaminjenazah == 'Perempuan') {
            $jeniskelamin = '2';
        }
        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, strtoupper('3.   Jenis Kelamin                            '), 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $jeniskelamin, 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(5, 5, strtoupper('1. Laki-Laki'), 0, '', 'L');
        $pdf->SetX(85);
        $pdf->Cell(5, 5, strtoupper('2. Perempuan'), 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '4.   TANGGAL LAHIR / UMUR                     ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(0, 5, 'TGL', 0, '', '');
        $pdf->SetX(63);
        if ($kematian->jenis_penduduk == 1) {
            $tanggaljenazah1 = substr($kematian->pribadi->tanggal_lahir, 0, 1);
            $tanggaljenazah2 = substr($kematian->pribadi->tanggal_lahir, 1, 1);
        }
        if ($kematian->jenis_penduduk == 2) {
            $tanggaljenazah1 = substr($kematian->non_penduduk->tanggal_lahir, 0, 1);
            $tanggaljenazah2 = substr($kematian->non_penduduk->tanggal_lahir, 1, 1);
        }
        $pdf->Cell(5, 4, $tanggaljenazah1, 1, '', 'L');
        $pdf->Cell(5, 4, $tanggaljenazah2, 1, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(0, 5, 'BLN', 0, '', '');
        $pdf->SetX(83);
        if ($kematian->jenis_penduduk == 1) {
            $blnjenazah1 = substr($kematian->pribadi->tanggal_lahir, 3, 1);
            $blnjenazah2 = substr($kematian->pribadi->tanggal_lahir, 4, 1);
        }
        if ($kematian->jenis_penduduk == 2) {
            $blnjenazah1 = substr($kematian->non_penduduk->tanggal_lahir, 3, 1);
            $blnjenazah2 = substr($kematian->non_penduduk->tanggal_lahir, 4, 1);
        }
        $pdf->Cell(5, 4, $blnjenazah1, 1, '', 'L');
        $pdf->Cell(5, 4, $blnjenazah2, 1, '', 'L');
        $pdf->SetX(95);
        $pdf->Cell(0, 5, 'THN', 0, '', '');
        $pdf->SetX(103);
        if ($kematian->jenis_penduduk == 1) {
            $thnjenazah1 = substr($kematian->pribadi->tanggal_lahir, 6, 1);
            $thnjenazah2 = substr($kematian->pribadi->tanggal_lahir, 7, 1);
            $thnjenazah3 = substr($kematian->pribadi->tanggal_lahir, 8, 1);
            $thnjenazah4 = substr($kematian->pribadi->tanggal_lahir, 9, 1);
        }
        if ($kematian->jenis_penduduk == 2) {
            $thnjenazah1 = substr($kematian->non_penduduk->tanggal_lahir, 6, 1);
            $thnjenazah2 = substr($kematian->non_penduduk->tanggal_lahir, 7, 1);
            $thnjenazah3 = substr($kematian->non_penduduk->tanggal_lahir, 8, 1);
            $thnjenazah4 = substr($kematian->non_penduduk->tanggal_lahir, 9, 1);
        }
        $pdf->Cell(5, 4, $thnjenazah1, 1, '', 'L');
        $pdf->Cell(5, 4, $thnjenazah2, 1, '', 'L');
        $pdf->Cell(5, 4, $thnjenazah3, 1, '', 'L');
        $pdf->Cell(5, 4, $thnjenazah4, 1, '', 'L');
        $pdf->SetX(128);
        $pdf->Cell(0, 5, 'UMUR', 0, '', '');
        $pdf->SetX(143);
        if ($kematian->jenis_penduduk == 1) {
            $thnkurangjenazah = substr($kematian->pribadi->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kematian->jenis_penduduk == 2) {
            $thnkurangjenazah = substr($kematian->non_penduduk->tanggal_lahir, 6, 4) - date('Y');
        }

        if (substr($thnkurangjenazah, 2, 1) == '') {
            $umurjenazah1 = substr($thnkurangjenazah, 1, 1);
            $pdf->Cell(5, 4, '0', 1, '', 'L');
            $pdf->Cell(5, 4, $umurjenazah1, 1, '', 'L');
        }
        if (substr($thnkurangjenazah, 2, 1) != '') {
            $umurjenazah1 = substr($thnkurangjenazah, 1, 1);
            $umurjenazah2 = substr($thnkurangjenazah, 2, 1);
            $pdf->Cell(5, 4, $umurjenazah1, 1, '', 'L');
            $pdf->Cell(5, 4, $umurjenazah2, 1, '', 'L');
        }

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(34, 4, strtoupper('5.   Tempat kelahiran                   '), 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatatempatlahir = strlen($tempatlahirjenazah);

        $kurangtempatlahir = 14;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangtempatlahir; $i++) {
            $hasil1 = substr($tempatlahirjenazah, $i, $totalkatatempatlahir);
            $tampil1 = substr($hasil1, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array(strtoupper($tampil1));
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        $pdf->SetX(128);
        $pdf->Cell(5, 4, 'Kode Prov', 0, '', 'L');
        $pdf->SetX(143);
        $pdf->Cell(5, 5, substr($kodeprovinsi, 0, 1), 1, '', 'L');
        $pdf->Cell(5, 5, substr($kodeprovinsi, 1, 1), 1, '', 'L');
        $pdf->SetX(155);
        $pdf->Cell(5, 4, 'Kode Kab', 0, '', 'L');
        $pdf->SetX(170);
        $pdf->Cell(5, 5, substr($kodekabupaten, 0, 1), 1, '', 'L');
        $pdf->Cell(5, 5, substr($kodekabupaten, 1, 1), 1, '', 'L');
        if ($kematian->jenis_penduduk == 1) {
            $pekerjaanjenazahid = $kematian->pribadi->pekerjaan_id;
            if ($kematian->pribadi->pekerjaan_id == 89) {
                $pekerjaanjenzah = $kematian->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanjenazah = $kematian->pribadi->pekerjaan->pekerjaan;
            }

            if ($kematian->pribadi->agama->id == 1) {
                $islam = 'X';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($kematian->pribadi->agama->id == 2) {
                $islam = '';
                $kristen = 'X';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($kematian->pribadi->agama->id == 3) {
                $islam = '';
                $kristen = '';
                $khatolik = 'X';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($kematian->pribadi->agama->id == 4) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = 'X';
                $budha = '';
                $lainnya = '';
            }
            if ($kematian->pribadi->agama->id == 5) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = 'X';
                $lainnya = '';
            }
            if ($kematian->pribadi->agama->id == 6) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = 'X';
            }
            if ($kematian->pribadi->agama->id == 7) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = 'X';
            }
        }
        if ($kematian->jenis_penduduk == 2) {
            $pekerjaanjenazahid = $kematian->non_penduduk->pekerjaan_id;
            if ($kematian->non_penduduk->pekerjaan_id == 89) {
                $pekerjaanjenzah = $kematian->non_penduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanjenazah = $kematian->non_penduduk->pekerjaan->pekerjaan;
            }

            if ($kematian->non_penduduk->agama->id == 1) {
                $islam = 'X';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($kematian->non_penduduk->agama->id == 2) {
                $islam = '';
                $kristen = 'X';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($kematian->non_penduduk->agama->id == 3) {
                $islam = '';
                $kristen = '';
                $khatolik = 'X';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($kematian->non_penduduk->agama->id == 4) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = 'X';
                $budha = '';
                $lainnya = '';
            }
            if ($kematian->non_penduduk->agama->id == 5) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = 'X';
                $lainnya = '';
            }
            if ($kematian->non_penduduk->agama->id == 6) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = 'X';
            }
            if ($kematian->non_penduduk->agama->id == 7) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = 'X';
            }
        }
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '6.   AGAMA', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->Cell(-2);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(5, 5, $islam, 1, '', 'L');
        $pdf->Cell(5, 5, 'Islam', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(5, 5, $kristen, 1, '', 'L');
        $pdf->Cell(5, 5, 'Kristen', 0, '', 'L');
        $pdf->SetX(100);
        $pdf->Cell(5, 5, $khatolik, 1, '', 'L');
        $pdf->Cell(5, 5, 'Katholik', 0, '', 'L');
        $pdf->SetX(125);
        $pdf->Cell(5, 5, $hindu, 1, '', 'L');
        $pdf->Cell(5, 5, 'Hindu', 0, '', 'L');
        $pdf->SetX(150);
        $pdf->Cell(5, 5, $budha, 1, '', 'L');
        $pdf->Cell(5, 5, 'Budha', 0, '', 'L');
        $pdf->SetX(170);
        $pdf->Cell(5, 5, $lainnya, 1, '', 'L');
        $pdf->Cell(5, 5, 'Lainnya', 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '7.   PEKERJAAN                                       ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pekerjaanjenazah = substr($pekerjaanjenazahid, 0, 1);
        $pekerjaanjenazah2 = substr($pekerjaanjenazahid, 1, 1);

        if ($pekerjaanjenazahid == 89) {
            $pekerjaannamajenazah = $pekerjaanjenazah;
            $totalkatanamapekerjaanjenazah = strlen($pekerjaannamajenazah);
            $kurangnamapekerjaan = 26;
            for ($i = 0; $i <= $kurangnamapekerjaan; $i++) {
                $hasil5 = substr($totalkatanamapekerjaanjenazah, $i, $totalkatanamapekerjaanjenazah);
                $tampil5 = substr($hasil5, 0, 1);
                $widd = 5;
                $pdf->SetFont('Arial', '', 7);
                $widths = array($widd);
                $caption = array($tampil5);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
        } else {
            if ($pekerjaanjenazah2 != '') {
                $pdf->Cell(5, 4, $pekerjaanjenazah, 1, '', 'L');
                $pdf->Cell(5, 4, $pekerjaanjenazah2, 1, '', 'L');
            }
            if ($pekerjaanjenazah2 == '') {
                $pdf->Cell(5, 4, '0', 1, '', 'L');
                $pdf->Cell(5, 4, $pekerjaanjenazah, 1, '', 'L');
            }
        }
        // ALamat Kematian

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '8.   ALAMAT             ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->jenis_penduduk == 1) {
            $keluargajenazah = $this->keluarga->cekalamat($kematian->penduduk_id);
            $pdf->Cell(135, 4, strtoupper($keluargajenazah->alamat), 1, '', 'L');
        }
        if ($kematian->jenis_penduduk == 2) {
            $pdf->Cell(135, 4, strtoupper($kematian->non_penduduk->alamat), 1, '', 'L');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN     :', 0, '', '');

        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->jenis_penduduk == 1) {

            $pdf->Cell(40, 4, strtoupper($kematian->desa->desa), 1, '', '');
        }
        if ($kematian->jenis_penduduk == 2) {
            $pdf->Cell(40, 4, strtoupper($kematian->non_penduduk->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->jenis_penduduk == 1) {
            $pdf->Cell(40, 5, strtoupper($kematian->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kematian->jenis_penduduk == 2) {
            $pdf->Cell(40, 5, strtoupper($kematian->non_penduduk->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->jenis_penduduk == 1) {

            $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kematian->jenis_penduduk == 2) {
            $pdf->Cell(40, 4, strtoupper($kematian->non_penduduk->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -6, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->jenis_penduduk == 1) {
            $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kematian->jenis_penduduk == 2) {
            $pdf->Cell(40, 4, strtoupper($kematian->non_penduduk->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, strtoupper('9.   Anak Ke'), 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $kematian->anak_ke, 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(5, 5, strtoupper('1,'), 0, '', 'L');
        $pdf->SetX(65);
        $pdf->Cell(5, 5, strtoupper('2,'), 0, '', 'L');
        $pdf->SetX(70);
        $pdf->Cell(5, 5, strtoupper('3,'), 0, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(5, 5, strtoupper('4,'), 0, '', 'L');
        if ($kematian->anak_ke >= 5) {
            $pdf->SetX(80);
            $pdf->Cell(5, 5, strtoupper('4,'), 0, '', 'L');
        }
        if ($kematian->anak_ke <= 4) {
            $pdf->SetX(80);
            $pdf->Cell(5, 5, '........', 0, '', 'L');
        }
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '10.   TANGGAL LAHIR / UMUR                     ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(0, 5, 'TGL', 0, '', '');
        $pdf->SetX(63);
        $tanggaljenazah1 = substr($kematian->tanggal_kematian, 0, 1);
        $tanggaljenazah2 = substr($kematian->tanggal_kematian, 1, 1);
        $pdf->Cell(5, 4, $tanggaljenazah1, 1, '', 'L');
        $pdf->Cell(5, 4, $tanggaljenazah2, 1, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(0, 5, 'BLN', 0, '', '');
        $pdf->SetX(83);
        $blnjenazah1 = substr($kematian->tanggal_kematian, 3, 1);
        $blnjenazah2 = substr($kematian->tanggal_kematian, 4, 1);
        $pdf->Cell(5, 4, $blnjenazah1, 1, '', 'L');
        $pdf->Cell(5, 4, $blnjenazah2, 1, '', 'L');
        $pdf->SetX(95);
        $pdf->Cell(0, 5, 'THN', 0, '', '');
        $pdf->SetX(103);
        $thnjenazah1 = substr($kematian->tanggal_kematian, 6, 1);
        $thnjenazah2 = substr($kematian->tanggal_kematian, 7, 1);
        $thnjenazah3 = substr($kematian->tanggal_kematian, 8, 1);
        $thnjenazah4 = substr($kematian->tanggal_kematian, 9, 1);
        $pdf->Cell(5, 4, $thnjenazah1, 1, '', 'L');
        $pdf->Cell(5, 4, $thnjenazah2, 1, '', 'L');
        $pdf->Cell(5, 4, $thnjenazah3, 1, '', 'L');
        $pdf->Cell(5, 4, $thnjenazah4, 1, '', 'L');

        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, strtoupper('11.   Waktu                            '), 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 5, substr($kematian->waktu_mati, 0, 1), 1, '', 'L');
        $pdf->Cell(5, 5, substr($kematian->waktu_mati, 1, 1), 1, '', 'L');
        $pdf->Cell(5, 5, substr($kematian->waktu_mati, 3, 1), 1, '', 'L');
        $pdf->Cell(5, 5, substr($kematian->waktu_mati, 4, 1), 1, '', 'L');
        $pdf->Ln(5);
        if ($kematian->sebab_kematian == 'Sakit biasa/tua') {
            $sebabkematian = 1;
        } else if ($kematian->sebab_kematian == 'Wabah Penyakit') {
            $sebabkematian = 2;
        } else if ($kematian->sebab_kematian == 'Kecelakaan') {
            $sebabkematian = 3;
        } else if ($kematian->sebab_kematian == 'Kriminalisasi') {
            $sebabkematian = 4;
        } else if ($kematian->sebab_kematian == 'Bunuh Diri') {
            $sebabkematian = 5;
        } else {
            $sebabkematian = 6;
        }
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '12.   Sebab Kematian', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $sebabkematian, 1, '', 'L');

//        $pdf->Cell(0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(5, 5, '1. Sakit biasa/tua', 0, '', 'L');
        $pdf->SetX(83);
        $pdf->Cell(5, 5, '2. Wabah Penyakit', 0, '', 'L');
        $pdf->SetX(111);
        $pdf->Cell(5, 5, '3. Kecelakaan', 0, '', 'L');
        $pdf->SetX(132);
        $pdf->Cell(5, 5, '4. Kriminalisasi', 0, '', 'L');
        $pdf->SetX(154);
        $pdf->Cell(5, 5, '5. Bunuh Diri', 0, '', 'L');
        $pdf->SetX(173);
        $pdf->Cell(5, 5, '6. Lainnya', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(34, 4, strtoupper('13.   Tempat Kematian                   '), 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatatempatlahir = strlen($kematian->tempat_kematian);

        $kurangtempatlahir = 15;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangtempatlahir; $i++) {
            $hasil1 = substr($kematian->tempat_kematian, $i, $totalkatatempatlahir);
            $tampil1 = substr($hasil1, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array(strtoupper($tampil1));
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        if ($kematian->menerangkan_kematian == 'Dokter') {
            $menerangkankematian = 1;
        } else if ($kematian->menerangkan_kematian == 'Tenaga Kesehatan') {
            $menerangkankematian = 2;
        } else if ($kematian->menerangkan_kematian == 'Kepolisian') {
            $menerangkankematian = 3;
        } else {
            $menerangkankematian = 4;
        }
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '14.   Yang Menerangkan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $menerangkankematian, 1, '', 'L');

//        $pdf->Cell(0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(5, 5, '1. Dokter', 0, '', 'L');
        $pdf->SetX(83);
        $pdf->Cell(5, 5, '2. Tenaga Kesehatan', 0, '', 'L');
        $pdf->SetX(120);
        $pdf->Cell(5, 5, '3. Kepolisian', 0, '', 'L');
        $pdf->SetX(150);
        $pdf->Cell(5, 5, '4. Lainnya', 0, '', 'L');


//        ============================================================================================================================================================================================================================
        // BAPAK

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 33, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'AYAH', 0, '', 'L');

        // nik bapak
        if ($kematian->bapak_bayi_nik == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($kematian->penduduk_id);
            $nikbapakpenduduk = $orangtuabapak2->nik;
        } else {
            $nikbapakpenduduk = $kematian->bapak_bayi_nik;
        }
        $pdf->Ln(1);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '1.   NIK         ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatanikbapak = strlen($nikbapakpenduduk);
        $kurangnikbapak = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnikbapak; $i++) {
            $hasil3 = substr($nikbapakpenduduk, $i, $totalkatanikbapak);
            $tampil3 = substr($hasil3, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil3);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);
        }

        //nama lengkap bapak

        $pdf->Ln(4);
        $pdf->Cell(35, 5, '2.   NAMA LENGKAP                                :', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->bapak_bayi == 1) {
            $namabapakpenduduk = $kematian->pribadibapak->nama;
        } else if ($kematian->bapak_bayi == 2) {
            $namabapakpenduduk = $kematian->non_penduduk_bapak->nama;
        } else if ($kematian->bapak_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($kematian->penduduk_id);
            $namabapakpenduduk = $orangtuabapak2->nama;
        }

        $totalkatanamabapak = strlen($namabapakpenduduk);
        $kurangnamabapak = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnamabapak; $i++) {
            $hasil4 = substr($namabapakpenduduk, $i, $totalkatanamabapak);
            $tampil4 = substr($hasil4, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil4);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }

        //tanggal lahir/umur bapak

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '3.   TANGGAL LAHIR / UMUR                     ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(0, 5, 'TGL', 0, '', '');
        $pdf->SetX(63);
        if ($kematian->bapak_bayi == '--') {
            $tanggalbapak1 = '';
            $tanggalbapak2 = '';
        } else {
            if ($kematian->bapak_bayi == 1) {
                $tanggalbapak1 = substr($kematian->pribadibapak->tanggal_lahir, 0, 1);
                $tanggalbapak2 = substr($kematian->pribadibapak->tanggal_lahir, 1, 1);
            }
            if ($kematian->bapak_bayi == 2) {
                $tanggalbapak1 = substr($kematian->non_penduduk_bapak->tanggal_lahir, 0, 1);
                $tanggalbapak2 = substr($kematian->non_penduduk_bapak->tanggal_lahir, 1, 1);
            }
        }
        $pdf->Cell(5, 4, $tanggalbapak1, 1, '', 'L');
        $pdf->Cell(5, 4, $tanggalbapak2, 1, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(0, 5, 'BLN', 0, '', '');
        $pdf->SetX(83);
        if ($kematian->bapak_bayi == '--') {
            $blnbapak1 = '';
            $blnbapak2 = '';
        } else {
            if ($kematian->bapak_bayi == 1) {
                $blnbapak1 = substr($kematian->pribadibapak->tanggal_lahir, 3, 1);
                $blnbapak2 = substr($kematian->pribadibapak->tanggal_lahir, 4, 1);
            }
            if ($kematian->bapak_bayi == 2) {
                $blnbapak1 = substr($kematian->non_penduduk_bapak->tanggal_lahir, 3, 1);
                $blnbapak2 = substr($kematian->non_penduduk_bapak->tanggal_lahir, 4, 1);
            }
        }
        $pdf->Cell(5, 4, $blnbapak1, 1, '', 'L');
        $pdf->Cell(5, 4, $blnbapak2, 1, '', 'L');
        $pdf->SetX(95);
        $pdf->Cell(0, 5, 'THN', 0, '', '');
        $pdf->SetX(103);
        if ($kematian->bapak_bayi == '--') {
            $thnbapak1 = '';
            $thnbapak2 = '';
            $thnbapak3 = '';
            $thnbapak4 = '';
        } else {
            if ($kematian->bapak_bayi == 1) {
                $thnbapak1 = substr($kematian->pribadibapak->tanggal_lahir, 6, 1);
                $thnbapak2 = substr($kematian->pribadibapak->tanggal_lahir, 7, 1);
                $thnbapak3 = substr($kematian->pribadibapak->tanggal_lahir, 8, 1);
                $thnbapak4 = substr($kematian->pribadibapak->tanggal_lahir, 9, 1);
            }
            if ($kematian->bapak_bayi == 2) {
                $thnbapak1 = substr($kematian->non_penduduk_bapak->tanggal_lahir, 6, 1);
                $thnbapak2 = substr($kematian->non_penduduk_bapak->tanggal_lahir, 7, 1);
                $thnbapak3 = substr($kematian->non_penduduk_bapak->tanggal_lahir, 8, 1);
                $thnbapak4 = substr($kematian->non_penduduk_bapak->tanggal_lahir, 9, 1);
            }
        }
        $pdf->Cell(5, 4, $thnbapak1, 1, '', 'L');
        $pdf->Cell(5, 4, $thnbapak2, 1, '', 'L');
        $pdf->Cell(5, 4, $thnbapak3, 1, '', 'L');
        $pdf->Cell(5, 4, $thnbapak4, 1, '', 'L');
        $pdf->SetX(128);
        $pdf->Cell(0, 5, 'UMUR', 0, '', '');
        $pdf->SetX(143);
        if ($kematian->bapak_bayi == '--') {
            $thnkurangbapak = '';
        } else {
            if ($kematian->bapak_bayi == 1) {
                $thnkurangbapak = substr($kematian->pribadibapak->tanggal_lahir, 6, 4) - date('Y');
            }
            if ($kematian->bapak_bayi == 2) {
                $thnkurangbapak = substr($kematian->non_penduduk_bapak->tanggal_lahir, 6, 4) - date('Y');
            }
        }
        if ($kematian->bapak_bayi == '--') {
            $pdf->Cell(5, 4, '', 1, '', 'L');
            $pdf->Cell(5, 4, '', 1, '', 'L');
        } else {
            if (substr($thnkurangbapak, 2, 1) == '') {
                $umurbapak1 = substr($thnkurangbapak, 1, 1);
                $pdf->Cell(5, 4, '0', 1, '', 'L');
                $pdf->Cell(5, 4, $umurbapak1, 1, '', 'L');
            }
            if (substr($thnkurangbapak, 2, 1) != '') {
                $umurbapak1 = substr($thnkurangbapak, 1, 1);
                $umurbapak2 = substr($thnkurangbapak, 2, 1);
                $pdf->Cell(5, 4, $umurbapak1, 1, '', 'L');
                $pdf->Cell(5, 4, $umurbapak2, 1, '', 'L');
            }
        }
        // pekerjaan bapak

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '4.   PEKERJAAN                                       ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->bapak_bayi == '--') {
            $totalkatanamapekerjaanbapak = 0;
            $kurangnamapekerjaan = 26;
            for ($i = 0; $i <= $kurangnamapekerjaan; $i++) {
                $hasil5 = substr($totalkatanamapekerjaanbapak, $i, $totalkatanamapekerjaanbapak);
                $tampil5 = substr($hasil5, 0, 1);
                $widd = 5;
                $pdf->SetFont('Arial', '', 7);
                $widths = array($widd);
                $caption = array($tampil5);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
        } else {
            if ($kematian->bapak_bayi == 1) {
                $pekerjaanbapak = substr($kematian->pribadibapak->pekerjaan_id, 0, 1);
                $pekerjaanbapak2 = substr($kematian->pribadibapak->pekerjaan_id, 1, 1);

                if ($kematian->pribadibapak->pekerjaan_id == 89) {
                    $pekerjaannamabapak = $kematian->pribadibapak->pekerjaan_lain->pekerjaan_lain;
                    $totalkatanamapekerjaanbapak = strlen($pekerjaannamabapak);
                    $kurangnamapekerjaan = 26;
                    for ($i = 0; $i <= $kurangnamapekerjaan; $i++) {
                        $hasil5 = substr($totalkatanamapekerjaanbapak, $i, $totalkatanamapekerjaanbapak);
                        $tampil5 = substr($hasil5, 0, 1);
                        $widd = 5;
                        $pdf->SetFont('Arial', '', 7);
                        $widths = array($widd);
                        $caption = array($tampil5);
                        $pdf->SetWidths($widths);
                        $pdf->FancyRow2($caption);

                    }
                } else {
                    if ($pekerjaanbapak2 != '') {
                        $pdf->Cell(5, 4, $pekerjaanbapak, 1, '', 'L');
                        $pdf->Cell(5, 4, $pekerjaanbapak2, 1, '', 'L');
                    }
                    if ($pekerjaanbapak2 == '') {
                        $pdf->Cell(5, 4, '0', 1, '', 'L');
                        $pdf->Cell(5, 4, $pekerjaanbapak, 1, '', 'L');
                    }
                }
            }
            if ($kematian->bapak_bayi == 2) {
                $pekerjaanbapak = substr($kematian->non_penduduk_bapak->pekerjaan_id, 0, 1);
                $pekerjaanbapak2 = substr($kematian->non_penduduk_bapak->pekerjaan_id, 1, 1);
                if ($kematian->non_penduduk_bapak->pekerjaan_id == 89) {
                    $pekerjaannamabapak = $kematian->non_penduduk_bapak->pekerjaan_lain->pekerjaan_lain;
                    $totalkatanamapekerjaanbapak = strlen($pekerjaannamabapak);
                    $kurangnamapekerjaan = 26;
                    for ($i = 0; $i <= $kurangnamapekerjaan; $i++) {
                        $hasil5 = substr($pekerjaannamabapak, $i, $totalkatanamapekerjaanbapak);
                        $tampil5 = substr($hasil5, 0, 1);
                        $widd = 5;
                        $pdf->SetFont('Arial', '', 7);
                        $widths = array($widd);
                        $caption = array($tampil5);
                        $pdf->SetWidths($widths);
                        $pdf->FancyRow2($caption);
                    }
                } else {
                    if ($pekerjaanbapak2 != '') {
                        $pdf->Cell(5, 4, $pekerjaanbapak, 1, '', 'L');
                        $pdf->Cell(5, 4, $pekerjaanbapak2, 1, '', 'L');
                    }
                    if ($pekerjaanbapak2 == '') {
                        $pdf->Cell(5, 4, '0', 1, '', 'L');
                        $pdf->Cell(5, 4, $pekerjaanbapak, 1, '', 'L');
                    }
                }
            }
        }
        // ALamat Kematian

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '5.   ALAMAT             ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->bapak_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $keluargabapak = $this->keluarga->cekalamat($kematian->penduduk_id);
                $pdf->Cell(135, 4, strtoupper($keluargabapak->alamat), 1, '', 'L');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $alamatdomisili = $orangtuabukanpendudukbapak->alamat;
                $pdf->Cell(135, 4, strtoupper($alamatdomisili->alamat), 1, '', 'L');
            }
        } else {
            if ($kematian->bapak_bayi == 1) {
                $keluargabapak = $this->keluarga->cekalamat($kematian->bapak_penduduk_id);
                $pdf->Cell(135, 4, strtoupper($keluargabapak->alamat), 1, '', 'L');
            }
            if ($kematian->bapak_bayi == 2) {
                $pdf->Cell(135, 4, strtoupper($kematian->non_penduduk_bapak->alamat), 1, '', 'L');
            }
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN     :', 0, '', '');

        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->bapak_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->desa), 1, '', '');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $pdf->Cell(40, 4, strtoupper($orangtuabukanpendudukbapak->desa->desa), 1, '', '');
            }
        } else {
            if ($kematian->bapak_bayi == 1) {

                $pdf->Cell(40, 4, strtoupper($kematian->desa->desa), 1, '', '');
            }
            if ($kematian->bapak_bayi == 2) {
                $pdf->Cell(40, 4, strtoupper($kematian->non_penduduk_bapak->desa->desa), 1, '', '');
            }
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->bapak_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $pdf->Cell(40, 5, strtoupper($kematian->desa->kecamatan->kecamatan), 1, '', '');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $pdf->Cell(40, 5, strtoupper($orangtuabukanpendudukbapak->desa->kecamatan->kecamatan), 1, '', '');
            }
        } else {
            if ($kematian->bapak_bayi == 1) {
                $pdf->Cell(40, 5, strtoupper($kematian->desa->kecamatan->kecamatan), 1, '', '');
            }
            if ($kematian->bapak_bayi == 2) {
                $pdf->Cell(40, 5, strtoupper($kematian->non_penduduk_bapak->desa->kecamatan->kecamatan), 1, '', '');
            }
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->bapak_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $pdf->Cell(40, 4, strtoupper($orangtuabukanpendudukbapak->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
            }
        } else {
            if ($kematian->bapak_bayi == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
            }
            if ($kematian->bapak_bayi == 2) {
                $pdf->Cell(40, 4, strtoupper($kematian->non_penduduk_bapak->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
            }
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -6, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->bapak_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $pdf->Cell(40, 4, strtoupper($orangtuabukanpendudukbapak->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
            }
        } else {

            if ($kematian->bapak_bayi == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
            }
            if ($kematian->bapak_bayi == 2) {
                $pdf->Cell(40, 4, strtoupper($kematian->non_penduduk_bapak->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
            }
        }
//        ============================================================================================================================================================================================================================
//         IBU

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 33, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'IBU', 0, '', 'L');

        // nik ibu
        if ($kematian->ibu_bayi_nik == '--') {
            $orangtuaibu2 = $this->orangtua->cekorangtuaibu($kematian->penduduk_id);
            $nikpenduduk = $orangtuaibu2->nik;
        } else {
            $nikpenduduk = $kematian->ibu_bayi_nik;
        }
        $pdf->Ln(0);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '1.   NIK                                                ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatanikibu = strlen($nikpenduduk);

        $kurangnikibu = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangnikibu; $i++) {
            $hasilibu3 = substr($nikpenduduk, $i, $totalkatanikibu);
            $tampilibu3 = substr($hasilibu3, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array(strtoupper($tampilibu3));
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);
        }

//        // nama lengkap ibu bayi
//
        $pdf->Ln(4);
        $pdf->Cell(35, 5, '2.   NAMA LENGKAP                               ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->ibu_bayi_nik == '--') {
            $orangtuaibu2 = $this->orangtua->cekorangtuaibu($kematian->penduduk_id);
            $namaibupenduduk = $orangtuaibu2->nama;
        } else {

            if ($kematian->ibu_bayi == 1) {
                $namaibupenduduk = $kematian->pribadiibu->nama;
            }
            if ($kematian->ibu_bayi == 2) {
                $namaibupenduduk = $kematian->non_pendudukibu->nama;
            }
        }
        $totalkatanamaibu = strlen($namaibupenduduk);
        $kurangnamaibu = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnamaibu; $i++) {
            $hasilibu4 = substr($namaibupenduduk, $i, $totalkatanamaibu);
            $tampilibu4 = substr($hasilibu4, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampilibu4);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);
        }
//
//        //tanggal lahir/umur ibu
//
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '3.   TANGGAL LAHIR / UMUR                     ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(0, 5, 'TGL', 0, '', '');
        $pdf->SetX(63);
        if ($kematian->ibu_bayi_nik == '--') {
            $tglibu1 = '';
            $tglibu2 = '';
        } else {
            if ($kematian->ibu_bayi == 1) {
                $tglibu1 = substr($kematian->pribadiibu->tanggal_lahir, 0, 1);
                $tglibu2 = substr($kematian->pribadiibu->tanggal_lahir, 1, 1);
            }
            if ($kematian->ibu_bayi == 2) {
                $tglibu1 = substr($kematian->non_pendudukibu->tanggal_lahir, 0, 1);
                $tglibu2 = substr($kematian->non_pendudukibu->tanggal_lahir, 1, 1);
            }
        }
        $pdf->Cell(5, 4, $tglibu1, 1, '', 'L');
        $pdf->Cell(5, 4, $tglibu2, 1, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(0, 5, 'BLN', 0, '', '');
        $pdf->SetX(83);
        if ($kematian->ibu_bayi_nik == '--') {
            $blnibu1 = '';
            $blnibu2 = '';
        } else {

            if ($kematian->ibu_bayi == 1) {
                $blnibu1 = substr($kematian->pribadiibu->tanggal_lahir, 3, 1);
                $blnibu2 = substr($kematian->pribadiibu->tanggal_lahir, 4, 1);
            }
            if ($kematian->ibu_bayi == 2) {
                $blnibu1 = substr($kematian->non_pendudukibu->tanggal_lahir, 3, 1);
                $blnibu2 = substr($kematian->non_pendudukibu->tanggal_lahir, 4, 1);
            }
        }
        $pdf->Cell(5, 4, $blnibu1, 1, '', 'L');
        $pdf->Cell(5, 4, $blnibu2, 1, '', 'L');
        $pdf->SetX(95);
        $pdf->Cell(0, 5, 'THN', 0, '', '');
        $pdf->SetX(103);
        if ($kematian->ibu_bayi_nik == '--') {
            $thnibu1 = '';
            $thnibu2 = '';
            $thnibu3 = '';
            $thnibu4 = '';
        } else {

            if ($kematian->ibu_bayi == 1) {
                $thnibu1 = substr($kematian->pribadiibu->tanggal_lahir, 6, 1);
                $thnibu2 = substr($kematian->pribadiibu->tanggal_lahir, 7, 1);
                $thnibu3 = substr($kematian->pribadiibu->tanggal_lahir, 8, 1);
                $thnibu4 = substr($kematian->pribadiibu->tanggal_lahir, 9, 1);
            }
            if ($kematian->ibu_bayi == 2) {
                $thnibu1 = substr($kematian->non_pendudukibu->tanggal_lahir, 6, 1);
                $thnibu2 = substr($kematian->non_pendudukibu->tanggal_lahir, 7, 1);
                $thnibu3 = substr($kematian->non_pendudukibu->tanggal_lahir, 8, 1);
                $thnibu4 = substr($kematian->non_pendudukibu->tanggal_lahir, 9, 1);
            }
        }
        $pdf->Cell(5, 4, $thnibu1, 1, '', 'L');
        $pdf->Cell(5, 4, $thnibu2, 1, '', 'L');
        $pdf->Cell(5, 4, $thnibu3, 1, '', 'L');
        $pdf->Cell(5, 4, $thnibu4, 1, '', 'L');
        $pdf->SetX(128);
        $pdf->Cell(0, 5, 'UMUR', 0, '', '');
        $pdf->SetX(143);
        if ($kematian->ibu_bayi == 1) {
            $thnkurangibu = substr($kematian->pribadiibu->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kematian->ibu_bayi == 2) {
            $thnkurangibu = substr($kematian->non_pendudukibu->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kematian->ibu_bayi_nik == '--') {
            $pdf->Cell(5, 5, '', 1, '', 'L');
            $pdf->Cell(5, 5, '', 1, '', 'L');
        } else {

            if (substr($thnkurangibu, 2, 1) == '') {
                $umur1 = substr($thnkurangibu, 1, 1);
                $pdf->Cell(5, 5, '0', 1, '', 'L');
                $pdf->Cell(5, 5, $umur1, 1, '', 'L');
            }
            if (substr($thnkurangibu, 2, 1) != '') {
                $umur1 = substr($thnkurangibu, 1, 1);
                $umur2 = substr($thnkurangibu, 2, 1);
                $pdf->Cell(5, 5, $umur1, 1, '', 'L');
                $pdf->Cell(5, 5, $umur2, 1, '', 'L');
            }
        }

        // pekerjaan ibu

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '4.   PEKERJAAN', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->ibu_bayi == '--') {
            $totalkatanamapekerjaanbapak = 0;
            $kurangnamapekerjaan = 26;
            for ($i = 0; $i <= $kurangnamapekerjaan; $i++) {
                $hasil5 = substr($totalkatanamapekerjaanbapak, $i, $totalkatanamapekerjaanbapak);
                $tampil5 = substr($hasil5, 0, 1);
                $widd = 5;
                $pdf->SetFont('Arial', '', 7);
                $widths = array($widd);
                $caption = array($tampil5);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
        } else {

            if ($kematian->ibu_bayi == 1) {
                $pekerjaanibu = substr($kematian->pribadiibu->pekerjaan_id, 0, 1);
                $pekerjaanibu2 = substr($kematian->pribadiibu->pekerjaan_id, 1, 1);
                if ($kematian->pribadiibu->pekerjaan_id == 89) {
                    $pekerjaanibunama = $kematian->pribadiibu->pekerjaan_lain->pekerjaan_lain;
                    $totalkatanamapekerjaanibu = strlen($pekerjaanibunama);
                    $kurangnamapekerjaan = 26;
                    for ($i = 0; $i <= $kurangnamapekerjaan; $i++) {
                        $hasil5 = substr($pekerjaanibunama, $i, $totalkatanamapekerjaanibu);
                        $tampil5 = substr($hasil5, 0, 1);
                        $widd = 5;
                        $pdf->SetFont('Arial', '', 7);
                        $widths = array($widd);
                        $caption = array($tampil5);
                        $pdf->SetWidths($widths);
                        $pdf->FancyRow2($caption);

                    }
                } else {
                    if ($pekerjaanibu2 != '') {
                        $pdf->Cell(5, 4, $pekerjaanibu, 1, '', 'L');
                        $pdf->Cell(5, 4, $pekerjaanibu2, 1, '', 'L');
                    }
                    if ($pekerjaanibu2 == '') {
                        $pdf->Cell(5, 4, '0', 1, '', 'L');
                        $pdf->Cell(5, 4, $pekerjaanibu, 1, '', 'L');
                    }
                }
            }
            if ($kematian->ibu_bayi == 2) {
                $pekerjaanibu = substr($kematian->non_pendudukibu->pekerjaan_id, 0, 1);
                $pekerjaanibu2 = substr($kematian->non_pendudukibu->pekerjaan_id, 1, 1);
                if ($kematian->non_pendudukibu->pekerjaan_id == 89) {
                    $pekerjaanibunama = $kematian->non_pendudukibu->pekerjaan_lain->pekerjaan_lain;
                    $totalkatanamapekerjaanibu = strlen($pekerjaanibunama);
                    $kurangnamapekerjaan = 26;
                    for ($i = 0; $i <= $kurangnamapekerjaan; $i++) {
                        $hasil5 = substr($pekerjaanibunama, $i, $totalkatanamapekerjaanibu);
                        $tampil5 = substr($hasil5, 0, 1);
                        $widd = 5;
                        $pdf->SetFont('Arial', '', 7);
                        $widths = array($widd);
                        $caption = array($tampil5);
                        $pdf->SetWidths($widths);
                        $pdf->FancyRow2($caption);
                    }
                } else {
                    if ($pekerjaanibu2 != '') {
                        $pdf->Cell(5, 4, $pekerjaanibu, 1, '', 'L');
                        $pdf->Cell(5, 4, $pekerjaanibu2, 1, '', 'L');
                    }
                    if ($pekerjaanibu2 == '') {
                        $pdf->Cell(5, 4, '0', 1, '', 'L');
                        $pdf->Cell(5, 4, $pekerjaanibu, 1, '', 'L');
                    }
                }
            }
        }
//
//        // Alamat Ibu
//
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '5.   ALAMAT', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->ibu_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuaibu($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $keluargabapak = $this->keluarga->cekalamat($kematian->penduduk_id);
                $pdf->Cell(135, 4, strtoupper($keluargabapak->alamat), 1, '', 'L');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $alamatdomisili = $orangtuabukanpendudukbapak->alamat;
                $pdf->Cell(135, 4, strtoupper($alamatdomisili->alamat), 1, '', 'L');
            }
        } else {

            if ($kematian->ibu_bayi == 1) {
                $keluargaibu = $this->keluarga->cekalamat($kematian->ibu_penduduk_id);
                $pdf->Cell(135, 5, strtoupper($keluargaibu->alamat), 1, '', 'L');
            }
            if ($kematian->ibu_bayi == 2) {
                $pdf->Cell(135, 5, strtoupper($kematian->non_pendudukibu->alamat), 1, '', 'L');
            }
        }
        $pdf->Ln(7);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN     :', 0, '', '');
        $pdf->SetFont('Arial', '', 7);

        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->ibu_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuaibu($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->desa), 1, '', '');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $pdf->Cell(40, 4, strtoupper($orangtuabukanpendudukbapak->desa->desa), 1, '', '');
            }
        } else {

            if ($kematian->ibu_bayi == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->desa), 1, '', '');
            }
            if ($kematian->ibu_bayi == 2) {
                $pdf->Cell(40, 4, strtoupper($kematian->non_pendudukibu->desa->desa), 1, '', '');
            }
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 2, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->ibu_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuaibu($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kecamatan), 1, '', '');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $pdf->Cell(40, 4, strtoupper($orangtuabukanpendudukbapak->desa->kecamatan->kecamatan), 1, '', '');
            }
        } else {

            if ($kematian->ibu_bayi == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kecamatan), 1, '', '');
            }
            if ($kematian->ibu_bayi == 2) {
                $pdf->Cell(40, 4, strtoupper($kematian->non_pendudukibu->desa->kecamatan->kecamatan), 1, '', '');
            }
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->ibu_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuaibu($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $pdf->Cell(40, 4, strtoupper($orangtuabukanpendudukbapak->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
            }
        } else {

            if ($kematian->ibu_bayi == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
            }
            if ($kematian->ibu_bayi == 2) {
                $pdf->Cell(40, 4, strtoupper($kematian->non_pendudukibu->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
            }
        }
        $pdf->Ln(9);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -4, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-4);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->ibu_bayi == '--') {
            $orangtuabapak2 = $this->orangtua->cekorangtuaibu($kematian->penduduk_id);
            $statusorangtua = $orangtuabapak2->status_orang_tua;
            if ($statusorangtua == 0 || $statusorangtua == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
            }
            if ($statusorangtua == 2) {
                $orangtuabukanpendudukbapak = $this->orangtuanonpenduduk->cekorangtuacetak($orangtuabapak2->id);
                $pdf->Cell(40, 4, strtoupper($orangtuabukanpendudukbapak->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
            }
        } else {

            if ($kematian->ibu_bayi == 1) {
                $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
            }
            if ($kematian->ibu_bayi == 2) {
                $pdf->Cell(40, 4, strtoupper($kematian->non_pendudukibu->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
            }
        }
//        ===========================================================================================================================================================================
        // PELAPOR

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 38, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'PELAPOR', 0, '', 'L');

        // nik pelapor

        $pdf->Ln(1);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '1.   NIK           ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->pelapor_penduduk == 1) {
            $pelaporlist = $this->pribadi->find($kematian->pelapor_penduduk_id);
        }
        if ($kematian->pelapor_penduduk == 2) {
            $pelaporlist = $this->nonpenduduk->find($kematian->pelapor_penduduk_id);
        }
        $totalkatanikpelapor = strlen($kematian->pelapor_nik);
        $kurangnikpelapor = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnikpelapor; $i++) {
            $hasil6 = substr($kematian->pelapor_nik, $i, $totalkatanikpelapor);
            $tampil6 = substr($hasil6, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil6);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);
        }

        // Nama pelapor

        $pdf->Ln(4);
        $pdf->Cell(35, 5, '2.   NAMA LENGKAP ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->pelapor_penduduk == 1) {
            $namapelaporpenduduk = $pelaporlist->nama;
        }
        if ($kematian->pelapor_penduduk == 2) {
            $namapelaporpenduduk = $pelaporlist->nama;
        }
        $totalkatanamapelapor = strlen($namapelaporpenduduk);
        $kurangnamapelapor = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');

        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnamapelapor; $i++) {
            $hasilpelapor7 = substr($namapelaporpenduduk, $i, $totalkatanamapelapor);
            $tampilpelapor7 = substr($hasilpelapor7, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampilpelapor7);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }

        //tanggal lahir/umur pelapor

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '3.   UMUR ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->pelapor_penduduk == 1) {
            $thnkurangpelapor = substr($pelaporlist->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kematian->pelapor_penduduk == 2) {
            $thnkurangpelapor = substr($pelaporlist->tanggal_lahir, 6, 4) - date('Y');
        }
        if (substr($thnkurangpelapor, 2, 1) == '') {
            $umur1 = substr($thnkurangpelapor, 1, 1);
            $pdf->Cell(5, 4, '0', 1, '', 'L');
            $pdf->Cell(5, 4, $umur1, 1, '', 'L');
        }
        if (substr($thnkurangpelapor, 2, 1) != '') {
            $umur1 = substr($thnkurangpelapor, 1, 1);
            $umur2 = substr($thnkurangpelapor, 2, 1);
            $pdf->Cell(5, 4, $umur1, 1, '', 'L');
            $pdf->Cell(5, 4, $umur2, 1, '', 'L');
        }
        $pdf->SetX(65);
        $pdf->Cell(0, 5, 'TAHUN', 0, '', '');

        if ($pelaporlist->jk->id == 1) {
            $jeniskelaminpelapor = '1';
        }
        if ($pelaporlist->jk->id == 2) {
            $jeniskelaminpelapor = '2';
        }
        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '4.   JENIS KELAMIN', 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $jeniskelaminpelapor, 1, '', 'L');
        $pdf->SetX(70);
        $pdf->Cell(5, 5, '1. LAKI-LAKI', 0, '', 'L');
        $pdf->SetX(95);
        $pdf->Cell(5, 5, '2. PEREMPUAN', 0, '', 'L');

        // pekerjaan pelapor

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '5.   PEKERJAAN', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->pelapor_penduduk == 1) {
            $pekerjaanpelapor = substr($pelaporlist->pekerjaan_id, 0, 1);
            $pekerjaanpelapor2 = substr($pelaporlist->pekerjaan_id, 1, 1);
            if ($pelaporlist->pekerjaan_id == 89) {
                $pekerjaannamapelapor = $pelaporlist->pekerjaan_lain->pekerjaan_lain;
                $totalkatanamapekerjaanpelapor = strlen($pekerjaannamapelapor);
                $kurangnamapekerjaanpelapor = 26;
                for ($i = 0; $i <= $kurangnamapekerjaanpelapor; $i++) {
                    $hasilpelapor5 = substr($pekerjaannamapelapor, $i, $totalkatanamapekerjaanpelapor);
                    $tampilpelapor5 = substr($hasilpelapor5, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 7);
                    $widths = array($widd);
                    $caption = array($tampilpelapor5);
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow2($caption);
                }
            } else {
                if ($pekerjaanpelapor2 != '') {
                    $pdf->Cell(5, 4, $pekerjaanpelapor, 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaanpelapor2, 1, '', 'L');
                }
                if ($pekerjaanpelapor2 == '') {
                    $pdf->Cell(5, 4, '0', 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaanpelapor, 1, '', 'L');
                }
            }
        }
        if ($kematian->pelapor_penduduk == 2) {
            $pekerjaanpelapor = substr($pelaporlist->pekerjaan_id, 0, 1);
            $pekerjaanpelapor2 = substr($pelaporlist->pekerjaan_id, 1, 1);
            if ($pelaporlist->pekerjaan_id == 89) {
                $pekerjaannamapelapor = $pelaporlist->pekerjaan_lain->pekerjaan_lain;
                $totalkatanamapekerjaan = strlen($pekerjaannamapelapor);
                $kurangnamapekerjaan = 26;
                for ($i = 0; $i <= $kurangnamapekerjaan; $i++) {
                    $hasil5 = substr($pekerjaannamapelapor, $i, $totalkatanamapekerjaan);
                    $tampil5 = substr($hasil5, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 7);
                    $widths = array($widd);
                    $caption = array($tampil5);
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow2($caption);
                }
            } else {
                if ($pekerjaanpelapor2 != '') {
                    $pdf->Cell(5, 4, $pekerjaanpelapor, 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaanpelapor2, 1, '', 'L');
                }
                if ($pekerjaanpelapor2 == '') {
                    $pdf->Cell(5, 4, '0', 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaanpelapor, 1, '', 'L');
                }
            }
        }

        // Alamat Pelapor

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '6.   ALAMAT', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->pelapor_penduduk == 1) {
            $keluargapelapor = $this->keluarga->cekalamat($kematian->pelapor_penduduk_id);
            $pdf->Cell(135, 4, strtoupper($keluargapelapor->alamat), 1, '', 'L');
        }
        if ($kematian->pelapor_penduduk == 2) {
            $pdf->Cell(135, 4, strtoupper($pelaporlist->alamat), 1, '', 'L');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN     :', 0, '', '');
        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->pelapor_penduduk == 1) {

            $pdf->Cell(40, 4, strtoupper($kematian->desa->desa), 1, '', '');
        }
        if ($kematian->pelapor_penduduk == 2) {
            $pdf->Cell(40, 4, strtoupper($pelaporlist->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->pelapor_penduduk == 1) {
            $pdf->Cell(40, 5, strtoupper($kematian->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kematian->pelapor_penduduk == 2) {
            $pdf->Cell(40, 5, strtoupper($pelaporlist->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->pelapor_penduduk == 1) {

            $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kematian->pelapor_penduduk == 2) {
            $pdf->Cell(40, 4, strtoupper($pelaporlist->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -6, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->pelapor_penduduk == 1) {
            $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kematian->pelapor_penduduk == 2) {
            $pdf->Cell(40, 4, strtoupper($pelaporlist->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }

        //        ===========================================================================================================================================================================
// Saksi1

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 34, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'SAKSI I', 0, '', 'L');

        // NIK saksi 1

        $pdf->Ln(1);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '1.   NIK', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi1 == 1) {
            $saksi1list = $this->pribadi->find($kematian->saksi1_penduduk_id);
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $saksi1list = $this->nonpenduduk->find($kematian->saksi1_penduduk_id);
        }
        $totalkataniksaksi1 = strlen($kematian->saksi1_nik);
        $kurangniksaksi1 = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangniksaksi1; $i++) {
            $hasil8 = substr($kematian->saksi1_nik, $i, $totalkataniksaksi1);
            $tampil8 = substr($hasil8, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil8);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);
        }
        $pdf->Ln(4);
        $pdf->Cell(35, 5, '2.   NAMA LENGKAP', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi1 == 1) {
            $namasaksi1penduduk = $saksi1list->nama;
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $namasaksi1penduduk = $saksi1list->nama;
        }
        $totalkatanamasaksi1 = strlen($namasaksi1penduduk);
        $kurangnamasaksi1 = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnamasaksi1; $i++) {
            $hasil7 = substr($namasaksi1penduduk, $i, $totalkatanamasaksi1);
            $tampil7 = substr($hasil7, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil7);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);
        }

        //tanggal lahir/umur saksi 1

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '3.   UMUR', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->penduduk_saksi1 == 1) {
            $thnkurangsaksi1 = substr($saksi1list->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $thnkurangsaksi1 = substr($saksi1list->tanggal_lahir, 6, 4) - date('Y');
        }
        if (substr($thnkurangsaksi1, 2, 1) == '') {
            $umursaksi1 = substr($thnkurangsaksi1, 1, 1);
            $pdf->Cell(5, 4, '0', 1, '', 'L');
            $pdf->Cell(5, 4, $umursaksi1, 1, '', 'L');
        }
        if (substr($thnkurangsaksi1, 2, 1) != '') {
            $umursaksi1 = substr($thnkurangsaksi1, 1, 1);
            $umursaksi12 = substr($thnkurangsaksi1, 2, 1);
            $pdf->Cell(5, 4, $umursaksi1, 1, '', 'L');
            $pdf->Cell(5, 4, $umursaksi12, 1, '', 'L');
        }
        $pdf->SetX(65);
        $pdf->Cell(0, 5, 'TAHUN', 0, '', '');

        // pekerjaan saksi 1

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '4.   PEKERJAAN', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->penduduk_saksi1 == 1) {
            $pekerjaansaksi1 = substr($saksi1list->pekerjaan_id, 0, 1);
            $pekerjaansaksi12 = substr($saksi1list->pekerjaan_id, 1, 1);
            if ($saksi1list->pekerjaan_id == 89) {
                $pekerjaannamasaksi1 = $saksi1list->pekerjaan_lain->pekerjaan_lain;
                $totalkatanamapekerjaansaksi1 = strlen($pekerjaannamasaksi1);
                $kurangnamapekerjaansaksi1 = 26;
                for ($i = 0; $i <= $kurangnamapekerjaansaksi1; $i++) {
                    $hasil5 = substr($pekerjaannamasaksi1, $i, $totalkatanamapekerjaansaksi1);
                    $tampil5 = substr($hasil5, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 7);
                    $widths = array($widd);
                    $caption = array($tampil5);
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow2($caption);
                }
            } else {
                if ($pekerjaansaksi12 != '') {
                    $pdf->Cell(5, 4, $pekerjaansaksi1, 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaansaksi12, 1, '', 'L');
                }
                if ($pekerjaansaksi12 == '') {
                    $pdf->Cell(5, 4, '0', 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaansaksi1, 1, '', 'L');
                }
            }
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $pekerjaansaksi1 = substr($saksi1list->pekerjaan_id, 0, 1);
            $pekerjaansaksi12 = substr($saksi1list->pekerjaan_id, 1, 1);

            if ($saksi1list->pekerjaan_id == 89) {
                $pekerjaannamasaksi1 = $saksi1list->pekerjaan_lain->pekerjaan_lain;
                $totalkatanamapekerjaansaksi1 = strlen($pekerjaannamasaksi1);
                $kurangnamapekerjaansaksi1 = 26;
                for ($i = 0; $i <= $kurangnamapekerjaansaksi1; $i++) {
                    $hasilpekerja5 = substr($pekerjaannamasaksi1, $i, $totalkatanamapekerjaansaksi1);
                    $tampilpekerja5 = substr($hasilpekerja5, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 7);
                    $widths = array($widd);
                    $caption = array($tampilpekerja5);
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow2($caption);
                }
            } else {
                if ($pekerjaansaksi12 != '') {
                    $pdf->Cell(5, 4, $pekerjaansaksi1, 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaansaksi12, 1, '', 'L');
                }
                if ($pekerjaansaksi12 == '') {
                    $pdf->Cell(5, 4, '0', 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaansaksi1, 1, '', 'L');
                }
            }
        }

        // Alamat saksi 1

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '5.   ALAMAT', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->penduduk_saksi1 == 1) {
            $keluargasaksi = $this->keluarga->cekalamat($kematian->saksi1_penduduk_id);
            $pdf->Cell(135, 4, strtoupper($keluargasaksi->alamat), 1, '', 'L');
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $pdf->Cell(135, 4, strtoupper($saksi1list->alamat), 1, '', 'L');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN    :', 0, '', '');
        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi1 == 1) {

            $pdf->Cell(40, 4, strtoupper($kematian->desa->desa), 1, '', '');
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi1list->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi1 == 1) {
            $pdf->Cell(40, 5, strtoupper($kematian->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $pdf->Cell(40, 5, strtoupper($saksi1list->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'D. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi1 == 1) {

            $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi1list->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -4, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi1 == 1) {
            $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kematian->penduduk_saksi1 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi1list->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }

        //        ===========================================================================================================================================================================

        // Saksi2

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 34, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'SAKSI II', 0, '', 'L');

        // NIK saksi 1

        $pdf->Ln(1);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '1.   NIK                                                 :', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi2 == 1) {
            $saksi2list = $this->pribadi->find($kematian->saksi2_penduduk_id);
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $saksi2list = $this->nonpenduduk->find($kematian->saksi2_penduduk_id);
        }
        $totalkataniksaksi2 = strlen($kematian->saksi2_nik);
        $kurangniksaksi2 = 26;
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangniksaksi2; $i++) {
            $hasil9 = substr($kematian->saksi2_nik, $i, $totalkataniksaksi2);
            $tampil9 = substr($hasil9, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil9);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }

        // nama lengkap saksi 2

        $pdf->Ln(4);
        $pdf->Cell(35, 5, '2.   NAMA LENGKAP', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi2 == 1) {
            $namasaksipenduduk = $saksi2list->nama;
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $namasaksipenduduk = $saksi2list->nama;
        }
        $totalkatanamasaksi2 = strlen($namasaksipenduduk);
        $kurangnamasaksi2 = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnamasaksi2; $i++) {
            $hasil10 = substr($namasaksipenduduk, $i, $totalkatanamasaksi2);
            $tampil10 = substr($hasil10, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil10);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }

        //tanggal lahir/umur saksi 2

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '3.   UMUR', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');

        $pdf->SetX(53);
        if ($kematian->penduduk_saksi2 == 1) {
            $thnkurangsaksi2 = substr($saksi2list->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $thnkurangsaksi2 = substr($saksi2list->tanggal_lahir, 6, 4) - date('Y');
        }

        if (substr($thnkurangsaksi2, 2, 1) == '') {
            $umursaksi21 = substr($thnkurangsaksi2, 1, 1);
            $pdf->Cell(5, 4, '0', 1, '', 'L');
            $pdf->Cell(5, 4, $umursaksi21, 1, '', 'L');
        }
        if (substr($thnkurangsaksi2, 2, 1) != '') {
            $umursaksi21 = substr($thnkurangsaksi2, 1, 1);
            $umursaksi22 = substr($thnkurangsaksi2, 2, 1);
            $pdf->Cell(5, 4, $umursaksi21, 1, '', 'L');
            $pdf->Cell(5, 4, $umursaksi22, 1, '', 'L');
        }
        $pdf->SetX(65);
        $pdf->Cell(0, 5, 'TAHUN', 0, '', '');

        // pekerjaan saksi 2

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '4.   PEKERJAAN', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');

        $pdf->SetX(53);
        if ($kematian->penduduk_saksi2 == 1) {
            $pekerjaansaksi2 = substr($saksi2list->pekerjaan_id, 0, 1);
            $pekerjaansaksi22 = substr($saksi2list->pekerjaan_id, 1, 1);

            if ($saksi2list->pekerjaan_id == 89) {
                $pekerjaannamasaksi2 = $saksi2list->pekerjaan_lain->pekerjaan_lain;
                $totalkatanamapekerjaansasksi2 = strlen($pekerjaannamasaksi2);

                $kurangnamapekerjaansaksi2 = 26;

                for ($i = 0; $i <= $kurangnamapekerjaansaksi2; $i++) {
                    $hasilsaksi25 = substr($pekerjaannamasaksi2, $i, $totalkatanamapekerjaansasksi2);
                    $tampilsaksi25 = substr($hasilsaksi25, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 7);
                    $widths = array($widd);
                    $caption = array($tampilsaksi25);
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow2($caption);

                }
            } else {
                if ($pekerjaansaksi22 != '') {
                    $pdf->Cell(5, 4, $pekerjaansaksi2, 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaansaksi22, 1, '', 'L');
                }
                if ($pekerjaansaksi22 == '') {
                    $pdf->Cell(5, 4, '0', 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaansaksi2, 1, '', 'L');
                }
            }
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $pekerjaansaksi2 = substr($saksi2list->pekerjaan_id, 0, 1);
            $pekerjaansaksi22 = substr($saksi2list->pekerjaan_id, 1, 1);

            if ($saksi2list->pekerjaan_id == 89) {
                $pekerjaannamasaksi2 = $saksi2list->pekerjaan_lain->pekerjaan_lain;
                $totalkatanamapekerjaansasksi2 = strlen($pekerjaannamasaksi2);

                $kurangnamapekerjaansaksi2 = 26;

                for ($i = 0; $i <= $kurangnamapekerjaansaksi2; $i++) {
                    $hasilsaksi25 = substr($pekerjaannamasaksi2, $i, $totalkatanamapekerjaansasksi2);
                    $tampilsaksi25 = substr($hasilsaksi25, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 7);
                    $widths = array($widd);
                    $caption = array($tampilsaksi25);
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow2($caption);

                }
            } else {
                if ($pekerjaansaksi22 != '') {
                    $pdf->Cell(5, 4, $pekerjaansaksi2, 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaansaksi22, 1, '', 'L');
                }
                if ($pekerjaansaksi22 == '') {
                    $pdf->Cell(5, 4, '0', 1, '', 'L');
                    $pdf->Cell(5, 4, $pekerjaansaksi2, 1, '', 'L');
                }
            }
        }

        // Alamat saksi 2
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '5.   ALAMAT', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kematian->penduduk_saksi2 == 1) {
            $keluargasaksi2 = $this->keluarga->cekalamat($kematian->saksi2_penduduk_id);
            $pdf->Cell(135, 4, $keluargasaksi2->alamat, 1, '', 'L');
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $pdf->Cell(135, 4, $saksi2list->alamat, 1, '', 'L');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURHAN      :', 0, '', '');
        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi2 == 1) {

            $pdf->Cell(40, 4, strtoupper($kematian->desa->desa), 1, '', '');
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi2list->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi2 == 1) {
            $pdf->Cell(40, 5, strtoupper($kematian->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $pdf->Cell(40, 5, strtoupper($saksi2list->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi2 == 1) {

            $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi2list->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -5, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kematian->penduduk_saksi2 == 1) {
            $pdf->Cell(40, 4, strtoupper($kematian->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kematian->penduduk_saksi2 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi2list->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        $pdf->Ln(3);

//        =====================================================================================================================================================================================?
        $pdf->SetX(120);
        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(3);
        if ($kematian->penandatangan == 'Atasnama Pimpinan' || $kematian->penandatangan == 'Jabatan Struktural') {
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
        if ($kematian->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($kematian->jabatan_lainnya);

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
        if ($kematian->penandatangan != 'Atasnama Pimpinan' && $kematian->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($kematian->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($kematian->penandatangan);
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
            if ($kematian->penandatangan == 'Pimpinan Organisasi' && $kematian->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($kematian->penandatangan);
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
        $tanggal = date('d/m/y');
        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

        $pdf->Output('cetak-data-kematian-' . $tanggal . '.pdf', 'I');
        exit;
    }
}