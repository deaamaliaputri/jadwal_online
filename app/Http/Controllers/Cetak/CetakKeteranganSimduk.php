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
use App\Domain\Repositories\DataPribadi\OrangTuaNonPendudukRepository;
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
use App\Http\Controllers\Controller;

class CetakKeteranganSimduk extends Controller
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
        DisabilitasPendudukRepository $disabilitasPendudukRepository,
        OrangTuaRepository $orangTuaRepository,
        DokumenPendudukRepository $dokumenPendudukRepository,
        OrangTuaNonPendudukRepository $orangTuaNonPendudukRepository,
        OrganisasiRepository $organisasiRepository

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
        $this->disabilitas = $disabilitasPendudukRepository;
        $this->orangtua = $orangTuaRepository;
        $this->dokumenpenduduk = $dokumenPendudukRepository;
        $this->orangtuanonpenduduk = $orangTuaNonPendudukRepository;
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

    function Kop($pdf, $id, $judul)
    {
        $pdf->AddFont('Times-Roman', '', 'times.php');
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        $pdf->AddPage();
        $pdf->SetFont('Times-Roman', '', 14);
        $desa = $this->desa->find(session('desa'));
        $keteranganpindah = $this->keteraganpindah->find($id);


        $jeniskodeadministrasi = $this->keteraganpindah->cekkodejenisadministrasi($keteranganpindah->jenis_pelayanan_id);
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
        $pdf->Cell(0, 0, $judul, 0, '', 'C');
        $pdf->Ln(5);
        if ($judul != 'SURAT KETERANGAN BIODATA PENDUDUK') {
            $pdf->SetX(25);
            $pdf->SetFont('arial', 'B', 9);
            $pdf->Cell(0, 0, '(' . $keteranganpindah->is_alamat_pindah . ')', 0, '', 'C');
            $pdf->Ln(5);
        }
        $pdf->SetFont('arial', '', 10);
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
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $keteranganpindah->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $keteranganpindah->tahun, 0, '', 'C');

    }

    function Koppengikut($pdf, $id, $judul, $kode)
    {
        $pdf->AddFont('Times-Roman', '', 'times.php');
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        $pdf->AddPage();
        $pdf->SetFont('Times-Roman', '', 14);
        $desa = $this->desa->find(session('desa'));
        $keteranganpindah = $this->keteraganpindah->find($id);


        $jeniskodeadministrasi = $this->keteraganpindah->cekkodejenisadministrasi($keteranganpindah->jenis_pelayanan_id);
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
        $pdf->Cell(0, 0, $judul, 0, '', 'C');
        $pdf->Ln(5);
        if ($judul != 'SURAT KETERANGAN BIODATA PENDUDUK') {
            $pdf->SetX(25);
            $pdf->SetFont('arial', 'B', 9);
            $pdf->Cell(0, 0, '(' . $keteranganpindah->is_alamat_pindah . ')', 0, '', 'C');
            $pdf->Ln(5);
        }
        $pdf->SetFont('arial', '', 10);
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
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $keteranganpindah->no_reg . '.' . $kode . '/' . $kodeadministrasikearsipanhasil . '/' . $keteranganpindah->tahun, 0, '', 'C');

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
//        dump($pdf);
        //Disable automatic page break
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('Surat Keterangan Keteragan Pindah');
        $judul = 'SURAT KETERANGAN PINDAH TEMPAT';

        $keteranganpindah = $this->keteraganpindah->find($id);
        $this->Kop($pdf, $id, $judul);
        $pdf->SetY(67);
        $desa = $this->desa->find(session('desa'));
        $cekkkkeluarga = $this->keluarga->ceknikkeluarga($keteranganpindah->nik_pemohon);
        $pribadi1 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut1);
        $pribadi2 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut2);
        $pribadi3 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut3);
        $pribadi4 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut4);
        $pribadi5 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut5);
        $pribadi6 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut6);
        $pribadi7 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut7);
        $pribadi8 = $this->pribadi->ceknikcetak($keteranganpindah->nik_pengikut8);

        if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
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
        if ($keteranganpindah->desa_tujuan->kecamatan->kabupaten->status == 1) {
            $statustujuan = 'Kabupaten';
            $kabupatentujuan = $keteranganpindah->desa_tujuan->kecamatan->kabupaten->kabupaten;
        }
        if ($keteranganpindah->desa_tujuan->kecamatan->kabupaten->status == 2) {
            $statustujuan = 'Kota';
            $kabupatentujuan = $keteranganpindah->desa_tujuan->kecamatan->kabupaten->kabupaten;
        }
        //kecamatan
        if ($keteranganpindah->desa_tujuan->kecamatan->status == 1) {

            $statuskecamatantujuan = 'Kecamatan';
            $kecamatantujuan = $keteranganpindah->desa_tujuan->kecamatan->kecamatan;
        }
        if ($keteranganpindah->desa_tujuan->kecamatan->kabupaten->status == 2) {
            $statuskecamatantujuan = 'Distrik';
            $kecamatantujuan = $keteranganpindah->desa_tujuan->kecamatan->kecamatan;
        }
        //desa
        if ($keteranganpindah->desa_tujuan->status == 1) {
            $statusdesatujuan = 'Kelurahan';
            $namadesatujuan = $keteranganpindah->desa_tujuan->desa;
        }
        if ($keteranganpindah->desa_tujuan->status == 2) {

            $statusdesatujuan = 'Desa';
            $namadesatujuan = $keteranganpindah->desa_tujuan->desa;
        }
        if ($keteranganpindah->desa_tujuan->status == 3) {

            $statusdesatujuan = 'Kampung';
            $namadesatujuan = $keteranganpindah->desa_tujuan->desa;
        }
        if ($keteranganpindah->desa_tujuan->status == 4) {

            $statusdesatujuan = 'Negeri';
            $namadesatujuan = $keteranganpindah->desa_tujuan->desa;
        }
        if ($keteranganpindah->penandatangan == 'Pimpinan Organisasi') {
            $pejabat2 = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);

            if ($pejabat2 != null) {
                $jabatanpimpinan = $pejabat2->jabatan;
            } else {
                $jabatanpimpinan = '';
            }

            $tampiljabatan = $jabatanpimpinan;

        } else if ($keteranganpindah->penandatangan == 'Sekretaris Organisasi') {
            $pejabat2 = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
            if ($pejabat2 != null) {
                $jabatansekretaris = $pejabat2->jabatan;
            } else {
                $jabatansekretaris = '';
            }

            $tampiljabatan = $jabatansekretaris;

        } else if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan') {
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

        } else if ($keteranganpindah->penandatangan == 'Jabatan Struktural') {
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

            $pejabat3 = $this->pejabat->find($keteranganpindah->jabatan_lainnya);

            $jabatanstruktural = $pejabat3->jabatan;
            $tampiljabatan = $jabatanstruktural . ' untuk beliau' . $jabatansekretaris . $jabatanpimpinan;
        }
        $pdf->Ln(2);
        $pdf->SetWidths([5, 180]);
        $pdf->Cell(4);
        $pdf->Row2(['', '                  Yang bertanda tangan di bawah ini ' . $tampiljabatan . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten . ' dengan ini menerangkan bahwa:']);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(35);
        $pdf->Ln(7);
        $cekpribadi = $this->pribadi->ceknikcetak($keteranganpindah->nik_pemohon);
        $keluarga = $this->keluarga->cekalamat($cekpribadi->id);
        if ($cekpribadi->titel_belakang != '') {

            $namalengkap = $cekpribadi->titel_depan . ' ' . $cekpribadi->nama . ', ' . $cekpribadi->titel_belakang;
        }
        if ($cekpribadi->titel_belakang == '') {

            $namalengkap = $cekpribadi->titel_depan . ' ' . $cekpribadi->nama . '' . $cekpribadi->titel_belakang;
        }
        $hari = substr($cekpribadi->tanggal_lahir, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($cekpribadi->tanggal_lahir, 3, 2) <= 9) {
            $bulan = $indo[substr($cekpribadi->tanggal_lahir, 4, 1)];
        } else {
            $bulan = $indo[substr($cekpribadi->tanggal_lahir, 3, 2)];
        }
        $tahun = substr($cekpribadi->tanggal_lahir, 6, 4);
        $tempatlahir = $cekpribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
        $tanggallahir = $hari . ' ' . $bulan . ' ' . $tahun;
        if ($cekpribadi->jk->id == 1) {
            $jk1 = 'x';
            $jk2 = '';
        }
        if ($cekpribadi->jk->id == 2) {
            $jk1 = '';
            $jk2 = 'x';
        }

        if ($cekpribadi->agama->id == 1) {
            $islam = 'x';
            $kristen = '';
            $khatolik = '';
            $hindu = '';
            $budha = '';
            $lainnya = '';
        }
        if ($cekpribadi->agama->id == 2) {
            $islam = '';
            $kristen = 'x';
            $khatolik = '';
            $hindu = '';
            $budha = '';
            $lainnya = '';
        }
        if ($cekpribadi->agama->id == 3) {
            $islam = '';
            $kristen = '';
            $khatolik = 'x';
            $hindu = '';
            $budha = '';
            $lainnya = '';
        }
        if ($cekpribadi->agama->id == 4) {
            $islam = '';
            $kristen = '';
            $khatolik = '';
            $hindu = 'x';
            $budha = '';
            $lainnya = '';
        }
        if ($cekpribadi->agama->id == 5) {
            $islam = '';
            $kristen = '';
            $khatolik = '';
            $hindu = '';
            $budha = 'x';
            $lainnya = '';
        }
        if ($cekpribadi->agama->id == 6) {
            $islam = '';
            $kristen = '';
            $khatolik = '';
            $hindu = '';
            $budha = '';
            $lainnya = 'x';
        }
        if ($cekpribadi->agama->id == 7) {
            $islam = '';
            $kristen = '';
            $khatolik = '';
            $hindu = '';
            $budha = '';
            $lainnya = 'x';
        }
        if ($cekpribadi->perkawinan->id == 1) {
            $belumkawin = 'x';
            $kawin = '';
            $ceraihidup = '';
            $ceraimati = '';
        }
        if ($cekpribadi->perkawinan->id == 2) {
            $belumkawin = '';
            $kawin = 'x';
            $ceraihidup = '';
            $ceraimati = '';
        }
        if ($cekpribadi->perkawinan->id == 3) {
            $belumkawin = '';
            $kawin = '';
            $ceraihidup = 'x';
            $ceraimati = '';
        }
        if ($cekpribadi->perkawinan->id == 4) {
            $belumkawin = '';
            $kawin = '';
            $ceraihidup = '';
            $ceraimati = 'x';
        }
        if ($cekpribadi->pendidikan->id == 1) {
            $sd = '';
            $sltp = '';
            $slta = '';
            $sarjana = '';
            $tidakataubelumsekolah = 'x';
            $tidaktamatsd = '';
        }
        if ($cekpribadi->pendidikan->id == 2) {
            $sd = '';
            $sltp = '';
            $slta = '';
            $sarjana = '';
            $tidakataubelumsekolah = '';
            $tidaktamatsd = 'x';
        }
        if ($cekpribadi->pendidikan->id == 3) {
            $sd = 'x';
            $sltp = '';
            $slta = '';
            $sarjana = '';
            $tidakataubelumsekolah = '';
            $tidaktamatsd = '';
        }
        if ($cekpribadi->pendidikan->id == 4) {
            $sd = '';
            $sltp = 'x';
            $slta = '';
            $sarjana = '';
            $tidakataubelumsekolah = '';
            $tidaktamatsd = '';
        }
        if ($cekpribadi->pendidikan->id == 5) {
            $sd = '';
            $sltp = '';
            $slta = 'x';
            $sarjana = '';
            $tidakataubelumsekolah = '';
            $tidaktamatsd = '';
        }
        if ($cekpribadi->pendidikan->id >= 6) {
            $sd = '';
            $sltp = '';
            $slta = '';
            $sarjana = 'x';
            $tidakataubelumsekolah = '';
            $tidaktamatsd = '';
        }
        $perkawinanan = $cekpribadi->perkawinan->kawin;
        if ($cekpribadi->pekerjaan_id == 89) {
            $pekerjaan = $cekpribadi->pekerjaan_lain->pekerjaan_lain;
        } else {
            $pekerjaan = $cekpribadi->pekerjaan->pekerjaan;
        }
        $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
        $alamattujuan = $keteranganpindah->alamat_tujuan . ' RT. ' . $keteranganpindah->rt_tujuan . ' RW. ' . $keteranganpindah->rw_tujuan;
        if ($keteranganpindah->nik_pengikut2 != '--' && $keteranganpindah->nik_pengikut3 != '--' && $keteranganpindah->nik_pengikut4 != '--' && $keteranganpindah->nik_pengikut5 != '--' && $keteranganpindah->nik_pengikut6 != '--' && $keteranganpindah->nik_pengikut7 != '--' && $keteranganpindah->nik_pengikut8 != '--') {
            $totalpengikut = 8 . ' orang pengikut, yaitu:';
        }
        if ($keteranganpindah->nik_pengikut2 != '--' && $keteranganpindah->nik_pengikut3 != '--' && $keteranganpindah->nik_pengikut4 != '--' && $keteranganpindah->nik_pengikut5 != '--' && $keteranganpindah->nik_pengikut6 != '--' && $keteranganpindah->nik_pengikut7 != '--' && $keteranganpindah->nik_pengikut8 == '--') {
            $totalpengikut = 7 . ' orang pengikut, yaitu:';
        }
        if ($keteranganpindah->nik_pengikut2 != '--' && $keteranganpindah->nik_pengikut3 != '--' && $keteranganpindah->nik_pengikut4 != '--' && $keteranganpindah->nik_pengikut5 != '--' && $keteranganpindah->nik_pengikut6 != '--' && $keteranganpindah->nik_pengikut7 == '--' && $keteranganpindah->nik_pengikut8 == '--') {
            $totalpengikut = 6 . ' orang pengikut, yaitu:';
        }
        if ($keteranganpindah->nik_pengikut2 != '--' && $keteranganpindah->nik_pengikut3 != '--' && $keteranganpindah->nik_pengikut4 != '--' && $keteranganpindah->nik_pengikut5 != '--' && $keteranganpindah->nik_pengikut6 == '--' && $keteranganpindah->nik_pengikut7 == '--' && $keteranganpindah->nik_pengikut8 == '--') {
            $totalpengikut = 5 . ' orang pengikut, yaitu:';
        }
        if ($keteranganpindah->nik_pengikut2 != '--' && $keteranganpindah->nik_pengikut3 != '--' && $keteranganpindah->nik_pengikut4 != '--' && $keteranganpindah->nik_pengikut5 == '--') {
            $totalpengikut = 4 . ' orang pengikut, yaitu:';
        }
        if ($keteranganpindah->nik_pengikut2 != '--' && $keteranganpindah->nik_pengikut3 != '--' && $keteranganpindah->nik_pengikut4 == '--' && $keteranganpindah->nik_pengikut5 == '--') {
            $totalpengikut = 3 . ' orang pengikut, yaitu:';
        }
        if ($keteranganpindah->nik_pengikut2 != '--' && $keteranganpindah->nik_pengikut3 == '--' && $keteranganpindah->nik_pengikut4 == '--' && $keteranganpindah->nik_pengikut5 == '--') {
            $totalpengikut = 2 . ' orang pengikut, yaitu:';
        }
        if ($keteranganpindah->nik_pengikut2 == '--' && $keteranganpindah->nik_pengikut3 == '--' && $keteranganpindah->nik_pengikut4 == '--' && $keteranganpindah->nik_pengikut5 == '--') {
            $totalpengikut = 1 . ' orang pengikut, yaitu:';
        }
        if ($keteranganpindah->nik_pengikut1 == '--' && $keteranganpindah->nik_pengikut2 == '--' && $keteranganpindah->nik_pengikut3 == '--' && $keteranganpindah->nik_pengikut4 == '--' && $keteranganpindah->nik_pengikut5 == '--') {
            $totalpengikut = 'NIHIL';
        }
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '1)    NIK', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':     ' . $keteranganpindah->nik_pemohon, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '2)    Nama Lengkap', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(20);
        $pdf->Cell(0, 0, '3)    Jenis Kelamin ', 0, '', 'L');
        $pdf->SetX(65);
        $pdf->Cell(0, 0, ':', 0, '', 'L');
        $pdf->Ln(-1);
        $pdf->SetX(73);
        $pdf->Cell(3, 3, $jk1, 1, '', 'C');
        $pdf->SetX(83);
        $pdf->Cell(3, 3, 'Laki-Laki', 0, '', 'L');
        $pdf->SetX(133);
        $pdf->Cell(3, 3, $jk2, 1, '', 'C');
        $pdf->SetX(143);
        $pdf->Cell(3, 3, 'Perempuan', 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '4)    Dilahirkan di ', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':      ' . $cekpribadi->tempat_lahir, 0, '', 'L');
        $pdf->SetX(132);
        $pdf->Cell(4, 0, 'Pada Tanggal    :', 0, '', 'L');
        $pdf->SetX(163);
        $pdf->Cell(5, 0, $tanggallahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(132);
        $pdf->Cell(5, 0, 'Umur', 0, '', 'L');
        $pdf->SetX(163);
        $pdf->Cell(5, 0, date('Y') - substr($cekpribadi->tanggal_lahir, 6, 4) . ' Tahun', 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '5)    Kewarganegaraan', 0, '', 'L');
        $pdf->SetX(65);
        $pdf->Cell(0, 0, ':', 0, '', 'L');
        $pdf->Ln(-3);
        $pdf->SetX(73);
        $pdf->Cell(3, 3, 'x', 1, '', 'C');
        $pdf->SetX(83);
        $pdf->Cell(3, 3, 'WNI', 0, '', 'L');
        $pdf->SetX(103);
        $pdf->Cell(3, 3, '', 1, '', 'L');
        $pdf->SetX(113);
        $pdf->Cell(3, 3, 'WNA', 0, '', 'L');
        $pdf->SetX(133);
        $pdf->Cell(65, 4, '', 1, '', 'L');
        $pdf->Ln(7);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '6)    Agama', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':', 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(73);
        $pdf->Cell(3, 3, $islam, 1, '', 'C');
        $pdf->Cell(3, 3, 'Islam', 0, '', 'L');
        $pdf->SetX(90);
        $pdf->Cell(3, 3, $kristen, 1, '', 'C');
        $pdf->Cell(3, 3, 'Kristen', 0, '', 'L');
        $pdf->SetX(110);
        $pdf->Cell(3, 3, $khatolik, 1, '', 'C');
        $pdf->Cell(3, 3, 'Katholik', 0, '', 'L');
        $pdf->SetX(130);
        $pdf->Cell(3, 3, $hindu, 1, '', 'C');
        $pdf->Cell(3, 3, 'Hindu', 0, '', 'L');
        $pdf->SetX(150);
        $pdf->Cell(3, 3, $budha, 1, '', 'C');
        $pdf->Cell(3, 3, 'Budha', 0, '', 'L');
        $pdf->SetX(170);
        $pdf->Cell(3, 3, $lainnya, 1, '', 'C');
        $pdf->Cell(3, 3, 'Lainnya', 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '7)    Status Perkawinan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':', 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(73);
        $pdf->Cell(3, 3, $kawin, 1, '', 'C');
        $pdf->Cell(3, 3, 'Kawin', 0, '', 'L');
        $pdf->SetX(90);
        $pdf->Cell(3, 3, $belumkawin, 1, '', 'C');
        $pdf->Cell(3, 3, 'Belum Kawin', 0, '', 'L');
        $pdf->SetX(120);
        $pdf->Cell(3, 3, $ceraihidup, 1, '', 'C');
        $pdf->Cell(3, 3, 'Cerai Hidup', 0, '', 'L');
        $pdf->SetX(150);
        $pdf->Cell(3, 3, $ceraimati, 1, '', 'C');
        $pdf->Cell(3, 3, 'Cerai Mati', 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '8)    Pekerjaan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':      ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '9)    Pendidikan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':', 0, '', 'L');
        $pdf->Ln(-2);
        $pdf->SetX(73);
        $pdf->Cell(3, 3, $sd, 1, '', 'C');
        $pdf->Cell(3, 3, 'SD', 0, '', 'L');
        $pdf->SetX(90);
        $pdf->Cell(3, 3, $sltp, 1, '', 'C');
        $pdf->Cell(3, 3, 'SLTP', 0, '', 'L');
        $pdf->SetX(120);
        $pdf->Cell(3, 3, $slta, 1, '', 'C');
        $pdf->Cell(3, 3, 'SLTA', 0, '', 'L');
        $pdf->SetX(150);
        $pdf->Cell(3, 3, $sarjana, 1, '', 'C');
        $pdf->Cell(3, 3, 'Diploma/Sarjana', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(73);
        $pdf->Cell(3, 3, $tidakataubelumsekolah, 1, '', 'L');
        $pdf->Cell(3, 3, 'Tidak/Belum Sekolah', 0, '', 'L');
        $pdf->SetX(150);
        $pdf->Cell(3, 3, $tidaktamatsd, 1, '', 'L');
        $pdf->Cell(3, 3, 'TIdak/Belum Tamat SD', 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '10)    Alamat Asal', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':      ' . $alamat, 0, '', 'L');
        $pdf->SetWidths([6, 150]);
        $pdf->Ln(2);
        $pdf->Cell(56);
        $pdf->Rowberpergian(['', $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten]);
        $pdf->Ln(2);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '11)   Nomor Kartu Keluarga', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(120, 0, ':      ' . $cekkkkeluarga->nomor_kk, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '12)   Alamat Tujuan', 0, '', 'L');
        $pdf->Cell(20);
        $pdf->Cell(40, 0, '       Jalan', 0, '', 'L');
        $pdf->Cell(73, 0, ':     ' . $alamattujuan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->Cell(55);
        $pdf->Cell(40, 0, '       Desa/Kelurahan', 0, '', 'L');
        $pdf->Cell(73, 0, ':     ' . $statusdesatujuan . ' ' . $namadesatujuan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->Cell(55);
        $pdf->Cell(40, 0, '       Kecamatan', 0, '', 'L');
        $pdf->Cell(70, 0, ':     ' . $statuskecamatantujuan . ' ' . $kecamatantujuan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->Cell(55);
        $pdf->Cell(40, 0, '       Kode Pos', 0, '', 'L');
        $pdf->Cell(70, 0, ':     ' . $keteranganpindah->kode_pos->kode_pos, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->Cell(55);
        $pdf->Cell(40, 0, '       Kabupaten/Kota', 0, '', 'L');
        $pdf->Cell(70, 0, ':     ' . $statustujuan . ' ' . $kabupatentujuan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->Cell(55);
        $pdf->Cell(40, 0, '       Provinsi', 0, '', 'L');
        $pdf->Cell(70, 0, ':     ' . $keteranganpindah->desa_tujuan->kecamatan->kabupaten->provinsi->provinsi, 0, '', 'L');
        $hari = substr($keteranganpindah->tanggal, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($keteranganpindah->tanggal, 3, 2) <= 9) {
            $bulan = $indo[substr($keteranganpindah->tanggal, 4, 1)];
        } else {
            $bulan = $indo[substr($keteranganpindah->tanggal, 3, 2)];
        }
        $tahun = substr($keteranganpindah->tanggal, 6, 4);
        $tempatlahir = $hari . ' ' . $bulan . ' ' . $tahun;

        $pdf->Ln(4);
        $pdf->Cell(55);
        $pdf->Cell(40, 0, '       Pada Tanggal', 0, '', 'L');
        $pdf->Cell(70, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(19);
        $pdf->Cell(25, 0, '12)    Alasan Pindah Tempat', 0, '', 'L');
        $pdf->Cell(21);
        $pdf->Cell(120, 0, ':     ' . $keteranganpindah->alasan_pindah, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(19);
        $pdf->Cell(25, 0, '13)    Pengikut', 0, '', 'L');
        $pdf->Cell(21);
        $pdf->Cell(120, 0, ':     ' . $totalpengikut, 0, '', 'L');
        $pdf->Ln(2);
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
        if ($keteranganpindah->nik_pengikut1 == '--') {

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
        if ($keteranganpindah->nik_pengikut1 != '--') {

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
        if ($keteranganpindah->nik_pengikut2 != '--') {
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
        if ($keteranganpindah->nik_pengikut3 != '--') {
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
        if ($keteranganpindah->nik_pengikut4 != '--') {
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
        if ($keteranganpindah->nik_pengikut5 != '--') {
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
        if ($keteranganpindah->nik_pengikut6 != '--') {
            $pdf->SetX(29);
            $pdf->Cell(13, 5, ' 6.', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, $pribadi6->nik, 'BLR', 0, 'C');
            if ($pribadi6->titel_belakang != '') {
                $pdf->Cell(50, 5, $pribadi6->titel_depan . ' ' . $pribadi6->nama . ', ' . $pribadi6->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi6->titel_belakang == '') {
                $pdf->Cell(50, 5, $pribadi6->titel_depan . ' ' . $pribadi6->nama, 'BLR', 0, 'J');
            }
            if ($pribadi6->jk->id == 1) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi6->jk->id != 1) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            if ($pribadi6->jk->id == 2) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi6->jk->id != 2) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, $pribadi6->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Cell(25, 5, 'Pengikut', 'BLR', 0, 'C');
            $pdf->Ln(5);

        }
        if ($keteranganpindah->nik_pengikut7 != '--') {
            $pdf->SetX(29);
            $pdf->Cell(13, 5, ' 7.', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, $pribadi7->nik, 'BLR', 0, 'C');
            if ($pribadi7->titel_belakang != '') {
                $pdf->Cell(50, 5, $pribadi7->titel_depan . ' ' . $pribadi7->nama . ', ' . $pribadi7->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi7->titel_belakang == '') {
                $pdf->Cell(50, 5, $pribadi7->titel_depan . ' ' . $pribadi7->nama, 'BLR', 0, 'J');
            }
            if ($pribadi7->jk->id == 1) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi7->jk->id != 1) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            if ($pribadi7->jk->id == 2) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi7->jk->id != 2) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, $pribadi7->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Cell(25, 5, 'Pengikut', 'BLR', 0, 'C');
            $pdf->Ln(5);

        }
        if ($keteranganpindah->nik_pengikut8 != '--') {
            $pdf->SetX(29);
            $pdf->Cell(13, 5, ' 8.', 'BLR', 0, 'C');
            $pdf->Cell(30, 5, $pribadi8->nik, 'BLR', 0, 'C');
            if ($pribadi8->titel_belakang != '') {
                $pdf->Cell(50, 5, $pribadi8->titel_depan . ' ' . $pribadi8->nama . ', ' . $pribadi8->titel_belakang, 'BLR', 0, 'J');
            }
            if ($pribadi8->titel_belakang == '') {
                $pdf->Cell(50, 5, $pribadi8->titel_depan . ' ' . $pribadi8->nama, 'BLR', 0, 'J');
            }
            if ($pribadi8->jk->id == 1) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi8->jk->id != 1) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            if ($pribadi8->jk->id == 2) {
                $pdf->Cell(10, 5, 'x', 1, 0, 'C');
            }
            if ($pribadi8->jk->id != 2) {
                $pdf->Cell(10, 5, '--', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, $pribadi8->shdrt->shdrt, 'BLR', 0, 'C');
            $pdf->Cell(25, 5, 'Pengikut', 'BLR', 0, 'C');
            $pdf->Ln(5);

        }
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(50, 5, 'Catatan:', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Ln(4);
        $pdf->SetWidths([5, 170]);
        $pdf->Cell(13);
        $pdf->SetAligns(['', 'J']);
        $pdf->Rowberpergian(['', 'Untuk WNI dan WNA pada waktu Surat Keterangan Pindah ini diberikan, nama yang bersangkutan pada Kartu Keluarga (KK) dicoret dan Kartu Tanda Penduduk (KTP) dicabut.']);
        $pdf->SetWidths([8, 170]);
        $pdf->SetX(21);
        $pdf->Ln(-1);
        $pdf->SetWidths([5, 165]);
        $pdf->Cell(18);
        $pdf->SetAligns(['', 'J']);
        $pdf->Rowberpergian(['1.', 'Apabila telah habis masa berlakunya, Surat Keterangan ini  paling lambat 1 (satu) hari harus sudah diperbarui, dan dinyatakan tidak berlaku lagi;']);
        $pdf->SetWidths([5, 165]);
        $pdf->Cell(18);
        $pdf->SetAligns(['', 'J']);
        $pdf->Rowberpergian(['2.', 'Penyalahgunaan dan/atau pemberian keterangan palsu kepada pejabat pembuat keterangan ini oleh yang bersangkutan dapat dikenakan tuntutan sesuai dengan peraturan perundang-undangan yang berlaku.']);
        $pdf->Ln(-1);
        $pdf->SetX(19);
        $pdf->Row2(['', 'Demikian Surat Keterangan ini dibuat untuk dipergunakan seperlunya.']);

        $pdf->Ln(2);

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
        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir, 0, '', 'C');
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
        $organisasi = $this->organisasi->find(session('organisasi'));


        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

        $pribadi = $keteranganpindah->nik_pemohon;
        $this->Biodata($pdf, $id, $pribadi, 1);

        if ($keteranganpindah->nik_pengikut1 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut1;
            $this->Biodata($pdf, $id, $pribadi, 2);
        }
        if ($keteranganpindah->nik_pengikut2 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut2;
            $this->Biodata($pdf, $id, $pribadi, 3);
        }
        if ($keteranganpindah->nik_pengikut3 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut3;
            $this->Biodata($pdf, $id, $pribadi, 4);
        }
        if ($keteranganpindah->nik_pengikut4 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut4;
            $this->Biodata($pdf, $id, $pribadi, 5);
        }
        if ($keteranganpindah->nik_pengikut5 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut5;
            $this->Biodata($pdf, $id, $pribadi, 6);
        }
        if ($keteranganpindah->nik_pengikut6 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut6;
            $this->Biodata($pdf, $id, $pribadi, 7);
        }
        if ($keteranganpindah->nik_pengikut7 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut7;
            $this->Biodata($pdf, $id, $pribadi, 8);
        }
        if ($keteranganpindah->nik_pengikut8 != '--') {
            $pribadi = $keteranganpindah->nik_pengikut8;
            $this->Biodata($pdf, $id, $pribadi, 9);
        }

        $tanggal = date('d-m-y');

        //
        //if pengikut biodata
        //


        $pdf->Output('cetak-data-keteraganpindah-' . $tanggal . '.pdf', 'I');
        exit;
    }

    public function Biodata($pdf, $id, $pribadi, $kode)
    {
//        array(215, 330)

        $pdf->AddFont('Arial', '', 'arial.php');
//        dump($pdf);
        //Disable automatic page break
        $pdf->SetTitle('Surat Keterangan Biodata Penduduk');
        $judul = 'SURAT KETERANGAN BIODATA PENDUDUK';
        $this->Koppengikut($pdf, $id, $judul, $kode);
        $pdf->SetY(67);
        $desa = $this->desa->find(session('desa'));
        $keteranganpindah = $this->keteraganpindah->find($id);
        $cekkkkeluarga = $this->keluarga->ceknikkeluarga($pribadi);

        if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
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
        if ($keteranganpindah->desa_tujuan->kecamatan->kabupaten->status == 1) {
            $statustujuan = 'Kabupaten';
            $kabupatentujuan = $keteranganpindah->desa_tujuan->kecamatan->kabupaten->kabupaten;
        }
        if ($keteranganpindah->desa_tujuan->kecamatan->kabupaten->status == 2) {
            $statustujuan = 'Kota';
            $kabupatentujuan = $keteranganpindah->desa_tujuan->kecamatan->kabupaten->kabupaten;
        }
        //kecamatan
        if ($keteranganpindah->desa_tujuan->kecamatan->status == 1) {

            $statuskecamatantujuan = 'Kecamatan';
            $kecamatantujuan = $keteranganpindah->desa_tujuan->kecamatan->kecamatan;
        }
        if ($keteranganpindah->desa_tujuan->kecamatan->kabupaten->status == 2) {
            $statuskecamatantujuan = 'Distrik';
            $kecamatantujuan = $keteranganpindah->desa_tujuan->kecamatan->kecamatan;
        }
        //desa
        if ($keteranganpindah->desa_tujuan->status == 1) {
            $statusdesatujuan = 'Kelurahan';
            $namadesatujuan = $keteranganpindah->desa_tujuan->desa;
        }
        if ($keteranganpindah->desa_tujuan->status == 2) {

            $statusdesatujuan = 'Desa';
            $namadesatujuan = $keteranganpindah->desa_tujuan->desa;
        }
        if ($keteranganpindah->desa_tujuan->status == 3) {

            $statusdesatujuan = 'Kampung';
            $namadesatujuan = $keteranganpindah->desa_tujuan->desa;
        }
        if ($keteranganpindah->desa_tujuan->status == 4) {

            $statusdesatujuan = 'Negeri';
            $namadesatujuan = $keteranganpindah->desa_tujuan->desa;
        }
        if ($keteranganpindah->penandatangan == 'Pimpinan Organisasi') {
            $pejabat2 = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);

            if ($pejabat2 != null) {
                $jabatanpimpinan = $pejabat2->jabatan;
            } else {
                $jabatanpimpinan = '';
            }

            $tampiljabatan = $jabatanpimpinan;

        } else if ($keteranganpindah->penandatangan == 'Sekretaris Organisasi') {
            $pejabat2 = $this->pejabat->cekjabatan($keteranganpindah->penandatangan);
            if ($pejabat2 != null) {
                $jabatansekretaris = $pejabat2->jabatan;
            } else {
                $jabatansekretaris = '';
            }

            $tampiljabatan = $jabatansekretaris;

        } else if ($keteranganpindah->penandatangan == 'Atasnama Pimpinan') {
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

        } else if ($keteranganpindah->penandatangan == 'Jabatan Struktural') {
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

            $pejabat3 = $this->pejabat->find($keteranganpindah->jabatan_lainnya);

            $jabatanstruktural = $pejabat3->jabatan;
            $tampiljabatan = $jabatanstruktural . ' untuk beliau' . $jabatansekretaris . $jabatanpimpinan;
        }
        $pdf->Ln(3);
        $pdf->SetWidths([5, 180]);
        $pdf->Cell(5);
        $pdf->Row2(['', '                  Yang bertanda tangan di bawah ini ' . $tampiljabatan . ' ' . $namadesa . ' dengan ini menerangkan bahwa:']);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(35);
        $pdf->Ln(5);
        $cekpribadi = $this->pribadi->ceknikcetak($pribadi);
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
        $tanggallahir = $hari . ' ' . $bulan . ' ' . $tahun;
        $perkawinanan = $cekpribadi->perkawinan->kawin;
        if ($cekpribadi->pekerjaan_id == 89) {
            $pekerjaan = $cekpribadi->pekerjaan_lain->pekerjaan_lain;
        } else {
            $pekerjaan = $cekpribadi->pekerjaan->pekerjaan;
        }
        $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
        $alamattujuan = $keteranganpindah->alamat_tujuan . ' RT. ' . $keteranganpindah->rt_tujuan . ' RW. ' . $keteranganpindah->rw_tujuan;
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '1    Nomor Induk Kependudukan', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . $pribadi, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '2    Alamat Domisili', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->Ln(10);
        $pdf->SetX(20);
        $pdf->Cell(0, 0, '3    Nama Lengkap', 0, '', 'L');
        $pdf->SetX(80);
        if ($cekpribadi->titel_belakang != '') {
            if ($cekpribadi->titel_depan != '') {
                $pdf->Cell(120, 0, ':     ' . $cekpribadi->titel_depan . ' ' . $cekpribadi->nama . ', ' . $cekpribadi->titel_belakang, 0, 0, '');
            }
            if ($cekpribadi->titel_depan == '') {
                $pdf->Cell(120, 0, ':     ' . $cekpribadi->nama . ', ' . $cekpribadi->titel_belakang, 0, 0, '');
            }
        }
        if ($cekpribadi->titel_belakang == '') {
            if ($cekpribadi->titel_depan != '') {
                $pdf->Cell(120, 0, ':     ' . $cekpribadi->titel_depan . ' ' . $cekpribadi->nama, 0, 0, '');

            }
            if ($cekpribadi->titel_depan == '') {
                $pdf->Cell(120, 0, ':     ' . $cekpribadi->nama, 0, 0, '');
            }
        }
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '4    Hubungan Dalam Keluarga', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . $cekpribadi->shdrt->shdrt, 0, '', 'L');
        $pdf->SetX(132);
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '5    No. Akte Pengesahan Anak', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . '--', 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '6    Tempat, Tgl Lahir', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '7    Nomor Akte Kelahiran', 0, '', 'L');
        $pdf->SetX(80);
        $aktekelahiran = $this->dokumenpenduduk->cekdokumenaktekelahiran($cekpribadi->id);
        if ($aktekelahiran == null) {
            $pdf->Cell(120, 0, ':     ' . '--', 0, 0, '');
        }
        if ($aktekelahiran != null) {
            $pdf->Cell(120, 0, ':     ' . $aktekelahiran->nomor_dokumen, 0, 0, '');
        }
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '8    Status Perkawinan', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . $perkawinanan, 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '9    Nomor Akte Kawin/Cerai', 0, '', 'L');
        $pdf->SetX(80);
        $nomoraktecerai = $this->dokumenpenduduk->cekdokumenaktecerai($cekpribadi->id);
        if ($nomoraktecerai == null) {
            $pdf->Cell(120, 0, ':     ' . '--', 0, 0, '');
        }
        if ($nomoraktecerai != null) {

            $pdf->Cell(120, 0, ':     ' . $nomoraktecerai->nomor_dokumen, 0, 0, '');
        }
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '10    Agama', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . $cekpribadi->agama->agama, 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '11    Golongan Darah', 0, '', 'L');
        $pdf->SetX(80);
        if ($cekpribadi->gol_darah_id != 13) {
            $golongandarahpemohon = $cekpribadi->golongan_darah->golongan_darah;
        }
        if ($cekpribadi->gol_darah_id == 13) {
            $golongandarahpemohon = '--';
        }
        $pdf->Cell(120, 0, ':     ' . $golongandarahpemohon, 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '12    Kewarganegaraan', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '13    Nomor SBKRI', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     --', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '14    Nomor SK. Ganti Nama', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     --', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '15    Nama Lama', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     --', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '16    Nomor SKPPT', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     --', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '17    Nomor Induk Orang Asing', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     --', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '18    Pendidikan Terakhir', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . $cekpribadi->pendidikan->pendidikan, 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '19    Pekerjaaan', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '20    Tempat Tinggal Terakhir', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     --', 0, 0, '');
        $pdf->Ln(10);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '21    Kelainan Khusus', 0, '', 'L');
        $pdf->SetX(80);
        $disabilitasview = $this->disabilitas->ceknikdisabilitas($keteranganpindah->nik_pemohon);
        if ($disabilitasview == null) {
            $disabilitas = '--';
        }
        if ($disabilitasview != null) {
            $disabilitas = $disabilitasview->disabilitas->disabilitas;
        }
        $pdf->Cell(120, 0, ':     ' . $disabilitas, 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '22    Akseptor KB', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     --', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '23    Nama Lengkap Ibu', 0, '', 'L');
        $pdf->SetX(80);
        $nikibu = $this->orangtua->cekorangtuaibu($cekpribadi->id);
        if ($nikibu != null) {
            if ($nikibu->titel_belakang != '') {
                if ($nikibu->titel_depan != '') {
                    $pdf->Cell(120, 0, ':     1' . $nikibu->titel_depan . ' ' . $nikibu->nama . ', ' . $nikibu->titel_belakang, 0, 0, '');
                }
                if ($nikibu->titel_depan == '') {
                    $pdf->Cell(120, 0, ':     ' . $nikibu->nama . ', ' . $nikibu->titel_belakang, 0, 0, '');
                }
            }
            if ($nikibu->titel_belakang == '') {
                if ($nikibu->titel_depan != '') {
                    $pdf->Cell(120, 0, ':     ' . $nikibu->titel_depan . ' ' . $nikibu->nama, 0, 0, '');

                }
                if ($nikibu->titel_depan == '') {
                    $pdf->Cell(120, 0, ':     ' . $nikibu->nama, 0, 0, '');
                }
            }
        }
        if ($nikibu == null) {
            $pdf->Cell(120, 0, ':     ' . '--', 0, 0, '');
        }
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '24    N I K Ibu', 0, '', 'L');
        $pdf->SetX(80);
        $nikibu = $this->orangtua->cekorangtuaibu($cekpribadi->id);
        if ($nikibu->nik != '') {
            $pdf->Cell(120, 0, ':     ' . $nikibu->nik, 0, 0, '');
        }
        if ($nikibu->nik == '') {
            $pdf->Cell(120, 0, ':     ' . '--', 0, 0, '');
        }
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '25    Kewarganegaraan Ibu', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     ' . 'Warga Negara Indonesia', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '26    Tempat Tinggal Ibu', 0, '', 'L');
        $pdf->SetX(80);
        if ($nikibu->status_orang_tua == 1 || $nikibu->status_orang_tua == 0) {
            $alamatwilayah = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
            $alamatdomisili = $alamat;

        }

        // if orang tua non penduduk
        if ($nikibu->status_orang_tua == 2) {
            $nikibunonpenduduk = $this->orangtuanonpenduduk->cekorangtuacetak($nikibu->id);
            if ($nikibunonpenduduk != null) {
                if ($nikibunonpenduduk->desa->kecamatan->kabupaten->status == 1) {
                    $status1 = 'Kabupaten';
                    $kabupaten1 = $nikibunonpenduduk->desa->kecamatan->kabupaten->kabupaten;
                }
                if ($nikibunonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                    $status1 = 'Kota';
                    $kabupaten1 = $nikibunonpenduduk->desa->kecamatan->kabupaten->kabupaten;
                }
                //kecamatan
                if ($nikibunonpenduduk->desa->kecamatan->status == 1) {
                    $statuskecamatan1 = 'Kecamatan';
                    $kecamatan1 = $nikibunonpenduduk->desa->kecamatan->kecamatan;
                }
                if ($nikibunonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                    $statuskecamatan1 = 'Distrik';
                    $kecamatan1 = $nikibunonpenduduk->desa->kecamatan->kecamatan;
                }
                //desa
                if ($nikibunonpenduduk->desa->status == 1) {
                    $statusdesa1 = 'Kelurahan';
                    $namadesa1 = $nikibunonpenduduk->desa->desa;
                }
                if ($nikibunonpenduduk->desa->status == 2) {
                    $statusdesa1 = 'Desa';
                    $namadesa1 = $nikibunonpenduduk->desa->desa;
                }
                if ($nikibunonpenduduk->desa->status == 3) {
                    $statusdesa1 = 'Kampung';
                    $namadesa1 = $nikibunonpenduduk->desa->desa;
                }
                if ($nikibunonpenduduk->desa->status == 4) {
                    $statusdesa1 = 'Negeri';
                    $namadesa1 = $nikibunonpenduduk->desa->desa;
                }
            }
            $alamatwilay1 = $statusdesa1 . ' ' . $namadesa1 . ' ' . $statuskecamatan1 . ' ' . $kecamatan1 . ' ' . $status1 . ' ' . $kabupaten1;
            $alamatdomisili = $nikibunonpenduduk->alamat . ' RT. ' . $nikibunonpenduduk->alamat_rt . ' RW. ' . $nikibunonpenduduk->alamat_rw;

            if ($nikibunonpenduduk == null) {
                $alamatwilay1 = '--';
                $alamatdomisili = '--';
            }
            $alamatwilayah = $alamatwilay1;

        }
        $pdf->Cell(120, 0, ':     ' . $alamatdomisili, 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(80);
        $pdf->Cell(120, 0, '       ' . $alamatwilayah, 0, 0, '');

        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '27    Nama Lengkap Bapak', 0, '', 'L');
        $pdf->SetX(80);
        $nikbapak = $this->orangtua->cekorangtuabapak($cekpribadi->id);
        if ($nikbapak != null) {
            if ($nikbapak->titel_belakang != '') {
                if ($nikbapak->titel_depan != '') {
                    $pdf->Cell(120, 0, ':     ' . $nikbapak->titel_depan . ' ' . $nikbapak->nama . ', ' . $nikbapak->titel_belakang, 0, 0, '');
                }
                if ($nikbapak->titel_depan == '') {
                    $pdf->Cell(120, 0, ':     ' . $nikbapak->nama . ', ' . $nikbapak->titel_belakang, 0, 0, '');
                }
            }
            if ($nikbapak->titel_belakang == '') {
                if ($nikbapak->titel_depan != '') {
                    $pdf->Cell(120, 0, ':     ' . $nikbapak->titel_depan . ' ' . $nikbapak->nama, 0, 0, '');

                }
                if ($nikbapak->titel_depan == '') {
                    $pdf->Cell(120, 0, ':     ' . $nikbapak->nama, 0, 0, '');
                }
            }
        }
        if ($nikbapak == null) {
            $pdf->Cell(120, 0, ':     ' . '--', 0, 0, '');
        }
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '28    N I K Ayah', 0, '', 'L');
        $pdf->SetX(80);
        if ($nikbapak != null) {
            if ($nikbapak->nik != null) {
                $pdf->Cell(120, 0, ':     ' . $nikbapak->nik, 0, 0, '');
            }
            if ($nikbapak->nik == null) {
                $pdf->Cell(120, 0, ':     ' . '--', 0, 0, '');
            }
        }
        if ($nikbapak == null) {
            $pdf->Cell(120, 0, ':     ' . '--', 0, 0, '');
        }
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '29    Kewarganegaraan', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, 0, '');

        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '30    Tempat Tinggal Ayah', 0, '', 'L');
        $pdf->SetX(80);
        if ($nikbapak->status_orang_tua == 1 || $nikbapak->status_orang_tua == 0) {
            $alamatwilayah = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
            $alamatdomisili = $alamat;

        }

        // if orang tua non penduduk
        if ($nikbapak->status_orang_tua == 2) {
            $nikbapaknonpenduduk = $this->orangtuanonpenduduk->cekorangtuacetak($nikbapak->id);
            if ($nikbapaknonpenduduk != null) {
                if ($nikbapaknonpenduduk->desa->kecamatan->kabupaten->status == 1) {
                    $status1 = 'Kabupaten';
                    $kabupaten1 = $nikbapaknonpenduduk->desa->kecamatan->kabupaten->kabupaten;
                }
                if ($nikbapaknonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                    $status1 = 'Kota';
                    $kabupaten1 = $nikbapaknonpenduduk->desa->kecamatan->kabupaten->kabupaten;
                }
                //kecamatan
                if ($nikbapaknonpenduduk->desa->kecamatan->status == 1) {
                    $statuskecamatan1 = 'Kecamatan';
                    $kecamatan1 = $nikbapaknonpenduduk->desa->kecamatan->kecamatan;
                }
                if ($nikbapaknonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                    $statuskecamatan1 = 'Distrik';
                    $kecamatan1 = $nikbapaknonpenduduk->desa->kecamatan->kecamatan;
                }
                //desa
                if ($nikbapaknonpenduduk->desa->status == 1) {
                    $statusdesa1 = 'Kelurahan';
                    $namadesa1 = $nikbapaknonpenduduk->desa->desa;
                }
                if ($nikbapaknonpenduduk->desa->status == 2) {
                    $statusdesa1 = 'Desa';
                    $namadesa1 = $nikbapaknonpenduduk->desa->desa;
                }
                if ($nikbapaknonpenduduk->desa->status == 3) {
                    $statusdesa1 = 'Kampung';
                    $namadesa1 = $nikbapaknonpenduduk->desa->desa;
                }
                if ($nikbapaknonpenduduk->desa->status == 4) {
                    $statusdesa1 = 'Negeri';
                    $namadesa1 = $nikbapaknonpenduduk->desa->desa;
                }
            }
            $alamatwilay1 = $statusdesa1 . ' ' . $namadesa1 . ' ' . $statuskecamatan1 . ' ' . $kecamatan1 . ' ' . $status1 . ' ' . $kabupaten1;
            $alamatdomisili = $nikbapaknonpenduduk->alamat . ' RT. ' . $nikbapaknonpenduduk->alamat_rt . ' RW. ' . $nikbapaknonpenduduk->alamat_rw;

            if ($nikbapaknonpenduduk == null) {
                $alamatwilay1 = '--';
                $alamatdomisili = '--';
            }
            $alamatwilayah = $alamatwilay1;

        }
        $pdf->Cell(120, 0, ':     ' . $alamatdomisili, 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(80);
        $pdf->Cell(120, 0, '       ' . $alamatwilayah, 0, 0, '');


        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '31    Keterangan', 0, '', 'L');
        $pdf->SetX(80);
        if ($keteranganpindah->penggunaan_surat != '') {
            $pdf->Cell(120, 0, ':     ' . $keteranganpindah->penggunaan_surat, 0, 0, '');
        }
        if ($keteranganpindah->penggunaan_surat == '') {
            $pdf->Cell(120, 0, ':     --', 0, 0, '');
        }
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(25, 0, '32    E.T.', 0, '', 'L');
        $pdf->SetX(80);
        $pdf->Cell(120, 0, ':     --', 0, 0, '');
        $pdf->Ln(5);
        $pdf->SetX(15);
        $pdf->Row2(['', 'Demikian Surat Keterangan ini dibuat untuk dipergunakan seperlunya.']);

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
        $pdf->Cell(0, 10, $namadesa . ', ' . $tanggallahir, 0, '', 'C');
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
        $tanggal = date('d-m-y');

        $organisasi = $this->organisasi->find(session('organisasi'));


        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }
//        $pdf->Output('cetak-data-keteraganpindah-' . $tanggal . '.pdf', 'I');
//        exit;
    }


}