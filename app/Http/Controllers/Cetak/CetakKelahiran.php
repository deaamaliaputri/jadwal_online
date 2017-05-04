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
use App\Domain\Repositories\DataPribadi\PendudukLainRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\KelahiranRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Penduduk\RincianNonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakKelahiran extends Controller
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
        KelahiranRepository $kelahiranRepository,
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
        OrganisasiRepository $organisasiRepository

    )
    {
        $this->kelahiran = $kelahiranRepository;
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
        $kelahiran = $this->kelahiran->find($id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();

        if ($kelahiran->ibu_bayi == 1) {
            $keluarga = $this->keluarga->cekalamat($kelahiran->ibu_penduduk_id);
        } else if ($kelahiran->bapak_bayi == 1) {
            $keluarga = $this->keluarga->cekalamat($kelahiran->bapak_penduduk_id);
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
        $pdf->Cell(35, 10, 'Kode . F-2.01', 1, 0, 'C');
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
        $pdf->Cell(0, 0, ' SURAT KETERANGAN KELAHIRAN', 0, 0, 'C');
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

    public function Kelahiran($id)
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
        $pdf->SetTitle('Surat kelahiran');
        $this->Kop($pdf, $id);
        $pdf->SetY(51);
        $desa = $this->desa->find(session('desa'));
        $kelahiran = $this->kelahiran->find($id);
        $jeniskodeadministrasi = $this->kelahiran->cekkodejenisadministrasi($kelahiran->jenis_pelayanan_id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();
        $cekwaktu = $this->kodeadministrasi->cekwaktuadministrasi();

        if ($kelahiran->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($kelahiran->penandatangan);
        }
        //tempat lahir bayi

        $kabupatenkelahiran = $kelahiran->desa_lahir->kecamatan->kabupaten->kabupaten;

//==============================================================================================================================================================================

        // Bayi
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 47, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'BAYI / ANAK', 0, '', 'L');
        // nama bayi
        $pdf->Ln(0);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, strtoupper('1.   Nama                                             '), 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatanama = strlen($kelahiran->nama);

        $kurangnama = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangnama; $i++) {
            $hasil = substr($kelahiran->nama, $i, $totalkatanama);
            $tampil = substr($hasil, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array($tampil);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        if ($kelahiran->jk->id == 1) {
            $jeniskelamin = '1';
        }
        if ($kelahiran->jk->id == 2) {
            $jeniskelamin = '2';
        }
        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, strtoupper('2.   Jenis Kelamin                            '), 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $jeniskelamin, 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(5, 5, strtoupper('1. Laki-Laki'), 0, '', 'L');
        $pdf->SetX(85);
        $pdf->Cell(5, 5, strtoupper('2. Perempuan'), 0, '', 'L');
        // tempat lahir bayi
        if ($kelahiran->tempat_lahir == 'RS/RB') {
            $tempatlahirbayi = '1';
        }
        if ($kelahiran->tempat_lahir == 'Puskesmas') {
            $tempatlahirbayi = '2';
        }
        if ($kelahiran->tempat_lahir == 'Polindes') {
            $tempatlahirbayi = '3';
        }
        if ($kelahiran->tempat_lahir == 'Rumah') {
            $tempatlahirbayi = '4';
        }
        if ($kelahiran->tempat_lahir == 'Lainnya') {
            $tempatlahirbayi = '5';
        }
        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, strtoupper('3.   Tempat dilahirkan                  '), 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $tempatlahirbayi, 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(4, 4, strtoupper('1. RS/RB'), 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(4, 4, strtoupper('2. Puskesmas'), 0, '', 'L');
        $pdf->SetX(105);
        $pdf->Cell(4, 4, strtoupper('3. Polindes'), 0, '', 'L');
        $pdf->SetX(125);
        $pdf->Cell(4, 4, strtoupper('4. Rumah'), 0, '', 'L');
        $pdf->SetX(150);
        $pdf->Cell(4, 4, strtoupper('5. Lainnya'), 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(34, 4, strtoupper('4.   Tempat kelahiran                   '), 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatatempatlahir = strlen($kabupatenkelahiran);

        $kurangtempatlahir = 15;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangtempatlahir; $i++) {
            $hasil1 = substr($kabupatenkelahiran, $i, $totalkatatempatlahir);
            $tampil1 = substr($hasil1, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array(strtoupper($tampil1));
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        //hari dan tanngal lahir
        $datetime = \DateTime::createFromFormat('d/m/Y', $kelahiran->tanggal_lahir);
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
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(34, 4, strtoupper('5.   Hari dan Tanggal lahir        '), 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatahari = strlen($hariindo);

        $kuranghari = 5;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(0, 5, 'HARI', 0, '', '');
        $pdf->SetX(63);
        for ($i = 0; $i <= $kuranghari; $i++) {
            $hasil2 = substr($hariindo, $i, $totalkatahari);
            $tampil2 = substr($hasil2, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array(strtoupper($tampil2));
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        $pdf->SetX(95);
        $pdf->Cell(0, 5, 'TGL', 0, '', '');
        $pdf->SetX(103);
        $tgl1 = substr($kelahiran->tanggal_lahir, 0, 1);
        $tgl2 = substr($kelahiran->tanggal_lahir, 1, 1);
        $pdf->Cell(4, 4, $tgl1, 1, '', 'L');
        $pdf->Cell(4, 4, $tgl2, 1, '', 'L');
        $pdf->SetX(115);
        $pdf->Cell(0, 5, 'BLN', 0, '', '');
        $pdf->SetX(123);
        $bln1 = substr($kelahiran->tanggal_lahir, 3, 1);
        $bln2 = substr($kelahiran->tanggal_lahir, 4, 1);
        $pdf->Cell(4, 4, $bln1, 1, '', 'L');
        $pdf->Cell(4, 4, $bln2, 1, '', 'L');
        $pdf->SetX(135);
        $pdf->Cell(0, 5, 'THN', 0, '', '');
        $pdf->SetX(143);
        $thn1 = substr($kelahiran->tanggal_lahir, 6, 1);
        $thn2 = substr($kelahiran->tanggal_lahir, 7, 1);
        $thn3 = substr($kelahiran->tanggal_lahir, 8, 1);
        $thn4 = substr($kelahiran->tanggal_lahir, 9, 1);
        $pdf->Cell(4, 4, $thn1, 1, '', 'L');
        $pdf->Cell(4, 4, $thn2, 1, '', 'L');
        $pdf->Cell(4, 4, $thn3, 1, '', 'L');
        $pdf->Cell(4, 4, $thn4, 1, '', 'L');
//pukul
        if ($cekwaktu != null) {
            $waktubagian = ' ' . $cekwaktu->kode;
        }
        if ($cekwaktu == null) {
            $waktubagian = '';
        }
        $pdf->ln(4);
        $pdf->Cell(0, 5, '6.   PUKUL                                           ', 0, '', '');
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $wkt1 = substr($kelahiran->waktu_lahir, 0, 1);
        $wkt2 = substr($kelahiran->waktu_lahir, 1, 1);
        $wkt3 = substr($kelahiran->waktu_lahir, 2, 1);
        $wkt4 = substr($kelahiran->waktu_lahir, 3, 1);
        $wkt5 = substr($kelahiran->waktu_lahir, 4, 1);
        $pdf->Cell(4, 4, $wkt1, 1, '', 'L');
        $pdf->Cell(4, 4, $wkt2, 1, '', 'L');
        $pdf->Cell(4, 4, $wkt3, 1, '', 'L');
        $pdf->Cell(4, 4, $wkt4, 1, '', 'L');
        $pdf->Cell(4, 4, $wkt5, 1, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(5, 4, $waktubagian, 0, '', 'L');

        //Jenis Kelahiran

        if ($kelahiran->jenis_lahir == 'Tunggal') {
            $jenislahir = '1';
        }
        if ($kelahiran->jenis_lahir == 'Kembar 2') {
            $jenislahir = '2';
        }
        if ($kelahiran->jenis_lahir == 'Kembar 3') {
            $jenislahir = '3';
        }
        if ($kelahiran->jenis_lahir == 'Kembar 4') {
            $jenislahir = '4';
        }
        if ($kelahiran->jenis_lahir == 'Lainnya') {
            $jenislahir = '5';
        }
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '7.   JENIS KELAHIRAN                       ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $jenislahir, 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(4, 4, '1. TUNGGAL', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(4, 4, '2. KEMBAR 2', 0, '', 'L');
        $pdf->SetX(105);
        $pdf->Cell(4, 4, '3. KEMBAR 3', 0, '', 'L');
        $pdf->SetX(125);
        $pdf->Cell(5, 5, '4. KEMBAR 4', 0, '', 'L');
        $pdf->SetX(150);
        $pdf->Cell(5, 5, '5. LAINNYA', 0, '', 'L');

        //kelahiran ke

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '8.   KELAHIRAN KE                             ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $kelahiran->kelahiran_ke, 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(5, 5, '1.', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(5, 5, '2.', 0, '', 'L');
        $pdf->SetX(105);
        $pdf->Cell(5, 5, '3.', 0, '', 'L');
        $pdf->SetX(125);
        $pdf->Cell(5, 5, '4.', 0, '', 'L');
        $pdf->SetX(150);
        if ($kelahiran->kelahiran_ke >= 5) {
            $pdf->Cell(5, 4, $kelahiran->kelahiran_ke, 0, '', 'L');
        }
        if ($kelahiran->kelahiran_ke <= 4) {
            $pdf->Cell(5, 4, '........', 0, '', 'L');
        }
        //Penolong kelahiran
        if ($kelahiran->penolong_lahir == 'Dokter') {
            $penolong_lahir = '1';
        }
        if ($kelahiran->penolong_lahir == 'Bidan/Perawat') {
            $penolong_lahir = '2';
        }
        if ($kelahiran->penolong_lahir == 'Dukun') {
            $penolong_lahir = '3';
        }
        if ($kelahiran->penolong_lahir == 'Lainnya') {
            $penolong_lahir = '4';
        }
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '9.   PENOLONG KELAHIRAN             ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, $penolong_lahir, 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(5, 5, '1. DOKTER', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(5, 5, '2. BIDAN/PERAWAT', 0, '', 'L');
        $pdf->SetX(105);
        $pdf->Cell(5, 5, '3. DUKUN', 0, '', 'L');
        $pdf->SetX(125);
        $pdf->Cell(5, 5, '4. LAINNYA', 0, '', 'L');

        // Berat Kelahiran

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '10. BERAT BAYI                                     ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(20, 4, $kelahiran->berat_bayi, 1, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(20, 5, 'KG', 0, '', 'L');

        // Panjang Kelahiran

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '11. PANJANG BAYI                                ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, substr($kelahiran->panjang_bayi, 0, 1), 1, '', 'L');
        $pdf->Cell(5, 4, substr($kelahiran->panjang_bayi, 1, 1), 1, '', 'L');
        $pdf->SetX(70);
        $pdf->Cell(20, 5, 'CM', 0, '', 'L');

//        ============================================================================================================================================================================================================================
        // IBU

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 46, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'IBU', 0, '', 'L');

        // nik ibu

        $pdf->Ln(0);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '1.   NIK                                                ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatanikibu = strlen($kelahiran->ibu_bayi_nik);

        $kurangnikibu = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangnikibu; $i++) {
            $hasilibu3 = substr($kelahiran->ibu_bayi_nik, $i, $totalkatanikibu);
            $tampilibu3 = substr($hasilibu3, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 7);
            $widths = array($widd);
            $caption = array(strtoupper($tampilibu3));
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);
        }

        // nama lengkap ibu bayi

        $pdf->Ln(4);
        $pdf->Cell(35, 5, '2.   NAMA LENGKAP                               ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->ibu_bayi == 1) {
            $namaibupenduduk = $kelahiran->pribadi->nama;
        }
        if ($kelahiran->ibu_bayi == 2) {
            $namaibupenduduk = $kelahiran->non_penduduk->nama;
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

        //tanggal lahir/umur ibu

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
        if ($kelahiran->ibu_bayi == 1) {
            $tglibu1 = substr($kelahiran->pribadi->tanggal_lahir, 0, 1);
            $tglibu2 = substr($kelahiran->pribadi->tanggal_lahir, 1, 1);
        }
        if ($kelahiran->ibu_bayi == 2) {
            $tglibu1 = substr($kelahiran->non_penduduk->tanggal_lahir, 0, 1);
            $tglibu2 = substr($kelahiran->non_penduduk->tanggal_lahir, 1, 1);
        }
        $pdf->Cell(5, 4, $tglibu1, 1, '', 'L');
        $pdf->Cell(5, 4, $tglibu2, 1, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(0, 5, 'BLN', 0, '', '');
        $pdf->SetX(83);
        if ($kelahiran->ibu_bayi == 1) {
            $blnibu1 = substr($kelahiran->pribadi->tanggal_lahir, 3, 1);
            $blnibu2 = substr($kelahiran->pribadi->tanggal_lahir, 4, 1);
        }
        if ($kelahiran->ibu_bayi == 2) {
            $blnibu1 = substr($kelahiran->non_penduduk->tanggal_lahir, 3, 1);
            $blnibu2 = substr($kelahiran->non_penduduk->tanggal_lahir, 4, 1);
        }
        $pdf->Cell(5, 4, $blnibu1, 1, '', 'L');
        $pdf->Cell(5, 4, $blnibu2, 1, '', 'L');
        $pdf->SetX(95);
        $pdf->Cell(0, 5, 'THN', 0, '', '');
        $pdf->SetX(103);
        if ($kelahiran->ibu_bayi == 1) {
            $thnibu1 = substr($kelahiran->pribadi->tanggal_lahir, 6, 1);
            $thnibu2 = substr($kelahiran->pribadi->tanggal_lahir, 7, 1);
            $thnibu3 = substr($kelahiran->pribadi->tanggal_lahir, 8, 1);
            $thnibu4 = substr($kelahiran->pribadi->tanggal_lahir, 9, 1);
        }
        if ($kelahiran->ibu_bayi == 2) {
            $thnibu1 = substr($kelahiran->non_penduduk->tanggal_lahir, 6, 1);
            $thnibu2 = substr($kelahiran->non_penduduk->tanggal_lahir, 7, 1);
            $thnibu3 = substr($kelahiran->non_penduduk->tanggal_lahir, 8, 1);
            $thnibu4 = substr($kelahiran->non_penduduk->tanggal_lahir, 9, 1);
        }
        $pdf->Cell(5, 4, $thnibu1, 1, '', 'L');
        $pdf->Cell(5, 4, $thnibu2, 1, '', 'L');
        $pdf->Cell(5, 4, $thnibu3, 1, '', 'L');
        $pdf->Cell(5, 4, $thnibu4, 1, '', 'L');
        $pdf->SetX(128);
        $pdf->Cell(0, 5, 'UMUR', 0, '', '');
        $pdf->SetX(143);
        if ($kelahiran->ibu_bayi == 1) {
            $thnkurangibu = substr($kelahiran->pribadi->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kelahiran->ibu_bayi == 2) {
            $thnkurangibu = substr($kelahiran->non_penduduk->tanggal_lahir, 6, 4) - date('Y');
        }
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

        // pekerjaan ibu

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '4.   PEKERJAAN', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kelahiran->ibu_bayi == 1) {
            $pekerjaanibu = substr($kelahiran->pribadi->pekerjaan_id, 0, 1);
            $pekerjaanibu2 = substr($kelahiran->pribadi->pekerjaan_id, 1, 1);
            if ($kelahiran->pribadi->pekerjaan_id == 89) {
                $pekerjaanibunama = $kelahiran->pribadi->pekerjaan_lain->pekerjaan_lain;
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
        if ($kelahiran->ibu_bayi == 2) {
            $pekerjaanibu = substr($kelahiran->non_penduduk->pekerjaan_id, 0, 1);
            $pekerjaanibu2 = substr($kelahiran->non_penduduk->pekerjaan_id, 1, 1);
            if ($kelahiran->non_penduduk->pekerjaan_id == 89) {
                $pekerjaanibunama = $kelahiran->non_penduduk->pekerjaan_lain->pekerjaan_lain;
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

        // Alamat Ibu

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '5.   ALAMAT', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kelahiran->ibu_bayi == 1) {
            $keluargaibu = $this->keluarga->cekalamat($kelahiran->ibu_penduduk_id);
            $pdf->Cell(135, 5, strtoupper($keluargaibu->alamat), 1, '', 'L');
        }
        if ($kelahiran->ibu_bayi == 2) {
            $pdf->Cell(135, 5, strtoupper($kelahiran->non_penduduk->alamat), 1, '', 'L');
        }
        $pdf->Ln(7);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN     :', 0, '', '');
        $pdf->SetFont('Arial', '', 7);

        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->ibu_bayi == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->desa), 1, '', '');
        }
        if ($kelahiran->ibu_bayi == 2) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->non_penduduk->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 2, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->ibu_bayi == 1) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kelahiran->ibu_bayi == 2) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->non_penduduk->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->ibu_bayi == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kelahiran->ibu_bayi == 2) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->non_penduduk->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(9);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -4, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-4);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->ibu_bayi == 1) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kelahiran->ibu_bayi == 2) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->non_penduduk->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '6.   KEWARGANEGARAAN', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, '1', 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(5, 5, '1. WNI', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(5, 5, '2. WNA', 0, '', 'L');
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '7.   KEBANGSAAN', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(80, 4, 'INDONESIA', 1, '', 'L');

        //tanggal Pencatatan Kawin ibu

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(35, 5, '8.   TGL PERCATATAN PERKAWINAN           ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(0, 5, 'Tgl', 0, '', '');
        $pdf->SetX(63);
        if ($kelahiran->ibu_bayi == 1) {
            $dokumenpenduduk = $this->dokumenpenduduk->find($kelahiran->pernikahan_ibu);
            $tglpernikahan1 = substr($dokumenpenduduk->tanggal, 0, 1);
            $tglpernikahan2 = substr($dokumenpenduduk->tanggal, 1, 1);
        }
        if ($kelahiran->ibu_bayi == 2) {
            $dokumenpenduduk = $this->rinciannonpenduduk->find($kelahiran->pernikahan_ibu);

            $tglpernikahan1 = substr($dokumenpenduduk->rincian_non_penduduk, 0, 1);
            $tglpernikahan2 = substr($dokumenpenduduk->rincian_non_penduduk, 1, 1);
        }
        $pdf->Cell(5, 4, $tglpernikahan1, 1, '', 'L');
        $pdf->Cell(5, 4, $tglpernikahan2, 1, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(0, 5, 'BLN', 0, '', '');
        $pdf->SetX(83);
        if ($kelahiran->ibu_bayi == 1) {
            $dokumenpenduduk = $this->dokumenpenduduk->find($kelahiran->pernikahan_ibu);
            $tglpernikahan3 = substr($dokumenpenduduk->tanggal, 3, 1);
            $tglpernikahan4 = substr($dokumenpenduduk->tanggal, 4, 1);
        }
        if ($kelahiran->ibu_bayi == 2) {
            $dokumenpenduduk = $this->rinciannonpenduduk->find($kelahiran->pernikahan_ibu);

            $tglpernikahan3 = substr($dokumenpenduduk->rincian_non_penduduk, 3, 1);
            $tglpernikahan4 = substr($dokumenpenduduk->rincian_non_penduduk, 4, 1);
        }
        $pdf->Cell(5, 4, $tglpernikahan3, 1, '', 'L');
        $pdf->Cell(5, 4, $tglpernikahan4, 1, '', 'L');
        $pdf->SetX(95);
        $pdf->Cell(0, 5, 'THN', 0, '', '');
        $pdf->SetX(103);
        if ($kelahiran->ibu_bayi == 1) {
            $dokumenpenduduk = $this->dokumenpenduduk->find($kelahiran->pernikahan_ibu);
            $tglpernikahan6 = substr($dokumenpenduduk->tanggal, 6, 1);
            $tglpernikahan7 = substr($dokumenpenduduk->tanggal, 7, 1);
            $tglpernikahan8 = substr($dokumenpenduduk->tanggal, 8, 1);
            $tglpernikahan9 = substr($dokumenpenduduk->tanggal, 9, 1);
        }
        if ($kelahiran->ibu_bayi == 2) {
            $dokumenpenduduk = $this->rinciannonpenduduk->find($kelahiran->pernikahan_ibu);
            $tglpernikahan6 = substr($dokumenpenduduk->rincian_non_penduduk, 6, 1);
            $tglpernikahan7 = substr($dokumenpenduduk->rincian_non_penduduk, 7, 1);
            $tglpernikahan8 = substr($dokumenpenduduk->rincian_non_penduduk, 8, 1);
            $tglpernikahan9 = substr($dokumenpenduduk->rincian_non_penduduk, 9, 1);
        }
        $pdf->Cell(5, 4, $tglpernikahan6, 1, '', 'L');
        $pdf->Cell(5, 4, $tglpernikahan7, 1, '', 'L');
        $pdf->Cell(5, 4, $tglpernikahan8, 1, '', 'L');
        $pdf->Cell(5, 4, $tglpernikahan9, 1, '', 'L');

//        ============================================================================================================================================================================================================================
        // BAPAK

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 43, '', 1, '', 'L');
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'BAPAK', 0, '', 'L');

        // nik bapak

        $pdf->Ln(1);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '1.   NIK         ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $totalkatanikbapak = strlen($kelahiran->bapak_bayi_nik);
        $kurangnikbapak = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnikbapak; $i++) {
            $hasil3 = substr($kelahiran->bapak_bayi_nik, $i, $totalkatanikbapak);
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
        if ($kelahiran->bapak_bayi == 1) {
            $namabapakpenduduk = $kelahiran->pribadi_bapak->nama;
        }
        if ($kelahiran->bapak_bayi == 2) {
            $namabapakpenduduk = $kelahiran->non_penduduk_bapak->nama;
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
        if ($kelahiran->bapak_bayi == 1) {
            $tanggalbapak1 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 0, 1);
            $tanggalbapak2 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 1, 1);
        }
        if ($kelahiran->bapak_bayi == 2) {
            $tanggalbapak1 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 0, 1);
            $tanggalbapak2 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 1, 1);
        }
        $pdf->Cell(5, 4, $tanggalbapak1, 1, '', 'L');
        $pdf->Cell(5, 4, $tanggalbapak2, 1, '', 'L');
        $pdf->SetX(75);
        $pdf->Cell(0, 5, 'BLN', 0, '', '');
        $pdf->SetX(83);
        if ($kelahiran->bapak_bayi == 1) {
            $blnbapak1 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 3, 1);
            $blnbapak2 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 4, 1);
        }
        if ($kelahiran->bapak_bayi == 2) {
            $blnbapak1 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 3, 1);
            $blnbapak2 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 4, 1);
        }
        $pdf->Cell(5, 4, $blnbapak1, 1, '', 'L');
        $pdf->Cell(5, 4, $blnbapak2, 1, '', 'L');
        $pdf->SetX(95);
        $pdf->Cell(0, 5, 'THN', 0, '', '');
        $pdf->SetX(103);
        if ($kelahiran->bapak_bayi == 1) {
            $thnbapak1 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 6, 1);
            $thnbapak2 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 7, 1);
            $thnbapak3 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 8, 1);
            $thnbapak4 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 9, 1);
        }
        if ($kelahiran->bapak_bayi == 2) {
            $thnbapak1 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 6, 1);
            $thnbapak2 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 7, 1);
            $thnbapak3 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 8, 1);
            $thnbapak4 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 9, 1);
        }
        $pdf->Cell(5, 4, $thnbapak1, 1, '', 'L');
        $pdf->Cell(5, 4, $thnbapak2, 1, '', 'L');
        $pdf->Cell(5, 4, $thnbapak3, 1, '', 'L');
        $pdf->Cell(5, 4, $thnbapak4, 1, '', 'L');
        $pdf->SetX(128);
        $pdf->Cell(0, 5, 'UMUR', 0, '', '');
        $pdf->SetX(143);
        if ($kelahiran->bapak_bayi == 1) {
            $thnkurangbapak = substr($kelahiran->pribadi_bapak->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kelahiran->bapak_bayi == 2) {
            $thnkurangbapak = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 6, 4) - date('Y');
        }

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

        // pekerjaan bapak

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '4.   PEKERJAAN                                       ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kelahiran->bapak_bayi == 1) {
            $pekerjaanbapak = substr($kelahiran->pribadi_bapak->pekerjaan_id, 0, 1);
            $pekerjaanbapak2 = substr($kelahiran->pribadi_bapak->pekerjaan_id, 1, 1);

            if ($kelahiran->pribadi_bapak->pekerjaan_id == 89) {
                $pekerjaannamabapak = $kelahiran->pribadi_bapak->pekerjaan_lain->pekerjaan_lain;
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
        if ($kelahiran->bapak_bayi == 2) {
            $pekerjaanbapak = substr($kelahiran->non_penduduk_bapak->pekerjaan_id, 0, 1);
            $pekerjaanbapak2 = substr($kelahiran->non_penduduk_bapak->pekerjaan_id, 1, 1);
            if ($kelahiran->non_penduduk_bapak->pekerjaan_id == 89) {
                $pekerjaannamabapak = $kelahiran->non_penduduk_bapak->pekerjaan_lain->pekerjaan_lain;
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

        // ALamat Kelahiran

        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '5.   ALAMAT             ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kelahiran->bapak_bayi == 1) {
            $keluargabapak = $this->keluarga->cekalamat($kelahiran->bapak_penduduk_id);
            $pdf->Cell(135, 4, strtoupper($keluargabapak->alamat), 1, '', 'L');
        }
        if ($kelahiran->bapak_bayi == 2) {
            $pdf->Cell(135, 4, strtoupper($kelahiran->non_penduduk_bapak->alamat), 1, '', 'L');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN     :', 0, '', '');

        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->bapak_bayi == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->desa), 1, '', '');
        }
        if ($kelahiran->bapak_bayi == 2) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->non_penduduk_bapak->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->bapak_bayi == 1) {
            $pdf->Cell(40, 5, strtoupper($kelahiran->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kelahiran->bapak_bayi == 2) {
            $pdf->Cell(40, 5, strtoupper($kelahiran->non_penduduk_bapak->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->bapak_bayi == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kelahiran->bapak_bayi == 2) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->non_penduduk_bapak->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -6, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->bapak_bayi == 1) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kelahiran->bapak_bayi == 2) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->non_penduduk_bapak->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '6.   KEWARGANEGARAAN                          ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(5, 4, '1', 1, '', 'L');
        $pdf->SetX(60);
        $pdf->Cell(5, 4, '1. WNI', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(5, 5, '2. WNA', 0, '', 'L');
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, '7.   KEBANGSAAN                                   ', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        $pdf->Cell(80, 4, 'INDONESIA', 1, '', 'L');

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
        if ($kelahiran->pelapor_penduduk == 1) {
            $pelaporlist = $this->pribadi->find($kelahiran->pelapor_penduduk_id);
        }
        if ($kelahiran->pelapor_penduduk == 2) {
            $pelaporlist = $this->nonpenduduk->find($kelahiran->pelapor_penduduk_id);
        }
        $totalkatanikpelapor = strlen($kelahiran->pelapor_nik);
        $kurangnikpelapor = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangnikpelapor; $i++) {
            $hasil6 = substr($kelahiran->pelapor_nik, $i, $totalkatanikpelapor);
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
        if ($kelahiran->pelapor_penduduk == 1) {
            $namapelaporpenduduk = $pelaporlist->nama;
        }
        if ($kelahiran->pelapor_penduduk == 2) {
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
        if ($kelahiran->pelapor_penduduk == 1) {
            $thnkurangpelapor = substr($pelaporlist->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kelahiran->pelapor_penduduk == 2) {
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
        if ($kelahiran->pelapor_penduduk == 1) {
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
        if ($kelahiran->pelapor_penduduk == 2) {
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
        if ($kelahiran->pelapor_penduduk == 1) {
            $keluargapelapor = $this->keluarga->cekalamat($kelahiran->pelapor_penduduk_id);
            $pdf->Cell(135, 4, strtoupper($keluargapelapor->alamat), 1, '', 'L');
        }
        if ($kelahiran->pelapor_penduduk == 2) {
            $pdf->Cell(135, 4, strtoupper($pelaporlist->alamat), 1, '', 'L');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN     :', 0, '', '');
        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->pelapor_penduduk == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->desa), 1, '', '');
        }
        if ($kelahiran->pelapor_penduduk == 2) {
            $pdf->Cell(40, 4, strtoupper($pelaporlist->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->pelapor_penduduk == 1) {
            $pdf->Cell(40, 5, strtoupper($kelahiran->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kelahiran->pelapor_penduduk == 2) {
            $pdf->Cell(40, 5, strtoupper($pelaporlist->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->pelapor_penduduk == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kelahiran->pelapor_penduduk == 2) {
            $pdf->Cell(40, 4, strtoupper($pelaporlist->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -6, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->pelapor_penduduk == 1) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kelahiran->pelapor_penduduk == 2) {
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
        if ($kelahiran->penduduk_saksi1 == 1) {
            $saksi1list = $this->pribadi->find($kelahiran->saksi1_penduduk_id);
        }
        if ($kelahiran->penduduk_saksi1 == 2) {
            $saksi1list = $this->nonpenduduk->find($kelahiran->saksi1_penduduk_id);
        }
        $totalkataniksaksi1 = strlen($kelahiran->saksi1_nik);
        $kurangniksaksi1 = 26;
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        for ($i = 0; $i <= $kurangniksaksi1; $i++) {
            $hasil8 = substr($kelahiran->saksi1_nik, $i, $totalkataniksaksi1);
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
        if ($kelahiran->penduduk_saksi1 == 1) {
            $namasaksi1penduduk = $saksi1list->nama;
        }
        if ($kelahiran->penduduk_saksi1 == 2) {
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
        $pdf->Cell(35, 5, '3.   TANGGAL LAHIR / UMUR', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kelahiran->penduduk_saksi1 == 1) {
            $thnkurangsaksi1 = substr($saksi1list->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kelahiran->penduduk_saksi1 == 2) {
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
        $pdf->SetX(70);
        $pdf->Cell(0, 5, 'UMUR', 0, '', '');

        // pekerjaan saksi 1

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(35, 5, '4.   PEKERJAAN', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');
        $pdf->SetX(53);
        if ($kelahiran->penduduk_saksi1 == 1) {
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
        if ($kelahiran->penduduk_saksi1 == 2) {
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
        if ($kelahiran->penduduk_saksi1 == 1) {
            $keluargasaksi = $this->keluarga->cekalamat($kelahiran->saksi1_penduduk_id);
            $pdf->Cell(135, 4, strtoupper($keluargasaksi->alamat), 1, '', 'L');
        }
        if ($kelahiran->penduduk_saksi1 == 2) {
            $pdf->Cell(135, 4, strtoupper($saksi1list->alamat), 1, '', 'L');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN    :', 0, '', '');
        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->penduduk_saksi1 == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->desa), 1, '', '');
        }
        if ($kelahiran->penduduk_saksi1 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi1list->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->penduduk_saksi1 == 1) {
            $pdf->Cell(40, 5, strtoupper($kelahiran->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kelahiran->penduduk_saksi1 == 2) {
            $pdf->Cell(40, 5, strtoupper($saksi1list->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'D. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->penduduk_saksi1 == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kelahiran->penduduk_saksi1 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi1list->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -4, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->penduduk_saksi1 == 1) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kelahiran->penduduk_saksi1 == 2) {
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
        if ($kelahiran->penduduk_saksi2 == 1) {
            $saksi2list = $this->pribadi->find($kelahiran->saksi2_penduduk_id);
        }
        if ($kelahiran->penduduk_saksi2 == 2) {
            $saksi2list = $this->nonpenduduk->find($kelahiran->saksi2_penduduk_id);
        }
        $totalkataniksaksi2 = strlen($kelahiran->saksi2_nik);
        $kurangniksaksi2 = 26;
        $pdf->SetX(53);

        for ($i = 0; $i <= $kurangniksaksi2; $i++) {
            $hasil9 = substr($kelahiran->saksi2_nik, $i, $totalkataniksaksi2);
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
        if ($kelahiran->penduduk_saksi2 == 1) {
            $namasaksipenduduk = $saksi2list->nama;
        }
        if ($kelahiran->penduduk_saksi2 == 2) {
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
        $pdf->Cell(35, 5, '3.   TANGGAL LAHIR / UMUR', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(50);
        $pdf->Cell(5, 4, ':', 0, '', 'L');

        $pdf->SetX(53);
        if ($kelahiran->penduduk_saksi2 == 1) {
            $thnkurangsaksi2 = substr($saksi2list->tanggal_lahir, 6, 4) - date('Y');
        }
        if ($kelahiran->penduduk_saksi2 == 2) {
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
        if ($kelahiran->penduduk_saksi2 == 1) {
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
        if ($kelahiran->penduduk_saksi2 == 2) {
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
        if ($kelahiran->penduduk_saksi2 == 1) {
            $keluargasaksi2 = $this->keluarga->cekalamat($kelahiran->saksi2_penduduk_id);
            $pdf->Cell(135, 4, $keluargasaksi2->alamat, 1, '', 'L');
        }
        if ($kelahiran->penduduk_saksi2 == 2) {
            $pdf->Cell(135, 4, $saksi2list->alamat, 1, '', 'L');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 0, 'A. DESA/KELURHAN      :', 0, '', '');
        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->penduduk_saksi2 == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->desa), 1, '', '');
        }
        if ($kelahiran->penduduk_saksi2 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi2list->desa->desa), 1, '', '');
        }
        $pdf->Ln(6);
        $pdf->SetX(53);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN         :', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(80);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->penduduk_saksi2 == 1) {
            $pdf->Cell(40, 5, strtoupper($kelahiran->desa->kecamatan->kecamatan), 1, '', '');
        }
        if ($kelahiran->penduduk_saksi2 == 2) {
            $pdf->Cell(40, 5, strtoupper($saksi2list->desa->kecamatan->kecamatan), 1, '', '');
        }
        $pdf->SetX(120);
        $pdf->Cell(0, -5, 'C. KAB/KOTA               :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->penduduk_saksi2 == 1) {

            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        if ($kelahiran->penduduk_saksi2 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi2list->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        }
        $pdf->Ln(10);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -5, 'D. PROVINSI                :', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(148);
        $pdf->SetFont('Arial', '', 7);
        if ($kelahiran->penduduk_saksi2 == 1) {
            $pdf->Cell(40, 4, strtoupper($kelahiran->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        if ($kelahiran->penduduk_saksi2 == 2) {
            $pdf->Cell(40, 4, strtoupper($saksi2list->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        }
        $pdf->Ln(10);

//        =====================================================================================================================================================================================?
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($kelahiran->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($kelahiran->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($kelahiran->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        //mengetahui kepala desa/kelurahan

        $pdf->Cell(85, 0, 'Mengetahui,', 0, 0, 'C');
        $pdf->Ln(3);
        $pdf->Cell(85, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $kelahiran->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $kelahiran->tahun, 0, '', 'C');

        $pdf->Ln(-2);
        if ($kelahiran->penandatangan == 'Atasnama Pimpinan' || $kelahiran->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 8);
            if ($pejabatpimpinan != null) {
                $pdf->Cell(85, 10, $an . ' ' . strtoupper($pejabatpimpinan->jabatan) . ' ' . strtoupper($desa->desa) . ',', 0, '', 'C');

            } else {
                $pdf->Cell(85, 10, $an . ' ' . strtoupper($desa->desa), 0, '', 'C');

            }
            $pdf->Ln(3);
            $pdf->SetFont('Arial', '', 8);
            if ($pejabat != null) {
                $idpejabat = 'Sekretaris Organisasi';
                $pejabatsekre = $this->pejabat->cekjabatan($idpejabat);
                $pdf->Cell(85, 10, $pejabatsekre->jabatan . ',', 0, '', 'C');
            }

        }
        if ($kelahiran->penandatangan == 'Jabatan Struktural') {

            $pejabatstruktural = $this->pejabat->find($kelahiran->jabatan_lainnya);
            $pdf->Ln(3);
            $pdf->Cell(85, 10, 'u.b.', 0, '', 'C');
            $pdf->Ln(3);
            if ($pejabatstruktural != null) {
                $pdf->Cell(85, 10, $pejabat->jabatan . ',', 0, '', 'C');
            }
        }
        if ($kelahiran->penandatangan != 'Atasnama Pimpinan' && $kelahiran->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 8);
            if ($kelahiran->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($kelahiran->penandatangan);
                if ($pejabatsekretaris != null) {
                    $pdf->Cell(85, 10, strtoupper($pejabatsekretaris->jabatan . ','), 0, '', 'C');
                }
            }
            if ($kelahiran->penandatangan == 'Pimpinan Organisasi' && $kelahiran->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($kelahiran->penandatangan);
                if ($pejabatsekretaris != null) {
                    $pdf->Cell(85, 10, strtoupper($pejabatsekretaris->jabatan . ' ' . $desa->desa . ','), 0, '', 'C');
                }
            }

        }
        if ($kelahiran->penandatangan != 'Jabatan Struktural') {
            $pdf->Ln(20);
        }
        if ($kelahiran->penandatangan == 'Jabatan Struktural') {
            $pdf->Ln(17);
        }

        if ($pejabat != null) {
            if ($pejabat->titel_belakang != '') {
                $pdf->SetFont('Arial', 'BU', 8);
                if ($pejabat->titel_depan != '') {
                    $pdf->Cell(85, 10, $pejabat->titel_depan . ' ' . $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
                } else {
                    $pdf->Cell(85, 10, $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
                }
            } else {
                if ($pejabat->titel_depan != '') {
                    $pdf->Cell(85, 10, $pejabat->titel_depan . ' ' . $pejabat->nama . ' ' . $pejabat->titel_belakang, 0, '', 'C');
                } else {
                    $pdf->Cell(85, 10, $pejabat->nama . ' ' . $pejabat->titel_belakang, 0, '', 'C');
                }
            }
            $pdf->SetFont('Arial', '', 8);
            $pdf->Ln(3);
            $pdf->Cell(85, 10, $pejabat->pangkat, 0, '', 'C');
            $pdf->Ln(3);

            if ($pejabat->nip != '') {
                $pdf->Cell(85, 10, 'NIP.' . $pejabat->nip, 0, '', 'C');
            }
        }
        if ($kelahiran->penandatangan == 'Jabatan Struktural') {
            $pdf->Ln(-43);
        }
        if ($kelahiran->penandatangan == 'Pimpinan Organisasi') {
            $pdf->Ln(-37);
        }
        if ($kelahiran->penandatangan == 'Sekretaris Organisasi') {
            $pdf->Ln(-37);
        }
        if ($kelahiran->penandatangan == 'Atasnama Pimpinan') {
            $pdf->Ln(-40);
        }
        // pelapor kelahiran
        $hari = substr($kelahiran->tanggal, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($kelahiran->tanggal, 3, 2) <= 9) {
            $bulan = $indo[substr($kelahiran->tanggal, 4, 1)];
        } else {
            $bulan = $indo[substr($kelahiran->tanggal, 3, 2)];
        }
        $tahun = substr($kelahiran->tanggal, 6, 4);

        $tanggalcetak = $hari . ' ' . $bulan . ' ' . $tahun;
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(-45);
        $pdf->SetX(150);
        $pdf->Cell(10, 70, $desa->desa . ', ' . $tanggalcetak, 0, '', 'C');
        $pdf->Ln(4);
        $pdf->SetX(150);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(10, 70, 'PELAPOR,', 0, '', 'C');

        if ($kelahiran->penandatangan == 'Jabatan Struktural') {
            $pdf->Ln(28);
            $pdf->SetX(150);
        }
        if ($kelahiran->penandatangan == 'Pimpinan Organisasi') {
            $pdf->Ln(20);
            $pdf->SetX(150);
        }
        if ($kelahiran->penandatangan == 'Sekretaris Organisasi') {
            $pdf->Ln(20);
            $pdf->SetX(150);
        }
        if ($kelahiran->penandatangan == 'Atasnama Pimpinan') {
            $pdf->Ln(25);
            $pdf->SetX(150);
        }
        $pdf->SetFont('Arial', 'BU', 8);
        $pdf->Cell(10, 70, '' . $namapelaporpenduduk . '', 0, '', 'C');
        $tanggal = date('d/m/y');
        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

        $pdf->Output('cetak-data-kelahiran-' . $tanggal . '.pdf', 'I');
        exit;
    }
}