<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DataPribadi\KeluargaRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\KtpRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakKtpSiak extends Controller
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
        KtpRepository $ktpRepository,
        PribadiRepository $pribadiRepository,
        NonPendudukRepository $nonPendudukRepository,
        PejabatRepository $pejabatRepository,
        LogoRepository $logoRepository,
        AlamatRepository $alamatRepository,
        DesaRepository $desaRepository,
        KodeAdministrasiRepository $kodeAdministrasiRepository,
        KeluargaRepository $keluargaRepository,
        OrganisasiRepository $organisasiRepository
    )
    {
        $this->ktp = $ktpRepository;
        $this->pribadi = $pribadiRepository;
        $this->nonpenduduk = $nonPendudukRepository;
        $this->pejabat = $pejabatRepository;
        $this->logo = $logoRepository;
        $this->alamat = $alamatRepository;
        $this->desa = $desaRepository;
        $this->kodeadministrasi = $kodeAdministrasiRepository;
        $this->keluarga = $keluargaRepository;
        $this->organisasi = $organisasiRepository;
        $this->middleware('auth');

    }

    function Headers($pdf)
    {
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        //Put the watermark
        $pdf->SetFont('Arial', 'B', 50);
        $pdf->SetTextColor(128);
        $pdf->RotatedText(35, 130, 'Versi Ujicoba', 24);
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
        $pdf->SetFont('Times-Roman', 'B', 9);
        $desa = $this->desa->find(session('desa'));
        $ktp = $this->ktp->find($id);
        $jeniskodeadministrasi = $this->ktp->cekkodejenisadministrasi($ktp->jenis_pelayanan_id);
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
        $pdf->SetFont('Times-Roman', 'B', 9);
        $pdf->SetX(180);
        $pdf->Cell(20, 6, 'F-1.21', 1, 0, 'C');
        $pdf->ln(6);
        $pdf->SetFont('Times-Roman', 'B', 9);
        $pdf->Cell(0, 0, 'FORMULIR PERMOHONAN KARTU TANDA PENDUDUK (KTP) WARGA NEGARA INDONESIA', 0, 0, 'C');
        $pdf->ln(2);
        $pdf->Cell(190, 16, '', 1, 0, 'C');
        $pdf->SetFont('Times-Roman', 'B', 9);
        $pdf->SetX(15);
        $pdf->Cell(15, 5, 'Perhatian:', 0, 0, '');
        $pdf->SetX(15);
        $pdf->SetFont('Times-Roman', '', 8);
        $pdf->Cell(15, 12, '1.   Harap diisi dengan huruf cetak dan menggunakan tinta hitam', 0, 0, '');
        $pdf->SetX(15);
        $pdf->Cell(15, 19, '2.   Untuk kolom pilihan, harap memberi tanda silang (X) pada kotak pilihan.', 0, 0, '');
        $pdf->SetX(15);
        $pdf->Cell(15, 26, '3.   Setelah formulir ini diisi dan ditandatangani, harap diserahkan kembali ke Kantor Desa/Kelurahan', 0, 0, '');

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

    public function KtpSiak($id)
    {
//        array(215, 330)

        $pdf = new PdfClass('L', 'mm', 'A5');
        $pdf->is_header = false;
        $pdf->set_widths = 80;
        $pdf->set_footer = 29;
        $pdf->orientasi = 'p';
        $pdf->AddFont('Arial', '', 'arial.php');

        //Disable automatic page break
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 5, 10);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('F-1.21 - SIAK');
        $this->Kop($pdf, $id);
        $pdf->SetY(67);
        $desa = $this->desa->find(session('desa'));
        $ktp = $this->ktp->find($id);
        $jeniskodeadministrasi = $this->ktp->cekkodejenisadministrasi($ktp->jenis_pelayanan_id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();

        if ($ktp->pejabat_desa_id == 1) {
            $keluarga = $this->keluarga->cekalamat($ktp->pribadi->id);
        }
        $kodeadministrasi = $this->kodeadministrasi->cekkodeadminbysession();

        if ($ktp->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($ktp->penandatangan);
        }
        if ($desa->kecamatan->kabupaten->status == 1) {
            $status = 'Kabupaten';
            $kabupaten = $desa->kecamatan->kabupaten->kabupaten;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $status = 'Kota';
            $kabupaten = $desa->kecamatan->kabupaten->kabupaten;
        }
        //kecamatan
        if ($desa->kecamatan->status == 1) {
            $statuskecamatan = 'Kecamatan';
            $statuskecamatan2 = 'camat';
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $statuskecamatan = 'Distrik';
            $statuskecamatan2 = 'Kepala Distrik';
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        //desa
        if ($desa->status == 1) {
            $statusdesa = 'Kelurahan';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 2) {
            $statusdesa = 'Desa';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 3) {
            $statusdesa = 'Kampung';
            $namadesa = $desa->desa;
        }
        if ($desa->status == 4) {
            $statusdesa = 'Negeri';
            $namadesa = $desa->desa;
        }
        $pdf->Ln(-25);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(35, -20, 'PEMERINTAH PROVINSI', 0, '', 'L');
        $pdf->Ln(-12);
        $pdf->SetX(70);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(3.5, 3.5, substr($desa->kecamatan->kabupaten->provinsi->kode_prov, 0, 1), 1, 0, '');
        $pdf->Cell(3.5, 3.5, substr($desa->kecamatan->kabupaten->provinsi->kode_prov, 1, 1), 1, 0, '');
        $pdf->SetX(82);
        $pdf->Cell(118, 3.5, 'PROVINSI ' . strtoupper($desa->kecamatan->kabupaten->provinsi->provinsi), 1, 0, '');
        $pdf->Ln(13);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -14, 'PEMERINTAH KABUPATEN / KOTA', 0, '', 'L');
        $pdf->SetX(57);
        $pdf->Cell(25, -14, '', 0, '', 'L');

        $pdf->Ln(-9);
        $pdf->SetX(70);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(3.5, 3.5, substr($desa->kecamatan->kabupaten->kode_kab, 0, 1), 1, 0, '');
        $pdf->Cell(3.5, 3.5, substr($desa->kecamatan->kabupaten->kode_kab, 1, 1), 1, 0, '');
        $pdf->SetX(82);
        $pdf->Cell(118, 3.5, strtoupper($status . ' ' . $desa->kecamatan->kabupaten->kabupaten), 1, 0, '');
        $pdf->Ln(13);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -14, 'KECAMATAN', 0, '', 'L');
        $pdf->Ln(-9);
        $pdf->SetX(70);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(3.5, 3.5, substr($desa->kecamatan->kode_kec, 0, 1), 1, 0, '');
        $pdf->Cell(3.5, 3.5, substr($desa->kecamatan->kode_kec, 1, 1), 1, 0, '');
        $pdf->SetX(82);
        $pdf->Cell(118, 3.5, strtoupper($statuskecamatan . ' ' . $desa->kecamatan->kecamatan), 1, 0, '');
        $pdf->Ln(13);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -14, 'KELURAHAN/DESA', 0, '', 'L');
        $pdf->SetX(57);
        $pdf->Cell(118, -14, '', 0, '', 'L');

        $pdf->Ln(-9);
        $pdf->SetX(63);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(3.5, 3.5, substr($desa->kode_desa, 0, 1), 1, 0, '');
        $pdf->Cell(3.5, 3.5, substr($desa->kode_desa, 1, 1), 1, 0, '');
        $pdf->Cell(3.5, 3.5, substr($desa->kode_desa, 2, 1), 1, 0, '');
        $pdf->Cell(3.5, 3.5, substr($desa->kode_desa, 3, 1), 1, 0, '');
        $pdf->SetX(82);
        $pdf->Cell(118, 4, strtoupper($statusdesa . ' ' . $desa->desa), 1, 0, '');

        if ($ktp->is_jenis_ktp == 'Baru') {
            $baru = 'X';
            $perpanjangan = '';
            $pergantian = '';
        }
        if ($ktp->is_jenis_ktp == 'Perpanjangan') {
            $baru = '';
            $perpanjangan = 'X';
            $pergantian = '';
        }
        if ($ktp->is_jenis_ktp == 'Pergantian') {
            $baru = '';
            $perpanjangan = '';
            $pergantian = 'X';
        }
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'BU', 8);
        $pdf->Cell(25, -14, 'PERMOHONAN KTP', 0, '', 'L');
        $pdf->Ln(-10);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, $baru, 1, 0, '');
        $pdf->Cell(15, 4, 'A. Baru', 1, 0, '');
        $pdf->SetX(82);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, $perpanjangan, 1, 0, '');
        $pdf->Cell(24, 4, 'B. Perpanjangan', 1, 0, '');
        $pdf->SetX(113);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, 4, $pergantian, 1, 0, '');
        $pdf->Cell(24, 4, 'C. Pergantian', 1, 0, '');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50, 4, '1. Nama Lengkap', 1, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 8);
        if ($ktp->pejabat_desa_id == 1) {
            $totalkatanama = strlen($ktp->pribadi->nama);
            $namalengkap = $ktp->pribadi->nama;
        }
        if ($ktp->pejabat_desa_id == 2) {
            $totalkatanama = strlen($ktp->non_penduduk->nama);
            $namalengkap = $ktp->non_penduduk->nama;
        }

        $kurangnama = 25;
        $pdf->SetX(70);

        for ($i = 0; $i <= $kurangnama; $i++) {
            $hasil = substr($namalengkap, $i, $totalkatanama);
            $tampil = substr($hasil, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 8);
            $widths = array($widd);
            $caption = array($tampil);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50, 4, '2. Nomor Kartu Keluarga', 1, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 8);
        if ($ktp->pejabat_desa_id == 1) {

            $totalkatakk = strlen($keluarga->nomor_kk);
            $nomor_kk = $keluarga->nomor_kk;
        }
        if ($ktp->pejabat_desa_id == 2) {

            $totalkatakk = '0';
            $nomor_kk = "0";
        }
        $kurangkk = 15;
        $pdf->Ln(0);
        $pdf->SetX(70);

        for ($i = 0; $i <= $kurangkk; $i++) {
            $hasil1 = substr($nomor_kk, $i, $totalkatakk);
            $tampil1 = substr($hasil1, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 8);
            $widths = array($widd);
            $caption = array($tampil1);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50, 4, '3. Nomor Induk Kependudukan', 1, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 8);
        $totalkatanik = strlen($ktp->nik);

        $kurangnik = 15;
        $pdf->Ln(0);
        $pdf->SetX(70);

        for ($i = 0; $i <= $kurangnik; $i++) {
            $hasil2 = substr($ktp->nik, $i, $totalkatanik);
            $tampil2 = substr($hasil2, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 8);
            $widths = array($widd);
            $caption = array($tampil2);
            $pdf->SetWidths($widths);
            $pdf->FancyRow2($caption);

        }
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50, 4, '4. Alamat', 1, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(70);
        if ($ktp->pejabat_desa_id == 1) {
            $lokasialamat = $keluarga->alamat;
        }
        if ($ktp->pejabat_desa_id == 2) {
            $lokasialamat = $ktp->non_penduduk->alamat;
        }
        $pdf->Cell(130, 4, $lokasialamat, 1, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(70);
        $pdf->Cell(130, 4, strtoupper($statusdesa . ' ' . $namadesa . ' ' . $statuskecamatan . ' ' . $kecamatan . ' ' . $status . ' ' . $kabupaten), 1, 0, '');
        $pdf->Ln(5);
        $pdf->Cell(60);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(10, 4, 'RT.', 0, '', '');
        $pdf->Cell(5);
        if ($ktp->pejabat_desa_id == 1) {

            if (strlen($keluarga->alamat_rt) == 1) {
                $rt = substr($keluarga->alamat_rt, 0, 1);
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, $rt, 1, '', 'L');
                $pdf->Cell(5);
            }
            if (strlen($keluarga->alamat_rt) == 2) {
                $rt = substr($keluarga->alamat_rt, 0, 1);
                $rt2 = substr($keluarga->alamat_rt, 1, 1);
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, $rt, 1, '', 'L');
                $pdf->Cell(5, 4, $rt2, 1, '', 'L');
                $pdf->Cell(5);
            }
            if (strlen($keluarga->alamat_rt) == 3) {
                $rt = substr($keluarga->alamat_rt, 0, 1);
                $rt2 = substr($keluarga->alamat_rt, 1, 1);
                $rt3 = substr($keluarga->alamat_rt, 2, 1);
                $pdf->Cell(5, 4, $rt, 1, '', 'L');
                $pdf->Cell(5, 4, $rt2, 1, '', 'L');
                $pdf->Cell(5, 4, $rt3, 1, '', 'L');
                $pdf->Cell(5);
            }
            $pdf->Cell(25, 4, 'RW.', 0, '', 'L');
            $pdf->SetX(120);
