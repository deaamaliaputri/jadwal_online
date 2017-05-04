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

class CetakKtpSimduk extends Controller
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
        $pdf->SetFont('Times-Roman', 'B', 10);
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
        $pdf->SetX(15);
        $pdf->Cell(28, 28, '', 1, 0, 'C');
        $pdf->SetFont('arial', 'B', 8);
        $pdf->ln(-10);
        $pdf->SetX(20);
        $pdf->Cell(0, 80, 'Kode Grafer', 0, 0, '');
        $pdf->SetFont('arial', '', 8);
        $pdf->SetX(75);
        $pdf->ln(10);
        $pdf->SetX(65);
        $pdf->Cell(25, 28, '', 1, 0, 'C');
        $pdf->SetX(68);
        $pdf->Cell(0, 30, 'Foto 4 x 6 cm', 0, 0, '');
        $pdf->SetX(71);
        $pdf->Cell(0, 35, 'Berwarna', 0, 0, '');
        $pdf->SetX(65);
        $pdf->SetX(-95);
        $pdf->Cell(80, 28, '', 1, 0, 'C');
        $pdf->SetX(130);
        $pdf->Cell(0, 60, 'Tanda Tangan (Jangan Melebihi Batas)', 0, 0, '');
        $pdf->ln(35);
        $pdf->SetFont('Times-Roman', 'B', 13);
        $pdf->Cell(0, 0, 'PEMERINTAH ' . $status . ' ' . strtoupper($kabupaten), 0, 0, 'C');
        $pdf->ln(4);
        $pdf->SetFont('Times-Roman', '', 10);
        $pdf->Cell(0, 0, $statuskecamatan . '   :    ' . strtoupper($kecamatan), 0, 0, 'L');
        $pdf->Cell(0, 0, $statusdesa . '   :    ' . strtoupper($namadesa), 0, 0, 'R');
        $pdf->ln(2);
        $pdf->SetFont('Times-Roman', 'UB', 10);
        $pdf->Cell(0, 0, '', 1, 0, 'R');


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

    public function KtpSimduk($id)
    {
        $pdf = new PdfClass('L', 'mm', array(210, 168));
        $pdf->is_header = false;
        $pdf->set_widths = 80;
        $pdf->set_footer = 29;
        $pdf->orientasi = 'P';
        $pdf->AddFont('Arial', '', 'arial.php');

        //Disable automatic page break
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 5, 10);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('KP-01 SIMDUK');
        $this->Kop($pdf, $id);
        $pdf->SetY(44);
        $desa = $this->desa->find(session('desa'));
        $ktp = $this->ktp->find($id);
        if ($ktp->pejabat_desa_id == 1) {
            $keluarga = $this->keluarga->cekalamat($ktp->pribadi->id);
        }
        if ($ktp->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($ktp->penandatangan);
        }
        //desa
        if ($desa->status == 1) {
            $namadesa = $desa->desa;
        }
        if ($desa->status == 2) {
            $namadesa = $desa->desa;
        }
        if ($desa->status == 3) {
            $namadesa = $desa->desa;
        }
        if ($desa->status == 4) {
            $namadesa = $desa->desa;
        }
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(0, 0, 'FORMULIR ISIAN DATA KARTU TANDA PENDUDUK', 0, '', 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 0, '(Harap ditulis dengan HURUF CETAK, beri tanda x yang dipilih)', 0, '', 'C');

        $pdf->Ln(13);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);

        $pdf->Cell(25, -14, '1.    NIK', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', '', 9);

        $totalkata = strlen($ktp->nik);
        $kurang = $totalkata - 1;
        $pdf->Ln(-10);
        $pdf->SetX(60);
        for ($i = 0; $i <= $kurang; $i++) {
            $hasil = substr($ktp->nik, $i, $totalkata);
            $tampil = substr($hasil, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 9);
            $widths = array($widd);
            $caption = array($tampil);
            $pdf->SetWidths($widths);
            $pdf->FancyRow($caption);

        }
        $pdf->SetX(145);
        $pdf->Cell(5, 5, 'X', 1, '', 'C');
        $pdf->Cell(5, 5, 'WNI', 0, '', 'L');
        $pdf->SetX(165);
        $pdf->Cell(5, 5, '', 1, '', 'L');
        $pdf->Cell(5, 5, 'WNA', 0, '', 'L');

        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, -14, '2.    Nama Lengkap', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', 'B', 9);
        if ($ktp->pejabat_desa_id == 1) {
            $totalkatanama = strlen($ktp->pribadi->nama);
            $namalengkap = $ktp->pribadi->nama;
        }
        if ($ktp->pejabat_desa_id == 2) {
            $totalkatanama = strlen($ktp->non_penduduk->nama);
            $namalengkap = $ktp->non_penduduk->nama;
        }


        $kurangnama = 26;
        $pdf->Ln(-10);
        $pdf->SetX(60);

        for ($i = 0; $i <= $kurangnama; $i++) {
            $hasil1 = substr($namalengkap, $i, $totalkatanama);
            $tampil1 = substr($hasil1, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', 'B', 9);
            $widths = array($widd);
            $caption = array(strtoupper($tampil1));
            $pdf->SetWidths($widths);
            $pdf->FancyRow($caption);

        }
        if ($ktp->pejabat_desa_id == 1) {
            if ($ktp->pribadi->jk->id == 1) {
                $laki = 'X';
                $perempuan = '';
            }
            if ($ktp->pribadi->jk->id == 2) {
                $laki = '';
                $perempuan = 'X';
            }
        }
        if ($ktp->pejabat_desa_id == 2) {
            if ($ktp->non_penduduk->jk->id == 1) {
                $laki = 'X';
                $perempuan = '';
            }
            if ($ktp->non_penduduk->jk->id == 2) {
                $laki = '';
                $perempuan = 'X';
            }
        }
        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, -14, '3.    Jenis Kelamin', 0, '', 'L');
        $pdf->Ln(-10);
        $pdf->Cell(50);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(5, 5, $laki, 1, '', 'L');
        $pdf->Cell(5, 5, 'Laki-Laki', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(5, 5, $perempuan, 1, '', 'L');
        $pdf->Cell(5, 5, 'Perempuan', 0, '', 'L');
        $pdf->SetX(120);
        $pdf->Cell(5, 5, 'Golongan Darah', 0, '', 'L');
        if ($ktp->pejabat_desa_id == 1) {
            $totalkatagolongan = strlen($ktp->pribadi->golongan_darah->golongan_darah);
            $golongandarah = $ktp->pribadi->golongan_darah->golongan_darah;
            $cekgolongandarah = $ktp->pribadi->golongan_darah->id;
        }
        if ($ktp->pejabat_desa_id == 2) {
            $totalkatagolongan = strlen($ktp->non_penduduk->golongan_darah->golongan_darah);
            $golongandarah = $ktp->non_penduduk->golongan_darah->golongan_darah;
            $cekgolongandarah = $ktp->non_penduduk->golongan_darah->id;
        }

        $kuranggolongan = 1;
        $pdf->SetX(155);

        for ($i = 0; $i <= $kuranggolongan; $i++) {
            $hasil2 = substr($golongandarah, $i, $totalkatagolongan);
            if ($cekgolongandarah != 13) {
                $tampil2 = substr($hasil2, 0, 1);
            }
            if ($cekgolongandarah == 13) {
                $tampil2 = ' -';
            }
            $widd = 5;
            $pdf->SetFont('Arial', '', 9);
            $widths = array($widd);
            $caption = array($tampil2);
            $pdf->SetWidths($widths);
            $pdf->FancyRow($caption);

        }
        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, -14, '4.    Tanggal Lahir', 0, '', 'L');
        $pdf->Ln(-10);
        $pdf->Cell(50);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(5, 5, 'Tgl.', 0, '', 'L');
        $pdf->Cell(5);
        if ($ktp->pejabat_desa_id == 1) {
            $tgl1 = substr($ktp->pribadi->tanggal_lahir, 0, 1);
            $tgl2 = substr($ktp->pribadi->tanggal_lahir, 1, 1);
        }
        if ($ktp->pejabat_desa_id == 2) {
            $tgl1 = substr($ktp->non_penduduk->tanggal_lahir, 0, 1);
            $tgl2 = substr($ktp->non_penduduk->tanggal_lahir, 1, 1);
        }
        $pdf->Cell(5, 5, $tgl1, 1, '', 'L');
        $pdf->Cell(5, 5, $tgl2, 1, '', 'L');
        $pdf->Cell(10);
        $pdf->Cell(5, 5, 'Bulan', 0, '', 'L');
        $pdf->Cell(10);
        if ($ktp->pejabat_desa_id == 1) {
            $bln1 = substr($ktp->pribadi->tanggal_lahir, 3, 1);
            $bln2 = substr($ktp->pribadi->tanggal_lahir, 4, 1);
        }
        if ($ktp->pejabat_desa_id == 2) {
            $bln1 = substr($ktp->non_penduduk->tanggal_lahir, 3, 1);
            $bln2 = substr($ktp->non_penduduk->tanggal_lahir, 4, 1);
        }
        $pdf->Cell(5, 5, $bln1, 1, '', 'L');
        $pdf->Cell(5, 5, $bln2, 1, '', 'L');
        $pdf->Cell(10);
        $pdf->Cell(5, 5, 'Tahun', 0, '', 'L');
        $pdf->Cell(10);
        if ($ktp->pejabat_desa_id == 1) {
            $tahun1 = substr($ktp->pribadi->tanggal_lahir, 6, 1);
            $tahun2 = substr($ktp->pribadi->tanggal_lahir, 7, 1);
            $tahun3 = substr($ktp->pribadi->tanggal_lahir, 8, 1);
            $tahun4 = substr($ktp->pribadi->tanggal_lahir, 9, 1);
        }
        if ($ktp->pejabat_desa_id == 2) {
            $tahun1 = substr($ktp->non_penduduk->tanggal_lahir, 6, 1);
            $tahun2 = substr($ktp->non_penduduk->tanggal_lahir, 7, 1);
            $tahun3 = substr($ktp->non_penduduk->tanggal_lahir, 8, 1);
            $tahun4 = substr($ktp->non_penduduk->tanggal_lahir, 9, 1);
        }
        $pdf->Cell(5, 5, $tahun1, 1, '', 'L');
        $pdf->Cell(5, 5, $tahun2, 1, '', 'L');
        $pdf->Cell(5, 5, $tahun3, 1, '', 'L');
        $pdf->Cell(5, 5, $tahun4, 1, '', 'L');
        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, -14, '5.    Tempat Lahir', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', 'B', 9, 'C');
        if ($ktp->pejabat_desa_id == 1) {
            $totalkatatempat = strlen($ktp->pribadi->tempat_lahir);
            $letaktempatlahir = $ktp->pribadi->tempat_lahir;
        }
        if ($ktp->pejabat_desa_id == 2) {
            $totalkatatempat = strlen($ktp->non_penduduk->tempat_lahir);
            $letaktempatlahir = $ktp->non_penduduk->tempat_lahir;
        }


        $kurangtempat = 26;
        $pdf->Ln(-10);
        $pdf->SetX(60);

        for ($i = 0; $i <= $kurangtempat; $i++) {
            $hasil3 = substr($letaktempatlahir, $i, $totalkatatempat);
            $tampil3 = substr($hasil3, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 9);
            $widths = array($widd);
            $caption = array(strtoupper($tampil3));
            $pdf->SetWidths($widths);
            $pdf->FancyRow($caption);

        }
        if ($ktp->pejabat_desa_id == 1) {
            if ($ktp->pribadi->perkawinan->id == 1) {
                $belumkawin = 'X';
                $kawin = '';
                $ceraihidup = '';
                $ceraimati = '';
            }
            if ($ktp->pribadi->perkawinan->id == 2) {
                $belumkawin = '';
                $kawin = 'X';
                $ceraihidup = '';
                $ceraimati = '';
            }
            if ($ktp->pribadi->perkawinan->id == 3) {
                $belumkawin = '';
                $kawin = '';
                $ceraihidup = 'X';
                $ceraimati = '';
            }
            if ($ktp->pribadi->perkawinan->id == 4) {
                $belumkawin = '';
                $kawin = '';
                $ceraihidup = '';
                $ceraimati = 'X';
            }
        }
        if ($ktp->pejabat_desa_id == 2) {
            if ($ktp->non_penduduk->perkawinan->id == 1) {
                $belumkawin = 'X';
                $kawin = '';
                $ceraihidup = '';
                $ceraimati = '';
            }
            if ($ktp->non_penduduk->perkawinan->id == 2) {
                $belumkawin = '';
                $kawin = 'X';
                $ceraihidup = '';
                $ceraimati = '';
            }
            if ($ktp->non_penduduk->perkawinan->id == 3) {
                $belumkawin = '';
                $kawin = '';
                $ceraihidup = 'X';
                $ceraimati = '';
            }
            if ($ktp->non_penduduk->perkawinan->id == 4) {
                $belumkawin = '';
                $kawin = '';
                $ceraihidup = '';
                $ceraimati = 'X';
            }
        }
        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, -14, '6.    Status Kawin', 0, '', 'L');
        $pdf->Ln(-10);
        $pdf->Cell(50);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(5, 5, $kawin, 1, '', 'L');
        $pdf->Cell(5, 5, 'Kawin', 0, '', 'L');
        $pdf->SetX(90);
        $pdf->Cell(5, 5, $belumkawin, 1, '', 'L');
        $pdf->Cell(5, 5, 'Belum Kawin', 0, '', 'L');
        $pdf->SetX(125);
        $pdf->Cell(5, 5, $ceraihidup, 1, '', 'L');
        $pdf->Cell(5, 5, 'Cerai Hidup', 0, '', 'L');
        $pdf->SetX(155);
        $pdf->Cell(5, 5, $ceraimati, 1, '', 'L');
        $pdf->Cell(5, 5, 'Cerai Mati', 0, '', 'L');
        if ($ktp->pejabat_desa_id == 1) {

            if ($ktp->pribadi->agama->id == 1) {
                $islam = 'X';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($ktp->pribadi->agama->id == 2) {
                $islam = '';
                $kristen = 'X';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($ktp->pribadi->agama->id == 3) {
                $islam = '';
                $kristen = '';
                $khatolik = 'X';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($ktp->pribadi->agama->id == 4) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = 'X';
                $budha = '';
                $lainnya = '';
            }
            if ($ktp->pribadi->agama->id == 5) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = 'X';
                $lainnya = '';
            }
            if ($ktp->pribadi->agama->id == 6) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = 'X';
            }
            if ($ktp->pribadi->agama->id == 7) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = 'X';
            }
        }
        if ($ktp->pejabat_desa_id == 2) {

            if ($ktp->non_penduduk->agama->id == 1) {
                $islam = 'X';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($ktp->non_penduduk->agama->id == 2) {
                $islam = '';
                $kristen = 'X';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($ktp->non_penduduk->agama->id == 3) {
                $islam = '';
                $kristen = '';
                $khatolik = 'X';
                $hindu = '';
                $budha = '';
                $lainnya = '';
            }
            if ($ktp->non_penduduk->agama->id == 4) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = 'X';
                $budha = '';
                $lainnya = '';
            }
            if ($ktp->non_penduduk->agama->id == 5) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = 'X';
                $lainnya = '';
            }
            if ($ktp->non_penduduk->agama->id == 6) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = 'X';
            }
            if ($ktp->non_penduduk->agama->id == 7) {
                $islam = '';
                $kristen = '';
                $khatolik = '';
                $hindu = '';
                $budha = '';
                $lainnya = 'X';
            }
        }
        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, -14, '7.    Agama', 0, '', 'L');
        $pdf->Ln(-10);
        $pdf->Cell(50);
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
        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, -14, '8.    Pekerjaan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', 'B', 9, 'C');
        if ($ktp->pejabat_desa_id == 1) {

            if ($ktp->pribadi->pekerjaan_id != 89) {
                $totalkatapekerjaan = strlen($ktp->pribadi->pekerjaan->pekerjaan);
                $kurangpekerjaan = 26;
                $pdf->Ln(-10);
                $pdf->SetX(60);
                for ($i = 0; $i <= $kurangpekerjaan; $i++) {
                    $hasil4 = substr($ktp->pribadi->pekerjaan->pekerjaan, $i, $totalkatapekerjaan);
                    $tampil4 = substr($hasil4, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 8);
                    $widths = array($widd);
                    $caption = array((strtoupper($tampil4)));
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow($caption);
                }
            }
            if ($ktp->pribadi->pekerjaan_id == 89) {
                $totalkatapekerjaan = strlen($ktp->pribadi->pekerjaan_lain->pekerjaan_lain);
                $kurangpekerjaan = 26;
                $pdf->Ln(-10);
                $pdf->SetX(60);
                for ($i = 0; $i <= $kurangpekerjaan; $i++) {
                    $hasil4 = substr($ktp->pribadi->pekerjaan_lain->pekerjaan_lain, $i, $totalkatapekerjaan);
                    $tampil4 = substr($hasil4, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 8);
                    $widths = array($widd);
                    $caption = array((strtoupper($tampil4)));
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow($caption);
                }
            }
        }
        if ($ktp->pejabat_desa_id == 2) {

            if ($ktp->non_penduduk->pekerjaan_id != 89) {
                $totalkatapekerjaan = strlen($ktp->non_penduduk->pekerjaan->pekerjaan);
                $kurangpekerjaan = 26;
                $pdf->Ln(-10);
                $pdf->SetX(60);
                for ($i = 0; $i <= $kurangpekerjaan; $i++) {
                    $hasil4 = substr($ktp->non_penduduk->pekerjaan->pekerjaan, $i, $totalkatapekerjaan);
                    $tampil4 = substr($hasil4, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 9);
                    $widths = array($widd);
                    $caption = array((strtoupper($tampil4)));
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow($caption);
                }
            }
            if ($ktp->non_penduduk->pekerjaan_id == 89) {
                $totalkatapekerjaan = strlen($ktp->non_penduduk->pekerjaan_lain->pekerjaan_lain);
                $kurangpekerjaan = 26;
                $pdf->Ln(-10);
                $pdf->SetX(60);
                for ($i = 0; $i <= $kurangpekerjaan; $i++) {
                    $hasil4 = substr($ktp->non_penduduk->pekerjaan_lain->pekerjaan_lain, $i, $totalkatapekerjaan);
                    $tampil4 = substr($hasil4, 0, 1);
                    $widd = 5;
                    $pdf->SetFont('Arial', '', 8);
                    $widths = array($widd);
                    $caption = array((strtoupper($tampil4)));
                    $pdf->SetWidths($widths);
                    $pdf->FancyRow($caption);
                }
            }
        }
        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, -14, '9.    Alamat', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->SetFont('Arial', 'B', 9);
        if ($ktp->pejabat_desa_id == 1) {
            $totalkataalamat = strlen($keluarga->alamat);
            $lokasialamat = $keluarga->alamat;
        }
        if ($ktp->pejabat_desa_id == 2) {
            $totalkataalamat = strlen($ktp->non_penduduk->alamat);
            $lokasialamat = $ktp->non_penduduk->alamat;
        }
        $kurangalamat = 26;
        $pdf->Ln(-10);
        $pdf->SetX(60);

        for ($i = 0; $i <= $kurangalamat; $i++) {
            $hasil5 = substr($lokasialamat, $i, $totalkataalamat);
            $tampil5 = substr($hasil5, 0, 1);
            $widd = 5;
            $pdf->SetFont('Arial', '', 9);
            $widths = array($widd);
            $caption = array($tampil5);
            $pdf->SetWidths($widths);
            $pdf->FancyRow($caption);
        }
        $pdf->Ln(16);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25);
        $pdf->Ln(-10);
        $pdf->Cell(50);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(5, 5, 'RT.', 0, '', 'L');
        $pdf->Cell(5);
        if ($ktp->pejabat_desa_id == 1) {

            if (strlen($keluarga->alamat_rt) == 1) {
                $rt = substr($keluarga->alamat_rt, 0, 1);
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, $rt, 1, '', 'L');
                $pdf->Cell(10);
            }
            if (strlen($keluarga->alamat_rt) == 2) {
                $rt = substr($keluarga->alamat_rt, 0, 1);
                $rt2 = substr($keluarga->alamat_rt, 1, 1);
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, $rt, 1, '', 'L');
                $pdf->Cell(5, 5, $rt2, 1, '', 'L');
                $pdf->Cell(10);
            }
            if (strlen($keluarga->alamat_rt) == 3) {
                $rt = substr($keluarga->alamat_rt, 0, 1);
                $rt2 = substr($keluarga->alamat_rt, 1, 1);
                $rt3 = substr($keluarga->alamat_rt, 2, 1);
                $pdf->Cell(5, 5, $rt, 1, '', 'L');
                $pdf->Cell(5, 5, $rt2, 1, '', 'L');
                $pdf->Cell(5, 5, $rt3, 1, '', 'L');
                $pdf->Cell(10);
            }
            $pdf->Cell(5, 5, 'RW.', 0, '', 'L');
            $pdf->Cell(5);
            if (strlen($keluarga->alamat_rw) == 1) {
                $rw = substr($keluarga->alamat_rw, 0, 1);
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, $rw, 1, '', 'L');
                $pdf->Cell(10);
            }
            if (strlen($keluarga->alamat_rw) == 2) {
                $rw = substr($keluarga->alamat_rw, 0, 1);
                $rw2 = substr($keluarga->alamat_rw, 1, 1);
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, $rw, 1, '', 'L');
                $pdf->Cell(5, 5, $rw2, 1, '', 'L');
                $pdf->Cell(10);
            }
            if (strlen($keluarga->alamat_rw) == 3) {
                $rw = substr($keluarga->alamat_rw, 0, 1);
                $rw2 = substr($keluarga->alamat_rw, 1, 1);
                $rw3 = substr($keluarga->alamat_rw, 2, 1);
                $pdf->Cell(5, 5, $rw, 1, '', 'L');
                $pdf->Cell(5, 5, $rw2, 1, '', 'L');
                $pdf->Cell(5, 5, $rw3, 1, '', 'L');
                $pdf->Cell(10);
            }
        }
        if ($ktp->pejabat_desa_id == 2) {

            if (strlen($ktp->non_penduduk->alamat_rt) == 1) {
                $rt = substr($ktp->non_penduduk->alamat_rt, 0, 1);
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, $rt, 1, '', 'L');
                $pdf->Cell(10);
            }
            if (strlen($ktp->non_penduduk->alamat_rt) == 2) {
                $rt = substr($ktp->non_penduduk->alamat_rt, 0, 1);
                $rt2 = substr($ktp->non_penduduk->alamat_rt, 1, 1);
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, $rt, 1, '', 'L');
                $pdf->Cell(5, 5, $rt2, 1, '', 'L');
                $pdf->Cell(10);
            }
            if (strlen($ktp->non_penduduk->alamat_rt) == 3) {
                $rt = substr($ktp->non_penduduk->alamat_rt, 0, 1);
                $rt2 = substr($ktp->non_penduduk->alamat_rt, 1, 1);
                $rt3 = substr($ktp->non_penduduk->alamat_rt, 2, 1);
                $pdf->Cell(5, 5, $rt, 1, '', 'L');
                $pdf->Cell(5, 5, $rt2, 1, '', 'L');
                $pdf->Cell(5, 5, $rt3, 1, '', 'L');
                $pdf->Cell(10);
            }
            $pdf->Cell(5, 5, 'RW.', 0, '', 'L');
            $pdf->Cell(5);
            if (strlen($ktp->non_penduduk->alamat_rw) == 1) {
                $rw = substr($ktp->non_penduduk->alamat_rw, 0, 1);
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, $rw, 1, '', 'L');
                $pdf->Cell(10);
            }
            if (strlen($ktp->non_penduduk->alamat_rw) == 2) {
                $rw = substr($ktp->non_penduduk->alamat_rw, 0, 1);
                $rw2 = substr($ktp->non_penduduk->alamat_rw, 1, 1);
                $pdf->Cell(5, 5, 0, 1, '', 'L');
                $pdf->Cell(5, 5, $rw, 1, '', 'L');
                $pdf->Cell(5, 5, $rw2, 1, '', 'L');
                $pdf->Cell(10);
            }
            if (strlen($ktp->non_penduduk->alamat_rw) == 3) {
                $rw = substr($ktp->non_penduduk->alamat_rw, 0, 1);
                $rw2 = substr($ktp->non_penduduk->alamat_rw, 1, 1);
                $rw3 = substr($ktp->non_penduduk->alamat_rw, 2, 1);
                $pdf->Cell(5, 5, $rw, 1, '', 'L');
                $pdf->Cell(5, 5, $rw2, 1, '', 'L');
                $pdf->Cell(5, 5, $rw3, 1, '', 'L');
                $pdf->Cell(10);
            }
        }
        $jeniskodeadministrasi = $this->ktp->cekkodejenisadministrasi($ktp->jenis_pelayanan_id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();
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

        $pdf->Ln(4);
        $pdf->SetX(120);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $ktp->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $ktp->tahun, 0, '', 'C');
        if ($ktp->pejabat_desa_id == 1) {
            $namapemohon = $ktp->pribadi->nama;
        }
        if ($ktp->pejabat_desa_id == 2) {
            $namapemohon = $ktp->non_penduduk->nama;
        }
        $pdf->Ln(4);
        $pdf->SetX(30);
        $pdf->SetFont('Arial', 'B', 10);
        if ($ktp->penandatangan == 'Pimpinan Organisasi') {
            $pdf->Cell(50, 8, 'PEMOHON,', 0, '', 'C');
            $pdf->SetX(30);
            $pdf->Cell(50, 62, strtoupper($namapemohon), 0, '', 'C');
        }
        if ($ktp->penandatangan == 'Sekretaris Organisasi') {
            $pdf->Cell(50, 8, 'PEMOHON,', 0, '', 'C');
            $pdf->SetX(30);
            $pdf->Cell(50, 62, strtoupper($namapemohon), 0, '', 'C');
        }
        if ($ktp->penandatangan == 'Atasnama Pimpinan') {
            $pdf->Cell(50, 8, 'PEMOHON,', 0, '', 'C');
            $pdf->SetX(30);
            $pdf->Cell(50, 70, strtoupper($namapemohon), 0, '', 'C');
        }
        if ($ktp->penandatangan == 'Jabatan Struktural') {
            $pdf->Cell(50, 15, 'PEMOHON,', 0, '', 'C');
            $pdf->SetX(30);
            $pdf->Cell(50, 75, strtoupper($namapemohon), 0, '', 'C');
        }
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(120);
        $hari = substr($ktp->tanggal, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($ktp->tanggal, 3, 2) <= 9) {
            $bulan = $indo[substr($ktp->tanggal, 4, 1)];
        } else {
            $bulan = $indo[substr($ktp->tanggal, 3, 2)];
        }
        $tahun = substr($ktp->tanggal, 6, 4);
        $tempatlahir = $hari . ' ' . $bulan . ' ' . $tahun;

        $pdf->Cell(0, 6, $namadesa . ', ' . $tempatlahir, 0, '', 'C');
        $pdf->Ln(2);
        if ($ktp->penandatangan == 'Atasnama Pimpinan' || $ktp->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($pejabatpimpinan != null) {
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
                        $keteranganjabatansekre = $pejabatsekre->keterangan . ' ';
                    }
                    if ($pejabatsekre->keterangan == '') {
                        $keteranganjabatansekre = '';
                    }

                    $pdf->Cell(0, 10, $keteranganjabatansekre . $pejabatsekre->jabatan . ',', 0, '', 'C');
                }
            }

        }
        if ($ktp->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($ktp->jabatan_lainnya);
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
        if ($ktp->penandatangan != 'Atasnama Pimpinan' && $ktp->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($ktp->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($ktp->penandatangan);
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
            if ($ktp->penandatangan == 'Pimpinan Organisasi' && $ktp->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($ktp->penandatangan);
                if ($pejabatsekretaris != null) {
                    if ($pejabatsekretaris->keterangan != '') {
                        $keteranganjabatansekretaris = $pejabatsekretaris->keterangan . ' ';
                    }
                    if ($pejabatsekretaris->keterangan == '') {
                        $keteranganjabatansekretaris = '';
                    }

                    $pdf->Cell(0, 10, $keteranganjabatansekretaris . strtoupper($pejabatsekretaris->jabatan . ' ' . $namadesa . ','), 0, '', 'C');
                }
            }

        }
        $pdf->Ln(18);

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

        $pdf->Output('cetak-data-ktp-' . $tanggal . '.pdf', 'I');
        exit;
    }
}