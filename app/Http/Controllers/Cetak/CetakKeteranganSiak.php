<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DataPribadi\DisabilitasPendudukRepository;
use App\Domain\Repositories\DataPribadi\DokumenPendudukRepository;
use App\Domain\Repositories\DataPribadi\KeluargaRepository;
use App\Domain\Repositories\DataPribadi\OrangTuaRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\KeteranganPindahRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Domain\Repositories\Wilayah\KodeposRepository;
use App\Http\Controllers\Controller;

class CetakKeteranganSiak extends Controller
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
        KeteranganPindahRepository $keteranganPindahRepository,
        PribadiRepository $pribadiRepository,
        NonPendudukRepository $nonPendudukRepository,
        PejabatRepository $pejabatRepository,
        LogoRepository $logoRepository,
        AlamatRepository $alamatRepository,
        DesaRepository $desaRepository,
        KodeAdministrasiRepository $kodeAdministrasiRepository,
        KeluargaRepository $keluargaRepository,
        OrganisasiRepository $organisasiRepository,
        KodeposRepository $kodeposRepository,
        DisabilitasPendudukRepository $disabilitasPendudukRepository,
        OrangTuaRepository $orangTuaRepository,
        DokumenPendudukRepository $dokumenPendudukRepository

    )
    {
        $this->keteraganpindah = $keteranganPindahRepository;
        $this->pribadi = $pribadiRepository;
        $this->nonpenduduk = $nonPendudukRepository;
        $this->pejabat = $pejabatRepository;
        $this->logo = $logoRepository;
        $this->alamat = $alamatRepository;
        $this->desa = $desaRepository;
        $this->kodeadministrasi = $kodeAdministrasiRepository;
        $this->keluarga = $keluargaRepository;
        $this->organisasi = $organisasiRepository;
        $this->kodepos = $kodeposRepository;
        $this->dokumenpenduduk = $dokumenPendudukRepository;
        $this->disabilitas = $disabilitasPendudukRepository;
        $this->orangtua = $orangTuaRepository;
        $this->middleware('auth');

    }

    function Headers($pdf)
    {
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        //Put the watermark
        $pdf->SetFont('Arial', 'B', 55);
        $pdf->SetTextColor(128);
        $pdf->RotatedText(55, 190, 'Versi Ujicoba', 24);
    }

    function RotatedText($x, $y, $pdf, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $pdf->Text($x, $y, $pdf);
        $this->Rotate(0);
    }

    function Kop($pdf, $id)
    {
        $pdf->AddFont('Times-Roman', '', 'times.php');
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $desa = $this->desa->find(session('desa'));
        $keteranganpindah = $this->keteraganpindah->find($id);

        $jeniskodeadministrasi = $this->keteraganpindah->cekkodejenisadministrasi($keteranganpindah->jenis_pelayanan_id);
        $alamat = $this->alamat->cekalamatperdasarkandesa(session('organisasi'));
        $kodeadministrasi = $this->kodeadministrasi->cekkodeadminbysession();
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();
        $logogambar = $this->dokumenpenduduk->cekdokumenktp($desa->id);

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
        if ($desa->kecamatan->status == 2) {
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
        $cekpindah = $keteranganpindah->is_alamat_pindah;
        if($cekpindah == 'Dalam Satu Desa/Kelurahan'){
            $kodejudul = 'F.1-24';
        }
        else if($cekpindah == 'Antar Desa/Kelurahan Dalam Satu Kecamatan'){
            $kodejudul = 'F.1-26';
        }
        else if($cekpindah == 'Antar Kecamatan Dalam Satu Kabupaten/Kota'){
            $kodejudul = 'F.1-30';
        }
        else if($cekpindah == 'Antar Kabupaten/Kota'){
            $kodejudul = 'F.1-32';
        }
        else if($cekpindah == 'Antar Provinsi'){
            $kodejudul = 'F.1-34';
        }
        $pdf->SetX(180);
        $pdf->Cell(25, 6, $kodejudul, 1, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Ln(12);
        $pdf->Cell(25, -16, 'PROVINSI', 0, '', 'L');
        $pdf->SetX(57);
        $pdf->Cell(25, -16, ':', 0, '', 'L');

        $pdf->Ln(-10);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, substr($desa->kecamatan->kabupaten->provinsi->kode_prov, 0, 1), 1, 0, '');
        $pdf->Cell(4, 4, substr($desa->kecamatan->kabupaten->provinsi->kode_prov, 1, 1), 1, 0, '');
        $pdf->SetX(100);
        $pdf->Cell(105, 4, 'PROVINSI ' . strtoupper($desa->kecamatan->kabupaten->provinsi->provinsi), 1, 0, '');
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -16, 'KABUPATEN / KOTA', 0, '', 'L');
        $pdf->SetX(57);
        $pdf->Cell(25, -16, ':', 0, '', 'L');

        $pdf->Ln(-10);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, substr($desa->kecamatan->kabupaten->kode_kab, 0, 1), 1, 0, '');
        $pdf->Cell(4, 4, substr($desa->kecamatan->kabupaten->kode_kab, 1, 1), 1, 0, '');
        $pdf->SetX(100);
        $pdf->Cell(105, 4, strtoupper($status . ' ' . $desa->kecamatan->kabupaten->kabupaten), 1, 0, '');
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -16, 'KECAMATAN', 0, '', 'L');
        $pdf->SetX(57);
        $pdf->Cell(25, -16, ':', 0, '', 'L');
        $pdf->Ln(-10);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, substr($desa->kecamatan->kode_kec, 0, 1), 1, 0, '');
        $pdf->Cell(4, 4, substr($desa->kecamatan->kode_kec, 1, 1), 1, 0, '');
        $pdf->SetX(100);
        $pdf->Cell(105, 4, strtoupper($statuskecamatan . ' ' . $desa->kecamatan->kecamatan), 1, 0, '');
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -16, 'KELURAHAN/DESA', 0, '', 'L');
        $pdf->SetX(57);
        $pdf->Cell(25, -16, ':', 0, '', 'L');

        $pdf->Ln(-10);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, substr($desa->kode_desa, 0, 1), 1, 0, '');
        $pdf->Cell(4, 4, substr($desa->kode_desa, 1, 1), 1, 0, '');
        $pdf->Cell(4, 4, substr($desa->kode_desa, 2, 1), 1, 0, '');
        $pdf->Cell(4, 4, substr($desa->kode_desa, 3, 1), 1, 0, '');
        $pdf->SetX(100);
        $pdf->Cell(105, 4, strtoupper($statusdesa . ' ' . $desa->desa), 1, 0, '');
        $pdf->Ln(12);
        $pdf->SetFont('arial', 'BU', 12);
        $pdf->SetX(25);
        $pdf->Cell(0, 0, 'SURAT KETERANGAN PINDAH WNI', 0, '', 'C');
        $pdf->Ln(4);
        $pdf->SetFont('arial', 'B', 8);
        $pdf->SetX(25);
        $pdf->Cell(0, 0, $keteranganpindah->is_alamat_pindah, 0, '', 'C');
        $pdf->Ln(3);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($keteranganpindah->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($keteranganpindah->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($keteranganpindah->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetFont('arial', 'B', 8);
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $keteranganpindah->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $keteranganpindah->tahun, 0, '', 'C');

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

    public function KeteraganPindah($id)
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
        $pdf->SetMargins(10, 5, 10);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('Pindah Tempat-F.1-26');
        $this->Kop($pdf, $id);
        $pdf->SetY(60);
        $desa = $this->desa->find(session('desa'));
        $keteranganpindah = $this->keteraganpindah->find($id);
        $cekkkkeluarga = $this->keluarga->ceknikkeluarga($keteranganpindah->nik_pemohon);
        $pribadi1 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut1);
        $pribadi2 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut2);
        $pribadi3 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut3);
        $pribadi4 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut4);
        $pribadi5 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut5);
        $pribadi6 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut6);
        $pribadi7 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut7);
        $pribadi8 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut8);
        //kabupaten
        if ($desa->kecamatan->kabupaten->status == 1) {
            $status = 'KABUPATEN';
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $status = 'KOTA';
        }
        //kecamatan
        if ($desa->kecamatan->status == 1) {
            $statuskecamatan = 'KECAMATAN';
        }
        if ($desa->kecamatan->status == 2) {
            $statuskecamatan = 'DISTRIK';
        }
        //desa
        if ($desa->status == 1) {
            $statusdesa = 'KELURAHAN';
        }
        if ($desa->status == 2) {
            $statusdesa = 'DESA';
        }
        if ($desa->status == 3) {
            $statusdesa = 'KAMPUNG';
        }
        if ($desa->status == 4) {
            $statusdesa = 'NEGERI';
        }

        //desa tujuan
        //kabupaten
        if ($keteranganpindah->desa_tujuan->kecamatan->kabupaten->status == 1) {
            $statustujuan = 'KABUPATEN';
        }
        if ($keteranganpindah->desa_tujuan->kecamatan->kabupaten->status == 2) {
            $statustujuan = 'KOTA';
        }
        //kecamatan
        if ($keteranganpindah->desa_tujuan->kecamatan->status == 1) {
            $statuskecamatantujuan = 'KECAMATAN';
        }
        if ($keteranganpindah->desa_tujuan->kecamatan->status == 2) {
            $statuskecamatantujuan = 'DISTRIK';
        }
        //desa
        if ($keteranganpindah->desa_tujuan->status == 1) {
            $statusdesatujuan = 'KELURAHAN';
        }
        if ($keteranganpindah->desa_tujuan->status == 2) {
            $statusdesatujuan = 'DESA';
        }
        if ($keteranganpindah->desa_tujuan->status == 3) {
            $statusdesatujuan = 'KAMPUNG';
        }
        if ($keteranganpindah->desa_tujuan->status == 4) {
            $statusdesatujuan = 'NEGERI';
        }

        if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
        }
        $namadesa = $desa->desa;

        $cekpribadi = $this->pribadi->ceknikcetak($keteranganpindah->nik_pemohon);
        $keluarga = $this->keluarga->cekalamat($cekpribadi->id);
        $hari = substr($cekpribadi->tanggal_lahir, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($cekpribadi->tanggal_lahir, 3, 2) <= 9) {
            $bulan = $indo[substr($cekpribadi->tanggal_lahir, 4, 1)];
        } else {
            $bulan = $indo[substr($cekpribadi->tanggal_lahir, 3, 2)];
        }
        $tahun = substr($cekpribadi->tanggal_lahir, 6, 4);
        $tempatlahir = $cekpribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;

        $hari1 = substr($keteranganpindah->tanggal, 0, 2);
        if (substr($keteranganpindah->tanggal, 3, 2) <= 9) {
            $bulan1 = $indo[substr($keteranganpindah->tanggal, 4, 1)];
        } else {
            $bulan1 = $indo[substr($keteranganpindah->tanggal, 3, 2)];
        }
        $tahun1 = substr($keteranganpindah->tanggal, 6, 4);
        $tempatlahir1 = $hari1 . ' ' . $bulan1 . ' ' . $tahun1;
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'DATA DAERAH ASAL', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        //
        // Nomor kartu keluarga
        //
        $pdf->Ln(4);
        $totalkatakk = strlen($keluarga->nomor_kk);
        $kurangkk = 15;
        $pdf->Cell(23, 4, '1    Nomor Kartu Keluarga', 0, '', 'L');
        $pdf->SetX(73);
        for ($i = 0; $i <= $kurangkk; $i++) {
            $hasil1 = substr($keluarga->nomor_kk, $i, $totalkatakk);
            $tampil1 = substr($hasil1, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 8);
            $widths = array($widd);
            $caption = array(' ' . $tampil1);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        //
        // Nama Kepala Keluarga
        //
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, 4, '2    Nama Kepala Keluarga', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(73);
        $pdf->Cell(132, 4, $keluarga->nama, 1, '', 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, 4, '3    Alamat', 0, '', 'L');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(73);
        $pdf->Cell(75, 4, $keluarga->alamat, 1, '', 'L');
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetX(150);
        $pdf->Cell(10, 4, 'RT.', 0, '', '');
//        $pdf->Cell(5);
        if (strlen($keluarga->alamat_rt) == 1) {
            $rt = substr($keluarga->alamat_rt, 0, 1);
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, $rt, 1, '', 'L');
            $pdf->Cell(4);
        }
        if (strlen($keluarga->alamat_rt) == 2) {
            $rt = substr($keluarga->alamat_rt, 0, 1);
            $rt2 = substr($keluarga->alamat_rt, 1, 1);
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, $rt, 1, '', 'L');
            $pdf->Cell(4, 4, $rt2, 1, '', 'L');
            $pdf->Cell(4);
        }
        if (strlen($keluarga->alamat_rt) == 3) {
            $rt = substr($keluarga->alamat_rt, 0, 1);
            $rt2 = substr($keluarga->alamat_rt, 1, 1);
            $rt3 = substr($keluarga->alamat_rt, 2, 1);
            $pdf->Cell(4, 4, $rt, 1, '', 'L');
            $pdf->Cell(4, 4, $rt2, 1, '', 'L');
            $pdf->Cell(4, 4, $rt3, 1, '', 'L');
            $pdf->Cell(4);
        }
        $pdf->Cell(4, 4, 'RW.', 0, '', 'L');
        $pdf->Cell(4);
        if (strlen($keluarga->alamat_rw) == 1) {
            $rw = substr($keluarga->alamat_rw, 0, 1);
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, $rw, 1, '', 'L');
            $pdf->Cell(4);
        }
        if (strlen($keluarga->alamat_rw) == 2) {
            $rw = substr($keluarga->alamat_rw, 0, 1);
            $rw2 = substr($keluarga->alamat_rw, 1, 1);
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, $rw, 1, '', 'L');
            $pdf->Cell(4, 4, $rw2, 1, '', 'L');
            $pdf->Cell(4);
        }
        if (strlen($keluarga->alamat_rw) == 3) {
            $rw = substr($keluarga->alamat_rw, 0, 1);
            $rw2 = substr($keluarga->alamat_rw, 1, 1);
            $rw3 = substr($keluarga->alamat_rw, 2, 1);
            $pdf->Cell(4, 4, $rw, 1, '', 'L');
            $pdf->Cell(4, 4, $rw2, 1, '', 'L');
            $pdf->Cell(4, 4, $rw3, 1, '', 'L');
            $pdf->Cell(4);
        }
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, 4, '', 0, '', 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(73);
        $pdf->Cell(0, 4, 'Dusun/Dukuh/Kampung', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(120);
        $pdf->Cell(85, 4, $keluarga->alamat_dusun, 1, '', 'L');
        $pdf->Ln(7);
        $pdf->SetX(40);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN', 0, '', '');
        $pdf->Ln(-2);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(50, 4, strtoupper($statusdesa . ' ' . $keteranganpindah->desa->desa), 1, '', '');
        $pdf->Ln(6);
        $pdf->SetX(40);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(50, 4, strtoupper($statuskecamatan . ' ' . $keteranganpindah->desa->kecamatan->kecamatan), 1, '', '');
        $pdf->SetX(130);
        $pdf->Cell(0, -5, 'C. KAB/KOTA', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(158);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(47, 4, strtoupper($status . ' ' . $keteranganpindah->desa->kecamatan->kabupaten->kabupaten), 1, '', '');
        $pdf->Ln(10);
        $pdf->SetX(130);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -5, 'D. PROVINSI', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(158);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(47, 4, strtoupper($keteranganpindah->desa->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        //
        // NIK Pemohon
        //
        $pdf->Ln(5);
        $totalkatakk = strlen($keteranganpindah->nik_pemohon);
        $kurangkk = 15;
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, 4, '4    NIK Pemohon', 0, '', 'L');
        $pdf->SetX(73);
        for ($i = 0; $i <= $kurangkk; $i++) {
            $hasil1 = substr($keteranganpindah->nik_pemohon, $i, $totalkatakk);
            $tampil1 = substr($hasil1, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 8);
            $widths = array($widd);
            $caption = array(' ' . $tampil1);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        //
        // Nama Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, 4, '5    Nama Lengkap', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(73);
        $pdf->Cell(132, 4, $cekpribadi->nama, 1, '', 'L');
        $pdf->SetFont('Arial', 'B', 11);

        //
        // Data Kepindahan
        //

        $pdf->Ln(10);
        $pdf->Cell(0, 0, 'DATA KEPINDAHAN', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        //
        // Alasan Pindah
        //
        $pdf->Ln(5);
        if ($keteranganpindah->alasan_pindah == 'Pekerjaan') {
            $alasankkpindah = '1';
            $alasankkpindahlainnya = '......... .........';
        } else if ($keteranganpindah->alasan_pindah == 'Pendidikan') {
            $alasankkpindah = '2';
            $alasankkpindahlainnya = '......... .........';

        } else if ($keteranganpindah->alasan_pindah == 'Keamanan') {
            $alasankkpindah = '3';
            $alasankkpindahlainnya = '......... .........';

        } else if ($keteranganpindah->alasan_pindah == 'Kesehatan') {
            $alasankkpindah = '4';
            $alasankkpindahlainnya = '......... .........';

        } else if ($keteranganpindah->alasan_pindah == 'Perumahan') {
            $alasankkpindah = '5';
            $alasankkpindahlainnya = '......... .........';

        } else if ($keteranganpindah->alasan_pindah == 'Keluarga') {
            $alasankkpindah = '6';
            $alasankkpindahlainnya = '......... .........';

        } else {
            $alasankkpindah = '7';
            $alasankkpindahlainnya = $keteranganpindah->alasan_pindah;

        }
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, -14, '1', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(16);
        $pdf->Cell(25, -14, 'Alasan Pindah', 0, '', 'L');
        $pdf->Ln(-8);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, $alasankkpindah, 1, 0, '');
        $pdf->Ln(-2);
        $pdf->SetX(85);
        $pdf->Cell(20, 5, '1. Pekerjaan', 0, 0, '');
        $pdf->SetX(110);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, '3. Keamanan', 0, 0, '');
        $pdf->SetX(135);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, '5. Perumahan', 0, 0, '');
        $pdf->SetX(160);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, '7. Lainnya', 0, 0, '');
        $pdf->ln(2.5);
        $pdf->SetX(85);
        $pdf->Cell(20, 5, '2. Pendidikan', 0, 0, '');
        $pdf->SetX(110);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, '4. Kesehatan', 0, 0, '');
        $pdf->SetX(135);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, '6. Keluarga', 0, 0, '');
        $pdf->SetX(160);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, $alasankkpindahlainnya, 0, 0, '');
        //
        // alamat tujuan
        //
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, 4, '2     Alamat Tujuan Pindah', 0, '', 'L');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(73);
        $pdf->Cell(75, 4, $keteranganpindah->alamat_tujuan, 1, '', 'L');
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetX(150);
        $pdf->Cell(10, 4, 'RT.', 0, '', '');
