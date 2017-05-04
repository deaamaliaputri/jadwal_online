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
use App\Domain\Repositories\Pelayanan\GoipRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Penduduk\RincianNonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakGoip extends Controller
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
        GoipRepository $goipRepository,
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
        OrganisasiRepository $organisasiRepository
    )
    {
        $this->goip = $goipRepository;
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
        $pdf->SetFont('Times-Roman', '', 14);
        $desa = $this->desa->find(session('desa'));
        $goip = $this->goip->find($id);
        $jeniskodeadministrasi = $this->goip->cekkodejenisadministrasi($goip->jenis_pelayanan_id);
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
        if ($goip->is_penduduk_layan != null) {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN' . ' ' . strtoupper($goip->is_penduduk_layan), 0, '', 'C');

        } else {
            $pdf->Cell(0, 0, 'SURAT KETERANGAN', 0, '', 'C');

        }
        $pdf->Ln(5);
        $pdf->SetFont('arial', '', 10);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($goip->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($goip->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($goip->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }
        $pdf->SetX(25);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $goip->no_reg . '/' . $kodeadministrasikearsipanhasil . '/' . $goip->tahun, 0, '', 'C');

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
    }

    public function Goip($id)
    {
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
        $pdf->SetTitle('Surat Ghoib');
        $this->Kop($pdf, $id);
        $pdf->SetY(80);
        $desa = $this->desa->find(session('desa'));
        $goip = $this->goip->find($id);
        if ($goip->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($goip->penandatangan);
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

            if ($goip->penandatangan == 'Jabatan Struktural') {
                $pejabatstruktural2 = $this->pejabat->find($goip->jabatan_lainnya);
                if ($pejabatstruktural2->keterangan != '') {

                    $namajabatan = $pejabatstruktural2->keterangan . ' ' . $pejabatstruktural2->jabatan;
                }
                if ($pejabatstruktural2->keterangan == '') {
                    $namajabatan = $pejabatstruktural2->jabatan;
                }
            }
            if ($goip->penandatangan != 'Jabatan Struktural') {

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
        if ($goip->penandatangan == 'Jabatan Struktural') {
            $pdf->Cell(6, -15, ':     ', 0, '', 'L');
            $pdf->Cell(120, -15, $namajabatan . ' ' . $statusdesa1 . ' ' . $namadesa, 0, '', 'L');
        }
        if ($goip->penandatangan != 'Jabatan Struktural') {
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
        $pdf->Cell(120, -15, ':     ' . $goip->nik, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Nama Lengkap ', 0, '', 'L');

        // jika penduduk

        if ($goip->pejabat_desa_id == 1) {
            $penduduklain = $this->penduduklain->cekpenduduklaincetak($goip->pribadi->id);
            $keluarga = $this->keluarga->cekalamat($goip->pribadi->id);
            $namabapak = $this->orangtua->cekorangtuabapak($goip->pribadi->id);

            if ($goip->pribadi->jk->id == 1) {
                if ($goip->pribadi->titel_belakang != '') {
                    $namalengkap = $goip->pribadi->titel_depan . ' ' . $goip->pribadi->nama . ', ' . $goip->pribadi->titel_belakang . ' Bin ' . $namabapak->nama;
                }
                if ($goip->pribadi->titel_belakang == '') {
                    $namalengkap = $goip->pribadi->titel_depan . ' ' . $goip->pribadi->nama . '' . $goip->pribadi->titel_belakang . ' Bin ' . $namabapak->nama;
                }
            }
            if ($goip->pribadi->jk->id == 1) {
                if ($goip->pribadi->titel_belakang != '') {
                    $namalengkap = $goip->pribadi->titel_depan . ' ' . $goip->pribadi->nama . ', ' . $goip->pribadi->titel_belakang . ' Binti ' . $namabapak->nama;
                }
                if ($goip->pribadi->titel_belakang == '') {
                    $namalengkap = $goip->pribadi->titel_depan . ' ' . $goip->pribadi->nama . '' . $goip->pribadi->titel_belakang . ' Binti ' . $namabapak->nama;
                }
            }
            $hari = substr($goip->pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($goip->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($goip->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($goip->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($goip->pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $goip->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $jk = $goip->pribadi->jk->jk;
            if ($goip->pribadi->gol_darah_id != 13) {
                $golongandarah = $goip->pribadi->golongan_darah->golongan_darah;
            }
            if ($goip->pribadi->gol_darah_id == 13) {
                $golongandarah = '--';
            }
            $agama = $goip->pribadi->agama->agama;
            $perkawinanan = $goip->pribadi->perkawinan->kawin;
            if ($goip->pribadi->pekerjaan_id == 89) {
                $pekerjaan = $goip->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $goip->pribadi->pekerjaan->pekerjaan;
            }
            if ($penduduklain != null) {
                $kewarganegaraan = 'Kewarganegaraan ' . $penduduklain->penduduk_lain;
            } else {
                $kewarganegaraan = 'Warga Negara Indonesia';
            }
            //kabupaten
            if ($goip->pribadi->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $goip->pribadi->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($goip->pribadi->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $goip->pribadi->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($goip->pribadi->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $goip->pribadi->desa->kecamatan->kecamatan;
            }
            if ($goip->pribadi->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $goip->pribadi->desa->kecamatan->kecamatan;
            }
            //desa
            if ($goip->pribadi->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $goip->pribadi->desa->desa;
            }
            if ($goip->pribadi->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $goip->pribadi->desa->desa;
            }
            if ($goip->pribadi->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $goip->pribadi->desa->desa;
            }
            if ($goip->pribadi->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $goip->pribadi->desa->desa;
            }

            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rw . ' RW. ' . $keluarga->alamat_rw;
            $alamatwilayah = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
        }

        //jika non penduduk

        if ($goip->pejabat_desa_id == 2) {

            $namabapak = $this->rinciannonpenduduk->cekbapak($goip->non_penduduk->id);
            if ($namabapak == null) {
                $namabapaklist = '';
            }
            if ($namabapak != null) {
                $namabapaklist = $namabapak->rincian_non_penduduk;
            }
            if ($goip->non_penduduk->jk->id == 1) {
                if ($goip->non_penduduk->titel_belakang != '') {
                    $namalengkap = $goip->non_penduduk->titel_depan . ' ' . $goip->non_penduduk->nama . ', ' . $goip->non_penduduk->titel_belakang . ' Bin ' . $namabapaklist;
                }
                if ($goip->non_penduduk->titel_belakang == '') {

                    $namalengkap = $goip->non_penduduk->titel_depan . ' ' . $goip->non_penduduk->nama . '' . $goip->non_penduduk->titel_belakang . ' Bin ' . $namabapaklist;
                }
            }
            if ($goip->non_penduduk->jk->id == 2) {
                if ($goip->non_penduduk->titel_belakang != '') {
                    $namalengkap = $goip->non_penduduk->titel_depan . ' ' . $goip->non_penduduk->nama . ', ' . $goip->non_penduduk->titel_belakang . ' Binti ' . $namabapak->nama;
                }
                if ($goip->non_penduduk->titel_belakang == '') {
                    $namalengkap = $goip->non_penduduk->titel_depan . ' ' . $goip->non_penduduk->nama . '' . $goip->non_penduduk->titel_belakang . ' Binti ' . $namabapak->nama;
                }
            }
            $hari = substr($goip->non_penduduk->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($goip->non_penduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($goip->non_penduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($goip->non_penduduk->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($goip->non_penduduk->tanggal_lahir, 6, 4);
            $tempatlahir = $goip->non_penduduk->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $jk = $goip->non_penduduk->jk->jk;
            if ($goip->non_penduduk->gol_darah_id != 13) {
                $golongandarah = $goip->non_penduduk->golongan_darah->golongan_darah;
            }
            if ($goip->non_penduduk->gol_darah_id == 13) {
                $golongandarah = '--';
            }
            $agama = $goip->non_penduduk->agama->agama;
            $perkawinanan = $goip->non_penduduk->perkawinan->kawin;
            if ($goip->non_penduduk->pekerjaan_id == 89) {
                $pekerjaan = $goip->non_penduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $goip->non_penduduk->pekerjaan->pekerjaan;
            }
            $kewarganegaraan = 'Warga Negara Indonesia';
            //kabupaten
            if ($goip->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $goip->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($goip->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $goip->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($goip->non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $goip->non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($goip->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $goip->non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($goip->non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $goip->non_penduduk->desa->desa;
            }
            if ($goip->non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $goip->non_penduduk->desa->desa;
            }
            if ($goip->non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $goip->non_penduduk->desa->desa;
            }
            if ($goip->non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $goip->non_penduduk->desa->desa;
            }

            $alamat = $goip->non_penduduk->alamat . ' RT. ' . $goip->non_penduduk->alamat_rt . ' RW. ' . $goip->non_penduduk->alamat_rw;
            $alamatwilayah = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
        }
        if ($goip->dasar_keterangan_jenis == '') {
            $keterangan = 'menurut data, catatan dan keterangan yang bersangkutan';
        } else {
            $keterangan = 'menurut ' . $goip->dasar_keterangan_jenis;
        }
        $pdf->Cell(11);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(5.6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(-1);
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
        $pdf->Cell(25, -15, 'Alamat Domisili', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetWidths([5, 170]);
        $pdf->Ln(-5);
        $pdf->Cell(54);
        $pdf->Row2(['', $alamatwilayah]);
        $pdf->SetWidths([5, 170]);
        $pdf->Ln(2);
        $pdf->SetX(22);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetAligns(['', 'J']);

        if ($goip->terguguat_jenis_id == 1) {
            $namabapaktergugat = $this->orangtua->cekorangtuabapak($goip->pribadi_tergugat->id);
            $namabapaklengkap1 = $namabapaktergugat->nama;
            if ($goip->pribadi_tergugat->jk_id == 1) {
                $namalengkaptergugat = $goip->pribadi_tergugat->nama . ' Bin ' . $namabapaklengkap1;
            }
            if ($goip->pribadi_tergugat->jk_id == 2) {
                $namalengkaptergugat = $goip->pribadi_tergugat->nama . ' Binti ' . $namabapaklengkap1;
            }
            $nikktergugat = $goip->pribadi_tergugat->nik;
            $tahun = substr($goip->pribadi_tergugat->tanggal_lahir, 6, 4);
            $umur = date('Y') - $tahun;
            $agamatergugat = $goip->pribadi_tergugat->agama->agama;
            if ($goip->pribadi_tergugat->pekerjaan_id == 89) {
                $pekerjaantergugat = $goip->pribadi_tergugat->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaantergugat = $goip->pribadi_tergugat->pekerjaan->pekerjaan;
            }
            $keluargatergugat = $this->keluarga->cekalamat($goip->pribadi_tergugat->id);

            //kabupaten
            if ($goip->pribadi_tergugat->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $goip->pribadi_tergugat->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($goip->pribadi_tergugat->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $goip->pribadi_tergugat->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($goip->pribadi_tergugat->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $goip->pribadi_tergugat->desa->kecamatan->kecamatan;
            }
            if ($goip->pribadi_tergugat->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $goip->pribadi_tergugat->desa->kecamatan->kecamatan;
            }
            //desa
            if ($goip->pribadi_tergugat->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $goip->pribadi_tergugat->desa->desa;
            }
            if ($goip->pribadi_tergugat->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $goip->pribadi_tergugat->desa->desa;
            }
            if ($goip->pribadi_tergugat->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $goip->pribadi_tergugat->desa->desa;
            }
            if ($goip->pribadi_tergugat->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $goip->pribadi_tergugat->desa->desa;
            }

            $alamattergugat = $keluargatergugat->alamat . ' RT. ' . $keluargatergugat->alamat_rt . ' RW. ' . $keluargatergugat->alamat_rw;
            $alamatwilayahtergugat = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
        }


        if ($goip->terguguat_jenis_id == 2) {
            $namabapaktergugat = $this->rinciannonpenduduk->cekbapak($goip->non_penduduk_tergugat->id);
            if ($namabapaktergugat == null) {
                $namabapaklist3 = '';
            }
            if ($namabapaktergugat != null) {
                $namabapaklist3 = $namabapak->rincian_non_penduduk;
            }
            if ($goip->non_penduduk_tergugat->jk_id == 1) {
                $namalengkaptergugat = $goip->non_penduduk_tergugat->nama . ' Bin ' . $namabapaklist3;
            }
            if ($goip->non_penduduk_tergugat->jk_id == 2) {
                $namalengkaptergugat = $goip->non_penduduk_tergugat->nama . ' Binti ' . $namabapaklist3;
            }
            $nikktergugat = $goip->non_penduduk_tergugat->nik;
            $tahun = substr($goip->non_penduduk_tergugat->tanggal_lahir, 6, 4);
            $umur = date("y") - $tahun;
            $agamatergugat = $goip->non_penduduk_tergugat->agama->agama;
            if ($goip->non_penduduk_tergugat->pekerjaan_id == 89) {
                $pekerjaantergugat = $goip->non_penduduk_tergugat->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaantergugat = $goip->non_penduduk_tergugat->pekerjaan->pekerjaan;
            }
            //kabupaten
            if ($goip->non_penduduk_tergugat->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $goip->non_penduduk_tergugat->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($goip->non_penduduk_tergugat->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $goip->non_penduduk_tergugat->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($goip->non_penduduk_tergugat->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $goip->non_penduduk_tergugat->desa->kecamatan->kecamatan;
            }
            if ($goip->non_penduduk_tergugat->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $goip->non_penduduk_tergugat->desa->kecamatan->kecamatan;
            }
            //desa
            if ($goip->non_penduduk_tergugat->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $goip->non_penduduk_tergugat->desa->desa;
            }
            if ($goip->non_penduduk_tergugat->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $goip->non_penduduk_tergugat->desa->desa;
            }
            if ($goip->non_penduduk_tergugat->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $goip->non_penduduk_tergugat->desa->desa;
            }
            if ($goip->non_penduduk_tergugat->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $goip->non_penduduk_tergugat->desa->desa;
            }

            $alamattergugat = $goip->non_penduduk_tergugat->alamat . ' RT. ' . $goip->non_penduduk_tergugat->alamat_rt . ' RW. ' . $goip->non_penduduk_tergugat->alamat_rw;
            $alamatwilayahtergugat = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
        }
        if ($goip->pejabat_desa_id == 1) {
            if ($goip->pribadi->jk_id == 1) {
                $suamioristri = 'Suami';
            }
            if ($goip->pribadi->jk_id == 2) {
                $suamioristri = 'Istri';
            }
        }
        if ($goip->pejabat_desa_id == 2) {
            if ($goip->non_penduduk->jk_id == 1) {
                $suamioristri = 'Suami';
            }
            if ($goip->non_penduduk->jk_id == 2) {
                $suamioristri = 'Istri';
            }
        }
        $pdf->Row2(['', 'sesuai data dan catatan Dokumen Perkawinan yang bersangkutan adalah ' . $suamioristri . ' dari: ']);
        $pdf->Ln(15);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Nama Lengkap ', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(6, -15, ':     ', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, -15, '' . $namalengkaptergugat, 0, '', 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'NIK', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $nikktergugat, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Umur', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $umur . ' Tahun', 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Agama', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $agamatergugat, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Pekerjaan', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $pekerjaantergugat, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, 'Alamat Domisili', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, ':     ' . $alamattergugat, 0, '', 'L');
        $pdf->Ln(6);
        $pdf->SetX(27);
        $pdf->Cell(25, -15, '', 0, '', 'L');
        $pdf->Cell(11);
        $pdf->Cell(120, -15, '      ' . $alamatwilayahtergugat, 0, '', 'L');
        $pdf->Ln(-4);
        $pdf->SetX(22);

        //confert tanggal create

        $tlamatinggalgoip = $this->goip->ceksampaikapangoip($goip->id);
        $hari3 = substr($goip->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($goip->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($goip->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($goip->tanggal, 3, 2)];
        }
        $tahun3 = substr($goip->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;

        //confert tanggal mulai ditinggalkan

        $hari4 = substr($goip->lama_tinggal, 0, 2);
        if (substr($goip->lama_tinggal, 3, 2) <= 9) {
            $bulan4 = $indo3[substr($goip->lama_tinggal, 4, 1)];
        } else {
            $bulan4 = $indo3[substr($goip->lama_tinggal, 3, 2)];
        }
        $tahun4 = substr($goip->lama_tinggal, 6, 4);
        $tempatlahir4 = $hari4 . ' ' . $bulan4 . ' ' . $tahun4;
        if ($goip->pejabat_desa_id == 1) {
            if ($goip->pribadi_tergugat->jk_id == 1) {
                $suamioristri1 = 'suami';
            }
            if ($goip->pribadi_tergugat->jk_id == 2) {
                $suamioristri1 = 'istri';
            }
        }
        if ($goip->pejabat_desa_id == 2) {
            if ($goip->non_penduduk_tergugat->jk_id == 1) {
                $suamioristri1 = 'suami';
            }
            if ($goip->non_penduduk_tergugat->jk_id == 2) {
                $suamioristri1 = 'istri';
            }
        }

        //tidak diberi kabar
//        tidak diberi kabar keberadaanya
        //domisili
        // tidak diketahui domisili tempat tinggalnya
        //kewajipan
        //tidak memenuhi kewajiban sebagai seorang suami /istri
        //tidak menafkahkan
        //tidak memberikan nafkah lahir/batin kepada suami/istri dan/atau anaknya
//        lainnya
//        dan lainnya

        //if alasan 1 sampai 5
        if ($goip->jenis_alasan_gugatan1 != 'Lainnya') {
            if ($goip->jenis_alasan_gugatan1 == 'Tidak Diberi Kabar') {
                $alasangugatan1 = 'tidak diberi kabar keberadaanya';
            }
            if ($goip->jenis_alasan_gugatan1 == 'Tidak Diketahui Domisili') {
                $alasangugatan1 = 'tidak diketahui domisili tempat tinggalnya';
            }
            if ($goip->jenis_alasan_gugatan1 == 'Tidak Memenuhi Kewajiban') {
                $alasangugatan1 = 'tidak memenuhi kewajiban sebagai seorang ' . $suamioristri1;
            }
            if ($goip->jenis_alasan_gugatan1 == 'Tidak Menafkahi') {
                $alasangugatan1 = 'tidak memberikan nafkah lahir/batin kepada ' . $suamioristri1 . ' dan/atau ke- ' . $goip->jumlah_anak . ' orang anaknya';
            }
            if ($goip->jenis_alasan_gugatan1 == 1) {
                $alasangugatan1 = 1;
            }
        } else if ($goip->jenis_alasan_gugatan1 == 'Lainnya') {
            $alasangugatan1 = 'dan ' . $goip->jenis_alasan_lainnya;
        }
        if ($goip->jenis_alasan_gugatan2 != 'Lainnya') {
            if ($goip->jenis_alasan_gugatan2 == 'Tidak Diberi Kabar') {
                $alasangugatan2 = 'tidak diberi kabar keberadaanya';
            }
            if ($goip->jenis_alasan_gugatan2 == 'Tidak Diketahui Domisili') {
                $alasangugatan2 = 'tidak diketahui domisili tempat tinggalnya';
            }
            if ($goip->jenis_alasan_gugatan2 == 'Tidak Memenuhi Kewajiban') {
                $alasangugatan2 = 'tidak memenuhi kewajiban sebagai seorang ' . $suamioristri1;
            }
            if ($goip->jenis_alasan_gugatan2 == 'Tidak Menafkahi') {
                $alasangugatan2 = 'tidak memberikan nafkah lahir/batin kepada ' . $suamioristri1 . ' dan/atau ke- ' . $goip->jumlah_anak . ' orang anaknya';
            }
            if ($goip->jenis_alasan_gugatan2 == 1) {
                $alasangugatan2 = 1;
            }

        } else if ($goip->jenis_alasan_gugatan2 == 'Lainnya') {
            $alasangugatan2 = 'dan ' . $goip->jenis_alasan_lainnya;
        }
        if ($goip->jenis_alasan_gugatan3 != 'Lainnya') {
            if ($goip->jenis_alasan_gugatan3 == 'Tidak Diberi Kabar') {
                $alasangugatan3 = 'tidak diberi kabar keberadaanya';
            }
            if ($goip->jenis_alasan_gugatan3 == 'Tidak Diketahui Domisili') {
                $alasangugatan3 = 'tidak diketahui domisili tempat tinggalnya';
            }
            if ($goip->jenis_alasan_gugatan3 == 'Tidak Memenuhi Kewajiban') {
                $alasangugatan3 = 'tidak memenuhi kewajiban sebagai seorang ' . $suamioristri1;
            }
            if ($goip->jenis_alasan_gugatan3 == 'Tidak Menafkahi') {
                $alasangugatan3 = 'tidak memberikan nafkah lahir/batin kepada ' . $suamioristri1 . ' dan/atau ke- ' . $goip->jumlah_anak . ' orang anaknya';
            }
            if ($goip->jenis_alasan_gugatan3 == 1) {
                $alasangugatan3 = 1;
            }

        } else if ($goip->jenis_alasan_gugatan3 == 'Lainnya') {
            $alasangugatan3 = 'dan ' . $goip->jenis_alasan_lainnya;
        }
        if ($goip->jenis_alasan_gugatan4 != 'Lainnya') {
            if ($goip->jenis_alasan_gugatan4 == 'Tidak Diberi Kabar') {
                $alasangugatan4 = 'tidak diberi kabar keberadaanya';
            }
            if ($goip->jenis_alasan_gugatan4 == 'Tidak Diketahui Domisili') {
                $alasangugatan4 = 'tidak diketahui domisili tempat tinggalnya';
            }
            if ($goip->jenis_alasan_gugatan4 == 'Tidak Memenuhi Kewajiban') {
                $alasangugatan4 = 'tidak memenuhi kewajiban sebagai seorang ' . $suamioristri1;
            }
            if ($goip->jenis_alasan_gugatan4 == 'Tidak Menafkahi') {
                $alasangugatan4 = 'tidak memberikan nafkah lahir/batin kepada ' . $suamioristri1 . ' dan/atau ke- ' . $goip->jumlah_anak . ' orang anaknya';
            }
            if ($goip->jenis_alasan_gugatan4 == 1) {
                $alasangugatan4 = 1;
            }

        } else if ($goip->jenis_alasan_gugatan4 == 'Lainnya') {
            $alasangugatan4 = 'dan ' . $goip->jenis_alasan_lainnya;
        }
        if ($goip->jenis_alasan_gugatan5 != 'Lainnya') {
            if ($goip->jenis_alasan_gugatan5 == 'Tidak Diberi Kabar') {
                $alasangugatan5 = 'tidak diberi kabar keberadaanya';
            }
            if ($goip->jenis_alasan_gugatan5 == 'Tidak Diketahui Domisili') {
                $alasangugatan5 = 'tidak diketahui domisili tempat tinggalnya';
            }
            if ($goip->jenis_alasan_gugatan5 == 'Tidak Memenuhi Kewajiban') {
                $alasangugatan5 = 'tidak memenuhi kewajiban sebagai seorang ' . $suamioristri1;
            }
            if ($goip->jenis_alasan_gugatan5 == 'Tidak Menafkahi') {
                $alasangugatan5 = 'tidak memberikan nafkah lahir/batin kepada ' . $suamioristri1 . ' dan/atau ke- ' . 'ke-' . $goip->jumlah_anak . ' orang anaknya';
            }
            if ($goip->jenis_alasan_gugatan5 == 1) {
                $alasangugatan5 = 1;
            }

        } else if ($goip->jenis_alasan_gugatan5 == 'Lainnya') {
            $alasangugatan5 = 'dan ' . $goip->jenis_alasan_lainnya;
        }
//dump($alasangugatan1);
//dump($alasangugatan2);
//dump($alasangugatan3);
//dump($alasangugatan4);
        if ($goip->jenis_alasan_gugatan5 != 1) {
            $alasangugatan = $alasangugatan1 . ', ' . $alasangugatan2 . ', ' . $alasangugatan3 . ', ' . $alasangugatan4 . ', ' . $alasangugatan5 . ' ';
        } else if ($alasangugatan5 == 1 && $alasangugatan4 != 1) {
            $alasangugatan = $alasangugatan1 . ', ' . $alasangugatan2 . ', ' . $alasangugatan3 . ', ' . $alasangugatan4 . ' ';
        } else if ($alasangugatan5 == 1 && $alasangugatan4 == 1 && $alasangugatan3 != 1) {
            $alasangugatan = $alasangugatan1 . ', ' . $alasangugatan2 . ', ' . $alasangugatan3 . ' ';
        } else if ($alasangugatan5 == 1 && $alasangugatan4 == 1 && $alasangugatan3 == 1 && $alasangugatan2 != 1) {
            $alasangugatan = $alasangugatan1 . ', ' . $alasangugatan2 . ' ';
        } else if ($alasangugatan5 == 1 && $alasangugatan4 == 1 && $alasangugatan3 == 1 && $alasangugatan2 == 1 && $alasangugatan1 != 1) {
            $alasangugatan = $alasangugatan1 . ' ';
        } else {
            $alasangugatan = '';
        }
        // if alasan gugatan bila Tidak Memenuhi Kewajiban
//        $pdf->SetWidths([25, 20, 40, 25, 70, 45, 45,30, 30, 30,30]);
        $pdf->SetAligns(['', 'J']);
        $pdf->Row2(['', 'menurut keterangan yang bersangkutan, telah ditinggal pergi ' . $suamioristri1 . 'nya ' . ' selama ' . $tlamatinggalgoip . ' terhitung sejak ' . $tempatlahir4 . ' sampai dengan ' . $tempatlahir3 . ' dengan ' . $alasangugatan]);

        $pdf->Ln(7);

        // keterangan Surat
        $pdf->SetWidths([8, 170]);
        if ($goip->keterangan_tambahan == '') {
            $pdf->SetX(19);
            $pdf->SetAligns(['', 'J']);
            $pdf->Row2(['2', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan mengurus: ' . $goip->penggunaan_surat]);
        }
        if ($goip->keterangan_tambahan != '') {

            $pdf->SetX(19);
            $pdf->Row2(['2', $goip->keterangan_tambahan]);
            $pdf->Ln(1);
            $pdf->SetX(19);
            $pdf->SetAligns(['', 'J']);
            $pdf->Row2(['3', 'Surat Keterangan ini dibuat untuk dipergunakan sebagai kelengkapan mengurus: ' . $goip->penggunaan_surat]);

        }
        $pdf->Ln(5);
        $pdf->SetX(15);
        $pdf->Row2(['', 'Demikian Surat Keterangan ini dibuat untuk dipergunakan seperlunya.']);
        $pdf->Ln(10);
        $pdf->Cell(5);

        $pdf->ln(1);
        if ($goip->pejabat_camat_id == 1) {
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
        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(5);
        if ($goip->penandatangan == 'Atasnama Pimpinan' || $goip->penandatangan == 'Jabatan Struktural') {
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
        if ($goip->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($goip->jabatan_lainnya);

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

                $pdf->Cell(0, 10, $keteranganjabatanpejabat . $pejabatstruktural->jabatan . ',', 0, '', 'C');
            }
        }
        if ($goip->penandatangan != 'Atasnama Pimpinan' && $goip->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            if ($goip->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($goip->penandatangan);
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
            if ($goip->penandatangan == 'Pimpinan Organisasi' && $goip->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($goip->penandatangan);
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

        $pdf->Output('cetak-data-ghoib' . $tanggal . '.pdf', 'I');
        exit;
    }
}