<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DataPribadi\KeluargaRepository;
use App\Domain\Repositories\DataPribadi\OrangTuaNonPendudukRepository;
use App\Domain\Repositories\DataPribadi\OrangTuaRepository;
use App\Domain\Repositories\DataPribadi\PendudukLainRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\SkckRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakSkck extends Controller
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
        SkckRepository $skckRepository,
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
        OrganisasiRepository $organisasiRepository,
        OrangTuaNonPendudukRepository $orangTuaNonPendudukRepository
    )
    {
        $this->skck = $skckRepository;
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

    function Kop($pdf, $id)
    {
        $pdf->AddFont('Times-Roman', '', 'times.php');
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        $pdf->AddPage();
        $pdf->SetFont('Times-Roman', '', 14);
        $desa = $this->desa->find(session('desa'));
        $skck = $this->skck->find($id);
        $jeniskodeadministrasi = $this->skck->cekkodejenisadministrasi($skck->jenis_pelayanan_id);
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
        $pdf->Ln(4);
        $pdf->SetFont('Times-Roman', '', 13);
        $pdf->SetX(40);
        $pdf->Cell(0, 0, $statuskecamatan . ' ' . strtoupper($kecamatan), 0, 0, 'C');
        $pdf->Ln(4);
        if ($logogambar != null) {
            $pdf->SetFont('Times-Roman', 'B', 13);
            $pdf->Image('app/logo/' . $logogambar->logo, 20, 8, 20, 25);
        }
        $pdf->SetX(40);
        $pdf->SetFont('Times-Roman', 'B', 18);
        $pdf->Cell(0, 0, $statusdesa . ' ' . strtoupper($namadesa), 0, 0, 'C');
        $pdf->Ln(4);
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
            $pdf->Ln(4);
            $pdf->SetFont('Times-Roman', '', 10);
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
        if ($skck->is_penduduk_layan != null) {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN' . ' ' . strtoupper($skck->is_penduduk_layan), 0, '', 'C');

        } else {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN', 0, '', 'C');

        }
        $pdf->Ln(4);
        $pdf->SetFont('arial', '', 10);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($skck->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($skck->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($skck->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $skck->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $skck->tahun, 0, '', 'C');

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

    public function Skck($id)
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
        $pdf->SetTitle('Surat SKCK');
        $this->Kop($pdf, $id);
        $pdf->SetY(80);
        $desa = $this->desa->find(session('desa'));
        $skck = $this->skck->find($id);
        $jeniskodeadministrasi = $this->skck->cekkodejenisadministrasi($skck->jenis_pelayanan_id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();
        if ($skck->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($skck->penandatangan);
        }
        //kabupaten
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
            $kecamatan = $desa->kecamatan->kecamatan;
        }
        if ($desa->kecamatan->kabupaten->status == 2) {
            $statuskecamatan = 'Distrik';
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
        $pdf->SetFont('Arial', '', 10);

        $pdf->SetX(19);
        $pdf->Cell(0, -15, 'Yang bertanda tangan di bawah ini:', 0, '', 'L');
        $pdf->Ln(4);
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

            if ($skck->penandatangan == 'Jabatan Struktural') {
                $pejabatstruktural2 = $this->pejabat->find($skck->jabatan_lainnya);
                if ($pejabatstruktural2->keterangan != '') {

                    $namajabatan = $pejabatstruktural2->keterangan . ' ' . $pejabatstruktural2->jabatan;
                }
                if ($pejabatstruktural2->keterangan == '') {
                    $namajabatan = $pejabatstruktural2->jabatan;
                }
            }
            if ($skck->penandatangan != 'Jabatan Struktural') {

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

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(19);
        $pdf->Cell(25, -15, 'b.     Jabatan ', 0, '', 'L');
        $pdf->Cell(19);
        if ($skck->penandatangan == 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $statusdesa . ' ' . $namadesa, 0, '', 'L');
        }
        if ($skck->penandatangan != 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $namadesa, 0, '', 'L');
        }
        $pdf->Ln(7);
        $pdf->SetX(19);
        $pdf->Cell(0, -15, 'dengan ini menerangkan bahwa:', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(19);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(7.5, -15, 'A.', 0, '', 'L');
        $pdf->Cell(0, -15, 'BAPAK KANDUNG :', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);

        // nik Bapak
        if ($skck->nik_bapak == '-') {

            $cariorangtua = $this->orangtua->cekorangtuabapak($skck->penduduk_id);
//nama bapak lengkap
            $nikbapak = '--';
            if ($cariorangtua->titel_belakang != '') {
                if ($cariorangtua->titel_depan != '') {
                    $namalengkapbapak = $cariorangtua->titel_depan . ' ' . $cariorangtua->nama . ', ' . $cariorangtua->titel_belakang;
                }
                if ($cariorangtua->titel_depan == '') {
                    $namalengkapbapak = $cariorangtua->titel_depan . '' . $cariorangtua->nama . ', ' . $cariorangtua->titel_belakang;
                }
            }
            if ($cariorangtua->titel_belakang == '') {
                if ($cariorangtua->titel_depan != '') {
                    $namalengkapbapak = $cariorangtua->titel_depan . ' ' . $cariorangtua->nama . '' . $cariorangtua->titel_belakang;
                }
                if ($cariorangtua->titel_depan == '') {
                    $namalengkapbapak = $cariorangtua->titel_depan . '' . $cariorangtua->nama . '' . $cariorangtua->titel_belakang;
                }
            }
            $tempatlahirbapak = '--';
            $agamabapak = '--';
            $pekerjaanbapak = '--';

//          if orang tua penduduk

            if ($cariorangtua->status_orang_tua == 1 || $cariorangtua->status_orang_tua == 0) {
                $alamatbapak = '--';
                $alamatwilayah = $statusdesa . ' ' . $namadesa . ' ' . $statuskecamatan . ' ' . $kecamatan . ' ' . $status . ' ' . $kabupaten;

            }

            // if orang tua non penduduk
            if ($cariorangtua->status_orang_tua == 2) {
                $cariorangtuanonpenduduk = $this->orangtuanonpenduduk->cekorangtuacetak($cariorangtua->id);
                $alamatbapak = '--';
                if ($cariorangtuanonpenduduk->desa->kecamatan->kabupaten->status == 1) {
                    $status1 = 'Kabupaten';
                    $kabupaten1 = $cariorangtuanonpenduduk->desa->kecamatan->kabupaten->kabupaten;
                }
                if ($cariorangtuanonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                    $status1 = 'Kota';
                    $kabupaten1 = $cariorangtuanonpenduduk->desa->kecamatan->kabupaten->kabupaten;
                }
                //kecamatan
                if ($cariorangtuanonpenduduk->desa->kecamatan->status == 1) {
                    $statuskecamatan1 = 'Kecamatan';
                    $kecamatan1 = $cariorangtuanonpenduduk->desa->kecamatan->kecamatan;
                }
                if ($cariorangtuanonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                    $statuskecamatan1 = 'Distrik';
                    $kecamatan1 = $cariorangtuanonpenduduk->desa->kecamatan->kecamatan;
                }
                //desa
                if ($cariorangtuanonpenduduk->desa->status == 1) {
                    $statusdesa1 = 'Kelurahan';
                    $namadesa1 = $cariorangtuanonpenduduk->desa->desa;
                }
                if ($cariorangtuanonpenduduk->desa->status == 2) {
                    $statusdesa1 = 'Desa';
                    $namadesa1 = $cariorangtuanonpenduduk->desa->desa;
                }
                if ($cariorangtuanonpenduduk->desa->status == 3) {
                    $statusdesa1 = 'Kampung';
                    $namadesa1 = $cariorangtuanonpenduduk->desa->desa;
                }
                if ($cariorangtuanonpenduduk->desa->status == 4) {
                    $statusdesa1 = 'Negeri';
                    $namadesa1 = $cariorangtuanonpenduduk->desa->desa;
                }
                $alamatwilayah = $statusdesa1 . ' ' . $namadesa1 . ' ' . $statuskecamatan1 . ' ' . $kecamatan1 . ' ' . $status1 . ' ' . $kabupaten1;

            }
        }
//        if bila nik bapak terinputkan
        if ($skck->nik_bapak != '-') {

            $nikbapak = $skck->nik_bapak;

            // if bila bapak penduduk
            if ($skck->jenis_penduduk_bapak == '1') {
                $caripribadibapak = $this->pribadi->find($skck->penduduk_bapak_id);

                if ($caripribadibapak->titel_belakang != '') {
                    if ($caripribadibapak->titel_depan != '') {
                        $namalengkapbapak = $caripribadibapak->titel_depan . ' ' . $caripribadibapak->nama . ', ' . $caripribadibapak->titel_belakang;
                    }
                    if ($caripribadibapak->titel_depan == '') {
                        $namalengkapbapak = $caripribadibapak->titel_depan . '' . $caripribadibapak->nama . ', ' . $caripribadibapak->titel_belakang;
                    }
                }
                if ($caripribadibapak->titel_belakang == '') {
                    if ($caripribadibapak->titel_depan != '') {
                        $namalengkapbapak = $caripribadibapak->titel_depan . ' ' . $caripribadibapak->nama . '' . $caripribadibapak->titel_belakang;
                    }
                    if ($caripribadibapak->titel_depan == '') {
                        $namalengkapbapak = $caripribadibapak->titel_depan . '' . $caripribadibapak->nama . '' . $caripribadibapak->titel_belakang;
                    }
                }
                $hari = substr($caripribadibapak->tanggal_lahir, 0, 2);
                $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
                if (substr($caripribadibapak->tanggal_lahir, 3, 2) <= 9) {
                    $bulan = $indo[substr($caripribadibapak->tanggal_lahir, 4, 1)];
                } else {
                    $bulan = $indo[substr($caripribadibapak->tanggal_lahir, 3, 2)];
                }
                $tahun = substr($caripribadibapak->tanggal_lahir, 6, 4);
                $tempatlahirbapak = $caripribadibapak->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
                $agamabapak = $caripribadibapak->agama->agama;
                if ($caripribadibapak->pekerjaan_id == 89) {
                    $pekerjaanbapak = $caripribadibapak->pekerjaan_lain->pekerjaan_lain;
                } else {
                    $pekerjaanbapak = $caripribadibapak->pekerjaan->pekerjaan;
                }
                $carialamatbapak = $this->keluarga->cekalamat($skck->penduduk_bapak_id);

                $alamatbapak = $carialamatbapak->alamat . ' RT. ' . $carialamatbapak->alamat_rt . ' RW. ' . $carialamatbapak->alamat_rw;


                if ($caripribadibapak->desa->kecamatan->kabupaten->status == 1) {
                    $status2 = 'Kabupaten';
                    $kabupaten2 = $caripribadibapak->desa->kecamatan->kabupaten->kabupaten;
                }
                if ($caripribadibapak->desa->kecamatan->kabupaten->status == 2) {
                    $status2 = 'Kota';
                    $kabupaten2 = $caripribadibapak->desa->kecamatan->kabupaten->kabupaten;
                }
                //kecamatan
                if ($caripribadibapak->desa->kecamatan->status == 1) {
                    $statuskecamatan2 = 'Kecamatan';
                    $kecamatan2 = $caripribadibapak->desa->kecamatan->kecamatan;
                }
                if ($caripribadibapak->desa->kecamatan->kabupaten->status == 2) {
                    $statuskecamatan2 = 'Distrik';
                    $kecamatan2 = $caripribadibapak->desa->kecamatan->kecamatan;
                }
                //desa
                if ($caripribadibapak->desa->status == 1) {
                    $statusdesa2 = 'Kelurahan';
                    $namadesa2 = $caripribadibapak->desa->desa;
                }
                if ($caripribadibapak->desa->status == 2) {
                    $statusdesa2 = 'Desa';
                    $namadesa2 = $caripribadibapak->desa->desa;
                }
                if ($caripribadibapak->desa->status == 3) {
                    $statusdesa2 = 'Kampung';
                    $namadesa2 = $caripribadibapak->desa->desa;
                }
                if ($caripribadibapak->desa->status == 4) {
                    $statusdesa2 = 'Negeri';
                    $namadesa2 = $caripribadibapak->desa->desa;
                }
                $alamatwilayah = $statusdesa2 . ' ' . $namadesa2 . ' ' . $statuskecamatan2 . ' ' . $kecamatan2 . ' ' . $status2 . ' ' . $kabupaten2;

            }
            if ($skck->jenis_penduduk_bapak == '2') {
                $caripribadibapak = $this->nonpenduduk->find($skck->penduduk_bapak_id);

                if ($caripribadibapak->titel_belakang != '') {
                    if ($caripribadibapak->titel_depan != '') {
                        $namalengkapbapak = $caripribadibapak->titel_depan . ' ' . $caripribadibapak->nama . ', ' . $caripribadibapak->titel_belakang;
                    }
                    if ($caripribadibapak->titel_depan == '') {
                        $namalengkapbapak = $caripribadibapak->titel_depan . '' . $caripribadibapak->nama . ', ' . $caripribadibapak->titel_belakang;
                    }
                }
                if ($caripribadibapak->titel_belakang == '') {
                    if ($caripribadibapak->titel_depan != '') {
                        $namalengkapbapak = $caripribadibapak->titel_depan . ' ' . $caripribadibapak->nama . '' . $caripribadibapak->titel_belakang;
                    }
                    if ($caripribadibapak->titel_depan == '') {
                        $namalengkapbapak = $caripribadibapak->titel_depan . '' . $caripribadibapak->nama . '' . $caripribadibapak->titel_belakang;
                    }
                }
                $hari = substr($caripribadibapak->tanggal_lahir, 0, 2);
                $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
                if (substr($caripribadibapak->tanggal_lahir, 3, 2) <= 9) {
                    $bulan = $indo[substr($caripribadibapak->tanggal_lahir, 4, 1)];
                } else {
                    $bulan = $indo[substr($caripribadibapak->tanggal_lahir, 3, 2)];
                }
                $tahun = substr($caripribadibapak->tanggal_lahir, 6, 4);
                $tempatlahirbapak = $caripribadibapak->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
                $agamabapak = $caripribadibapak->agama->agama;
                if ($caripribadibapak->pekerjaan_id == 89) {
                    $pekerjaanbapak = $caripribadibapak->pekerjaan_lain->pekerjaan_lain;
                } else {
                    $pekerjaanbapak = $caripribadibapak->pekerjaan->pekerjaan;
                }
                $alamatbapak = $caripribadibapak->alamat . ' RT. ' . $caripribadibapak->alamat_rt . ' RW. ' . $caripribadibapak->alamat_rw;
                if ($caripribadibapak->desa->kecamatan->kabupaten->status == 1) {
                    $status2 = 'Kabupaten';
                    $kabupaten2 = $caripribadibapak->desa->kecamatan->kabupaten->kabupaten;
                }
                if ($caripribadibapak->desa->kecamatan->kabupaten->status == 2) {
                    $status2 = 'Kota';
                    $kabupaten2 = $caripribadibapak->desa->kecamatan->kabupaten->kabupaten;
                }
                //kecamatan
                if ($caripribadibapak->desa->kecamatan->status == 1) {
                    $statuskecamatan2 = 'Kecamatan';
                    $kecamatan2 = $caripribadibapak->desa->kecamatan->kecamatan;
                }
                if ($caripribadibapak->desa->kecamatan->kabupaten->status == 2) {
                    $statuskecamatan2 = 'Distrik';
                    $kecamatan2 = $caripribadibapak->desa->kecamatan->kecamatan;
                }
                //desa
                if ($caripribadibapak->desa->status == 1) {
                    $statusdesa2 = 'Kelurahan';
                    $namadesa2 = $caripribadibapak->desa->desa;
                }
                if ($caripribadibapak->desa->status == 2) {
                    $statusdesa2 = 'Desa';
                    $namadesa2 = $caripribadibapak->desa->desa;
                }
                if ($caripribadibapak->desa->status == 3) {
                    $statusdesa2 = 'Kampung';
                    $namadesa2 = $caripribadibapak->desa->desa;
                }
                if ($caripribadibapak->desa->status == 4) {
                    $statusdesa2 = 'Negeri';
                    $namadesa2 = $caripribadibapak->desa->desa;
                }
                $alamatwilayah = $statusdesa2 . ' ' . $namadesa2 . ' ' . $statuskecamatan2 . ' ' . $kecamatan2 . ' ' . $status2 . ' ' . $kabupaten2;

            }
        }

        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $nikbapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Nama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, -15, ':     ' . $namalengkapbapak, 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Tempat, Tgl. Lahir', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $tempatlahirbapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Agama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $agamabapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Pekerjaan', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $pekerjaanbapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Alamat Domisili', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $alamatbapak, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, '', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, '      ' . $alamatwilayah, 0, '', 'L');


//  +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  ibu kandung

        $pdf->Ln(4);
        $pdf->SetX(19);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(7.5, -15, 'B.', 0, '', 'L');
        $pdf->Cell(0, -15, 'IBU KANDUNG :', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);

        // nik ibu
        if ($skck->nik_ibu == '-') {

            $cariorangtuaibu = $this->orangtua->cekorangtuaibu($skck->penduduk_id);
//nama ibu lengkap
            $nikibu = '--';
            if ($cariorangtuaibu->titel_belakang != '') {
                if ($cariorangtuaibu->titel_depan != '') {
                    $namalengkapibu = $cariorangtuaibu->titel_depan . ' ' . $cariorangtuaibu->nama . ', ' . $cariorangtuaibu->titel_belakang;
                }
                if ($cariorangtuaibu->titel_depan == '') {
                    $namalengkapibu = $cariorangtuaibu->titel_depan . '' . $cariorangtuaibu->nama . ', ' . $cariorangtuaibu->titel_belakang;
                }
            }
            if ($cariorangtuaibu->titel_belakang == '') {
                if ($cariorangtuaibu->titel_depan != '') {
                    $namalengkapibu = $cariorangtuaibu->titel_depan . ' ' . $cariorangtuaibu->nama . '' . $cariorangtuaibu->titel_belakang;
                }
                if ($cariorangtuaibu->titel_depan == '') {
                    $namalengkapibu = $cariorangtuaibu->titel_depan . '' . $cariorangtuaibu->nama . '' . $cariorangtuaibu->titel_belakang;
                }
            }
            $tempatlahiribu = '--';
            $agamaibu = '--';
            $pekerjaanibu = '--';

//          if orang tua penduduk

            if ($cariorangtuaibu->status_orang_tua == 1) {
                $cariorangtuaibualamat = $this->keluarga->cekalamat($skck->penduduk_id);

                $alamatibu = '--';
                $alamatwilayahibu = $statusdesa . ' ' . $namadesa . ' ' . $statuskecamatan . ' ' . $kecamatan . ' ' . $status . ' ' . $kabupaten;

            }

            // if orang tua non penduduk
            if ($cariorangtuaibu->status_orang_tua == 2) {
                $cariorangtuaibunonpenduduk = $this->orangtuanonpenduduk->cekorangtuacetak($cariorangtuaibu->id);
                $alamatibu = '--';
                if ($cariorangtuaibunonpenduduk != null) {
                    if ($cariorangtuaibunonpenduduk->desa->kecamatan->kabupaten->status == 1) {
                        $status3 = 'Kabupaten';
                        $kabupaten3 = $cariorangtuaibunonpenduduk->desa->kecamatan->kabupaten->kabupaten;
                    }
                    if ($cariorangtuaibunonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                        $status3 = 'Kota';
                        $kabupaten3 = $cariorangtuaibunonpenduduk->desa->kecamatan->kabupaten->kabupaten;
                    }
                    //kecamatan
                    if ($cariorangtuaibunonpenduduk->desa->kecamatan->status == 1) {
                        $statuskecamatan3 = 'Kecamatan';
                        $kecamatan3 = $cariorangtuaibunonpenduduk->desa->kecamatan->kecamatan;
                    }
                    if ($cariorangtuaibunonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                        $statuskecamatan3 = 'Distrik';
                        $kecamatan3 = $cariorangtuaibunonpenduduk->desa->kecamatan->kecamatan;
                    }
                    //desa
                    if ($cariorangtuaibunonpenduduk->desa->status == 1) {
                        $statusdesa3 = 'Kelurahan';
                        $namadesa3 = $cariorangtuaibunonpenduduk->desa->desa;
                    }
                    if ($cariorangtuaibunonpenduduk->desa->status == 2) {
                        $statusdesa3 = 'Desa';
                        $namadesa3 = $cariorangtuaibunonpenduduk->desa->desa;
                    }
                    if ($cariorangtuaibunonpenduduk->desa->status == 3) {
                        $statusdesa3 = 'Kampung';
                        $namadesa3 = $cariorangtuaibunonpenduduk->desa->desa;
                    }
                    if ($cariorangtuaibunonpenduduk->desa->status == 4) {
                        $statusdesa3 = 'Negeri';
                        $namadesa3 = $cariorangtuaibunonpenduduk->desa->desa;
                    }
                    $alamatwilayahibu = $statusdesa3 . ' ' . $namadesa3 . ' ' . $statuskecamatan3 . ' ' . $kecamatan3 . ' ' . $status3 . ' ' . $kabupaten3;
                }
                if ($cariorangtuaibunonpenduduk = null) {
                    $alamatwilayahibu = '--';
                }
            }
        }
//        if bila nik ibu terinputkan
        if ($skck->nik_ibu != '-') {

            $nikibu = $skck->nik_ibu;

            // if bila ibu penduduk
            if ($skck->jenis_penduduk_ibu == '1') {
                $caripribadiibu = $this->pribadi->find($skck->penduduk_ibu_id);

                if ($caripribadiibu->titel_belakang != '') {
                    if ($caripribadiibu->titel_depan != '') {
                        $namalengkapibu = $caripribadiibu->titel_depan . ' ' . $caripribadiibu->nama . ', ' . $caripribadiibu->titel_belakang;
                    }
                    if ($caripribadiibu->titel_depan == '') {
                        $namalengkapibu = $caripribadiibu->titel_depan . '' . $caripribadiibu->nama . ', ' . $caripribadiibu->titel_belakang;
                    }
                }
                if ($caripribadiibu->titel_belakang == '') {
                    if ($caripribadiibu->titel_depan != '') {
                        $namalengkapibu = $caripribadiibu->titel_depan . ' ' . $caripribadiibu->nama . '' . $caripribadiibu->titel_belakang;
                    }
                    if ($caripribadiibu->titel_depan == '') {
                        $namalengkapibu = $caripribadiibu->titel_depan . '' . $caripribadiibu->nama . '' . $caripribadiibu->titel_belakang;
                    }
                }
                $hari = substr($caripribadiibu->tanggal_lahir, 0, 2);
                $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
                if (substr($caripribadiibu->tanggal_lahir, 3, 2) <= 9) {
                    $bulan = $indo[substr($caripribadiibu->tanggal_lahir, 4, 1)];
                } else {
                    $bulan = $indo[substr($caripribadiibu->tanggal_lahir, 3, 2)];
                }
                $tahun = substr($caripribadiibu->tanggal_lahir, 6, 4);
                $tempatlahiribu = $caripribadiibu->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
                $agamaibu = $caripribadiibu->agama->agama;
                if ($caripribadiibu->pekerjaan_id == 89) {
                    $pekerjaanibu = $caripribadiibu->pekerjaan_lain->pekerjaan_lain;
                } else {
                    $pekerjaanibu = $caripribadiibu->pekerjaan->pekerjaan;
                }
                $carialamatibu = $this->keluarga->cekalamat($skck->penduduk_ibu_id);

                $alamatibu = $carialamatibu->alamat . ' RT. ' . $carialamatibu->alamat_rt . ' RW. ' . $carialamatibu->alamat_rw;

                if ($caripribadiibu->desa->kecamatan->kabupaten->status == 1) {
                    $status4 = 'Kabupaten';
                    $kabupaten4 = $caripribadiibu->desa->kecamatan->kabupaten->kabupaten;
                }
                if ($caripribadiibu->desa->kecamatan->kabupaten->status == 2) {
                    $status4 = 'Kota';
                    $kabupaten4 = $caripribadiibu->desa->kecamatan->kabupaten->kabupaten;
                }
                //kecamatan
                if ($caripribadiibu->desa->kecamatan->status == 1) {
                    $statuskecamatan4 = 'Kecamatan';
                    $kecamatan4 = $caripribadiibu->desa->kecamatan->kecamatan;
                }
                if ($caripribadiibu->desa->kecamatan->kabupaten->status == 2) {
                    $statuskecamatan4 = 'Distrik';
                    $kecamatan4 = $caripribadiibu->desa->kecamatan->kecamatan;
                }
                //desa
                if ($caripribadiibu->desa->status == 1) {
                    $statusdesa4 = 'Kelurahan';
                    $namadesa4 = $caripribadiibu->desa->desa;
                }
                if ($caripribadiibu->desa->status == 2) {
                    $statusdesa4 = 'Desa';
                    $namadesa4 = $caripribadiibu->desa->desa;
                }
                if ($caripribadiibu->desa->status == 3) {
                    $statusdesa4 = 'Kampung';
                    $namadesa4 = $caripribadiibu->desa->desa;
                }
                if ($caripribadiibu->desa->status == 4) {
                    $statusdesa4 = 'Negeri';
                    $namadesa4 = $caripribadiibu->desa->desa;
                }
                $alamatwilayahibu = $statusdesa4 . ' ' . $namadesa4 . ' ' . $statuskecamatan4 . ' ' . $kecamatan4 . ' ' . $status4 . ' ' . $kabupaten4;

            }
            if ($skck->jenis_penduduk_ibu == '2') {
                $caripribadiibu = $this->nonpenduduk->find($skck->penduduk_ibu_id);

                if ($caripribadiibu->titel_belakang != '') {
                    if ($caripribadiibu->titel_depan != '') {
                        $namalengkapibu = $caripribadiibu->titel_depan . ' ' . $caripribadiibu->nama . ', ' . $caripribadiibu->titel_belakang;
                    }
                    if ($caripribadiibu->titel_depan == '') {
                        $namalengkapibu = $caripribadiibu->titel_depan . '' . $caripribadiibu->nama . ', ' . $caripribadiibu->titel_belakang;
                    }
                }
                if ($caripribadiibu->titel_belakang == '') {
                    if ($caripribadiibu->titel_depan != '') {
                        $namalengkapibu = $caripribadiibu->titel_depan . ' ' . $caripribadiibu->nama . '' . $caripribadiibu->titel_belakang;
                    }
                    if ($caripribadiibu->titel_depan == '') {
                        $namalengkapibu = $caripribadiibu->titel_depan . '' . $caripribadiibu->nama . '' . $caripribadiibu->titel_belakang;
                    }
                }
                $hari = substr($caripribadiibu->tanggal_lahir, 0, 2);
                $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
                if (substr($caripribadiibu->tanggal_lahir, 3, 2) <= 9) {
                    $bulan = $indo[substr($caripribadiibu->tanggal_lahir, 4, 1)];
                } else {
                    $bulan = $indo[substr($caripribadiibu->tanggal_lahir, 3, 2)];
                }
                $tahun = substr($caripribadiibu->tanggal_lahir, 6, 4);
                $tempatlahiribu = $caripribadiibu->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
                $agamaibu = $caripribadiibu->agama->agama;
                if ($caripribadiibu->pekerjaan_id == 89) {
                    $pekerjaanibu = $caripribadiibu->pekerjaan_lain->pekerjaan_lain;
                } else {
                    $pekerjaanibu = $caripribadiibu->pekerjaan->pekerjaan;
                }
                $alamatibu = $caripribadiibu->alamat . ' RT. ' . $caripribadiibu->alamat_rt . ' RW. ' . $caripribadiibu->alamat_rw;
                if ($caripribadiibu->desa->kecamatan->kabupaten->status == 1) {
                    $status4 = 'Kabupaten';
                    $kabupaten4 = $caripribadiibu->desa->kecamatan->kabupaten->kabupaten;
                }
                if ($caripribadiibu->desa->kecamatan->kabupaten->status == 2) {
                    $status4 = 'Kota';
                    $kabupaten4 = $caripribadiibu->desa->kecamatan->kabupaten->kabupaten;
                }
                //kecamatan
                if ($caripribadiibu->desa->kecamatan->status == 1) {
                    $statuskecamatan4 = 'Kecamatan';
                    $kecamatan4 = $caripribadiibu->desa->kecamatan->kecamatan;
                }
                if ($caripribadiibu->desa->kecamatan->kabupaten->status == 2) {
                    $statuskecamatan4 = 'Distrik';
                    $kecamatan4 = $caripribadiibu->desa->kecamatan->kecamatan;
                }
                //desa
                if ($caripribadiibu->desa->status == 1) {
                    $statusdesa4 = 'Kelurahan';
                    $namadesa4 = $caripribadiibu->desa->desa;
                }
                if ($caripribadiibu->desa->status == 2) {
                    $statusdesa4 = 'Desa';
                    $namadesa4 = $caripribadiibu->desa->desa;
                }
                if ($caripribadiibu->desa->status == 3) {
                    $statusdesa4 = 'Kampung';
                    $namadesa4 = $caripribadiibu->desa->desa;
                }
                if ($caripribadiibu->desa->status == 4) {
                    $statusdesa4 = 'Negeri';
                    $namadesa4 = $caripribadiibu->desa->desa;
                }
                $alamatwilayahibu = $statusdesa4 . ' ' . $namadesa4 . ' ' . $statuskecamatan4 . ' ' . $kecamatan4 . ' ' . $status4 . ' ' . $kabupaten4;

            }
        }

        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $nikibu, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Nama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, -15, ':     ' . $namalengkapibu, 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Tempat, Tgl. Lahir', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $tempatlahiribu, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Agama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $agamaibu, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Pekerjaan', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $pekerjaanibu, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Alamat Domisili', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $alamatibu, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, '', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, '      ' . $alamatwilayahibu, 0, '', 'L');


//  +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  pemohon

        $pdf->Ln(4);
        $pdf->SetX(19);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(7.5, -15, '', 0, '', 'L');
        $pdf->Cell(0, -15, 'adalah Bapak/Ibu Kandung dari seorang :', 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);

        // nik pemohon

        $nikpemohon = $skck->nik;

        $caripribadipelapor = $this->pribadi->find($skck->penduduk_id);

        if ($caripribadipelapor->titel_belakang != '') {
            if ($caripribadipelapor->titel_depan != '') {
                $namalengkappelapor = $caripribadipelapor->titel_depan . ' ' . $caripribadipelapor->nama . ', ' . $caripribadipelapor->titel_belakang;
            }
            if ($caripribadipelapor->titel_depan == '') {
                $namalengkappelapor = $caripribadipelapor->titel_depan . '' . $caripribadipelapor->nama . ', ' . $caripribadipelapor->titel_belakang;
            }
        }
        if ($caripribadipelapor->titel_belakang == '') {
            if ($caripribadipelapor->titel_depan != '') {
                $namalengkappelapor = $caripribadipelapor->titel_depan . ' ' . $caripribadipelapor->nama . '' . $caripribadipelapor->titel_belakang;
            }
            if ($caripribadipelapor->titel_depan == '') {
                $namalengkappelapor = $caripribadipelapor->titel_depan . '' . $caripribadipelapor->nama . '' . $caripribadipelapor->titel_belakang;
            }
        }
        $hari = substr($caripribadipelapor->tanggal_lahir, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($caripribadipelapor->tanggal_lahir, 3, 2) <= 9) {
            $bulan = $indo[substr($caripribadipelapor->tanggal_lahir, 4, 1)];
        } else {
            $bulan = $indo[substr($caripribadipelapor->tanggal_lahir, 3, 2)];
        }
        $tahun = substr($caripribadipelapor->tanggal_lahir, 6, 4);
        $tempatlahirpelapor = $caripribadipelapor->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
        $agamapelapor = $caripribadipelapor->agama->agama;
        if ($caripribadipelapor->pekerjaan_id == 89) {
            $pekerjaanpelapor = $caripribadipelapor->pekerjaan_lain->pekerjaan_lain;
        } else {
            $pekerjaanpelapor = $caripribadipelapor->pekerjaan->pekerjaan;
        }
        $carialamatpelapor = $this->keluarga->cekalamat($skck->penduduk_id);

        $alamatpelapor = $carialamatpelapor->alamat . ' RT. ' . $carialamatpelapor->alamat_rt . ' RW. ' . $carialamatpelapor->alamat_rw;

        $alamatwilayahpelapor = $statusdesa . ' ' . $namadesa . ' ' . $statuskecamatan . ' ' . $kecamatan . ' ' . $status . ' ' . $kabupaten;
        if ($caripribadipelapor->gol_darah_id != 13) {
            $golongandarah = $caripribadipelapor->golongan_darah->golongan_darah;
        }
        if ($caripribadipelapor->gol_darah_id == 13) {
            $golongandarah = '--';
        }

        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $nikpemohon, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Nama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, -15, ':     ' . $namalengkappelapor, 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Tempat, Tgl. Lahir', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $tempatlahirpelapor, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Jenis Kelamin', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $caripribadipelapor->jk->jk, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Golongan Darah', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $golongandarah, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Agama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $agamapelapor, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Pekerjaan', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $pekerjaanpelapor, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Status Perkawinan', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $caripribadipelapor->perkawinan->kawin, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Alamat Domisili', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $alamatpelapor, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, '', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, '      ' . $alamatwilayahpelapor, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(19);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(7.5, 0, 'C.', 0, '', 'L');
        $pdf->Cell(0, 0, 'KETERANGAN :', 0, '', 'L');
        $pdf->Ln(2);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(53);
        $pdf->SetWidths([5, 176]);
        $pdf->SetX(22);
        $pdf->SetX(27);
        if ($skck->jenis_catatan == '') {
            $jeniscatatan = 'Tidak Terdaftar';
        }
        if ($skck->jenis_catatan != '') {
            $jeniscatatan = $skck->jenis_catatan;
        }
        $pdf->Row2(['1.', 'Menurut pantauan, penelitian dan keterangan yang bersangkutan, bahwa sampai saat ini ' . $jeniscatatan . ' sebagai pengurus dan/atau anggota Organisasi Terlarang dan/atau Gerakan Terlarang;']);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Row2(['2.', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan penerbitan Surat Keterangan Catatan Kepolisian, untuk keperluan: ' . $skck->penggunaan_surat]);
        $pdf->Ln(7);
        $pdf->SetWidths([170]);
        $pdf->SetX(19);
        $pdf->Row2(['Demikian Surat Keterangan ini dibuat untuk dipergunakan seperlunya.']);

        // keterangan Surat
        $pdf->ln(15);
        if ($skck->pejabat_camat_id == 1) {
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
        $pdf->Cell(0, 10, $namadesa . ', ', 0, '', 'C');
        $pdf->Ln(5);
        if ($skck->penandatangan == 'Atasnama Pimpinan' || $skck->penandatangan == 'Jabatan Struktural') {
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
        if ($skck->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($skck->jabatan_lainnya);

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
        if ($skck->penandatangan != 'Atasnama Pimpinan' && $skck->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($skck->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($skck->penandatangan);
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
            if ($skck->penandatangan == 'Pimpinan Organisasi' && $skck->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($skck->penandatangan);
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
        $pdf->AddPage();
        $pdf->Ln(10);
        $hari3 = substr($skck->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($skck->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($skck->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($skck->tanggal, 3, 2)];
        }
        $tahun3 = substr($skck->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;

        $pdf->SetX(145);
        $pdf->Cell(0, 0, $namadesa . ', ' . $tempatlahir3, 0, '', '');
        $pdf->Ln(10);
        $pdf->SetX(27);
        $pdf->Cell(0, 0, 'Perihal :  Permohonan Penerbitan SKCK', 0, '', '');
        $pdf->SetX(145);
        $pdf->Cell(0, 0, 'Kepada :', 0, '', '');
        $pdf->Ln(5);
        $pdf->SetX(42);
        $pdf->Cell(0, 0, 'guna kelengkapan Melamar Pekerjaan', 0, '', '');
        $pdf->SetX(137);
        $pdf->Cell(0, 0, 'Yth.  Bapak/Ibu Kapolsek ' . $namadesa, 0, '', '');
        $pdf->Ln(5);
        $pdf->SetX(145);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 0, 'di-', 0, '', '');
        $pdf->Ln(5);
        $pdf->SetX(150);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 0, $namadesa, 0, '', '');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(15);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Dengan hormat,', 5, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Yang bertanda tangan di bawah ini:', 5, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $skck->nik, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Nama Lengkap ', 0, '', 'L');

        // jika penduduk
        if ($skck->jenis_penduduk == 1) {
            $penduduklain = $this->penduduklain->cekpenduduklaincetak($skck->pribadi->id);
            $keluarga = $this->keluarga->cekalamat($skck->pribadi->id);
            if ($skck->pribadi->titel_belakang != '') {
                $namalengkap = $skck->pribadi->titel_depan . ' ' . $skck->pribadi->nama . ', ' . $skck->pribadi->titel_belakang;
            }
            if ($skck->pribadi->titel_belakang == '') {
                $namalengkap = $skck->pribadi->titel_depan . ' ' . $skck->pribadi->nama . '' . $skck->pribadi->titel_belakang;
            }
            $hari = substr($skck->pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($skck->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($skck->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($skck->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($skck->pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $skck->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $jk = $skck->pribadi->jk->jk;
            if ($skck->pribadi->gol_darah_id != 13) {
                $golongandarah = $skck->pribadi->golongan_darah->golongan_darah;
            }
            if ($skck->pribadi->gol_darah_id == 13) {
                $golongandarah = '--';
            }
            $agama = $skck->pribadi->agama->agama;
            $perkawinanan = $skck->pribadi->perkawinan->kawin;
            if ($skck->pribadi->pekerjaan_id == 89) {
                $pekerjaan = $skck->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $skck->pribadi->pekerjaan->pekerjaan;
            }
            if ($penduduklain != null) {
                $kewarganegaraan = 'Kewarganegaraan ' . $penduduklain->penduduk_lain;
            } else {
                $kewarganegaraan = 'Warga Negara Indonesia';
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap2 = $statusdesa . ' ' . $namadesa . ' ' . $statuskecamatan . ' ' . $kecamatan . ' ' . $status . ' ' . $kabupaten;
        }
        if ($skck->dasar_keterangan_jenis == '') {
            $keterangan = 'menurut data, catatan dan keterangan yang bersangkutan';
        } else {
            $keterangan = 'menurut ' . $skck->dasar_keterangan_jenis;
        }
        $pdf->Cell(11);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(120, -15, ':    ' . strtoupper($namalengkap), 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Jenis Kelamin ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $jk, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Golongan Darah ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $golongandarah, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Agama', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Status Perkawinan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $perkawinanan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Pekerjaan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Kewarganegaraan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $kewarganegaraan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Alamat Domisili', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetWidths([5, 170]);
        $pdf->Ln(-5);
        $pdf->Cell(54);
        $pdf->Row2(['', $alamatlengkap2]);
        $pdf->SetWidths([175]);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Rowberpergian(['Bersama ini mohon dengan hormat agar dapatnya saya diterbitkan Surat Keterangan Catatan Kepolisian (SKCK) yang akan saya pergunakan sebagai kelengkapan : ' . $skck->penggunaan_surat]);
        $pdf->SetWidths([175]);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Rowberpergian(['Sebagai bahan pertimbangan Bapak Kapolsek Kepanjen dalam menerbitan Surat Keterangan Catatan Kepolisian (SKCK), bersama surat permohonan ini saya lampirkan berkas-berkas sebagai berikut :']);
        $pdf->SetWidths([5, 176]);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Row2(['1.', 'Foto terbaru berwarna terbaru ukuran 4 x 6 sebanyak 6 (enam) lembar;']);
        $pdf->Ln(4);
        $pdf->SetX(27);
        $pdf->Row2(['2.', 'Fotokopi Kartu Tanda Penduduk dan Kartu Keluarga sebanyak 2 (dua) lembar;']);
        $pdf->Ln(4);
        $pdf->SetX(27);
        if ($skck->jenis_catatan == '') {
            $jeniscatatan = 'Tidak Terdaftar';
        }
        if ($skck->jenis_catatan != '') {
            $jeniscatatan = $skck->jenis_catatan;
        }
        $pdf->Row2(['3.', 'Surat  Keterangan ' . $jeniscatatan . ' sebagai  Pelaku  dan/atau  Anggota  Organisasi Terlarang/Gerakan Terlarang.']);
        $pdf->Ln(10);
        $pdf->SetWidths([170]);
        $pdf->SetX(45);
        $pdf->Row2(['Demikian untuk menjadikan periksa dan petunjuknya lebih lanjut.']);
        $pdf->SetX(25);
        $pdf->Ln(10);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($skck->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($skck->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($skck->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetX(20);
        $pdf->Cell(55, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $skck->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $skck->tahun, 0, '', 'C');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->Cell(55, 0, 'Mengetahui :', 0, '', 'C');
        $pdf->Ln(5);
        $pdf->SetX(27);
        $pdf->Cell(55, 0, $namadesa . ', ' . $tempatlahir3, 0, '', '');
        $pdf->Ln(5);
        if ($skck->penandatangan == 'Atasnama Pimpinan' || $skck->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(25);
            if ($pejabatpimpinan != null) {
                if ($pejabatpimpinan->keterangan != '') {
                    $keteraganjabatan = $pejabatpimpinan->keterangan . ' ';
                }
                if ($pejabatpimpinan->keterangan == '') {
                    $keteraganjabatan = '';
                }
                $pdf->Cell(50, 0, $an . ' ' . $keteraganjabatan . strtoupper($pejabatpimpinan->jabatan) . ' ' . strtoupper($namadesa) . ',', 0, '', 'C');

            } else {
                $pdf->Cell(50, 0, $an . ' ' . strtoupper($namadesa), 0, '', 'C');

            }
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetX(25);
            if ($pejabat != null) {
                $idpejabat = 'Sekretaris Organisasi';
                $pejabatsekre = $this->pejabat->cekjabatan($idpejabat);
                if ($pejabatsekre != null) {
                    if ($pejabatsekre->keterangan != '') {
                        $keteraganjabatan2 = $pejabatsekre->keterangan . ' ';
                    }
                    if ($pejabatsekre->keterangan == '') {
                        $keteraganjabatan2 = '';
                    }
                    $pdf->Cell(50, 0, $keteraganjabatan2 . $pejabatsekre->jabatan . ',', 0, '', 'C');
                }
            }

        }
        if ($skck->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($skck->jabatan_lainnya);

            $pdf->Ln(4);
            $pdf->SetX(25);
            $pdf->Cell(50, 0, 'u.b.', 0, '', 'C');
            $pdf->Ln(4);
            $pdf->SetX(25);
            if ($pejabatstruktural != null) {
                if ($pejabat->keterangan != '') {
                    $keteraganjabatan3 = $pejabat->keterangan . ' ';
                }
                if ($pejabat->keterangan == '') {
                    $keteraganjabatan3 = '';
                }
                $pdf->Cell(50, 0, $keteraganjabatan3 . $pejabat->jabatan . ',', 0, '', 'C');
            }
        }
        if ($skck->penandatangan != 'Atasnama Pimpinan' && $skck->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(25);
            if ($skck->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($skck->penandatangan);
                if ($pejabatsekretaris != null) {
                    if ($pejabatsekretaris->keterangan != '') {
                        $keteraganjabatan4 = $pejabatsekretaris->keterangan . ' ';
                    }
                    if ($pejabatsekretaris->keterangan == '') {
                        $keteraganjabatan4 = '';
                    }
                    $pdf->Cell(50, 0, $keteraganjabatan4 . strtoupper($pejabatsekretaris->jabatan . ','), 0, '', 'C');
                }
            }
            if ($skck->penandatangan == 'Pimpinan Organisasi' && $skck->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($skck->penandatangan);
                if ($pejabatsekretaris != null) {
                    if ($pejabatsekretaris->keterangan != '') {
                        $keteraganjabatan5 = $pejabatsekretaris->keterangan . ' ';
                    }
                    if ($pejabatsekretaris->keterangan == '') {
                        $keteraganjabatan5 = '';
                    }
                    $pdf->Cell(50, 0, $keteraganjabatan5 . strtoupper($pejabatsekretaris->jabatan . ' ' . $namadesa . ','), 0, '', 'C');
                }
            }

        }
        $pdf->Ln(20);

        if ($pejabat != null) {
            $pdf->SetX(120);
            $pdf->SetFont('Arial', 'BU', 10);
            if ($pejabat->titel_belakang != '' && $pejabat->titel_depan != '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan != '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, $pejabat->titel_depan . ' ' . $pejabat->nama, 0, '', 'C');
            } else if ($pejabat->titel_belakang != '' && $pejabat->titel_depan == '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, $pejabat->nama . ', ' . $pejabat->titel_belakang, 0, '', 'C');
            } else if ($pejabat->titel_belakang == '' && $pejabat->titel_depan == '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, $pejabat->nama, 0, '', 'C');
            }
            $pdf->SetFont('Arial', '', 10);
            $pdf->Ln(4);
            $pdf->SetX(-320);
            $pdf->Cell(0, 10, $pejabat->pangkat, 0, '', 'C');
            $pdf->Ln(5);

            if ($pejabat->nip != '') {
                $pdf->SetX(-320);
                $pdf->Cell(0, 10, 'NIP.' . $pejabat->nip, 0, '', 'C');
            }
        }

        $pdf->SetX(150);
        $pdf->Cell(50, -75, 'Hormat Saya,', 0, '', 'C');
        $pdf->Ln(2);
        $pdf->SetX(150);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -72, 'PEMOHON,', 0, '', 'C');
        $pdf->Ln(30);
        $pdf->SetX(150);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -70, $namalengkap, 0, '', 'C');
        $pdf->SetX(30);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(-20);
        $pdf->Cell(0, 0, 'Menyetujui dan Merekomendasi:', 0, '', 'C');
        $pdf->Ln(7);
        $pdf->SetFont('Arial', 'B', 10);

        if ($skck->pejabat_camat_id == 1) {
            $pdf->SetX(15);

            $pdf->Cell(50, 0, 'KAPOLSEK ' . strtoupper($kecamatan) . ',', 0, '', 'C');
        }
        if ($skck->pejabat_camat_id == 1) {
            $pdf->SetX(85);

            $pdf->Cell(50, 0, 'DAN RAMIL ' . strtoupper($kecamatan) . ',', 0, '', 'C');
        }
        if ($skck->pejabat_camat_id == 1) {
            $pdf->SetX(155);

            $pdf->Cell(50, 0, 'CAMAT ' . strtoupper($kecamatan) . ',', 0, '', 'C');
        }


        $pdf->Output('cetak-data-skck' . $tanggal . '.pdf', 'I');
        exit;
    }
}