//        $pdf->Cell(5);
        if (strlen($keteranganpindah->rt_tujuan) == 1) {
            $rt = substr($keteranganpindah->rt_tujuan, 0, 1);
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, $rt, 1, '', 'L');
            $pdf->Cell(4);
        }
        if (strlen($keteranganpindah->rt_tujuan) == 2) {
            $rt = substr($keteranganpindah->rt_tujuan, 0, 1);
            $rt2 = substr($keteranganpindah->rt_tujuan, 1, 1);
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, $rt, 1, '', 'L');
            $pdf->Cell(4, 4, $rt2, 1, '', 'L');
            $pdf->Cell(4);
        }
        if (strlen($keteranganpindah->rt_tujuan) == 3) {
            $rt = substr($keteranganpindah->rt_tujuan, 0, 1);
            $rt2 = substr($keteranganpindah->rt_tujuan, 1, 1);
            $rt3 = substr($keteranganpindah->rt_tujuan, 2, 1);
            $pdf->Cell(4, 4, $rt, 1, '', 'L');
            $pdf->Cell(4, 4, $rt2, 1, '', 'L');
            $pdf->Cell(4, 4, $rt3, 1, '', 'L');
            $pdf->Cell(4);
        }
        $pdf->Cell(4, 4, 'RW.', 0, '', 'L');
        $pdf->Cell(4);
        if (strlen($keteranganpindah->rw_tujuan) == 1) {
            $rw = substr($keteranganpindah->rw_tujuan, 0, 1);
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, $rw, 1, '', 'L');
            $pdf->Cell(4);
        }
        if (strlen($keteranganpindah->rw_tujuan) == 2) {
            $rw = substr($keteranganpindah->rw_tujuan, 0, 1);
            $rw2 = substr($keteranganpindah->rw_tujuan, 1, 1);
            $pdf->Cell(4, 4, 0, 1, '', 'L');
            $pdf->Cell(4, 4, $rw, 1, '', 'L');
            $pdf->Cell(4, 4, $rw2, 1, '', 'L');
            $pdf->Cell(4);
        }
        if (strlen($keteranganpindah->rw_tujuan) == 3) {
            $rw = substr($keteranganpindah->rw_tujuan, 0, 1);
            $rw2 = substr($keteranganpindah->rw_tujuan, 1, 1);
            $rw3 = substr($keteranganpindah->rw_tujuan, 2, 1);
            $pdf->Cell(4, 4, $rw, 1, '', 'L');
            $pdf->Cell(4, 4, $rw2, 1, '', 'L');
            $pdf->Cell(4, 4, $rw3, 1, '', 'L');
            $pdf->Cell(4);
        }
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, 4, '', 0, '', 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(73);
        $pdf->Cell(0, 4, 'Dusun/Dukuh/Kampung', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(120);
        $pdf->Cell(85, 4, $keteranganpindah->dusun_tujuan, 1, '', 'L');
        $pdf->Ln(7);
        $pdf->SetX(40);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 0, 'A. DESA/KELURAHAN', 0, '', '');
        $pdf->Ln(-2);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(50, 4, strtoupper($statusdesatujuan . ' ' . $keteranganpindah->desa_tujuan->desa), 1, '', '');
        $pdf->Ln(6);
        $pdf->SetX(40);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'B. KECAMATAN', 0, '', '');
        $pdf->Ln(-1);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(50, 4, strtoupper($statuskecamatantujuan . ' ' . $keteranganpindah->desa_tujuan->kecamatan->kecamatan), 1, '', '');
        $pdf->SetX(130);
        $pdf->Cell(0, -5, 'C. KAB/KOTA', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(158);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(47, 4, strtoupper($statustujuan . ' ' . $keteranganpindah->desa_tujuan->kecamatan->kabupaten->kabupaten), 1, '', '');
        $pdf->Ln(10);
        $pdf->SetX(130);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, -5, 'D. PROVINSI', 0, '', '');
        $pdf->Ln(-5);
        $pdf->SetX(158);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(47, 4, strtoupper($keteranganpindah->desa_tujuan->kecamatan->kabupaten->provinsi->provinsi), 1, '', '');
        $pdf->Ln(5);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'Kodepos', 0, '', '');
        $pdf->SetX(90);
        $kodepos = substr($keteranganpindah->kode_pos->kode_pos, 0, 1);
        $kodepos2 = substr($keteranganpindah->kode_pos->kode_pos, 1, 1);
        $kodepos3 = substr($keteranganpindah->kode_pos->kode_pos, 2, 1);
        $kodepos4 = substr($keteranganpindah->kode_pos->kode_pos, 3, 1);
        $kodepos5 = substr($keteranganpindah->kode_pos->kode_pos, 4, 1);
        $pdf->Cell(4, 4, $kodepos, 1, '', 'L');
        $pdf->Cell(4, 4, $kodepos2, 1, '', 'L');
        $pdf->Cell(4, 4, $kodepos3, 1, '', 'L');
        $pdf->Cell(4, 4, $kodepos4, 1, '', 'L');
        $pdf->Cell(4, 4, $kodepos5, 1, '', 'L');
        $pdf->Cell(4.5);
        $pdf->SetX(120);
        $pdf->Cell(0, 3, 'Telepon', 0, '', '');
        $pdf->SetX(135);
        $telepon = substr($keteranganpindah->telepon, 0, 1);
        $telepon2 = substr($keteranganpindah->telepon, 1, 1);
        $telepon3 = substr($keteranganpindah->telepon, 2, 1);
        $telepon4 = substr($keteranganpindah->telepon, 3, 1);
        $telepon5 = substr($keteranganpindah->telepon, 4, 1);
        $telepon6 = substr($keteranganpindah->telepon, 5, 1);
        $telepon7 = substr($keteranganpindah->telepon, 6, 1);
        $telepon8 = substr($keteranganpindah->telepon, 7, 1);
        $telepon9 = substr($keteranganpindah->telepon, 8, 1);
        $telepon10 = substr($keteranganpindah->telepon, 9, 1);
        $telepon11 = substr($keteranganpindah->telepon, 10, 1);
        $telepon12 = substr($keteranganpindah->telepon, 11, 1);
        $telepon13 = substr($keteranganpindah->telepon, 12, 1);
        $telepon14 = substr($keteranganpindah->telepon, 13, 1);
        $pdf->Cell(4, 4, $telepon, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon2, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon3, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon4, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon5, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon6, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon7, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon8, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon9, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon10, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon11, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon12, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon13, 1, '', 'L');
        $pdf->Cell(4, 4, $telepon14, 1, '', 'L');
        $pdf->Cell(4);
        //
        // jenis kepindahan
        //
        $pdf->Ln(5);
        if ($keteranganpindah->jenis_pindah == 'Kepala Keluarga') {
            $jenis_pindah = '1';
        } else if ($keteranganpindah->jenis_pindah == 'Kepala Keluarga & Seluruh Anggota Keluarga') {
            $jenis_pindah = '2';
        } else if ($keteranganpindah->jenis_pindah == 'Kepala Keluarga & Sebagian Anggota Keluarga') {
            $jenis_pindah = '3';
        } else if ($keteranganpindah->jenis_pindah == 'Anggota Keluarga') {
            $jenis_pindah = '4';
        }
        $pdf->Ln(9);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, -14, '3', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(16);
        $pdf->Cell(25, -14, 'Jenis Pindah', 0, '', 'L');
        $pdf->Ln(-8);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, $jenis_pindah, 1, 0, '');
        $pdf->Ln(-2);
        $pdf->SetX(80);
        $pdf->Cell(20, 5, '1. Kep. Keluarga', 0, 0, '');
        $pdf->SetX(140);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, '3. Kep. Keluarga & Sebagian Anggota Keluarga', 0, 0, '');
        $pdf->ln(4);
        $pdf->SetX(80);
        $pdf->Cell(20, 4, '2. Kep. Keluarga & Seluruh Anggota Keluarga', 0, 0, '');
        $pdf->SetX(140);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 4, '4. Anggota Keluarga', 0, 0, '');
