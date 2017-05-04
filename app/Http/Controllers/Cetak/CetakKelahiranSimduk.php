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
use App\Domain\Repositories\Pelayanan\KelahiranRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakKelahiranSimduk extends Controller
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
        OrangTuaRepository $orangTuaRepository,
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
        $pdf->SetFont('Times-Roman', '', 11);
        $desa = $this->desa->find(session('desa'));
        $kelahiran = $this->kelahiran->find($id);
        $jeniskodeadministrasi = $this->kelahiran->cekkodejenisadministrasi($kelahiran->jenis_pelayanan_id);
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
        $pdf->Ln(-4);
        $pdf->SetX(40);
        $pdf->Cell(0, 0, 'PEMERINTAH ' . $status . ' ' . strtoupper($kabupaten), 0, 0, 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Times-Roman', '', 11);
        $pdf->SetX(40);
        $pdf->Cell(0, 0, $statuskecamatan . ' ' . strtoupper($kecamatan), 0, 0, 'C');
        $pdf->Ln(4);
        if ($logogambar != null) {
            $pdf->SetFont('Times-Roman', 'B', 11);
            $pdf->Image('app/logo/' . $logogambar->logo, 10, 5, 13, 15);
        }
        $pdf->SetX(40);
        $pdf->SetFont('Times-Roman', 'B', 14);
        $pdf->Cell(0, 0, $statusdesa . ' ' . strtoupper($namadesa), 0, 0, 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Times-Roman', 'B', 14);
        $pdf->SetX(40);
        $pdf->SetFont('Times-Roman', '', 8);
        if ($alamat != null) {
            if ($alamat->faxmile != 0) {
                $pdf->Cell(0, 0, 'Alamat:' . $alamat->alamat . ' Telp. ' . $alamat->telepon . ' Fax. ' . $alamat->faxmile, 0, 0, 'C');
            }
            if ($alamat->faxmile == 0) {
                $pdf->Cell(0, 0, 'Alamat:' . $alamat->alamat . ' Telp. ' . $alamat->telepon, 0, 0, 'C');
            }
            $pdf->Ln(3);
            $pdf->SetFont('Times-Roman', '', 8);
            $pdf->SetX(40);
            $pdf->Cell(0, 0, 'email: ' . $alamat->email . ' website: ' . $alamat->website, 0, 0, 'C');

        }

        $pdf->Ln(4);
        $pdf->SetFont('Times-Roman', 'BU', 8);
        if ($kodeadministrasi == null)
            $kodeadministrasinama = '';
        else {
            $pdf->SetX(40);

            $kodeadministrasinama = $kodeadministrasi->kode;
        }
        $pdf->SetFont('Times-Roman', 'BU', 8);
        if ($kodeadministrasinama != null) {
            $pdf->Cell(0, 0, strtoupper($namadesa) . '-' . strtoupper($kodeadministrasinama), 0, '', 'C');
        } else {
            $pdf->SetX(40);

            $pdf->Cell(0, 0, strtoupper($namadesa), 0, '', 'C');
        }
        $pdf->Ln(10);
        $pdf->SetFont('arial', 'BU', 9);
        $pdf->SetX(25);
        if ($kelahiran->is_penduduk_layan != null) {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN KELAHIRAN' . ' ' . strtoupper($kelahiran->is_penduduk_layan), 0, '', 'C');

        } else {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN KELAHIRAN', 0, '', 'C');

        }
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
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
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $kelahiran->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $kelahiran->tahun, 0, '', 'C');

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

    public function KelahiranSimduk($id)
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
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('Surat Kelahiran Simduk');
        $this->Kop($pdf, $id);
        $pdf->SetY(50);
        $desa = $this->desa->find(session('desa'));
        $kelahiran = $this->kelahiran->find($id);
        $cekwaktu = $this->kodeadministrasi->cekwaktuadministrasi();
        if ($kelahiran->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($kelahiran->penandatangan);
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
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(5, -15, 'Yang bertanda tangan di bawah ini:', 0, '', 'L');
        $pdf->Ln(3);
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

            if ($kelahiran->penandatangan == 'Jabatan Struktural') {
                $pejabatstruktural2 = $this->pejabat->find($kelahiran->jabatan_lainnya);
                if ($pejabatstruktural2->keterangan != '') {

                    $namajabatan = $pejabatstruktural2->keterangan . ' ' . $pejabatstruktural2->jabatan;
                }
                if ($pejabatstruktural2->keterangan == '') {
                    $namajabatan = $pejabatstruktural2->jabatan;
                }
            }
            if ($kelahiran->penandatangan != 'Jabatan Struktural') {

                if ($pejabat->keterangan != '') {
                    $namajabatan = $pejabat->keterangan . ' ' . $pejabat->jabatan;
                }
                if ($pejabat->keterangan == '') {
                    $namajabatan = $pejabat->jabatan;
                }
            }
        }

        //nama pejabat

        $pdf->SetX(14);
        $pdf->Cell(25, -13, 'a.     Nama ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(6, -13, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(120, -13, '' . $namalengkappejabat, 0, '', 'L');

        // jabatan pejabat

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'b.     Jabatan ', 0, '', 'L');
        $pdf->Cell(11);
        if ($kelahiran->penandatangan == 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $statusdesa1 . ' ' . $namadesa, 0, '', 'L');
        }
        if ($kelahiran->penandatangan != 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $namadesa, 0, '', 'L');
        }
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'dengan ini menerangkan bahwa:', 0, '', 'L');
//confert hari indonesia
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
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Hari', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $hariindo, 0, '', 'L');
        $pdf->Ln(4);
