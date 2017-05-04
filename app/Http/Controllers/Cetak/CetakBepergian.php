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
use App\Domain\Repositories\Pelayanan\BepergianRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakBepergian extends Controller
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
        BepergianRepository $bepergianRepository,
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
        $this->bepergian = $bepergianRepository;
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
        $bepergian = $this->bepergian->find($id);

        $jeniskodeadministrasi = $this->bepergian->cekkodejenisadministrasi($bepergian->jenis_pelayanan_id);
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
                $pdf->Cell(0, 0, 'Alamat:' . $alamat->alamat . ' Telp. ' . $alamat->telepon . ' Fax. ' . $alamat->faxmile, 0, 0, 'C');
            }
            if ($alamat->faxmile == 0) {
                $pdf->Cell(0, 0, 'Alamat:' . $alamat->alamat . ' Telp. ' . $alamat->telepon, 0, 0, 'C');
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
        $pdf->Ln(15);
        $pdf->SetFont('arial', 'BU', 12);
        $pdf->SetX(25);
        $pdf->Cell(0, 0, 'SURAT KETERANGAN BEPERGIAN', 0, '', 'C');

        $pdf->Ln(5);
        $pdf->SetFont('arial', '', 10);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($bepergian->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($bepergian->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($bepergian->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $bepergian->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $bepergian->tahun, 0, '', 'C');

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

    public function Bepergian($id)
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
        $pdf->SetTitle('Surat Keterangan Bepergian');
        $this->Kop($pdf, $id);
        $pdf->SetY(80);
        $desa = $this->desa->find(session('desa'));
        $bepergian = $this->bepergian->find($id);
        $sampaihari = $this->bepergian->ceksampaikapan($bepergian->id);
        $pribadi1 = $this->pribadi->ceknikcetak($bepergian->pengikut_1);
        $pribadi2 = $this->pribadi->ceknikcetak($bepergian->pengikut_2);
        $pribadi3 = $this->pribadi->ceknikcetak($bepergian->pengikut_3);
        $pribadi4 = $this->pribadi->ceknikcetak($bepergian->pengikut_4);
        $pribadi5 = $this->pribadi->ceknikcetak($bepergian->pengikut_5);
        if ($bepergian->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($bepergian->penandatangan);
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

        //desa->tujuan
        //kabupaten
        if ($bepergian->desa_tujuan_id->kecamatan->kabupaten->status == 1) {
            $statustujuan = 'Kabupaten';
            $kabupatentujuan = $bepergian->desa_tujuan_id->kecamatan->kabupaten->kabupaten;
        }
        if ($bepergian->desa_tujuan_id->kecamatan->kabupaten->status == 2) {
            $statustujuan = 'Kota';
            $kabupatentujuan = $bepergian->desa_tujuan_id->kecamatan->kabupaten->kabupaten;
        }
        //kecamatan
        if ($bepergian->desa_tujuan_id->kecamatan->status == 1) {

            $statuskecamatantujuan = 'Kecamatan';
            $kecamatantujuan = $bepergian->desa_tujuan_id->kecamatan->kecamatan;
        }
        if ($bepergian->desa_tujuan_id->kecamatan->kabupaten->status == 2) {
            $statuskecamatantujuan = 'Distrik';
            $kecamatantujuan = $bepergian->desa_tujuan_id->kecamatan->kecamatan;
        }
        //desa
        if ($bepergian->desa_tujuan_id->status == 1) {
            $statusdesatujuan = 'Kelurahan';
            $namadesatujuan = $bepergian->desa_tujuan_id->desa;
        }
        if ($bepergian->desa_tujuan_id->status == 2) {

            $statusdesatujuan = 'Desa';
            $namadesatujuan = $bepergian->desa_tujuan_id->desa;
        }
        if ($bepergian->desa_tujuan_id->status == 3) {

            $statusdesatujuan = 'Kampung';
            $namadesatujuan = $bepergian->desa_tujuan_id->desa;
        }
        if ($bepergian->desa_tujuan_id->status == 4) {

            $statusdesatujuan = 'Negeri';
            $namadesatujuan = $bepergian->desa_tujuan_id->desa;
        }
        if ($bepergian->penandatangan == 'Pimpinan Organisasi') {
            $pejabat2 = $this->pejabat->cekjabatan($bepergian->penandatangan);

            if ($pejabat2 != null) {
                $jabatanpimpinan = $pejabat2->jabatan;
            } else {
                $jabatanpimpinan = '';
            }

            $tampiljabatan = $jabatanpimpinan;

        } else if ($bepergian->penandatangan == 'Sekretaris Organisasi') {
            $pejabat2 = $this->pejabat->cekjabatan($bepergian->penandatangan);
            if ($pejabat2 != null) {
                $jabatansekretaris = $pejabat2->jabatan;
            } else {
                $jabatansekretaris = '';
            }

            $tampiljabatan = $jabatansekretaris;

        } else if ($bepergian->penandatangan == 'Atasnama Pimpinan') {
            $idpejabatpimpinan = 'Pimpinan Organisasi';
            $pejabat1 = $this->pejabat->cekjabatan($idpejabatpimpinan);

            if ($pejabat1 != null) {
                $jabatanpimpinan = ' atas nama ' . $pejabat1->jabatan;
            } else {
                $jabatanpimpinan = '';
            }

            $idpejabatsekretaris = 'Sekretaris Organisasi';
            $pejabat2 = $this->pejabat->cekjabatan($idpejabatsekretaris);

            if ($pejabat2 != null) {
                $jabatansekretaris = $pejabat2->jabatan;
            } else {
                $jabatansekretaris = '';
            }

            $tampiljabatan = $jabatansekretaris . $jabatanpimpinan;

        } else if ($bepergian->penandatangan == 'Jabatan Struktural') {
            $idpejabatpimpinan = 'Pimpinan Organisasi';
            $pejabat1 = $this->pejabat->cekjabatan($idpejabatpimpinan);

            if ($pejabat1 != null) {
                $jabatanpimpinan = ' atas nama ' . $pejabat1->jabatan;
            } else {
                $jabatanpimpinan = '';
            }
            $idpejabatsekretaris = 'Sekretaris Organisasi';

            $pejabat2 = $this->pejabat->cekjabatan($idpejabatsekretaris);

            if ($pejabat2 != null) {
                $jabatansekretaris = ' ' . $pejabat2->jabatan;
            } else {
                $jabatansekretaris = '';
            }

            $pejabat3 = $this->pejabat->find($bepergian->jabatan_lainnya);

            $jabatanstruktural = $pejabat3->jabatan;
            $tampiljabatan = $jabatanstruktural . ' untuk beliau' . $jabatansekretaris . $jabatanpimpinan;
        }
        $pdf->Ln(-15);
        $pdf->SetWidths([5, 180]);
        $pdf->Cell(5);
        $pdf->Row2(['', '                  Yang bertanda tangan di bawah ini ' . $tampiljabatan . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten . ' dengan ini menerangkan bahwa:']);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(35);
        $pdf->Ln(5);
        $pdf->Ln(5);
        $pdf->Ln(5);
        $keluarga = $this->keluarga->cekalamat($bepergian->pribadi->id);
        if ($bepergian->pribadi->titel_belakang != '') {

            $namalengkap = $bepergian->pribadi->titel_depan . ' ' . $bepergian->pribadi->nama . ', ' . $bepergian->pribadi->titel_belakang;
        }
        if ($bepergian->pribadi->titel_belakang == '') {

            $namalengkap = $bepergian->pribadi->titel_depan . ' ' . $bepergian->pribadi->nama . '' . $bepergian->pribadi->titel_belakang;
        }
        $hari = substr($bepergian->pribadi->tanggal_lahir, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($bepergian->pribadi->tanggal_lahir, 3, 2) <= 9) {
            $bulan = $indo[substr($bepergian->pribadi->tanggal_lahir, 4, 1)];
        } else {
            $bulan = $indo[substr($bepergian->pribadi->tanggal_lahir, 3, 2)];
        }
        $tahun = substr($bepergian->pribadi->tanggal_lahir, 6, 4);
        $tempatlahir = $bepergian->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
        $jk = $bepergian->pribadi->jk->jk;
        if ($bepergian->pribadi->gol_darah_id != 13) {
            $golongandarah = $bepergian->pribadi->golongan_darah->golongan_darah;
        }
        if ($bepergian->pribadi->gol_darah_id == 13) {
            $golongandarah = '--';
        }
        $agama = $bepergian->pribadi->agama->agama;
        $perkawinanan = $bepergian->pribadi->perkawinan->kawin;
        if ($bepergian->pribadi->pekerjaan_id == 89) {
            $pekerjaan = $bepergian->pribadi->pekerjaan_lain->pekerjaan_lain;
        } else {
            $pekerjaan = $bepergian->pribadi->pekerjaan->pekerjaan;
        }
        $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
        $alamattujuan = $bepergian->alamat . ' RT. ' . $bepergian->alamat_rt . ' RW. ' . $bepergian->alamat_rw;
        if ($bepergian->pengikut_2 != 1 && $bepergian->pengikut_3 != 1 && $bepergian->pengikut_4 != 1 && $bepergian->pengikut_5 != 1) {
            $totalpengikut = 5 . ' orang pengikut, yaitu:';
        }
        if ($bepergian->pengikut_2 != 1 && $bepergian->pengikut_3 != 1 && $bepergian->pengikut_4 != 1 && $bepergian->pengikut_5 == 1) {
            $totalpengikut = 4 . ' orang pengikut, yaitu:';
        }
        if ($bepergian->pengikut_2 != 1 && $bepergian->pengikut_3 != 1 && $bepergian->pengikut_4 == 1 && $bepergian->pengikut_5 == 1) {
            $totalpengikut = 3 . ' orang pengikut, yaitu:';
        }
        if ($bepergian->pengikut_2 != 1 && $bepergian->pengikut_3 == 1 && $bepergian->pengikut_4 == 1 && $bepergian->pengikut_5 == 1) {
            $totalpengikut = 2 . ' orang pengikut, yaitu:';
        }
        if ($bepergian->pengikut_2 == 1 && $bepergian->pengikut_3 == 1 && $bepergian->pengikut_4 == 1 && $bepergian->pengikut_5 == 1) {
            $totalpengikut = 1 . ' orang pengikut, yaitu:';
        }
        if ($bepergian->pengikut_1 == 1 && $bepergian->pengikut_2 == 1 && $bepergian->pengikut_3 == 1 && $bepergian->pengikut_4 == 1 && $bepergian->pengikut_5 == 1) {
            $totalpengikut = 'NIHIL';
        }
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '1)    NIK', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     ' . $bepergian->nik, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '2)    Nama Lengkap', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     ' . $namalengkap, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '3)    Tempat, Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '4)    Jenis Kelamin ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     ' . $jk, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '5)    Golongan Darah ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     ' . $golongandarah, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '6)    Agama', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '7)    Status Perkawinan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     ' . $perkawinanan, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '8)    Pekerjaan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, -14, '9)    Kewarganegaraan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, -14, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(19);
        $pdf->Cell(25, -14, '10)   Alamat Domisili', 0, '', 'L');
        $pdf->Cell(21);
        $pdf->Cell(120, -15, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetWidths([5, 150]);
        $pdf->Ln(-5);
        $pdf->Cell(56);
        $pdf->Row2(['', $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten]);
        $pdf->Ln(12);
        $pdf->SetX(19);
        $pdf->Cell(25, -15, '11)   Alamat Tujuan', 0, '', 'L');
        $pdf->Cell(19);
        $pdf->Cell(40, -15, '        Jalan', 0, '', 'L');
        $pdf->Cell(73, -15, ' :     ' . $alamattujuan, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->Cell(54);
        $pdf->Cell(40, -15, '       Desa/Kelurahan', 0, '', 'L');
        $pdf->Cell(70, -15, ':     ' . $statusdesatujuan . ' ' . $namadesatujuan, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->Cell(54);
        $pdf->Cell(40, -15, '       Kecamatan', 0, '', 'L');
        $pdf->Cell(70, -15, ':     ' . $statuskecamatantujuan . ' ' . $kecamatantujuan, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->Cell(54);
        $pdf->Cell(40, -15, '       Kabupaten/Kota', 0, '', 'L');
        $pdf->Cell(70, -15, ':     ' . $statustujuan . ' ' . $kabupatentujuan, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->Cell(54);
        $pdf->Cell(40, -15, '       Provinsi', 0, '', 'L');
        $pdf->Cell(70, -15, ':     ' . $bepergian->desa_tujuan_id->kecamatan->kabupaten->provinsi->provinsi, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->Cell(54);
        $pdf->Cell(40, -15, '       Berlaku selama', 0, '', 'L');
        $pdf->Cell(70, -15, ':     ' . $sampaihari, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->Cell(54);
        $pdf->Cell(40, -15, '       Terhitung Sejak', 0, '', 'L');
        $pdf->Cell(70, -15, ':     ' . $bepergian->dari_tanggal . ' s/d ' . $bepergian->sampai_tanggal, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(19);
        $pdf->Cell(25, -15, '12)    Alasan Bepergian', 0, '', 'L');
        $pdf->Cell(21);
        if ($bepergian->alasan_pergi != 'Urusan Lainnya') {
            $pdf->Cell(120, -15, ':     ' . $bepergian->alasan_pergi, 0, '', 'L');
        }
        if ($bepergian->alasan_pergi == 'Urusan Lainnya') {
            $pdf->Cell(120, -15, ':     ' . $bepergian->alasan_lainnya, 0, '', 'L');
        }
        $pdf->Ln(5);
        $pdf->SetX(19);
        $pdf->Cell(25, -15, '13)    Pengikut', 0, '', 'L');
        $pdf->Cell(21);
        $pdf->Cell(120, -15, ':     ' . $totalpengikut, 0, '', 'L');
        $pdf->Ln(-5);
        $pdf->SetX(29);
        $pdf->Cell(13, 5, 'No.', 'TLR', 0, 'C');
        $pdf->Cell(30, 5, 'Nomor Induk ', 'TLR', 0, 'C');
        $pdf->Cell(50, 10, 'Nama Lengkap', 1, 0, 'C');
        $pdf->Cell(20, 6, 'Kelamin', 'TLR', 0, 'C');
        $pdf->Cell(30, 7, 'Hubungan', 'TLR', 0, 'C');
        $pdf->Cell(25, 10, 'Keterangan', 1, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetX(29);
        $pdf->Cell(13, 5, ' Urut.', 'BLR', 0, 'C');
        $pdf->Cell(30, 5, 'Kependudukan', 'BLR', 0, 'C');
        $pdf->Cell(50);
        $pdf->Cell(10, 5, 'L', 1, 0, 'C');
        $pdf->Cell(10, 5, 'P', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Keluarga', 'BLR', 0, 'C');
        $pdf->Ln(5);
        if ($bepergian->pengikut_1 == 1) {

            $pdf->SetX(29);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(13, 5, '--', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, '--', 'BLR', 0, 'C');
            $pdf->Cell(50, 5, '--', 'BLR', 0, 'C');
            $pdf->Cell(10, 5, '--', 1, 0, 'C');
            $pdf->Cell(10, 5, '--', 1, 0, 'C');
            $pdf->Cell(30, 5, '--', 'BLR', 0, 'C');
            $pdf->Cell(25, 5, '--', 'BLR', 0, 'C');
            $pdf->Ln(5);
        }
        if ($bepergian->pengikut_1 != 1) {

            $pdf->SetX(29);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(13, 5, ' 1.', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, $pribadi1->nik, 'BLR', 0, 'C');
            if ($pribadi1->titel_belakang != '') {
                $pdf->Cell(50, 5, $pribadi1->titel_depan . ' ' . $pribadi1->nama . ', ' . $pribadi1->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi1->titel_belakang == '') {
                $pdf->Cell(50, 5, $pribadi1->titel_depan . ' ' . $pribadi1->nama, 'BLR', 0, 'J');
            }
            if ($pribadi1->jk->id == 1) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi1->jk->id != 1) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            if ($pribadi1->jk->id == 2) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi1->jk->id != 2) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, $pribadi1->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Cell(25, 5, 'Pengikut', 'BLR', 0, 'C');
            $pdf->Ln(5);
        }
        if ($bepergian->pengikut_2 != 1) {
            $pdf->SetX(29);
            $pdf->Cell(13, 5, ' 2.', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, $pribadi2->nik, 'BLR', 0, 'C');
            if ($pribadi2->titel_belakang != '') {
                $pdf->Cell(50, 5, $pribadi2->titel_depan . ' ' . $pribadi2->nama . ', ' . $pribadi2->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi2->titel_belakang == '') {
                $pdf->Cell(50, 5, $pribadi2->titel_depan . ' ' . $pribadi2->nama, 'BLR', 0, 'J');
            }
            if ($pribadi2->jk->id == 1) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi2->jk->id != 1) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            if ($pribadi2->jk->id == 2) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi2->jk->id != 2) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, $pribadi2->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Cell(25, 5, 'Pengikut', 'BLR', 0, 'C');
            $pdf->Ln(5);

        }
        if ($bepergian->pengikut_3 != 1) {
            $pdf->SetX(29);
            $pdf->Cell(13, 5, ' 3.', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, $pribadi3->nik, 'BLR', 0, 'C');
            if ($pribadi3->titel_belakang != '') {
                $pdf->Cell(50, 5, $pribadi3->titel_depan . ' ' . $pribadi3->nama . ', ' . $pribadi3->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi3->titel_belakang == '') {
                $pdf->Cell(50, 5, $pribadi3->titel_depan . ' ' . $pribadi3->nama, 'BLR', 0, 'J');
            }
            if ($pribadi3->jk->id == 1) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi3->jk->id != 1) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            if ($pribadi3->jk->id == 2) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi3->jk->id != 2) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, $pribadi3->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Cell(25, 5, 'Pengikut', 'BLR', 0, 'C');
            $pdf->Ln(5);

        }
        if ($bepergian->pengikut_4 != 1) {
            $pdf->SetX(29);
            $pdf->Cell(13, 5, ' 4.', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, $pribadi4->nik, 'BLR', 0, 'C');
            if ($pribadi4->titel_belakang != '') {
                $pdf->Cell(50, 5, $pribadi4->titel_depan . ' ' . $pribadi4->nama . ', ' . $pribadi4->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi4->titel_belakang == '') {
                $pdf->Cell(50, 5, $pribadi4->titel_depan . ' ' . $pribadi4->nama, 'BLR', 0, 'J');
            }
            if ($pribadi4->jk->id == 1) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi4->jk->id != 1) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            if ($pribadi4->jk->id == 2) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi4->jk->id != 2) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, $pribadi4->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Cell(25, 5, 'Pengikut', 'BLR', 0, 'C');
            $pdf->Ln(5);

        }
        if ($bepergian->pengikut_5 != 1) {
            $pdf->SetX(29);
            $pdf->Cell(13, 5, ' 5.', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, $pribadi5->nik, 'BLR', 0, 'C');
            if ($pribadi5->titel_belakang != '') {
                $pdf->Cell(50, 5, $pribadi5->titel_depan . ' ' . $pribadi5->nama . ', ' . $pribadi5->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi5->titel_belakang == '') {
                $pdf->Cell(50, 5, $pribadi5->titel_depan . ' ' . $pribadi5->nama, 'BLR', 0, 'J');
            }
            if ($pribadi5->jk->id == 1) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi5->jk->id != 1) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            if ($pribadi5->jk->id == 2) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi5->jk->id != 2) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, $pribadi5->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Cell(25, 5, 'Pengikut', 'BLR', 0, 'C');
            $pdf->Ln(5);

        }
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Ln(3);
        $pdf->Cell(56, 5, 'Keterangan:', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Ln(4);
        $pdf->SetWidths([5, 176]);
        $pdf->Cell(13);
        $pdf->SetAligns(['', 'J']);
        $pdf->Rowberpergian(['', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan untuk Ijin tinggal kepada Pejabat di Daerah tempat tujuan bepergian, dengan ketentuan:']);
        $pdf->SetWidths([8, 174]);
        $pdf->SetX(21);
        $pdf->Ln(2);
        $pdf->SetWidths([5, 173]);
        $pdf->Cell(18);
        $pdf->SetAligns(['', 'J']);
        $pdf->Rowberpergian(['1.', 'Apabila telah habis masa berlakunya, Surat Keterangan ini paling lambat 1 (satu) hari harus sudah diperbarui, dan dinyatakan tidak berlaku lagi;']);
        $pdf->SetWidths([5, 168]);
        $pdf->Cell(18);
        $pdf->SetAligns(['', 'J']);
        $pdf->Rowberpergian(['2.', 'Penyalahgunaan dan/atau pemberian keterangan palsu kepada pejabat pembuat keterangan ini oleh yang bersangkutan dapat dikenakan tuntutan sesuai dengan peraturan perundang-undangan yang berlaku.']);
        $pdf->Ln(5);
        $pdf->SetX(19);
        $pdf->Row2(['', 'Demikian Surat Keterangan ini dibuat untuk dipergunakan seperlunya.']);

        $pdf->Ln(15);

        if ($bepergian->pejabat_camat_id == 1) {
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
        $hari = substr($bepergian->tanggal, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($bepergian->tanggal, 3, 2) <= 9) {
            $bulan = $indo[substr($bepergian->tanggal, 4, 1)];
        } else {
            $bulan = $indo[substr($bepergian->tanggal, 3, 2)];
        }
        $tahun = substr($bepergian->tanggal, 6, 4);
        $tempatlahir = $hari . ' ' . $bulan . ' ' . $tahun;

        $pdf->SetX(120);
        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir, 0, '', 'C');
        $pdf->Ln(5);
        if ($bepergian->penandatangan == 'Atasnama Pimpinan' || $bepergian->penandatangan == 'Jabatan Struktural') {
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
        if ($bepergian->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($bepergian->jabatan_lainnya);

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
        if ($bepergian->penandatangan != 'Atasnama Pimpinan' && $bepergian->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($bepergian->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($bepergian->penandatangan);
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
            if ($bepergian->penandatangan == 'Pimpinan Organisasi' && $bepergian->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($bepergian->penandatangan);
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
        $tanggal = date('d-m-y');
        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

        $pdf->Output('cetak-data-bepergian-' . $tanggal . '.pdf', 'I');
        exit;
    }
}