//
        // status_kk_tidak_pindah
        //
        $pdf->Ln(5);
        if ($keteranganpindah->status_kk_tidak_pindah == 'Numpang') {
            $status_kk_tidak_pindah = '1';
        } else if ($keteranganpindah->status_kk_tidak_pindah == 'Membuat KK Baru') {
            $status_kk_tidak_pindah = '2';
        } else if ($keteranganpindah->status_kk_tidak_pindah == 'Nomor KK Tetap') {
            $status_kk_tidak_pindah = '3';
        }
        //
        // status kk bagi yang tidak pindah
        //
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, -18, '4', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(16);
        $pdf->Cell(25, -16, 'Status KK Bagi Tidak Pindah', 0, '', 'L');
        $pdf->Ln(-10);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, $status_kk_tidak_pindah, 1, 0, '');
        $pdf->SetX(80);
        $pdf->Cell(20, 5, '1. Numpang', 0, 0, '');
        $pdf->SetX(110);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, '2. Membuat KK Baru', 0, 0, '');
        $pdf->SetX(160);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, '3. Nomor KK Tetap', 0, 0, '');
        //
        // status_kk_pindah
        //
        $pdf->Ln(8);
        if ($keteranganpindah->status_kk_pindah == 'Numpang') {
            $status_kk_pindah = '1';
        } else if ($keteranganpindah->status_kk_pindah == 'Membuat KK Baru') {
            $status_kk_pindah = '2';
        } else if ($keteranganpindah->status_kk_pindah == 'Nomor KK Tetap') {
            $status_kk_pindah = '3';
        }
        //
        // status kk bagi yang tidak pindah
        //
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, -18, '5', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(16);
        $pdf->Cell(20, -18, 'Status KK Bagi Pindah', 0, '', 'L');
        $pdf->Ln(-11);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, $status_kk_pindah, 1, 0, '');
        $pdf->SetX(80);
        $pdf->Cell(20, 4, '1. Numpang', 0, 0, '');
        $pdf->SetX(110);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 4, '2. Membuat KK Baru', 0, 0, '');
        $pdf->SetX(160);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 4, '3. Nomor KK Tetap', 0, 0, '');