//tanggal lahir
        $harilahi = substr($kelahiran->tanggal_lahir, 0, 2);
        $bulanindo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($kelahiran->tanggal_lahir, 3, 2) <= 9) {
            $bulanlahir = $bulanindo[substr($kelahiran->tanggal_lahir, 4, 1)];
        } else {
            $bulanlahir = $bulanindo[substr($kelahiran->tanggal_lahir, 3, 2)];
        }
        $tahunlahir = substr($kelahiran->tanggal_lahir, 6, 4);
        $convertkelahiran = $harilahi . ' ' . $bulanlahir . ' ' . $tahunlahir;

        if ($cekwaktu != null) {
            $waktubagian = ' ' . $cekwaktu->kode;
        }
        if ($cekwaktu == null) {
            $waktubagian = '';
        }
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Tanggal', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $convertkelahiran, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Waktu', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kelahiran->waktu_lahir . $waktubagian, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Tempat Kelahiran', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kelahiran->desa_lahir->kecamatan->kabupaten->kabupaten, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Telah lahir seorang anak', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kelahiran->jk->jk, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Bernama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(120, -15, $kelahiran->nama, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);

//        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//        ibu bayi

        if ($kelahiran->ibu_bayi == 1) {
            $namaibupenduduk = $kelahiran->pribadi->nama;
            $tempatlahiribu = $kelahiran->pribadi->tempat_lahir;

            $hari3 = substr($kelahiran->pribadi->tanggal_lahir, 0, 2);
            $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($kelahiran->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan3 = $indo3[substr($kelahiran->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan3 = $indo3[substr($kelahiran->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun3 = substr($kelahiran->pribadi->tanggal_lahir, 6, 4);
            $tanggallahiribu = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;


            if ($kelahiran->pribadi->pekerjaan_id == 89) {
                $pekerjaanibu = $kelahiran->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanibu = $kelahiran->pribadi->pekerjaan->pekerjaan;
            }
            $keluarga = $this->keluarga->cekalamat($kelahiran->pribadi->id);
            $alamatibu = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatibulengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        if ($kelahiran->ibu_bayi == 2) {
            $namaibupenduduk = $kelahiran->non_penduduk->nama;
            $tempatlahiribu = $kelahiran->non_penduduk->tempat_lahir;

            $hari3 = substr($kelahiran->non_penduduk->tanggal_lahir, 0, 2);
            $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($kelahiran->non_penduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan3 = $indo3[substr($kelahiran->non_penduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan3 = $indo3[substr($kelahiran->non_penduduk->tanggal_lahir, 3, 2)];
            }
            $tahun3 = substr($kelahiran->non_penduduk->tanggal_lahir, 6, 4);
            $tanggallahiribu = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;


            if ($kelahiran->non_penduduk->pekerjaan_id == 89) {
                $pekerjaanibu = $kelahiran->non_penduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanibu = $kelahiran->non_penduduk->pekerjaan->pekerjaan;
            }
            $alamatibu = $kelahiran->non_penduduk->alamat . ' RT. ' . $kelahiran->non_penduduk->alamat_rt . ' RW. ' . $kelahiran->non_penduduk->alamat_rw;
            if ($kelahiran->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $kelahiran->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($kelahiran->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $kelahiran->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($kelahiran->non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $kelahiran->non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($kelahiran->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $kelahiran->non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($kelahiran->non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $kelahiran->non_penduduk->desa->desa;
            }
            if ($kelahiran->non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $kelahiran->non_penduduk->desa->desa;
            }
            if ($kelahiran->non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $kelahiran->non_penduduk->desa->desa;
            }
            if ($kelahiran->non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $kelahiran->non_penduduk->desa->desa;
            }
            $alamatibulengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;

        }

        $pdf->SetX(14);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -15, 'Dari seorang ibu:', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(3);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Nama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(120, -15, $namaibupenduduk, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kelahiran->ibu_bayi_nik, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Tempat, Tgl Lahir', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $tempatlahiribu . ', ' . $tanggallahiribu, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Pekerjaan', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $pekerjaanibu, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Alamat', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetWidths([8, 85]);
        $pdf->SetAligns(['', 'J']);
        $pdf->Ln(-9);
        $pdf->SetX(48);
        $pdf->Row3(['', $alamatibu]);
        $pdf->Ln(4);
        $pdf->Ln(-5);
        $pdf->SetX(48);
        $pdf->Row3(['', $alamatibulengkap]);


//        ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//        bapak bayi


        if ($kelahiran->bapak_bayi == 1) {
            $namabapakpenduduk = $kelahiran->pribadi_bapak->nama;
            $tempatlahirbapak = $kelahiran->pribadi_bapak->tempat_lahir;

            $hari4 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 0, 2);
            $indo4 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($kelahiran->pribadi_bapak->tanggal_lahir, 3, 2) <= 9) {
                $bulan4 = $indo4[substr($kelahiran->pribadi_bapak->tanggal_lahir, 4, 1)];
            } else {
                $bulan4 = $indo4[substr($kelahiran->pribadi_bapak->tanggal_lahir, 3, 2)];
            }
            $tahun4 = substr($kelahiran->pribadi_bapak->tanggal_lahir, 6, 4);
            $tanggallahirbapak = $hari4 . ' ' . $bulan4 . ' ' . $tahun4;


            if ($kelahiran->pribadi_bapak->pekerjaan_id == 89) {
                $pekerjaanbapak = $kelahiran->pribadi_bapak->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanbapak = $kelahiran->pribadi_bapak->pekerjaan->pekerjaan;
            }
            $keluarga = $this->keluarga->cekalamat($kelahiran->pribadi_bapak->id);
            $alamatbapak = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatbapaklengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        if ($kelahiran->bapak_bayi == 2) {
            $namabapakpenduduk = $kelahiran->non_penduduk_bapak->nama;
            $tempatlahirbapak = $kelahiran->non_penduduk_bapak->tempat_lahir;

            $hari4 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 0, 2);
            $indo4 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 3, 2) <= 9) {
                $bulan4 = $indo4[substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 4, 1)];
            } else {
                $bulan4 = $indo4[substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 3, 2)];
            }
            $tahun4 = substr($kelahiran->non_penduduk_bapak->tanggal_lahir, 6, 4);
            $tanggallahirbapak = $hari4 . ' ' . $bulan4 . ' ' . $tahun4;


            if ($kelahiran->non_penduduk_bapak->pekerjaan_id == 89) {
                $pekerjaanbapak = $kelahiran->non_penduduk_bapak->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanbapak = $kelahiran->non_penduduk_bapak->pekerjaan->pekerjaan;
            }
            $alamatbapak = $kelahiran->non_penduduk_bapak->alamat . ' RT. ' . $kelahiran->non_penduduk_bapak->alamat_rt . ' RW. ' . $kelahiran->non_penduduk_bapak->alamat_rw;
            if ($kelahiran->non_penduduk_bapak->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $kelahiran->non_penduduk_bapak->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($kelahiran->non_penduduk_bapak->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $kelahiran->non_penduduk_bapak->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($kelahiran->non_penduduk_bapak->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $kelahiran->non_penduduk_bapak->desa->kecamatan->kecamatan;
            }
            if ($kelahiran->non_penduduk_bapak->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $kelahiran->non_penduduk_bapak->desa->kecamatan->kecamatan;
            }
            //desa
            if ($kelahiran->non_penduduk_bapak->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $kelahiran->non_penduduk_bapak->desa->desa;
            }
            if ($kelahiran->non_penduduk_bapak->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $kelahiran->non_penduduk_bapak->desa->desa;
            }
            if ($kelahiran->non_penduduk_bapak->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $kelahiran->non_penduduk_bapak->desa->desa;
            }
            if ($kelahiran->non_penduduk_bapak->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $kelahiran->non_penduduk_bapak->desa->desa;
            }
            $alamatbapaklengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;

        }


        $pdf->Ln(8);
        $pdf->SetX(14);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -15, 'Istri dari:', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Nama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(120, -15, $namabapakpenduduk, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $kelahiran->bapak_bayi_nik, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Tempat, Tgl Lahir', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $tempatlahirbapak . ', ' . $tanggallahirbapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Pekerjaan', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $pekerjaanbapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Alamat', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetWidths([8, 90]);
        $pdf->SetAligns(['', 'J']);
        $pdf->Ln(-9);
        $pdf->SetX(48);
        $pdf->Row3(['', $alamatbapak]);
        $pdf->Ln(4);
        $pdf->Ln(-5);
        $pdf->SetX(48);
        $pdf->Row3(['', $alamatbapaklengkap]);

//        ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//        pelapor bayi


        if ($kelahiran->pelapor_penduduk == 1) {
            $pelaporlist = $this->pribadi->find($kelahiran->pelapor_penduduk_id);

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


            if ($pelaporlist->pekerjaan_id == 89) {
                $pekerjaanpelapor = $pelaporlist->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaanpelapor = $pelaporlist->pekerjaan->pekerjaan;
            }
            $keluarga = $this->keluarga->cekalamat($pelaporlist->id);
            $alamatpelapor = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatpelaporlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        if ($kelahiran->pelapor_penduduk == 2) {
            $pelaporlist = $this->nonpenduduk->find($kelahiran->pelapor_penduduk_id);

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


        $pdf->Ln(8);
        $pdf->SetX(14);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(25, -15, 'Surat Keterangan ini dibuat berdasarkan Keterangan Pelapor:', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
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
        $pdf->Cell(120, -15, $kelahiran->pelapor_nik, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Tempat, Tgl Lahir', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $tempatlahirpelapor . ', ' . $tanggallahirpelapor, 0, '', 'L');
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
        $pdf->Ln(1);
        $pdf->SetX(85);
        $hari3 = substr($kelahiran->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($kelahiran->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($kelahiran->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($kelahiran->tanggal, 3, 2)];
        }
        $tahun3 = substr($kelahiran->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;

        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(3);
        if ($kelahiran->penandatangan == 'Atasnama Pimpinan' || $kelahiran->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(85);
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
            $pdf->Ln(3);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(85);
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
        if ($kelahiran->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($kelahiran->jabatan_lainnya);

            $pdf->Ln(3);
            $pdf->SetX(85);
            $pdf->Cell(0, 10, 'u.b.', 0, '', 'C');
            $pdf->Ln(3);
            $pdf->SetX(85);
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
        if ($kelahiran->penandatangan != 'Atasnama Pimpinan' && $kelahiran->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(85);
            if ($kelahiran->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($kelahiran->penandatangan);
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
            if ($kelahiran->penandatangan == 'Pimpinan Organisasi' && $kelahiran->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($kelahiran->penandatangan);
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
        $pdf->Ln(14);

        if ($pejabat != null) {
            $pdf->SetX(85);
            $pdf->SetFont('Arial', 'BU', 7);
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
            $pdf->Ln(3);
            $pdf->SetX(85);
            $pdf->Cell(0, 10, $pejabat->pangkat, 0, '', 'C');
            $pdf->Ln(3);
            if ($pejabat->nip != '') {
                $pdf->SetX(85);
                $pdf->Cell(0, 10, 'NIP.' . $pejabat->nip, 0, '', 'C');
            }
        }
        $tanggal = date('d-m-y');
        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

        $pdf->Output('cetak-data-kelahiran-simduk' . $tanggal . '.pdf', 'I');
        exit;
    }
}