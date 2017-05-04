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

class CetakKematianKutipan extends Controller
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
        KematianRepository $KematianRepository,
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
        RincianNonPendudukRepository $rincianNonPendudukRepository,
        DokumenPendudukRepository $dokumenPendudukRepository,
        OrganisasiRepository $organisasiRepository
    )
    {
        $this->Kematian = $KematianRepository;
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
        $this->dokumen = $dokumenPendudukRepository;
        $this->rincian = $rincianNonPendudukRepository;

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
        $Kematian = $this->Kematian->find($id);
        $jeniskodeadministrasi = $this->Kematian->cekkodejenisadministrasi($Kematian->jenis_pelayanan_id);
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
        if ($Kematian->is_penduduk_layan != null) {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN KELAHIRAN' . ' ' . strtoupper($Kematian->is_penduduk_layan), 0, '', 'C');

        } else {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN KELAHIRAN', 0, '', 'C');

        }
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($Kematian->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($Kematian->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($Kematian->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $Kematian->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $Kematian->tahun, 0, '', 'C');

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

    public function KematianKutipan($id)
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
        $pdf->SetTitle('Surat Kematian Kutipan');
        $this->Kop($pdf, $id);
        $pdf->SetY(50);
        $desa = $this->desa->find(session('desa'));
        $cekwaktu = $this->kodeadministrasi->cekwaktuadministrasi();
        $Kematian = $this->Kematian->find($id);
        if ($Kematian->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($Kematian->penandatangan);
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
                    $namalengkappejabat = $pejabat->pribadi->titel_depan . ' ' . $pejabat->nama . ', ' . $pejabat->titel_belakang;
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

            if ($Kematian->penandatangan == 'Jabatan Struktural') {
                $pejabatstruktural2 = $this->pejabat->find($Kematian->jabatan_lainnya);
                if ($pejabatstruktural2->keterangan != '') {

                    $namajabatan = $pejabatstruktural2->keterangan . ' ' . $pejabatstruktural2->jabatan;
                }
                if ($pejabatstruktural2->keterangan == '') {
                    $namajabatan = $pejabatstruktural2->jabatan;
                }
            }
            if ($Kematian->penandatangan != 'Jabatan Struktural') {

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
        if ($Kematian->penandatangan == 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $statusdesa1 . ' ' . $namadesa, 0, '', 'L');
        }
        if ($Kematian->penandatangan != 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $namadesa, 0, '', 'L');
        }
//tanggal lahir
        if($Kematian->nama_dokumen != '') {
            if ($Kematian->jenis_penduduk == 1) {
                $dokumen = $this->dokumen->cekdokumenkematian($Kematian->penduduk_id);
                $tanggaldokumen = $dokumen->tanggal;
                $nomordokumen = $dokumen->nomor_dokumen;
            }
            if ($Kematian->jenis_penduduk == 2) {
                $dokumen = $this->rincian->cekdokumenkematian($Kematian->penduduk_id);
                $tanggaldokumen = $dokumen->nik;
                $nomordokumen = $dokumen->rincian_non_penduduk;
            }
            $hari = substr($tanggaldokumen, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($tanggaldokumen, 3, 2) <= 9) {
                $bulan = $indo[substr($tanggaldokumen, 4, 1)];
            } else {
                $bulan = $indo[substr($tanggaldokumen, 3, 2)];
            }
            $tahun = substr($tanggaldokumen, 6, 4);

            $tanggal_dokumen = $hari . ' ' . $bulan . ' ' . $tahun;

                $dokumenlist = 'dengan ini menerangkan bahwa merujuk Surat: ' . $Kematian->nama_dokumen . ' Nomor: ' . $nomordokumen . ', Tanggal: ' . $tanggal_dokumen . ',  sesuai dengan keterangan dari seorang:';
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetWidths([120]);
                $pdf->SetX(14);
                $pdf->Row3([$dokumenlist]);

                $pdf->Ln(10);
        }
        if($Kematian->nama_dokumen == '') {
            $dokumenlist = 'dengan ini menerangkan bahwa sesuai dengan keterangan dari seorang:';
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetWidths([ 120]);
            $pdf->SetX(14);
            $pdf->Row3([ $dokumenlist]);
            $pdf->Ln(10);

        }

//
//        ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//        pelapor kematian


        if ($Kematian->pelapor_penduduk == 1) {
            $pelaporlist = $this->pribadi->find($Kematian->pelapor_penduduk_id);

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
        if ($Kematian->pelapor_penduduk == 2) {
            $pelaporlist = $this->nonpenduduk->find($Kematian->pelapor_penduduk_id);

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
        $pdf->Cell(120, -15, $Kematian->pelapor_nik, 0, '', 'L');
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
        $pdf->Cell(25, -15, 'Hubungan Keluarga', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $Kematian->shdrt->shdrt, 0, '', 'L');
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
        $datetime = \DateTime::createFromFormat('d/m/Y', $Kematian->tanggal_kematian);
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
        $hari = substr($Kematian->tanggal_kematian, 0, 2);
        $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($Kematian->tanggal_kematian, 3, 2) <= 9) {
            $bulan = $indo[substr($Kematian->tanggal_kematian, 4, 1)];
        } else {
            $bulan = $indo[substr($Kematian->tanggal_kematian, 3, 2)];
        }
        $tahun = substr($Kematian->tanggal_kematian, 6, 4);

        $tanggal_kematiancetak = $hari . ' ' . $bulan . ' ' . $tahun;
        if ($cekwaktu != null) {
            $waktubagian = ' ' . $cekwaktu->kode;
        }
        if ($cekwaktu == null) {
            $waktubagian = '';
        }
        if ($Kematian->jenis_penduduk == 1) {
            if ($Kematian->pribadi->titel_belakang != '') {
                if ($Kematian->pribadi->titel_depan != '') {
                    $namaJenaza = $Kematian->pribadi->titel_depan . ' ' . $Kematian->pribadi->nama . ', ' . $Kematian->pribadi->titel_belakang;
                }
                if ($Kematian->pribadi->titel_depan == '') {
                    $namaJenaza = $Kematian->pribadi->titel_depan . '' . $Kematian->pribadi->nama . ', ' . $Kematian->pribadi->titel_belakang;
                }
            }
            if ($Kematian->pribadi->titel_belakang == '') {
                if ($Kematian->pribadi->titel_depan != '') {
                    $namaJenaza = $Kematian->pribadi->titel_depan . ' ' . $Kematian->pribadi->nama . '' . $Kematian->pribadi->titel_belakang;
                }
                if ($Kematian->pribadi->titel_depan == '') {
                    $namaJenaza = $Kematian->pribadi->titel_depan . '' . $Kematian->pribadi->nama . '' . $Kematian->pribadi->titel_belakang;
                }
            }
            $jeniskelaminjenazah = $Kematian->pribadi->jk->jk;
            $agamajenazah = $Kematian->pribadi->agama->agama;
            $umurjenazah = substr($Kematian->pribadi->tanggal_lahir, 6, 4);
            $keluarga = $this->keluarga->cekalamat($Kematian->pribadi->id);
            $alamatjenazah = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkapjenazah = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        if ($Kematian->jenis_penduduk == 2) {
            if ($Kematian->non_penduduk->titel_belakang != '') {
                if ($Kematian->non_penduduk->titel_depan != '') {
                    $namaJenaza = $Kematian->non_penduduk->titel_depan . ' ' . $Kematian->non_penduduk->nama . ', ' . $Kematian->non_penduduk->titel_belakang;
                }
                if ($Kematian->non_penduduk->titel_depan == '') {
                    $namaJenaza = $Kematian->non_penduduk->titel_depan . '' . $Kematian->non_penduduk->nama . ', ' . $Kematian->non_penduduk->titel_belakang;
                }
            }
            if ($Kematian->non_penduduk->titel_belakang == '') {
                if ($Kematian->non_penduduk->titel_depan != '') {
                    $namaJenaza = $Kematian->non_penduduk->titel_depan . ' ' . $Kematian->non_penduduk->nama . '' . $Kematian->non_penduduk->titel_belakang;
                }
                if ($Kematian->non_penduduk->titel_depan == '') {
                    $namaJenaza = $Kematian->non_penduduk->titel_depan . '' . $Kematian->non_penduduk->nama . '' . $Kematian->non_penduduk->titel_belakang;
                }
            }
            $jeniskelaminjenazah = $Kematian->non_penduduk->jk->jk;
            $umurjenazah = substr($Kematian->non_penduduk->tanggal_lahir, 6, 4);
            $agamajenazah = $Kematian->non_penduduk->agama->agama;
            if ($Kematian->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $Kematian->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($Kematian->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $Kematian->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($Kematian->non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $Kematian->non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($Kematian->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $Kematian->non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($Kematian->non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $Kematian->non_penduduk->desa->desa;
            }
            if ($Kematian->non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $Kematian->non_penduduk->desa->desa;
            }
            if ($Kematian->non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $Kematian->non_penduduk->desa->desa;
            }
            if ($Kematian->non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $Kematian->non_penduduk->desa->desa;
            }

            $alamatjenazah = $Kematian->non_penduduk->alamat . ' RT. ' . $Kematian->non_penduduk->alamat_rt . ' RW. ' . $Kematian->non_penduduk->alamat_rw;
            $alamatlengkapjenazah = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
        }
        $umurss =date('Y') - $umurjenazah.' Tahun';
        $pdf->Ln(2);
        $pdf->SetWidths([130]);
        $pdf->SetX(14);
        $pdf->Row3(['Telah terjadi Peristiwa Kematian Penduduk di: '.$Kematian->tempat_kematian.', pada hari: ' . (strtolower($hariindo)) .', tanggal: ' .$tanggal_kematiancetak.', waktu: '.$Kematian->waktu_mati.$waktubagian.',  disebabkan karena: '.$Kematian->sebab_kematian.', pada usia ke: '. $umurss. ', terhadap seorang: ']);
        $pdf->Ln(10);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Nama', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(120, -15, $namaJenaza, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'NIK', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $Kematian->nik, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Jenis Kelamin', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->Cell(120, -15, $jeniskelaminjenazah, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Umur', 5, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(120, -15, date('Y') - $umurjenazah . ' Tahun', 0, '', 'L');
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
        $pdf->Cell(25, -15, 'Domisili Terakhir', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(120, -15, $alamatjenazah, 0, '', 'L');
        $pdf->SetWidths([5, 170]);
        $pdf->Ln(-6);
        $pdf->Cell(41);
        $pdf->Row2(['', $alamatlengkapjenazah]);
        $pdf->Ln(12);
        $pdf->SetX(14);
        $pdf->Cell(25, -15, 'Demikian Surat Keterangan ini dibuat untuk dipergunakan seperlunya.', 0, '', 'L');
        $pdf->SetX(85);
        $hari3 = substr($Kematian->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($Kematian->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($Kematian->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($Kematian->tanggal, 3, 2)];
        }
        $tahun3 = substr($Kematian->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;

        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(3);
        if ($Kematian->penandatangan == 'Atasnama Pimpinan' || $Kematian->penandatangan == 'Jabatan Struktural') {
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
        if ($Kematian->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($Kematian->jabatan_lainnya);

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
        if ($Kematian->penandatangan != 'Atasnama Pimpinan' && $Kematian->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(85);
            if ($Kematian->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($Kematian->penandatangan);
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
            if ($Kematian->penandatangan == 'Pimpinan Organisasi' && $Kematian->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($Kematian->penandatangan);
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

        $pdf->Output('cetak-data-Kematian-simduk' . $tanggal . '.pdf', 'I');
        exit;
    }
}