// table keluarga pindah

        $pdf->Ln(13);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, -14, '6', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(16);
        $pdf->Cell(25, -14, 'Keluarga Yang Pindah', 0, '', 'L');
        $pdf->Ln(-4);
        $pdf->Cell(10, 10, 'No.', 1, 0, 'C');
        $pdf->Cell(64, 10, 'NIK', 1, 0, 'C');
        $pdf->Cell(55, 10, 'Nama', 1, 0, 'C');
        $pdf->Cell(35, 5, 'Masa Berlaku', 'TLR', 0, 'C');
        $pdf->Cell(30, 10, 'SHDK', 1, 0, 'C');
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(64);
        $pdf->Cell(55);
        $pdf->Cell(35, 5, 'KTP S/D', 'BLR', 0, 'C');

        $pdf->Ln(5);
        if ($keteranganpindah->nik_pengikut1 == '--') {


            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 10, '--', 1, 0, 'C');
            $pdf->Cell(64, 10, '--', 1, 0, 'C');
            $pdf->Cell(55, 10, '--', 1, 0, 'C');
            $pdf->Cell(35, 10, '--', 1, 0, 'C');
            $pdf->Cell(30, 10, '--', 1, 0, 'C');
            $pdf->Ln(5);
        }
        if ($keteranganpindah->nik_pengikut1 != '--') {


            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 4, ' 1.', 'BLR', 0, 'C');
            //
            // NIK Pemohon
            //
            $totalkatakk = strlen($keteranganpindah->nik_pengikut1);
            $kurangkk = 15;
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keteranganpindah->nik_pengikut1, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 4;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            if ($pribadi1->titel_belakang != '') {
                $pdf->Cell(55, 4, $pribadi1->titel_depan . ' ' . $pribadi1->nama . ', ' . $pribadi1->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi1->titel_belakang == '') {
                $pdf->Cell(55, 4, $pribadi1->titel_depan . ' ' . $pribadi1->nama, 'BLR', 0, 'J');
            }

            $dokumenktp = $this->dokumenpenduduk->cekdokumenktpcetak($pribadi1->id);
            if ($dokumenktp != null) {
                $hariktp = substr($dokumenktp->tanggal_akhir, 0, 2);
                if (substr($dokumenktp->tanggal_akhir, 3, 2) <= 9) {
                    $bulanktp = $indo[substr($dokumenktp->tanggal_akhir, 4, 1)];
                } else {
                    $bulanktp = $indo[substr($dokumenktp->tanggal_akhir, 3, 2)];
                }
                $tahunktp = substr($dokumenktp->tanggal_akhir, 6, 4);

                $pdf->Cell(35, 4, $hariktp . ' ' . $bulanktp . ' ' . $tahunktp, 'BLR', 0, 'C');

            } else {
                $pdf->Cell(35, 4, '--', 'BLR', 0, 'C');
            }

            $pdf->Cell(30, 4, $pribadi1->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Ln(4);
        }
        if ($keteranganpindah->nik_pengikut2 != '--') {

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 4, ' 2.', 'BLR', 0, 'C');
            //
            // NIK Pemohon
            //
            $totalkatakk = strlen($keteranganpindah->nik_pengikut2);
            $kurangkk = 15;
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keteranganpindah->nik_pengikut2, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 4;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            if ($pribadi2->titel_belakang != '') {
                $pdf->Cell(55, 4, $pribadi2->titel_depan . ' ' . $pribadi2->nama . ', ' . $pribadi2->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi2->titel_belakang == '') {
                $pdf->Cell(55, 4, $pribadi2->titel_depan . ' ' . $pribadi2->nama, 'BLR', 0, 'J');
            }
            $dokumenktp2 = $this->dokumenpenduduk->cekdokumenktpcetak($pribadi2->id);
            if ($dokumenktp != null) {
                $hariktp = substr($dokumenktp2->tanggal_akhir, 0, 2);
                if (substr($dokumenktp2->tanggal_akhir, 3, 2) <= 9) {
                    $bulanktp = $indo[substr($dokumenktp2->tanggal_akhir, 4, 1)];
                } else {
                    $bulanktp = $indo[substr($dokumenktp2->tanggal_akhir, 3, 2)];
                }
                $tahunktp = substr($dokumenktp2->tanggal_akhir, 6, 4);

                $pdf->Cell(35, 4, $hariktp . ' ' . $bulanktp . ' ' . $tahunktp, 'BLR', 0, 'C');

            } else {
                $pdf->Cell(35, 4, '--', 'BLR', 0, 'C');
            }
            $pdf->Cell(30, 4, $pribadi2->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Ln(4);

        }
        if ($keteranganpindah->nik_pengikut3 != '--') {

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 4, ' 3.', 'BLR', 0, 'C');
            //
            // NIK Pemohon
            //
            $totalkatakk = strlen($keteranganpindah->nik_pengikut3);
            $kurangkk = 15;
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keteranganpindah->nik_pengikut3, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 4;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            if ($pribadi3->titel_belakang != '') {
                $pdf->Cell(55, 4, $pribadi3->titel_depan . ' ' . $pribadi3->nama . ', ' . $pribadi3->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi3->titel_belakang == '') {
                $pdf->Cell(55, 4, $pribadi3->titel_depan . ' ' . $pribadi3->nama, 'BLR', 0, 'J');
            }
            $dokumenktp3 = $this->dokumenpenduduk->cekdokumenktpcetak($pribadi3->id);
            if ($dokumenktp != null) {
                $hariktp = substr($dokumenktp3->tanggal_akhir, 0, 2);
                if (substr($dokumenktp3->tanggal_akhir, 3, 2) <= 9) {
                    $bulanktp = $indo[substr($dokumenktp3->tanggal_akhir, 4, 1)];
                } else {
                    $bulanktp = $indo[substr($dokumenktp3->tanggal_akhir, 3, 2)];
                }
                $tahunktp = substr($dokumenktp3->tanggal_akhir, 6, 4);

                $pdf->Cell(35, 4, $hariktp . ' ' . $bulanktp . ' ' . $tahunktp, 'BLR', 0, 'C');

            } else {
                $pdf->Cell(35, 4, '--', 'BLR', 0, 'C');
            }
            $pdf->Cell(30, 4, $pribadi3->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Ln(4);
        }
        if ($keteranganpindah->nik_pengikut4 != '--') {

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 4, ' 4.', 'BLR', 0, 'C');
            //
            // NIK Pemohon
            //
            $totalkatakk = strlen($keteranganpindah->nik_pengikut4);
            $kurangkk = 15;
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keteranganpindah->nik_pengikut4, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 4;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            if ($pribadi4->titel_belakang != '') {
                $pdf->Cell(55, 4, $pribadi4->titel_depan . ' ' . $pribadi4->nama . ', ' . $pribadi4->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi4->titel_belakang == '') {
                $pdf->Cell(55, 4, $pribadi4->titel_depan . ' ' . $pribadi4->nama, 'BLR', 0, 'J');
            }
            $dokumenktp4 = $this->dokumenpenduduk->cekdokumenktpcetak($pribadi4->id);
            if ($dokumenktp != null) {
                $hariktp = substr($dokumenktp4->tanggal_akhir, 0, 2);
                if (substr($dokumenktp4->tanggal_akhir, 3, 2) <= 9) {
                    $bulanktp = $indo[substr($dokumenktp4->tanggal_akhir, 4, 1)];
                } else {
                    $bulanktp = $indo[substr($dokumenktp4->tanggal_akhir, 3, 2)];
                }
                $tahunktp = substr($dokumenktp4->tanggal_akhir, 6, 4);

                $pdf->Cell(35, 4, $hariktp . ' ' . $bulanktp . ' ' . $tahunktp, 'BLR', 0, 'C');

            } else {
                $pdf->Cell(35, 4, '--', 'BLR', 0, 'C');
            }
            $pdf->Cell(30, 4, $pribadi4->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Ln(4);
        }
        if ($keteranganpindah->nik_pengikut5 != '--') {

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 4, ' 5.', 'BLR', 0, 'C');
            //
            // NIK Pemohon
            //
            $totalkatakk = strlen($keteranganpindah->nik_pengikut5);
            $kurangkk = 15;
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keteranganpindah->nik_pengikut5, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 4;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            if ($pribadi5->titel_belakang != '') {
                $pdf->Cell(55, 4, $pribadi5->titel_depan . ' ' . $pribadi5->nama . ', ' . $pribadi5->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi5->titel_belakang == '') {
                $pdf->Cell(55, 4, $pribadi5->titel_depan . ' ' . $pribadi5->nama, 'BLR', 0, 'J');
            }
            $dokumenktp5 = $this->dokumenpenduduk->cekdokumenktpcetak($pribadi5->id);
            if ($dokumenktp != null) {
                $hariktp = substr($dokumenktp5->tanggal_akhir, 0, 2);
                if (substr($dokumenktp5->tanggal_akhir, 3, 2) <= 9) {
                    $bulanktp = $indo[substr($dokumenktp5->tanggal_akhir, 4, 1)];
                } else {
                    $bulanktp = $indo[substr($dokumenktp5->tanggal_akhir, 3, 2)];
                }
                $tahunktp = substr($dokumenktp5->tanggal_akhir, 6, 4);

                $pdf->Cell(35, 4, $hariktp . ' ' . $bulanktp . ' ' . $tahunktp, 'BLR', 0, 'C');

            } else {
                $pdf->Cell(35, 4, '--', 'BLR', 0, 'C');
            }
            $pdf->Cell(30, 4, $pribadi5->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Ln(4);
        }
        if ($keteranganpindah->nik_pengikut6 != '--') {

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 4, ' 6.', 'BLR', 0, 'C');
            //
            // NIK Pemohon
            //
            $totalkatakk = strlen($keteranganpindah->nik_pengikut6);
            $kurangkk = 15;
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keteranganpindah->nik_pengikut6, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 4;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            if ($pribadi6->titel_belakang != '') {
                $pdf->Cell(55, 4, $pribadi6->titel_depan . ' ' . $pribadi6->nama . ', ' . $pribadi6->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi6->titel_belakang == '') {
                $pdf->Cell(55, 4, $pribadi6->titel_depan . ' ' . $pribadi6->nama, 'BLR', 0, 'J');
            }
            $dokumenktp6 = $this->dokumenpenduduk->cekdokumenktpcetak($pribadi6->id);
            if ($dokumenktp != null) {
                $hariktp = substr($dokumenktp6->tanggal_akhir, 0, 2);
                if (substr($dokumenktp6->tanggal_akhir, 3, 2) <= 9) {
                    $bulanktp = $indo[substr($dokumenktp6->tanggal_akhir, 4, 1)];
                } else {
                    $bulanktp = $indo[substr($dokumenktp6->tanggal_akhir, 3, 2)];
                }
                $tahunktp = substr($dokumenktp6->tanggal_akhir, 6, 4);

                $pdf->Cell(35, 4, $hariktp . ' ' . $bulanktp . ' ' . $tahunktp, 'BLR', 0, 'C');

            } else {
                $pdf->Cell(35, 4, '--', 'BLR', 0, 'C');
            }
            $pdf->Cell(30, 4, $pribadi6->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Ln(4);
        }
        if ($keteranganpindah->nik_pengikut7 != '--') {

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 4, ' 7.', 'BLR', 0, 'C');
            //
            // NIK Pemohon
            //
            $totalkatakk = strlen($keteranganpindah->nik_pengikut7);
            $kurangkk = 15;
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keteranganpindah->nik_pengikut7, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 4;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            if ($pribadi7->titel_belakang != '') {
                $pdf->Cell(55, 4, $pribadi7->titel_depan . ' ' . $pribadi7->nama . ', ' . $pribadi7->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi7->titel_belakang == '') {
                $pdf->Cell(55, 4, $pribadi7->titel_depan . ' ' . $pribadi7->nama, 'BLR', 0, 'J');
            }
            $dokumenktp7 = $this->dokumenpenduduk->cekdokumenktpcetak($pribadi7->id);
            if ($dokumenktp != null) {
                $hariktp = substr($dokumenktp7->tanggal_akhir, 0, 2);
                if (substr($dokumenktp7->tanggal_akhir, 3, 2) <= 9) {
                    $bulanktp = $indo[substr($dokumenktp7->tanggal_akhir, 4, 1)];
                } else {
                    $bulanktp = $indo[substr($dokumenktp7->tanggal_akhir, 3, 2)];
                }
                $tahunktp = substr($dokumenktp7->tanggal_akhir, 6, 4);

                $pdf->Cell(35, 4, $hariktp . ' ' . $bulanktp . ' ' . $tahunktp, 'BLR', 0, 'C');

            } else {
                $pdf->Cell(35, 4, '--', 'BLR', 0, 'C');
            }
            $pdf->Cell(30, 4, $pribadi7->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Ln(4);
        }
        if ($keteranganpindah->nik_pengikut8 != '--') {

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(10, 4, ' 8.', 'BLR', 0, 'C');
            //
            // NIK Pemohon
            //
            $totalkatakk = strlen($keteranganpindah->nik_pengikut8);
            $kurangkk = 15;
            for ($i = 0; $i <= $kurangkk; $i++) {
                $hasil1 = substr($keteranganpindah->nik_pengikut8, $i, $totalkatakk);
                $tampil1 = substr($hasil1, 0, 1);
                $widd = 4;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil1);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);

            }
            if ($pribadi8->titel_belakang != '') {
                $pdf->Cell(55, 4, $pribadi8->titel_depan . ' ' . $pribadi8->nama . ', ' . $pribadi8->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi8->titel_belakang == '') {
                $pdf->Cell(55, 4, $pribadi8->titel_depan . ' ' . $pribadi8->nama, 'BLR', 0, 'J');
            }
            $dokumenktp8 = $this->dokumenpenduduk->cekdokumenktpcetak($pribadi8->id);
            if ($dokumenktp != null) {
                $hariktp = substr($dokumenktp8->tanggal_akhir, 0, 2);
                if (substr($dokumenktp8->tanggal_akhir, 3, 2) <= 9) {
                    $bulanktp = $indo[substr($dokumenktp8->tanggal_akhir, 4, 1)];
                } else {
                    $bulanktp = $indo[substr($dokumenktp8->tanggal_akhir, 3, 2)];
                }
                $tahunktp = substr($dokumenktp8->tanggal_akhir, 6, 4);

                $pdf->Cell(35, 4, $hariktp . ' ' . $bulanktp . ' ' . $tahunktp, 'BLR', 0, 'C');

            } else {
                $pdf->Cell(35, 4, '--', 'BLR', 0, 'C');
            }
            $pdf->Cell(30, 4, $pribadi8->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Ln(4);
        }
        $pdf->Ln(5);
        $pdf->SetX(30);
        $pdf->SetFont('Arial', '', 10);

        $pdf->SetFont('Arial', 'B', 10);
        if ($keteranganpindah->penandatangan == 'Pimpinan Organisasi' || $keteranganpindah->penandatangan == 'Sekretaris Organisasi') {
            $pdf->Cell(50, 8, 'PEMOHON,', 0, '', 'C');
            $pdf->SetX(30);
            $pdf->Cell(50, 70, $cekpribadi->nama, 0, '', 'C');
        }
        if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan') {
            $pdf->Cell(50, 8, 'PEMOHON,', 0, '', 'C');
            $pdf->SetX(30);
            $pdf->Cell(50, 80, $cekpribadi->nama, 0, '', 'C');
        }
        if ($keteranganpindah->penandatangan == 'Jabatan Struktural') {
            $pdf->Cell(50, 20, 'PEMOHON,', 0, '', 'C');
            $pdf->SetX(30);
            $pdf->Cell(50, 95, $cekpribadi->nama, 0, '', 'C');
        }
        $pdf->SetFont('Arial', '', 10);
//            $pdf->Cell(-50, 70, ' _____________________', 0, '', 'C');
//            $pdf->SetFont('Arial', '', 10);

        $pdf->SetX(120);
        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir1, 0, '', 'C');
        $pdf->Ln(5);
        if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan' || $keteranganpindah->penandatangan == 'Jabatan Struktural') {
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
        if ($keteranganpindah->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($keteranganpindah->jabatan_lainnya);

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
        if ($keteranganpindah->penandatangan != 'Atasnama Pimpinan' && $keteranganpindah->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($keteranganpindah->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
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
            if ($keteranganpindah->penandatangan == 'Pimpinan Organisasi' && $keteranganpindah->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
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
                $pdf->Cell(0, 10, 'NIP. ' . $pejabat->nip, 0, '', 'C');
            }
        }
        if ($keteranganpindah->pejabat_camat_id == 1) {

            if ($desa->kecamatan->kabupaten->status == 1) {
                $statuskecamatan2 = 'CAMAT';
                $kabupaten3 = $desa->kecamatan->kecamatan;
            }
            if ($desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatan2 = 'KEPALA DISTRIK';
                $kabupaten3 = $desa->kecamatan->kecamatan;
            }
            $pdf->Ln(10);
            $pdf->SetX(90);
            $pdf->Cell(50, 8, 'Mengetahui:', 0, '', 'C');
            $pdf->SetX(90);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(50, 16, $statuskecamatan2 . ' ' . strtoupper($kabupaten3) . ',', 0, '', 'C');
            $pdf->SetFont('Arial', 'U', 10);
            $pdf->Cell(-50, 70, '', 0, '', 'C');
            $pdf->SetFont('Arial', '', 10);


        }

        $tanggal = date('d-m-y');


//        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        $organisasi = $this->organisasi->find(session('organisasi'));


        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }
        $pribadi = $keteranganpindah->nik_pemohon;
        $this->Biodata($pdf, $id, $pribadi);


        //
        //if pengikut biodata
        //

        if ($keteranganpindah->nik_pengikut1 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut1;
            $this->Biodata($pdf, $id, $pribadi);
        }
        if ($keteranganpindah->nik_pengikut2 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut2;
            $this->Biodata($pdf, $id, $pribadi);
        }
        if ($keteranganpindah->nik_pengikut3 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut3;
            $this->Biodata($pdf, $id, $pribadi);
        }
        if ($keteranganpindah->nik_pengikut4 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut4;
            $this->Biodata($pdf, $id, $pribadi);
        }
        if ($keteranganpindah->nik_pengikut5 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut5;
            $this->Biodata($pdf, $id, $pribadi);
        }
        if ($keteranganpindah->nik_pengikut6 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut6;
            $this->Biodata($pdf, $id, $pribadi);
        }
        if ($keteranganpindah->nik_pengikut7 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut7;
            $this->Biodata($pdf, $id, $pribadi);
        }
        if ($keteranganpindah->nik_pengikut8 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut8;
            $this->Biodata($pdf, $id, $pribadi);
        }

        $pdf->Output('cetak-data-keteraganpindah-' . $tanggal . '.pdf', 'I');
        exit;
    }

    public function Biodata($pdf, $id, $pribadi)
    {

        //
        // BIODATA PENDUDUK
        //

        $pdf->AddFont('Arial', '', 'arial.php');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->SetX(180);
        $pdf->Cell(25, 6, 'F-1.07', 1, 0, 'C');
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'No. KK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, 0, ':     ', 0, '', 'L');
        $keteranganpindah = $this->keteraganpindah->find($id);
//        dump($pdf);
        if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
        }
        $cekkkkeluarga = $this->keluarga->ceknikkeluarga($pribadi);
        $pribadi1 = $this->pribadi->ceknikcetak($pribadi);

        $pdf->Cell(120, 0, $cekkkkeluarga->nomor_kk, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->Image('images/bg/Pancasila.png', 100, 35, 20, 25);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, 0, ':     ', 0, '', 'L');
        $pdf->Cell(120, 0, $pribadi, 0, '', 'L');
        $pdf->Ln(45);
        $pdf->SetFont('Arial', 'BU', 12);
        $pdf->Cell(0, 0, 'BIODATA PENDUDUK WARGA NEGARA INDONESIA', 5, '', 'C');
        $pdf->Ln(5);
        $pdf->SetFont('arial', '', 10);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();
        $jeniskodeadministrasi = $this->keteraganpindah->cekkodejenisadministrasi($keteranganpindah->jenis_pelayanan_id);

        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($keteranganpindah->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($keteranganpindah->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($keteranganpindah->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $keteranganpindah->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $keteranganpindah->tahun, 0, '', 'C');


        $pdf->Ln(18);
        $pdf->SetX(14);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(25, 0, 'DATA PERSONAL', 5, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        //
        // Nama Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '1.     Nama Lengkap', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        if ($pribadi1->titel_belakang != '') {
            if ($pribadi1->titel_depan != '') {
                $pdf->Cell(0, 0, $pribadi1->titel_depan . ' ' . $pribadi1->nama . ', ' . $pribadi1->titel_belakang, 0, 0, '');
            }
            if ($pribadi1->titel_depan == '') {
                $pdf->Cell(0, 0, $pribadi1->nama . ', ' . $pribadi1->titel_belakang, 0, 0, '');
            }
        }
        if ($pribadi1->titel_belakang == '') {
            if ($pribadi1->titel_depan != '') {
                $pdf->Cell(0, 0, $pribadi1->titel_depan . ' ' . $pribadi1->nama, 0, 0, '');

            }
            if ($pribadi1->titel_depan == '') {
                $pdf->Cell(0, 0, $pribadi1->nama, 0, 0, '');

            }
        }
        //
        // Tempat Lahir pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.     Tempat Lahir', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $pdf->Cell(0, 0, $pribadi1->tempat_lahir, 0, 0, '');
        //
        // Tanggal Lahir pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.     Tanggal Lahir', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $hari = substr($pribadi1->tanggal_lahir, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($pribadi1->tanggal_lahir, 3, 2) <= 9) {
            $bulan = $indo[substr($pribadi1->tanggal_lahir, 4, 1)];
        } else {
            $bulan = $indo[substr($pribadi1->tanggal_lahir, 3, 2)];
        }
        $tahun = substr($pribadi1->tanggal_lahir, 6, 4);
        $tempatlahir = $pribadi1->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
        $tanggallahir = $hari . ' ' . $bulan . ' ' . $tahun;
        $hari1 = substr($keteranganpindah->tanggal, 0, 2);
        if (substr($keteranganpindah->tanggal, 3, 2) <= 9) {
            $bulan1 = $indo[substr($keteranganpindah->tanggal, 4, 1)];
        } else {
            $bulan1 = $indo[substr($keteranganpindah->tanggal, 3, 2)];
        }
        $tahun1 = substr($keteranganpindah->tanggal, 6, 4);
        $tempatlahir1 = $hari1 . ' ' . $bulan1 . ' ' . $tahun1;
        $pdf->Cell(0, 0, $tanggallahir, 0, 0, '');

        //
        // Jenis Kelamin pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.     Jenis Kelamin', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $pdf->Cell(0, 0, $pribadi1->jk->jk, 0, 0, '');
        //
        // Golongan Darah pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.     Golongan Darah', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        if ($pribadi1->gol_darah_id != 13) {
            $golongandarahpemohon = $pribadi1->golongan_darah->golongan_darah;
        }
        if ($pribadi1->gol_darah_id == 13) {
            $golongandarahpemohon = '--';
        }
        $pdf->Cell(0, 0, $golongandarahpemohon, 0, 0, '');
        //
        // Agama pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.     Agama', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $pdf->Cell(0, 0, $pribadi1->agama->agama, 0, 0, '');
        //
        // Pendidikan pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '7.     Pendidikan Terakhir', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $pdf->Cell(0, 0, $pribadi1->pendidikan->pendidikan, 0, 0, '');
        //
        // Pekerjaan pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '8.     Pekerjaan', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        if ($pribadi1->pekerjaan_id == 89) {
            $pekerjaan = $pribadi1->pekerjaan_lain->pekerjaan_lain;
        } else {
            $pekerjaan = $pribadi1->pekerjaan->pekerjaan;
        }
        $pdf->Cell(0, 0, $pekerjaan, 0, 0, '');
        //
        // Penyandang Cacad pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '9.     Penyandang Cacat', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $disabilitasview = $this->disabilitas->ceknikdisabilitas($keteranganpindah->nik_pemohon);
        if ($disabilitasview == null) {
            $disabilitas = '--';
        }
        if ($disabilitasview != null) {
            $disabilitas = $disabilitasview->disabilitas->disabilitas;
        }
        $pdf->Cell(0, 0, $disabilitas, 0, 0, '');
        //
        // Status Perkawinan pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '10.     Status Perkawinan', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $pdf->Cell(0, 0, $pribadi1->perkawinan->kawin, 0, 0, '');
        //
        // Shdrt pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '11.     Status Hubungan Dalam Keluarga', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $pdf->Cell(0, 0, $pribadi1->shdrt->shdrt, 0, 0, '');
        //
        // Nik Ibu pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '12.     NIK Ibu', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $nikibu = $this->orangtua->cekorangtuaibu($pribadi1->id);
        if ($nikibu->nik != '') {
            $pdf->Cell(0, 0, $nikibu->nik, 0, 0, '');
        }
        if ($nikibu->nik == '') {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        //
        // Nama Ibu pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '13.     Nama Ibu', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(84);
        if ($nikibu->titel_belakang != '') {
            $pdf->Cell(0, 0, $nikibu->titel_depan . ' ' . $nikibu->nama . ', ' . $nikibu->titel_belakang, 0, 0, '');
        }
        if ($nikibu->titel_belakang == '') {
            $pdf->Cell(0, 0, $nikibu->titel_depan . ' ' . $nikibu->nama, 0, 0, '');
        }
        //
        // Nik Bapak pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '14.     NIK Bapak', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $nikbapak = $this->orangtua->cekorangtuabapak($pribadi1->id);
        if ($nikbapak->nik != '') {
            $pdf->Cell(0, 0, $nikbapak->nik, 0, 0, '');
        }
        if ($nikbapak->nik == '') {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        //
        // Nama Bapak pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '15.     Nama Bapak', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(84);
        if ($nikbapak->titel_belakang != '') {
            $pdf->Cell(0, 0, $nikbapak->titel_depan . ' ' . $nikbapak->nama . ', ' . $nikbapak->titel_belakang, 0, 0, '');
        }
        if ($nikbapak->titel_belakang == '') {
            $pdf->Cell(0, 0, $nikbapak->titel_depan . ' ' . $nikbapak->nama, 0, 0, '');
        }
        //
        // Alamat Sebelumnya pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '16.     Alamat Sebelumnya', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $pdf->Cell(0, 0, '--', 0, 0, '');
        //
        // Alamat Sebelumnya pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '17.     Alamat Sekarang', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $alamattujuan = $keteranganpindah->alamat_tujuan . ' RT. ' . $keteranganpindah->rt_tujuan . ' RW. ' . $keteranganpindah->rw_tujuan;
        $pdf->Cell(0, 0, $alamattujuan, 0, 0, '');

        //
        // Data Kepemilikan Dokumen

        $pdf->Ln(10);
        $pdf->SetX(14);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(25, 0, 'DATA KEPEMILIKAN DOKUMEN', 5, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        //
        // nomor kk Pemohon
        //
        $pdf->Ln(8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '18.     Nomor Kartu Keluarga (No. KK)', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $pdf->Cell(0, 0, $cekkkkeluarga->nomor_kk, 0, 0, '');
        //
        // Nomor Paspor Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '19.     Nomor Pasport', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $passport = $this->dokumenpenduduk->cekdokumenpassport($pribadi1->id);
        if ($passport == null) {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        if ($passport != null) {
            $pdf->Cell(0, 0, $passport->nomor_dokumen, 0, 0, '');
        }
        //
        // Tanggal berakhir  Paspor Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '20.     Tanggal Berakhir Pasport', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        if ($passport == null) {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        if ($passport != null) {
            $haripassort = substr($passport->tanggal_akhir, 0, 2);
            if (substr($pribadi1->tanggal_lahir, 3, 2) <= 9) {
                $bulanpassort = $indo[substr($passport->tanggal_akhir, 4, 1)];
            } else {
                $bulanpassort = $indo[substr($passport->tanggal_akhir, 3, 2)];
            }
            $tahunpassort = substr($passport->tanggal_akhir, 6, 4);
            $pdf->Cell(0, 0, $haripassort . ' ' . $bulanpassort . ' ' . $tahunpassort, 0, 0, '');
        }
        //
        // Nomor akta Kelahiran Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '21.     Nomor Akta/Surat Kenal Lahir', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $aktekelahiran = $this->dokumenpenduduk->cekdokumenaktekelahiran($pribadi1->id);
        if ($aktekelahiran == null) {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        if ($aktekelahiran != null) {
            $pdf->Cell(0, 0, $aktekelahiran->nomor_dokumen, 0, 0, '');
        }
        //
        // Nomor akta Perkawinan Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '22.     No. Akta Perkawinan/Buku Nikah', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $nomorbukunikah = $this->dokumenpenduduk->cekdokumenbukunikahcetak($pribadi1->id);
        if ($nomorbukunikah == null) {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        if ($nomorbukunikah != null) {
            $pdf->Cell(0, 0, $nomorbukunikah->nomor_dokumen, 0, 0, '');
        }
        //
        // Tanggal akta Perkawinan Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '23.     Tanggal Perkawinan', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        if ($nomorbukunikah == null) {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        if ($nomorbukunikah != null) {
            $haribukunikah = substr($nomorbukunikah->tanggal, 0, 2);
            if (substr($pribadi1->tanggal_lahir, 3, 2) <= 9) {
                $bulanbukunikah = $indo[substr($nomorbukunikah->tanggal, 4, 1)];
            } else {
                $bulanbukunikah = $indo[substr($nomorbukunikah->tanggal, 3, 2)];
            }
            $tahunbukunikah = substr($nomorbukunikah->tanggal, 6, 4);

            $pdf->Cell(0, 0, $haribukunikah . ' ' . $bulanbukunikah . ' ' . $tahunbukunikah, 0, 0, '');
        }
        //
        // Nomor akta Cerai Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '24.     No. Akta Perceraian/Surat Cerai', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        $nomoraktecerai = $this->dokumenpenduduk->cekdokumenaktecerai($pribadi1->id);
        if ($nomoraktecerai == null) {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        if ($nomoraktecerai != null) {

            $pdf->Cell(0, 0, $nomoraktecerai->nomor_dokumen, 0, 0, '');
        }
        //
        // Nomor akta Cerai Pemohon
        //
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '25.     Tanggal Perceraian', 5, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(0, 0, ':', 0, '', '');
        $pdf->SetX(85);
        if ($nomoraktecerai == null) {
            $pdf->Cell(0, 0, '--', 0, 0, '');
        }
        if ($nomoraktecerai != null) {

            $hariaktecerai = substr($nomoraktecerai->tanggal, 0, 2);
            if (substr($pribadi1->tanggal_lahir, 3, 2) <= 9) {
                $bulanaktecerai = $indo[substr($nomoraktecerai->tanggal, 4, 1)];
            } else {
                $bulanaktecerai = $indo[substr($nomoraktecerai->tanggal, 3, 2)];
            }
            $tahunaktecerai = substr($nomoraktecerai->tanggal, 6, 4);

            $pdf->Cell(0, 0, $hariaktecerai . ' ' . $bulanaktecerai . ' ' . $tahunaktecerai, 0, 0, '');
        }
        $pdf->Ln(5);
        $desa = $this->desa->find(session('desa'));

        if ($keteranganpindah->pejabat_camat_id == 1) {
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
            $pdf->SetFont('Arial', '', 10);
//            $pdf->Cell(-50, 70, ' _____________________', 0, '', 'C');
//            $pdf->SetFont('Arial', '', 10);


        }

        $pdf->SetX(120);
        $pdf->Cell(0, 10, $desa->desa . ', ' . $tempatlahir1, 0, '', 'C');
        $pdf->Ln(5);
        if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan' || $keteranganpindah->penandatangan == 'Jabatan Struktural') {
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
                $pdf->Cell(0, 10, $an . ' ' . $keteranganjabatanpimpinan . strtoupper($pejabatpimpinan->jabatan) . ' ' . strtoupper($desa->desa) . ',', 0, '', 'C');

            } else {
                $pdf->Cell(0, 10, $an . ' ' . strtoupper($desa->desa), 0, '', 'C');

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
        if ($keteranganpindah->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($keteranganpindah->jabatan_lainnya);

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
        if ($keteranganpindah->penandatangan != 'Atasnama Pimpinan' && $keteranganpindah->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($keteranganpindah->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
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
            if ($keteranganpindah->penandatangan == 'Pimpinan Organisasi' && $keteranganpindah->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
                if ($pejabatsekretaris != null) {
                    if ($pejabatsekretaris->keterangan != '') {
                        $keteranganjabatansekretaris3 = $pejabatsekretaris->keterangan . ' ';
                    }
                    if ($pejabatsekretaris->keterangan == '') {
                        $keteranganjabatansekretaris3 = '';
                    }

                    $pdf->Cell(0, 10, $keteranganjabatansekretaris3 . strtoupper($pejabatsekretaris->jabatan . ' ' . $desa->desa . ','), 0, '', 'C');
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
                $pdf->Cell(0, 10, 'NIP. ' . $pejabat->nip, 0, '', 'C');
            }
        }

        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }
//        exit;

    }
}