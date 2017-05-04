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
use App\Domain\Repositories\Pelayanan\SatuOrangRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakSatuOrang extends Controller
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
        SatuOrangRepository $satuOrangRepository,
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
        OrganisasiRepository $organisasiRepository
    )
    {
        $this->satuorang = $satuOrangRepository;
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
        $satuorang = $this->satuorang->find($id);
        $jeniskodeadministrasi = $this->satuorang->cekkodejenisadministrasi($satuorang->jenis_pelayanan_id);
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
        $pdf->Ln(20);
        $pdf->SetFont('arial', 'BU', 14);
        $pdf->SetX(25);
        if ($satuorang->is_penduduk_layan != null) {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN' . ' ' . strtoupper($satuorang->is_penduduk_layan), 0, '', 'C');

        } else {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN', 0, '', 'C');

        }
        $pdf->Ln(5);
        $pdf->SetFont('arial', '', 10);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($satuorang->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($satuorang->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($satuorang->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $satuorang->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $satuorang->tahun, 0, '', 'C');

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

    public function SatuOrang($id)
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
        $pdf->SetTitle('Surat Satu Orang');
        $this->Kop($pdf, $id);
        $pdf->SetY(80);
        $desa = $this->desa->find(session('desa'));
        $satuorang = $this->satuorang->find($id);
        if ($satuorang->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($satuorang->penandatangan);
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
            if ($satuorang->penandatangan == 'Jabatan Struktural') {
                $pejabatstruktural2 = $this->pejabat->find($satuorang->jabatan_lainnya);
                if ($pejabatstruktural2->keterangan != '') {

                    $namajabatan = $pejabatstruktural2->keterangan . ' ' . $pejabatstruktural2->jabatan;
                }
                if ($pejabatstruktural2->keterangan == '') {
                    $namajabatan = $pejabatstruktural2->jabatan;
                }
            }
            if ($satuorang->penandatangan != 'Jabatan Struktural') {

                if ($pejabat->keterangan != '') {
                    $namajabatan = $pejabat->keterangan . ' ' . $pejabat->jabatan;
                }
                if ($pejabat->keterangan == '') {
                    $namajabatan = $pejabat->jabatan;
                }
            }
        }

        //nama pejabat

        $pdf->SetX(19);
        $pdf->Cell(25, -15, 'a.     Nama ', 0, '', 'L');
        $pdf->Cell(19);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, -15, '' . $namalengkappejabat, 0, '', 'L');

        // jabatan pejabat

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(19);
        $pdf->Cell(25, -15, 'b.     Jabatan ', 0, '', 'L');
        $pdf->Cell(19);
        if ($satuorang->penandatangan == 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $statusdesa1 . ' ' . $namadesa, 0, '', 'L');
        }
        if ($satuorang->penandatangan != 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $namadesa, 0, '', 'L');
        }
        $pdf->Ln(5);
        $pdf->SetX(19);
        $pdf->Cell(0, -15, 'dengan ini menerangkan bahwa:', 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(19);
        $pdf->Cell(7.5, -15, '1.', 0, '', 'L');
        $pdf->Cell(0, -15, 'Orang sebagaimana tersebut: ', 0, '', 'L');

        // nik pejabat

        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $satuorang->nik, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Nama Lengkap ', 0, '', 'L');

        // jika penduduk

        $penduduklain = $this->penduduklain->cekpenduduklaincetak($satuorang->pribadi->id);
        $keluarga = $this->keluarga->cekalamat($satuorang->pribadi->id);
        if ($satuorang->pribadi->titel_belakang != '') {
            $namalengkap = $satuorang->pribadi->titel_depan . ' ' . $satuorang->pribadi->nama . ', ' . $satuorang->pribadi->titel_belakang;
        }
        if ($satuorang->pribadi->titel_belakang == '') {
            $namalengkap = $satuorang->pribadi->titel_depan . ' ' . $satuorang->pribadi->nama . '' . $satuorang->pribadi->titel_belakang;
        }
        $hari = substr($satuorang->pribadi->tanggal_lahir, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($satuorang->pribadi->tanggal_lahir, 3, 2) <= 9) {
            $bulan = $indo[substr($satuorang->pribadi->tanggal_lahir, 4, 1)];
        } else {
            $bulan = $indo[substr($satuorang->pribadi->tanggal_lahir, 3, 2)];
        }
        $tahun = substr($satuorang->pribadi->tanggal_lahir, 6, 4);
        $tempatlahir = $satuorang->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
        $jk = $satuorang->pribadi->jk->jk;

        if ($satuorang->pribadi->gol_darah_id != 13) {
            $golongandarah = $satuorang->pribadi->golongan_darah->golongan_darah;
        }
        if ($satuorang->pribadi->gol_darah_id == 13) {
            $golongandarah = '--';
        }
        $agama = $satuorang->pribadi->agama->agama;
        $perkawinanan = $satuorang->pribadi->perkawinan->kawin;
        if ($satuorang->pribadi->pekerjaan_id == 89) {
            $pekerjaan = $satuorang->pribadi->pekerjaan_lain->pekerjaan_lain;
        } else {
            $pekerjaan = $satuorang->pribadi->pekerjaan->pekerjaan;
        }
        if ($penduduklain != null) {
            $kewarganegaraan = 'Kewarganegaraan ' . $penduduklain->penduduk_lain;
        } else {
            $kewarganegaraan = 'Warga Negara Indonesia';
        }
        $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
        $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;

        if ($satuorang->dasar_keterangan_jenis == '') {
            $keterangan = 'menurut data, catatan dan keterangan yang bersangkutan';
        } else {
            $keterangan = 'menurut ' . $satuorang->dasar_keterangan_jenis;
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
        $pdf->Ln(2);
        if ($satuorang->beda_data1 != '--') {
            if ($satuorang->beda_data1 != 'Lainnya') {
                $beda1 = $satuorang->beda_data1 . ' seharusnya: ';
            }
            if ($satuorang->beda_data1 == 'Lainnya') {
                $beda1 = $satuorang->beda_data1lainnya . ' seharusnya: ';
            }
        }
        if ($satuorang->beda_data1 == '--') {
            $beda1 = '';
        }
        if ($satuorang->data_seharusnya1 != '--') {
            $seharusnya1 = $satuorang->data_seharusnya1;
        }
        if ($satuorang->data_seharusnya1 == '--') {
            $seharusnya1 = '';
        }

        if ($satuorang->beda_data2 != '--') {
            if ($satuorang->beda_data2 != 'Lainnya') {
                $beda2 = $satuorang->beda_data2 . ' seharusnya: ';
            }
            if ($satuorang->beda_data2 == 'Lainnya') {
                $beda2 = $satuorang->beda_data2lainnya . ' seharusnya: ';
            }
        }
        if ($satuorang->beda_data2 == '--') {
            $beda2 = '';
        }
        if ($satuorang->data_seharusnya2 != '--') {
            $seharusnya2 = $satuorang->data_seharusnya2;
        }
        if ($satuorang->data_seharusnya2 == '--') {
            $seharusnya2 = '';
        }
        if ($satuorang->beda_data3 != '--') {
            if ($satuorang->beda_data3 != 'Lainnya') {
                $beda3 = $satuorang->beda_data3;
            }
            if ($satuorang->beda_data3 == 'Lainnya') {
                $beda3 = $satuorang->beda_data3lainnya;
            }
        }
        if ($satuorang->beda_data3 == '--') {
            $beda3 = '';
        }
        if ($satuorang->data_seharusnya3 != '--') {
            $seharusnya3 = $satuorang->data_seharusnya3;
        }
        if ($satuorang->data_seharusnya3 == '--') {
            $seharusnya3 = '';
        }
        $pdf->SetAligns(['J', 'J']);
        $pembeda = $beda1 . ' ' . $seharusnya1 . ', ' . $beda2 . ' ' . $seharusnya2 . ', ' . $beda3 . ' seharusnya: ' . $seharusnya3;

        if ($satuorang->dokumen_pembeda_jenis == 'Lainnya') {
//            $pdf->SetFont('Arial', 'B', 10);
            $dokumen = $satuorang->dokumen_pembeda_jenis_lain;
//            $pdf->SetFont('Arial', '', 10);
        }
        if ($satuorang->dokumen_pembeda_jenis != 'Lainnya') {
            $dokumen = $satuorang->dokumen_pembeda_jenis;

        }
//        dump($dokumen);
        if ($satuorang->nomor_dokumen != '') {
            $nomordokumen = 'Nomor: ' . $satuorang->nomor_dokumen;
        }
        if ($satuorang->nomor_dokumen == '') {
            $nomordokumen = '';
        }
        if ($satuorang->dikeluarkan != '') {
            $dikeluarkan = 'yang dikeluarkan oleh ' . $satuorang->dikeluarkan;
        }
        if ($satuorang->dikeluarkan == '') {
            $dikeluarkan = '';
        }
        if ($satuorang->tanggal_keluar != '') {
            $hari4 = substr($satuorang->tanggal_keluar, 0, 2);
            $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($satuorang->tanggal_keluar, 3, 2) <= 9) {
                $bulan4 = $indo3[substr($satuorang->tanggal_keluar, 4, 1)];
            } else {
                $bulan4 = $indo3[substr($satuorang->tanggal_keluar, 3, 2)];
            }
            $tahun4 = substr($satuorang->tanggal_keluar, 6, 4);
            $tempatlahir4 = ' Tanggal ' . $hari4 . ' ' . $bulan4 . ' ' . $tahun4;

        }
        if ($satuorang->tanggal_keluar == '') {
            $tempatlahir4 = '';

        }
        $pdf->SetWidths([5, 175]);
        $pdf->Cell(12);
        $pdf->Rowberpergian(['', 'menurut data, catatan dan keterangan dalam ' . $dokumen . ' dengan ' . $satuorang->dokumen_banding . ' ' . $nomordokumen . $tempatlahir4 . ' ' . $dikeluarkan . ' diketahui terdapat perbedaan data ' . ($pembeda) . ' yang merupakan Data Pribadi Satu Orang']);
        if ($satuorang->keterangan == '') {
            $pdf->Ln(2);
            // keterangan Surat
            $pdf->SetX(19);
            $pdf->Row2(['2.', '']);
            $pdf->Ln(-4);
            $pdf->SetX(22);
            $pdf->Row2(['', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan mengurus: ' . $satuorang->penggunaan]);
        }
        if ($satuorang->keterangan != '') {
            $pdf->Ln(2);
            $pdf->SetX(19);
            $pdf->Row2(['2.', '']);
            $pdf->Ln(-4);
            $pdf->SetX(22);
            $pdf->Row2(['', $satuorang->keterangan]);
            $pdf->Ln(3);
            $pdf->SetX(19);
            $pdf->Row2(['3.', '']);
            $pdf->Ln(-4);
            $pdf->SetX(22);
            $pdf->Row2(['', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan mengurus: ' . $satuorang->penggunaan]);
        }

        $pdf->Ln(4);
        $pdf->SetX(19);
        $pdf->SetWidths([176, 5]);
        $pdf->Row2(['Demikian Surat Keterangan ini dibuat untuk dipergunakan seperlunya.', '']);

        $pdf->ln(14);
        if ($satuorang->pejabat_camat_id == 1) {
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
        $hari3 = substr($satuorang->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($satuorang->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($satuorang->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($satuorang->tanggal, 3, 2)];
        }
        $tahun3 = substr($satuorang->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;

        $pdf->SetX(120);
        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(5);
        if ($satuorang->penandatangan == 'Atasnama Pimpinan' || $satuorang->penandatangan == 'Jabatan Struktural') {
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
        if ($satuorang->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($satuorang->jabatan_lainnya);

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
        if ($satuorang->penandatangan != 'Atasnama Pimpinan' && $satuorang->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($satuorang->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($satuorang->penandatangan);
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
            if ($satuorang->penandatangan == 'Pimpinan Organisasi' && $satuorang->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($satuorang->penandatangan);
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
        $pdf->Output('cetak-data-beasiswa' . $tanggal . '.pdf', 'I');
        exit;
    }
}