//            $pdf->Cell(5);
            if (strlen($keluarga->alamat_rw) == 1) {
                $rw = substr($keluarga->alamat_rw, 0, 1);
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, $rw, 1, '', 'L');
                $pdf->Cell(5);
            }
            if (strlen($keluarga->alamat_rw) == 2) {
                $rw = substr($keluarga->alamat_rw, 0, 1);
                $rw2 = substr($keluarga->alamat_rw, 1, 1);
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, $rw, 1, '', 'L');
                $pdf->Cell(5, 4, $rw2, 1, '', 'L');
                $pdf->Cell(5);
            }
            if (strlen($keluarga->alamat_rw) == 3) {
                $rw = substr($keluarga->alamat_rw, 0, 1);
                $rw2 = substr($keluarga->alamat_rw, 1, 1);
                $rw3 = substr($keluarga->alamat_rw, 2, 1);
                $pdf->Cell(5, 4, $rw, 1, '', 'L');
                $pdf->Cell(5, 4, $rw2, 1, '', 'L');
                $pdf->Cell(5, 4, $rw3, 1, '', 'L');
                $pdf->Cell(5);
            }
        }
        if ($ktp->pejabat_desa_id == 2) {

            if (strlen($ktp->non_penduduk->alamat_rt) == 1) {
                $rt = substr($ktp->non_penduduk->alamat_rt, 0, 1);
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, $rt, 1, '', 'L');
                $pdf->Cell(5);
            }
            if (strlen($ktp->non_penduduk->alamat_rt) == 2) {
                $rt = substr($ktp->non_penduduk->alamat_rt, 0, 1);
                $rt2 = substr($ktp->non_penduduk->alamat_rt, 1, 1);
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, $rt, 1, '', 'L');
                $pdf->Cell(5, 4, $rt2, 1, '', 'L');
                $pdf->Cell(5);
            }
            if (strlen($ktp->non_penduduk->alamat_rt) == 3) {
                $rt = substr($ktp->non_penduduk->alamat_rt, 0, 1);
                $rt2 = substr($ktp->non_penduduk->alamat_rt, 1, 1);
                $rt3 = substr($ktp->non_penduduk->alamat_rt, 2, 1);
                $pdf->Cell(5, 4, $rt, 1, '', 'L');
                $pdf->Cell(5, 4, $rt2, 1, '', 'L');
                $pdf->Cell(5, 4, $rt3, 1, '', 'L');
                $pdf->Cell(5);
            }
            $pdf->Cell(5, 4, 'RW.', 0, '', 'L');
            $pdf->SetX(120);
            if (strlen($ktp->non_penduduk->alamat_rw) == 1) {
                $rw = substr($ktp->non_penduduk->alamat_rw, 0, 1);
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, $rw, 1, '', 'L');
                $pdf->Cell(5);
            }
            if (strlen($ktp->non_penduduk->alamat_rw) == 2) {
                $rw = substr($ktp->non_penduduk->alamat_rw, 0, 1);
                $rw2 = substr($ktp->non_penduduk->alamat_rw, 1, 1);
                $pdf->Cell(5, 4, 0, 1, '', 'L');
                $pdf->Cell(5, 4, $rw, 1, '', 'L');
                $pdf->Cell(5, 4, $rw2, 1, '', 'L');
                $pdf->Cell(5);
            }
            if (strlen($ktp->non_penduduk->alamat_rw) == 3) {
                $rw = substr($ktp->non_penduduk->alamat_rw, 0, 1);
                $rw2 = substr($ktp->non_penduduk->alamat_rw, 1, 1);
                $rw3 = substr($ktp->non_penduduk->alamat_rw, 2, 1);
                $pdf->Cell(5, 4, $rw, 1, '', 'L');
                $pdf->Cell(5, 4, $rw2, 1, '', 'L');
                $pdf->Cell(5, 4, $rw3, 1, '', 'L');
                $pdf->Cell(5);
            }
        }

        $pdf->SetX(160);
        $pdf->Cell(20, 4, 'Kodepos', 0, '', 'L');
        $pdf->Cell(5);
        if ($kodeadministrasi != null) {

            $totalkatakodepos = strlen($kodeadministrasi->kode);

            $kurangkodepos = 4;
            $pdf->Ln(0);
            $pdf->SetX(175);

            for ($i = 0; $i <= $kurangkodepos; $i++) {
                $hasil6 = substr($kodeadministrasi->kode, $i, $totalkatakodepos);
                $tampil6 = substr($hasil6, 0, 1);
                $widd = 5;
                $pdf->SetFont('Arial', '', 8);
                $widths = array($widd);
                $caption = array($tampil6);
                $pdf->SetWidths($widths);
                $pdf->FancyRow2($caption);
            }
        } else {
            $pdf->Cell(5, 5, '', 1, '', 'L');
            $pdf->Cell(5, 5, '', 1, '', 'L');
            $pdf->Cell(5, 5, '', 1, '', 'L');
            $pdf->Cell(5, 5, '', 1, '', 'L');
            $pdf->Cell(5, 5, '', 1, '', 'L');

        }
        $pdf->Ln(6);

        $pdf->Cell(20, 30, '', 1, 0, 'C');
        $pdf->Cell(20, 30, '', 1, 0, 'C');
        $pdf->Cell(60, 27, '', 1, 0, 'C');
        $pdf->SetFont('Arial', '', 6);
        $pdf->Ln(0);
        $pdf->Cell(100, 5, 'Pas Photo (2 X 3)          Cap Jempol                           Specimen Tanda Tangan', 1, 0, '');
        $pdf->SetX(46);
        $pdf->Cell(10, 30, 'Atau ->', 0, 1, '');
        $pdf->Ln(-16);
        $pdf->SetX(50);
        $pdf->Cell(10, 30, 'Ket:  Cap Jempol/Tanda Tangan', 0, 0, '');
        $tanggal = date('d-m-y');
        $hari = substr($ktp->tanggal, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($ktp->tanggal, 3, 2) <= 9) {
            $bulan = $indo[substr($ktp->tanggal, 4, 1)];
        } else {
            $bulan = $indo[substr($ktp->tanggal, 3, 2)];
        }
        $tahun = substr($ktp->tanggal, 6, 4);

        $tanggalcetak = $hari . ' ' . $bulan . ' ' . $tahun;
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Ln(-68);
        $pdf->SetX(160);
        $pdf->Cell(10, 70, $namadesa . ', ' . $tanggalcetak, 0, '', 'C');
        $pdf->Ln(5);
        $pdf->SetX(160);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(10, 70, 'PEMOHON,', 0, '', 'C');
        $pdf->Ln(17);
        if ($ktp->pejabat_desa_id == 1) {
            $namapemoohon = $ktp->pribadi->nama;
        }
        if ($ktp->pejabat_desa_id == 2) {
            $namapemoohon = $ktp->non_penduduk->nama;
        }
        $pdf->SetX(160);
        $pdf->SetFont('Arial', 'BU', 9);
        $pdf->Cell(10, 70, '' . $namapemoohon . '', 0, '', 'C');
        $pdf->SetFont('Arial', '', 9);

        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($ktp->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($ktp->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($ktp->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }

        if ($ktp->pejabat_kecamatan_id == 1) {
            $pdf->SetX(145);
            $pdf->Cell(12, 85, ' Nomor: ' . $jeniskodeadministrasi . '/' . $ktp->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $ktp->tahun, 0, '', 'C');
            $pdf->Ln(-1);
            $pdf->SetX(42);
            $pdf->Cell(112, 90, 'Mengetahui,', 0, 0, '');
        }
        if ($ktp->pejabat_kecamatan_id == 0) {
            $pdf->SetX(145);
            $pdf->Cell(12, 87, ' Nomor: ' . $jeniskodeadministrasi . '/' . $ktp->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $ktp->tahun, 0, '', 'C');
            $pdf->Ln(-1);
            $pdf->SetX(145);
            $pdf->Cell(148, 82, 'Mengetahui,', 0, 0, '');
        }
        $pdf->Ln(43);
        if ($ktp->pejabat_kecamatan_id == 1) {
            $pdf->SetX(25);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(50, 10, strtoupper($statuskecamatan2) . ' ' . strtoupper($kecamatan) . ',', 0, '', 'C');
        }
        $pdf->SetX(110);
        if ($ktp->penandatangan == 'Atasnama Pimpinan' || $ktp->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetX(110);
            if ($pejabatpimpinan != null) {
                $pdf->Cell(0, 10, $an . ' ' . strtoupper($pejabatpimpinan->jabatan) . ' ' . strtoupper($namadesa) . ',', 0, '', 'C');

            } else {
                $pdf->Cell(0, 10, $an . ' ' . strtoupper($namadesa), 0, '', 'C');

            }
            $pdf->Ln(3);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetX(110);
            if ($pejabat != null) {
                $idpejabat = 'Sekretaris Organisasi';
                $pejabatsekre = $this->pejabat->cekjabatan($idpejabat);
                if ($pejabatsekre != null) {
                    $pdf->Cell(0, 10, $pejabatsekre->jabatan . ',', 0, '', 'C');
                }
            }

        }
        if ($ktp->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($ktp->jabatan_lainnya);
            $pdf->Ln(3);
            $pdf->SetX(110);
            $pdf->Cell(0, 10, 'u.b.', 0, '', 'C');
            $pdf->Ln(3);
            $pdf->SetX(110);
            if ($pejabatstruktural != null) {
                $pdf->Cell(0, 10, $pejabat->jabatan . ',', 0, '', 'C');
            }
        }
        if ($ktp->penandatangan != 'Atasnama Pimpinan' && $ktp->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetX(110);
            if ($ktp->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($ktp->penandatangan);
                if ($pejabatsekretaris != null) {
                    $pdf->Cell(0, 10, strtoupper($pejabatsekretaris->jabatan . ','), 0, '', 'C');
                }
            }
            if ($ktp->penandatangan == 'Pimpinan Organisasi' && $ktp->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($ktp->penandatangan);
                if ($pejabatsekretaris != null) {
                    $pdf->Cell(0, 10, strtoupper($pejabatsekretaris->jabatan . ' ' . $namadesa . ','), 0, '', 'C');
                }
            }

        }
        if ($ktp->penandatangan != 'Jabatan Struktural') {
            $pdf->Ln(18);//Pejabatan
        }
        if ($ktp->penandatangan == 'Jabatan Struktural') {
            $pdf->Ln(12.5);//Pejabatan
        }
        if ($pejabat != null) {
            $pdf->SetX(120);
            $pdf->SetFont('Arial', 'BU', 9);
            if ($pejabat->titel_belakang != '' && $pejabat->titel_depan != '') {
                $pdf->SetX(110);
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan != '') {
                $pdf->SetX(110);
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama, 0, '', 'C');
            } else if ($pejabat->titel_belakang != '' && $pejabat->titel_depan == '') {
                $pdf->SetX(110);
                $pdf->Cell(0, 10, $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan == '') {
                $pdf->SetX(110);
                $pdf->Cell(0, 10, $pejabat->nama, 0, '', 'C');
            }
            $pdf->SetFont('Arial', '', 9);
            $pdf->Ln(3);
            $pdf->SetX(110);
            $pdf->Cell(0, 10, $pejabat->pangkat, 0, '', 'C');
            $pdf->Ln(3);

            if ($pejabat->nip != '') {
                $pdf->SetX(110);
                $pdf->Cell(0, 10, 'NIP. ' . $pejabat->nip, 0, '', 'C');
            }
        }

        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

        $pdf->Output('cetak-data-ktp-' . $tanggal . '.pdf', 'I');
        exit;
    }
}