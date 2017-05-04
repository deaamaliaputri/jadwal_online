<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//untuk N1 ada di line 158 sampai 667

//untuk N2 ada di line 689 sampai 936
//untuk N3 ada di line 689 sampai 936
//untuk N4 ada di line 1147 sampai 1387
//untuk N5 ada di line 1397 sampai 1637
//untuk N6 ada di line 1647 sampai 1932
//untuk N7 ada di line 1647 sampai 1932


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DataPribadi\DokumenPendudukRepository;
use App\Domain\Repositories\DataPribadi\KeluargaRepository;
use App\Domain\Repositories\DataPribadi\OrangTuaRepository;
use App\Domain\Repositories\DataPribadi\PendudukLainRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\SuratNikahRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Penduduk\RincianNonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakSuratNikah extends Controller
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
        SuratNikahRepository $suratnikahRepository,
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
        $this->SuratNikah = $suratnikahRepository;
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

    public function SuratNikah($id)
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
        $pdf->SetMargins(10, 10, 8);
        $pdf->SetAutoPageBreak(0, 20);
        $pdf->SetTitle('Surat Nikah');

        //
        // N1
        //

        $pdf->AddPage();
        $desa = $this->desa->find(session('desa'));
        $suratnikah = $this->SuratNikah->find($id);
        if ($suratnikah->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($suratnikah->penandatangan);
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
//        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Lampiran 7 PMA No. 2 Tahun 1990', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetX(94);
        $pdf->Cell(0, 0, 'pasal 3', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 0, 'Model N-1', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $statusdesa1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $namadesa, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $statuskecamatan1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $kecamatan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $status1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $kabupaten, 0, '', 'L');
        $jeniskodeadministrasi = $this->SuratNikah->cekkodejenisadministrasi($suratnikah->jenis_pelayanan_id);
        $kodeadministrasikearsipan = $this->kodeadministrasi->cekkodeadminkearsipanbysession();

        $pdf->Ln(8);
        $pdf->SetFont('arial', 'BU', 9);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, 'SURAT KETERANGAN UNTUK NIKAH', 0, '', 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 8);
        if ($kodeadministrasikearsipan == null) {
            $indo = array("", "I", "II", "III", "IV", "V", "VI", "VI", "VII", "IX", "X", "XI", "XII");
            if (substr($suratnikah->tanggal, 3, 2) <= 9) {
                $kodeadministrasikearsipanhasil = $indo[substr($suratnikah->tanggal, 4, 1)];
            } else {
                $kodeadministrasikearsipanhasil = $indo[substr($suratnikah->tanggal, 3, 2)];
            }

        } else {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan->kode;
        }

        $pdf->SetX(15);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $suratnikah->no_reg . '.N-1/' . $kodeadministrasikearsipanhasil . '/' . $suratnikah->tahun, 0, '', 'C');
        $pdf->Ln(15);
        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Yang bertanda tangan di bawah ini:', 0, '', '');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');

        // jika penduduk

        if ($suratnikah->jenis_penduduk == 1) {
            $keluarga = $this->keluarga->cekalamat($suratnikah->pribadi->id);
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($suratnikah->pribadi->id);
            $orangtuabapak = $orangtuabapak2->nama;
            $jenis_kelamin = $suratnikah->pribadi->jk->jk;
            if ($suratnikah->pribadi->titel_belakang != '') {
                if ($suratnikah->pribadi->titel_depan != '') {
                    $namalengkap = $suratnikah->pribadi->titel_depan . ' ' . $suratnikah->pribadi->nama . ', ' . $suratnikah->pribadi->titel_belakang;
                }
                if ($suratnikah->pribadi->titel_depan == '') {
                    $namalengkap = $suratnikah->pribadi->nama . ', ' . $suratnikah->pribadi->titel_belakang;
                }
            }
            if ($suratnikah->pribadi->titel_belakang == '') {
                if ($suratnikah->pribadi->titel_depan != '') {
                    $namalengkap = $suratnikah->pribadi->titel_depan . ' ' . $suratnikah->pribadi->nama . '' . $suratnikah->pribadi->titel_belakang;
                }
                if ($suratnikah->pribadi->titel_depan == '') {
                    $namalengkap = $suratnikah->pribadi->nama . '' . $suratnikah->pribadi->titel_belakang;
                }
            }
            $hari = substr($suratnikah->pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($suratnikah->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($suratnikah->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($suratnikah->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($suratnikah->pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $suratnikah->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $suratnikah->pribadi->agama->agama;

            if ($suratnikah->pribadi->pekerjaan_id == 89) {
                $pekerjaan = $suratnikah->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $suratnikah->pribadi->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
            $perkawinanawal = $suratnikah->pribadi->perkawinan->kawin;
        }
        //jika non penduduk
        if ($suratnikah->jenis_penduduk == 2) {
            if ($suratnikah->non_penduduk->titel_belakang != '') {
                if ($suratnikah->non_penduduk->titel_depan != '') {
                    $namalengkap = $suratnikah->non_penduduk->titel_depan . ' ' . $suratnikah->non_penduduk->nama . ', ' . $suratnikah->non_penduduk->titel_belakang;
                }
                if ($suratnikah->non_penduduk->titel_depan == '' || $suratnikah->non_penduduk->titel_depan == null) {
                    $namalengkap = $suratnikah->non_penduduk->nama . ', ' . $suratnikah->non_penduduk->titel_belakang;
                }
            }
            if ($suratnikah->non_penduduk->titel_belakang == '') {
                if ($suratnikah->non_penduduk->titel_depan != '') {
                    $namalengkap = $suratnikah->non_penduduk->titel_depan . ' ' . $suratnikah->non_penduduk->nama . '' . $suratnikah->non_penduduk->titel_belakang;
                }
                if ($suratnikah->non_penduduk->titel_depan == '' || $suratnikah->non_penduduk->titel_depan == null) {
                    $namalengkap = $suratnikah->non_penduduk->nama . '' . $suratnikah->non_penduduk->titel_belakang;
                }
            }
            $hari = substr($suratnikah->non_penduduk->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($suratnikah->non_penduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($suratnikah->non_penduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($suratnikah->non_penduduk->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($suratnikah->non_penduduk->tanggal_lahir, 6, 4);
            $tempatlahir = $suratnikah->non_penduduk->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $suratnikah->non_penduduk->agama->agama;
            if ($suratnikah->non_penduduk->pekerjaan_id == 89) {
                $pekerjaan = $suratnikah->non_penduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $suratnikah->non_penduduk->pekerjaan->pekerjaan;
            }
            $alamat = $suratnikah->non_penduduk->alamat . ' RT. ' . $suratnikah->non_penduduk->alamat_rt . ' RW. ' . $suratnikah->non_penduduk->alamat_rw;
            //kabupaten
            if ($suratnikah->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $suratnikah->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($suratnikah->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $suratnikah->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($suratnikah->non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $suratnikah->non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($suratnikah->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $suratnikah->non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($suratnikah->non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $suratnikah->non_penduduk->desa->desa;
            }
            if ($suratnikah->non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $suratnikah->non_penduduk->desa->desa;
            }
            if ($suratnikah->non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $suratnikah->non_penduduk->desa->desa;
            }
            if ($suratnikah->non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $suratnikah->non_penduduk->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $orangtuabapak = '';
            $jenis_kelamin = $suratnikah->non_penduduk->jk->jk;
            $perkawinanawal = $suratnikah->non_penduduk->perkawinan->kawin;

        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  Jenis Kelamin', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $jenis_kelamin, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '7.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);
        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);
        if ($jenis_kelamin == 'Laki-Laki') {
            if ($perkawinanawal == 'Belum Kawin') {
                $tatuskawin = 'Jejaka';
            }
            if ($perkawinanawal == 'Kawin') {
                $tatuskawin = 'Kawin';
            }
            if ($perkawinanawal == 'Cerai Hidup') {
                $tatuskawin = 'Duda Hidup';
            }
            if ($perkawinanawal == 'Cerai Mati') {
                $tatuskawin = 'Duda Mati';
            }
            if ($suratnikah->istri_1 == 1 && $suratnikah->istri_2 == 1 && $suratnikah->istri_3 == 1 && $suratnikah->istri_4 == 1) {
                $keteraganlanjutan = '';
            }
            if ($suratnikah->istri_1 != 1 && $suratnikah->istri_2 == 1 && $suratnikah->istri_3 == 1 && $suratnikah->istri_4 == 1) {
                $keteraganlanjutan = ', jumlah istrinya: 1 orang, yaitu: ' . $suratnikah->istri_1;
            }
            if ($suratnikah->istri_1 != 1 && $suratnikah->istri_2 != 1 && $suratnikah->istri_3 == 1 && $suratnikah->istri_4 == 1) {
                $keteraganlanjutan = ', jumlah istrinya: 2 orang, yaitu: ' . $suratnikah->istri_1 . ' dan ' . $suratnikah->istri_2;
            }
            if ($suratnikah->istri_1 != 1 && $suratnikah->istri_2 != 1 && $suratnikah->istri_3 != 1 && $suratnikah->istri_4 == 1) {
                $keteraganlanjutan = ', jumlah istrinya: 3 orang, yaitu: ' . $suratnikah->istri_1 . ', ' . $suratnikah->istri_2 . ' dan ' . $suratnikah->istri_3;
            }
            if ($suratnikah->istri_1 != 1 && $suratnikah->istri_2 != 1 && $suratnikah->istri_3 != 1 && $suratnikah->istri_4 != 1) {
                $keteraganlanjutan = ', jumlah istrinya: 4 orang, yaitu: ' . $suratnikah->istri_1 . ', ' . $suratnikah->istri_2 . ', ' . $suratnikah->istri_3 . ' dan ' . $suratnikah->istri_4;
            }
            $untuklaki = $tatuskawin . $keteraganlanjutan;
            $untukperempuan = '--';
            $bin_binti = 'Bin';
        }
        if ($jenis_kelamin == 'Perempuan') {
            $bin_binti = 'Binti';
            if ($perkawinanawal == 'Belum Kawin') {
                $tatuskawin = 'Perawan';
            }
            if ($perkawinanawal == 'Kawin') {
                $tatuskawin = 'Kawin';
            }
            if ($perkawinanawal == 'Cerai Hidup') {
                $tatuskawin = 'Janda Hidup';
            }
            if ($perkawinanawal == 'Cerai Mati') {
                $tatuskawin = 'Janda Mati';
            }
            $untuklaki = '--';
            $untukperempuan = $tatuskawin;

        }
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '8.  ' . $bin_binti, 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(5);
        $pdf->Cell(0, 0, ':     ' . $orangtuabapak, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '9.  Status Perkawinan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetWidths([26]);
        $pdf->SetX(18);
        $pdf->Row3(['a.  Jika Laki-Laki, terangkan   Jejaka, Duda/  Beristri  dan berapa istrinya']);
        $pdf->SetX(44);
        $pdf->Cell(0, -37, ':     ' . $untuklaki, 0, '', 'L');
        $pdf->SetX(18);
        $pdf->Row3(['b.  Jika Perempuan, terangkan Janda atau Perawan']);
        $pdf->SetX(44);
        $pdf->Cell(0, -30, ':     ' . $untukperempuan, 0, '', 'L');
        if ($suratnikah->jenis_terdahulu == '') {
            $namaterdahulu = '--';
        }
        if ($suratnikah->jenis_terdahulu == '1') {
            $listterdahulu = $this->pribadi->find($suratnikah->penduduk_terdahulu);

            if ($listterdahulu->titel_belakang != '') {
                if ($listterdahulu->titel_depan != '') {
                    $namaterdahulu = $listterdahulu->titel_depan . ' ' . $listterdahulu->nama . ', ' . $listterdahulu->titel_belakang;
                }
                if ($listterdahulu->titel_depan == '' || $listterdahulu->titel_depan == null) {
                    $namaterdahulu = $listterdahulu->nama . ', ' . $listterdahulu->titel_belakang;
                }
            }
            if ($listterdahulu->titel_belakang == '') {
                if ($listterdahulu->titel_depan != '') {
                    $namaterdahulu = $listterdahulu->titel_depan . ' ' . $listterdahulu->nama . '' . $listterdahulu->titel_belakang;
                }
                if ($listterdahulu->titel_depan == '' || $listterdahulu->titel_depan == null) {
                    $namaterdahulu = $listterdahulu->nama . '' . $listterdahulu->titel_belakang;
                }
            }
        }
        if ($suratnikah->jenis_terdahulu == '2') {
            $listterdahulu = $this->nonpenduduk->find($suratnikah->penduduk_terdahulu);

            if ($listterdahulu->titel_belakang != '') {
                if ($listterdahulu->titel_depan != '') {
                    $namaterdahulu = $listterdahulu->titel_depan . ' ' . $listterdahulu->nama . ', ' . $listterdahulu->titel_belakang;
                }
                if ($listterdahulu->titel_depan == '' || $listterdahulu->titel_depan == null) {
                    $namaterdahulu = $listterdahulu->nama . ', ' . $listterdahulu->titel_belakang;
                }
            }
            if ($listterdahulu->titel_belakang == '') {
                if ($listterdahulu->titel_depan != '') {
                    $namaterdahulu = $listterdahulu->titel_depan . ' ' . $listterdahulu->nama . '' . $listterdahulu->titel_belakang;
                }
                if ($listterdahulu->titel_depan == '' || $listterdahulu->titel_depan == null) {
                    $namaterdahulu = $listterdahulu->nama . '' . $listterdahulu->titel_belakang;
                }
            }
        }
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '10.  Nama istri terdahulu', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(120, 0, ' :     ' . $namaterdahulu, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);

        $pdf->Ln(5);

        $pdf->SetWidths([120]);
        $pdf->SetX(14);
        $pdf->Row3(['                 Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan sebagaimana mestinya.']);
        $this->pejabat($pdf, $id);


        $organisasi = $this->organisasi->find(session('organisasi'));
//
        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        //
        // N2
        //

        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
//        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Lampiran 8 KMA No. 289 Tahun 2003', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetX(91);
        $pdf->Cell(0, 0, 'pasal 8 ayat (1) huruf b', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 0, 'Model N-2', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $statusdesa1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $namadesa, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $statuskecamatan1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $kecamatan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $status1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $kabupaten, 0, '', 'L');

        $pdf->Ln(8);
        $pdf->SetFont('arial', 'BU', 9);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, 'SURAT KETERANGAN ASAL-USUL', 0, '', 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $suratnikah->no_reg . '.N-2/' . $kodeadministrasikearsipanhasil . '/' . $suratnikah->tahun, 0, '', 'C');
        $pdf->Ln(10);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa :', 0, '', '');
        $pdf->Ln(4);
        $pdf->SetX(11);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(14, 0, 'I.', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);

        $pdf->SetX(14);

        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);

        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'adalah benar anak kandung dari pernikahan seorang pria :', 0, '', '');
        $pdf->Ln(4);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'II.', 0, '', '');
        $pdf->SetFont('Arial', '', 8);

        $this->bapakkandung($pdf, $id);

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'dengan seorang Wanita :', 0, '', '');
        $pdf->Ln(4);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'III.', 0, '', '');
        $pdf->SetFont('Arial', '', 8);
        $this->ibukandung($pdf, $id);
        $pdf->SetWidths([120]);
        $pdf->SetX(14);
        $pdf->Row3(['                 Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan sebagaimana mestinya.']);

        $this->pejabat($pdf, $id);

        $organisasi = $this->organisasi->find(session('organisasi'));
//
        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        //
        // cetak N3
        //


        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
//        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Lampiran 9 KMA No. 298 Tahun 2003', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetX(91);
        $pdf->Cell(0, 0, 'pasal 8 ayat (1) huruf c', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 0, 'Model N-3', 0, '', 'R');
        $pdf->Ln(8);
        $pdf->SetFont('arial', 'BU', 9);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, 'SURAT PERSETUJUAN MEMPELAI', 0, '', 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $suratnikah->no_reg . '.N-3/' . $kodeadministrasikearsipanhasil . '/' . $suratnikah->tahun, 0, '', 'C');

        //
        //calon suami
        //
        $pdf->Ln(10);
        $pdf->SetX(10);
        $pdf->SetFont('arial', 'B', 8);
        $pdf->Cell(14, 0, 'I. CALON SUAMI :', 0, '', 'L');
        $pdf->SetFont('arial', '', 8);
        if ($suratnikah->jenis_penduduk == 1) {
            if ($suratnikah->pribadi->jk->jk == 'Laki-Laki') {
                $this->pemohon($pdf, $id);
                $pdf->Ln(4);
                $pdf->SetX(10);
                $pdf->SetFont('arial', 'B', 8);
                $pdf->Cell(14, 0, 'II. CALON ISTRI :', 0, '', 'L');
                $pdf->SetFont('arial', '', 8);
                $this->calon($pdf, $id);
            } else {
                $this->calon($pdf, $id);
                $pdf->Ln(4);
                $pdf->SetX(10);
                $pdf->SetFont('arial', 'B', 8);
                $pdf->Cell(14, 0, 'II. CALON ISTRI :', 0, '', 'L');
                $pdf->SetFont('arial', '', 8);

                $this->pemohon($pdf, $id);
            }
        }
        if ($suratnikah->jenis_penduduk == 2) {
            if ($suratnikah->non_penduduk->jk->jk == 'Laki-Laki') {
                $this->pemohon($pdf, $id);
                $pdf->Ln(4);
                $pdf->SetX(10);
                $pdf->SetFont('arial', 'B', 8);
                $pdf->Cell(14, 0, 'II. CALON ISTRI :', 0, '', 'L');
                $pdf->SetFont('arial', '', 8);
                $this->calon($pdf, $id);
            } else {
                $this->calon($pdf, $id);
                $pdf->Ln(4);
                $pdf->SetX(10);
                $pdf->SetFont('arial', 'B', 8);
                $pdf->Cell(14, 0, 'II. CALON ISTRI :', 0, '', 'L');
                $pdf->SetFont('arial', '', 8);
                $this->pemohon($pdf, $id);
            }
        }


        $pdf->Ln(5);

        $pdf->SetWidths([120]);
        $pdf->SetX(14);
        $pdf->Row3(['Menyatakan dengan sesungguhnya bahwa atas dasar sukarela, dengan kesadaran sendiri, tanpa paksaan dari siapapun juga, setuju untuk melangsungkan pernikahan']);
        $pdf->Ln(5);
        $pdf->SetX(14);
        $pdf->Row3(['Demikian Surat Persetujuan ini dibuat untuk dipergunakan seperlunya.']);

        $hari3 = substr($suratnikah->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($suratnikah->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($suratnikah->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($suratnikah->tanggal, 3, 2)];
        }
        $tahun3 = substr($suratnikah->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;
        if ($suratnikah->jenis_calon == 1) {
            if ($suratnikah->pribadi_calon->titel_belakang != '') {
                if ($suratnikah->pribadi_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->titel_depan . ' ' . $suratnikah->pribadi_calon->nama . ', ' . $suratnikah->pribadi_calon->titel_belakang;
                }
                if ($suratnikah->pribadi_calon->titel_depan == '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->nama . ', ' . $suratnikah->pribadi_calon->titel_belakang;
                }
            }
            if ($suratnikah->pribadi_calon->titel_belakang == '') {
                if ($suratnikah->pribadi_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->titel_depan . ' ' . $suratnikah->pribadi_calon->nama . '' . $suratnikah->pribadi_calon->titel_belakang;
                }
                if ($suratnikah->pribadi_calon->titel_depan == '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->nama . '' . $suratnikah->pribadi_calon->titel_belakang;
                }
            }
        }
        if ($suratnikah->jenis_calon == 2) {
            if ($suratnikah->non_penduduk_calon->titel_belakang != '') {
                if ($suratnikah->non_penduduk_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->titel_depan . ' ' . $suratnikah->non_penduduk_calon->nama . ', ' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
                if ($suratnikah->non_penduduk_calon->titel_depan == '') {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->nama . ', ' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
            }
            if ($suratnikah->non_penduduk_calon->titel_belakang == '') {
                if ($suratnikah->non_penduduk_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->titel_depan . ' ' . $suratnikah->non_penduduk_calon->nama . '' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
                if ($suratnikah->non_penduduk_calon->titel_depan == '') {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->nama . '' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
            }
        }

        $pdf->ln(14);
        $pdf->SetX(14);

        if ($jenis_kelamin == 'Laki-Laki') {
            $suami = $namalengkap;
            $istri = $namalengkapcalon;
        } else {
            $suami = $namalengkapcalon;
            $istri = $namalengkap;
        }
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(50, 8, 'I.  CALON SUAMI', 0, '', 'C');
        $pdf->SetFont('Arial', 'B', 8);

        $pdf->Cell(-50, 60, $suami, 0, '', 'C');
        $pdf->SetFont('Arial', '', 8);


        $pdf->SetFont('Arial', '', 8);


        $pdf->SetX(90);
        $pdf->Cell(0, 0, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(90);
        $pdf->Cell(0, 0, 'II.  CALON ISTRI', 0, '', 'C');
        $pdf->Ln(26);

        $pdf->SetX(90);
        $pdf->Cell(0, 0, $istri, 0, '', 'C');


        $tanggal = date('d-m-y');
        $organisasi = $this->organisasi->find(session('organisasi'));
//
        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        //
        // N4
        //
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
//        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Lampiran 10 PMA No. 2 Tahun 1990', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetX(93);
        $pdf->Cell(0, 0, 'pasal 8 ayat (1) huruf c', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 0, 'Model N-4', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $statusdesa1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $namadesa, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $statuskecamatan1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $kecamatan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, $status1, 0, '', 'L');
        $pdf->SetX(35);
        $pdf->Cell(5, 0, ':     ' . $kabupaten, 0, '', 'L');
        $pdf->Ln(8);
        $pdf->SetFont('arial', 'BU', 9);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, 'SURAT KETERANGAN TENTANG ORANG TUA', 0, '', 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $suratnikah->no_reg . '.N-4/' . $kodeadministrasikearsipanhasil . '/' . $suratnikah->tahun, 0, '', 'C');
        $pdf->Ln(10);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa :', 0, '', '');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'I.', 0, '', '');
        $pdf->SetFont('Arial', '', 8);
        $this->bapakkandung($pdf, $id);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'II.', 0, '', '');
        $pdf->SetFont('Arial', '', 8);
        $this->ibukandung($pdf, $id);
        $pdf->Ln(4);
        $pdf->SetX(11);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(14, 0, 'adalah benar ayah kandung dan ibu kandung dari seorang :', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(11);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(14, 0, 'III.', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');

        $pdf->Ln(1);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);
        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);
        $pdf->SetWidths([120]);
        $pdf->Ln(1);
        $pdf->SetX(14);
        $pdf->Row3(['                 Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan sebagaimana mestinya.']);

        $this->pejabat($pdf, $id);

        $organisasi = $this->organisasi->find(session('organisasi'));
//
        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        //
        // N5
        //
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
//        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Lampiran 11 PMA No. 2 Tahun 1990', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetX(93);
        $pdf->Cell(0, 0, 'pasal 8 ayat (1) huruf d', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 0, 'Model N-5', 0, '', 'R');
        $pdf->Ln(8);
        $pdf->SetFont('arial', 'BU', 9);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, 'SURAT IZIN ORANG TUA', 0, '', 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(15);
        $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $suratnikah->no_reg . '.N-5/' . $kodeadministrasikearsipanhasil . '/' . $suratnikah->tahun, 0, '', 'C');
        $pdf->Ln(5);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'Yang bertanda tangan di bawah ini :', 0, '', '');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'I.', 0, '', '');
        $pdf->SetFont('Arial', '', 8);
        $this->bapakkandung($pdf, $id);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'II.', 0, '', '');
        $pdf->SetFont('Arial', '', 8);
        $this->ibukandung($pdf, $id);
        $pdf->Ln(4);
        $pdf->SetX(11);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(14, 0, 'adalah ayah kandung dan ibu kandung dari :', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(11);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(14, 0, 'III.', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->Ln(1);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);
        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(11);
        $pdf->Cell(0, 0, 'memberikan izin kepadanya untuk melakukan pernikahan dengan :', 0, '', '');
        $pdf->SetFont('Arial', '', 8);
        $this->calontanpabapak($pdf, $id);

        $pdf->SetWidths([120]);
        $pdf->Ln(1);
        $pdf->SetX(14);
        $pdf->Row3(['                 Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan sebagaimana mestinya.']);

        $this->pejabat($pdf, $id);

        $organisasi = $this->organisasi->find(session('organisasi'));
//
        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

        if ($suratnikah->tanggal_kematian != '') {
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

            //
            // N6
            //
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(14);
            $pdf->Cell(0, 0, 'Lampiran 12 PMA No. 2 Tahun 1990', 0, '', 'R');
            $pdf->Ln(4);
            $pdf->SetX(93);
            $pdf->Cell(0, 0, 'pasal 8 ayat (3) huruf b', 0, '', 'L');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(0, 0, 'Model N-6', 0, '', 'R');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(14);
            $pdf->Cell(5, 0, $statusdesa1, 0, '', 'L');
            $pdf->SetX(35);
            $pdf->Cell(5, 0, ':     ' . $namadesa, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(14);
            $pdf->Cell(5, 0, $statuskecamatan1, 0, '', 'L');
            $pdf->SetX(35);
            $pdf->Cell(5, 0, ':     ' . $kecamatan, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(14);
            $pdf->Cell(5, 0, $status1, 0, '', 'L');
            $pdf->SetX(35);
            $pdf->Cell(5, 0, ':     ' . $kabupaten, 0, '', 'L');
            $pdf->Ln(8);
            $pdf->SetFont('arial', 'BU', 9);
            $pdf->SetX(15);
            $pdf->Cell(0, 0, 'SURAT KETERANGAN KEMATIAN', 0, '', 'C');
            $pdf->Ln(3);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(15);
            $pdf->Cell(0, 0, ' Nomor: ' . $jeniskodeadministrasi . '/' . $suratnikah->no_reg . '.N-6/' . $kodeadministrasikearsipanhasil . '/' . $suratnikah->tahun, 0, '', 'C');
            $pdf->Ln(5);
            $datetime = \DateTime::createFromFormat('d/m/Y', $suratnikah->tanggal_kematian);
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
            $hari1 = substr($suratnikah->tanggal_kematian, 0, 2);
            if (substr($suratnikah->tanggal_kematian, 3, 2) <= 9) {
                $bulan1 = $indo[substr($suratnikah->tanggal_kematian, 4, 1)];
            } else {
                $bulan1 = $indo[substr($suratnikah->tanggal_kematian, 3, 2)];
            }
            $tahun1 = substr($suratnikah->tanggal_kematian, 6, 4);
            $tempatlahir1 = $hari1 . ' ' . $bulan1 . ' ' . $tahun1;

            $pdf->SetX(11);
            $pdf->Cell(0, 0, 'Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa :', 0, '', '');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(11);
            $pdf->Cell(0, 0, 'I.', 0, '', '');
            $pdf->SetFont('Arial', '', 8);
            $this->terdahulu($pdf, $id);
            $pdf->Ln(4);
            $pdf->SetX(11);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(14, 0, 'telah meninggal dunia pada hari :' . $hariindo . ' tanggal: ' . $tempatlahir1 . ' di ' . $suratnikah->tempat_kematian, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(11);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(14, 0, 'II.', 0, '', 'L');
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(14);
            $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(16);
            $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(14);
            $pdf->Cell(25, 0, '2.  ' . $bin_binti, 0, '', 'L');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(5);
            $pdf->Cell(0, 0, ':     ' . $orangtuabapak, 0, '', 'L');
            $pdf->SetFont('Arial', '', 8);
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(14);
            $pdf->Cell(25, 0, '3.  Tempat,Tgl. Lahir ', 0, '', 'L');
            $pdf->Cell(5);
            $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(14);
            $pdf->Cell(25, 0, '4.  Warga Negara', 0, '', 'L');
            $pdf->Cell(5);
            $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(14);
            $pdf->Cell(25, 0, '5.  Agama', 0, '', 'L');
            $pdf->Cell(5);
            $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(14);
            $pdf->Cell(25, 0, '6.  Pekerjaan', 0, '', 'L');
            $pdf->Cell(5);
            $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
            $pdf->Ln(4);
            $pdf->SetX(14);
            $pdf->Cell(25, 0, '7.  Alamat', 0, '', 'L');
            $pdf->Cell(5);
            $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
            $pdf->Ln(1);
            $pdf->SetWidths([85]);
            $pdf->SetAligns(['J']);
            $pdf->SetX(49);
            $pdf->Row3([$alamatlengkap]);

            $pdf->SetWidths([120]);
            $pdf->Ln(1);
            $pdf->SetX(14);
            $pdf->Row3(['                 Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan sebagaimana mestinya.']);

            $this->pejabat($pdf, $id);
            $organisasi = $this->organisasi->find(session('organisasi'));
//
            if ($organisasi->is_lock == 0) {
                $this->Headers($pdf);
            }
        }

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        //
        // N7
        //
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Lampiran 13 PMA No. 2 Tahun 1990', 0, '', 'R');
        $pdf->Ln(4);
        $pdf->SetX(93);
        $pdf->Cell(0, 0, 'pasal 6 ayat (2)', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 0, 'Model N-7', 0, '', 'R');
        $pdf->Ln(7);
        $pdf->SetX(93);
        $pdf->Cell(0, 0, $namadesa . ', ' . $tempatlahir3, 0, '', 'L');
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, 'Lampiran : 1 (satu) Berkas', 0, '', 'L');
        $pdf->SetX(93);
        $pdf->Cell(5, 0, 'Kepada Yth. :', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(5, 0, 'Perihal  :', 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(35);
        $pdf->Cell(5, 0, '     Pemberitahuan Kehendak Nikah', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(93);
        $pdf->Cell(0, 0, 'Pegawai  Pencatatat  Nikah  pada', 0, '', 'L');
        $pdf->Ln(3);
        $pdf->SetX(93);
        $pdf->Cell(0, 0, 'KUA/Pembantu  PPN  pada  KUA.', 0, '', 'L');
        $pdf->Ln(3);
        $pdf->SetX(93);
        $pdf->Cell(0, 0, '' . $statuskecamatan1 . ' ' . $kecamatan, 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetX(93);
        $pdf->Cell(0, 0, 'di-', 0, '', 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'BU', 8);
        $pdf->SetX(100);
        $pdf->Cell(0, 0, $kecamatan, 0, '', 'L');
        $pdf->Ln(7);
        $pdf->SetFont('Arial', 'B', 8);

        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Assalamu\'alaikum Warohmatullahi Wabarokatuh.', 0, '', '');
        $datetime = \DateTime::createFromFormat('d/m/Y', $suratnikah->tanggal_kawin);
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
        $hari1 = substr($suratnikah->tanggal_kawin, 0, 2);
        if (substr($suratnikah->tanggal_kawin, 3, 2) <= 9) {
            $bulan1 = $indo[substr($suratnikah->tanggal_kawin, 4, 1)];
        } else {
            $bulan1 = $indo[substr($suratnikah->tanggal_kawin, 3, 2)];
        }
        $tahun1 = substr($suratnikah->tanggal_kawin, 6, 4);
        $tempatlahir1 = $hari1 . ' ' . $bulan1 . ' ' . $tahun1;
        $cekwaktu = $this->kodeadministrasi->cekwaktuadministrasi();
        if ($cekwaktu != null) {
            $waktubagian = ' ' . $cekwaktu->kode;
        }
        if ($cekwaktu == null) {
            $waktubagian = '';
        }
        if ($suratnikah->jenis_calon == 1) {
            $keluarga = $this->keluarga->cekalamat($suratnikah->pribadi_calon->id);
            $alamatnikah = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkapnikah = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;

        }
        //jika non penduduk
        if ($suratnikah->jenis_calon == 2) {
            $alamatnikah = $suratnikah->non_penduduk_calon->alamat . ' RT. ' . $suratnikah->non_penduduk_calon->alamat_rt . ' RW. ' . $suratnikah->non_penduduk_calon->alamat_rw;
            //kabupaten
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kecamatan;
            }
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kecamatan;
            }
            //desa
            if ($suratnikah->non_penduduk_calon->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            $alamatlengkapnikah = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
        }
        if ($suratnikah->tempat_nikah == 'Alamat Suami') {
            if ($jenis_kelamin == 'Laki-Laki') {
                $hasiltempatnikah = $alamat . ' ' . $alamatlengkap;
            }
            if ($jenis_kelamin == 'Perempuan') {
                $hasiltempatnikah = $alamatnikah . ' ' . $alamatlengkapnikah;
            }
        } else if ($suratnikah->tempat_nikah == 'Alamat Istri') {
            if ($jenis_kelamin == 'Laki-Laki') {
                $hasiltempatnikah = $alamat . ' ' . $alamatlengkap;
            }
            if ($jenis_kelamin == 'Perempuan') {
                $hasiltempatnikah = $alamat . ' ' . $alamatlengkap;
            }
        } else {
            $hasiltempatnikah = $suratnikah->tempat_nikah;
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(2);
        $pdf->SetX(14);
        $pdf->Row3(['                 Dengan ini kami memberitahukan bahwa kami bermaksud akan melangsungkan pernikahan antara ' . $namalengkap . ' dengan ' . $namalengkapcalon . ' pada hari ' . $hariindo . ', tanggal ' . $tempatlahir1 . ' Pukul ' . $suratnikah->waktu_kawin . ' ' . $waktubagian . ' dengan mas kawin ' . $suratnikah->mas_kawin . ' bertempat di ' . $hasiltempatnikah]);
        $pdf->SetX(14);
        $pdf->Row3(['Bersama ini kami lampirkan surat-surat yang diperlukan untu diperiksa sebagai berikut :']);

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Surat Keterangan Untuk Nikah', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50);
        $pdf->Cell(120, 0, '     , Model N1', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '2.  Surat Keterangan Asal-Usul', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50);
        $pdf->Cell(120, 0, '     , Model N2', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '3.  Surat Persetujuan Mempelai', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50);
        $pdf->Cell(120, 0, '     , Model N3', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '4.  Surat Keterangan tentang Orang Tua', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50);
        $pdf->Cell(120, 0, '     , Model N4', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '5.  Surat izin Orang Tua', 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(50);
        $pdf->Cell(120, 0, '     , Model N5', 0, '', 'L');
        $pdf->Ln(4);
        if ($suratnikah->tanggal_kematian != '') {

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(14);
            $pdf->Cell(14, 0, '6.  Surat Keterangan Kematian', 0, '', 'L');
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(50);
            $pdf->Cell(120, 0, '     , Model N6', 0, '', 'L');
            $pdf->Ln(4);
        }

        $pdf->SetWidths([120]);
        $pdf->Ln(1);
        $pdf->SetX(14);
        $pdf->Row3(['                 Kiranya dapat dihadiri dan dicatat pelaksanaannya sesuai dengan ketentuan perundang-undangan yang berlaku.']);
        $pdf->Ln(7);
        $pdf->SetFont('Arial', 'B', 8);

        $pdf->SetX(14);
        $pdf->Cell(0, 0, 'Wassalamu\'alaikum Warohmatullahi Wabarokatuh.', 0, '', '');
        $pdf->Ln(2);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(53, 8, 'Diterima tanggal, .........................', 0, '', 'C');
        $pdf->Ln(4);
        $pdf->Cell(50, 8, 'Yang menerima,', 0, '', 'C');
        $pdf->Ln(4);
        $pdf->Cell(50, 8, '(PPN/Pembantu PPN *)', 0, '', 'C');
        $pdf->SetFont('Arial', 'B', 8);
        if ($suratnikah->pejabat_nikah->titel_belakang != '' && $suratnikah->pejabat_nikah->titel_depan != '') {
            $pdf->Cell(-50, 60, $suratnikah->pejabat_nikah->titel_depan . ' ' . $suratnikah->pejabat_nikah->nama . ', ' . $suratnikah->pejabat_nikah->titel_belakang, 0, '', 'C');
        } else if ($suratnikah->pejabat_nikah->titel_belakang == '' && $suratnikah->pejabat_nikah->titel_depan != '') {
            $pdf->Cell(-50, 60, $suratnikah->pejabat_nikah->titel_depan . ' ' . $suratnikah->pejabat_nikah->nama, 0, '', 'C');
        } else if ($suratnikah->pejabat_nikah->titel_belakang != '' && $suratnikah->pejabat_nikah->titel_depan == '') {
            $pdf->Cell(-50, 60, $suratnikah->pejabat_nikah->nama . ', ' . $suratnikah->pejabat_nikah->titel_belakang, 0, '', 'C');
        } else if ($suratnikah->pejabat_nikah->titel_belakang == '' && $suratnikah->pejabat_nikah->titel_depan == '') {
            $pdf->Cell(-50, 60, $suratnikah->pejabat_nikah->nama, 0, '', 'C');
        }
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(90);
        $pdf->Cell(0, 0, 'Yang Memberitahukan,', 0, '', 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(90);
        $pdf->Cell(0, 0, '(Calon Mempelai/Wali/Wakil Wali *)', 0, '', 'C');
        $pdf->Ln(26);

        $pdf->SetX(90);
        $pdf->Cell(0, 0, $namalengkap, 0, '', 'C');


        $organisasi = $this->organisasi->find(session('organisasi'));
//
        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }

//
        $pdf->Output('cetak-data-surat-nikah' . $tanggal . '.pdf', 'I');
        exit;
    }

    function pemohon($pdf, $id)
    {
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');
        $desa = $this->desa->find(session('desa'));
        $suratnikah = $this->SuratNikah->find($id);

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

        // jika penduduk

        if ($suratnikah->jenis_penduduk == 1) {
            $keluarga = $this->keluarga->cekalamat($suratnikah->pribadi->id);
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($suratnikah->pribadi->id);
            $orangtuabapak = $orangtuabapak2->nama;
            $jenis_kelamin = $suratnikah->pribadi->jk->jk;
            if ($suratnikah->pribadi->titel_belakang != '') {
                if ($suratnikah->pribadi->titel_depan != '') {
                    $namalengkap = $suratnikah->pribadi->titel_depan . ' ' . $suratnikah->pribadi->nama . ', ' . $suratnikah->pribadi->titel_belakang;
                }
                if ($suratnikah->pribadi->titel_depan == '') {
                    $namalengkap = $suratnikah->pribadi->nama . ', ' . $suratnikah->pribadi->titel_belakang;
                }
            }
            if ($suratnikah->pribadi->titel_belakang == '') {
                if ($suratnikah->pribadi->titel_depan != '') {
                    $namalengkap = $suratnikah->pribadi->titel_depan . ' ' . $suratnikah->pribadi->nama . '' . $suratnikah->pribadi->titel_belakang;
                }
                if ($suratnikah->pribadi->titel_depan == '') {
                    $namalengkap = $suratnikah->pribadi->nama . '' . $suratnikah->pribadi->titel_belakang;
                }
            }
            $hari = substr($suratnikah->pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($suratnikah->pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($suratnikah->pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($suratnikah->pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($suratnikah->pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $suratnikah->pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $suratnikah->pribadi->agama->agama;

            if ($suratnikah->pribadi->pekerjaan_id == 89) {
                $pekerjaan = $suratnikah->pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $suratnikah->pribadi->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
            $perkawinanawal = $suratnikah->pribadi->perkawinan->kawin;
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkap, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        if ($jenis_kelamin == 'Laki-Laki') {
            $bin_binti = 'Bin';
        }
        if ($jenis_kelamin == 'Perempuan') {
            $bin_binti = 'Binti';
        }
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  ' . $bin_binti, 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(5);
        $pdf->Cell(0, 0, ':     ' . $orangtuabapak, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '7.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);

        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);


    }

    function calon($pdf, $id)
    {

        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');
        $desa = $this->desa->find(session('desa'));
        $suratnikah = $this->SuratNikah->find($id);

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

        // jika penduduk

        if ($suratnikah->jenis_calon == 1) {
            $keluarga = $this->keluarga->cekalamat($suratnikah->pribadi_calon->id);
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($suratnikah->pribadi_calon->id);
            $orangtuabapak = $orangtuabapak2->nama;
            $jenis_kelamin = $suratnikah->pribadi_calon->jk->jk;
            if ($suratnikah->pribadi_calon->titel_belakang != '') {
                if ($suratnikah->pribadi_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->titel_depan . ' ' . $suratnikah->pribadi_calon->nama . ', ' . $suratnikah->pribadi_calon->titel_belakang;
                }
                if ($suratnikah->pribadi_calon->titel_depan == '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->nama . ', ' . $suratnikah->pribadi_calon->titel_belakang;
                }
            }
            if ($suratnikah->pribadi_calon->titel_belakang == '') {
                if ($suratnikah->pribadi_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->titel_depan . ' ' . $suratnikah->pribadi_calon->nama . '' . $suratnikah->pribadi_calon->titel_belakang;
                }
                if ($suratnikah->pribadi_calon->titel_depan == '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->nama . '' . $suratnikah->pribadi_calon->titel_belakang;
                }
            }
            $hari = substr($suratnikah->pribadi_calon->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($suratnikah->pribadi_calon->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($suratnikah->pribadi_calon->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($suratnikah->pribadi_calon->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($suratnikah->pribadi_calon->tanggal_lahir, 6, 4);
            $tempatlahir = $suratnikah->pribadi_calon->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $suratnikah->pribadi_calon->agama->agama;

            if ($suratnikah->pribadi_calon->pekerjaan_id == 89) {
                $pekerjaan = $suratnikah->pribadi_calon->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $suratnikah->pribadi_calon->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
            $perkawinanawal = $suratnikah->pribadi_calon->perkawinan->kawin;
        }
        //jika non penduduk
        if ($suratnikah->jenis_calon == 2) {
            if ($suratnikah->non_penduduk_calon->titel_belakang != '') {
                if ($suratnikah->non_penduduk_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->titel_depan . ' ' . $suratnikah->non_penduduk_calon->nama . ', ' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
                if ($suratnikah->non_penduduk_calon->titel_depan == '' || $suratnikah->non_penduduk_calon->titel_depan == null) {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->nama . ', ' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
            }
            if ($suratnikah->non_penduduk_calon->titel_belakang == '') {
                if ($suratnikah->non_penduduk_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->titel_depan . ' ' . $suratnikah->non_penduduk_calon->nama . '' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
                if ($suratnikah->non_penduduk_calon->titel_depan == '' || $suratnikah->non_penduduk_calon->titel_depan == null) {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->nama . '' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
            }
            $hari = substr($suratnikah->non_penduduk_calon->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($suratnikah->non_penduduk_calon->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($suratnikah->non_penduduk_calon->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($suratnikah->non_penduduk_calon->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($suratnikah->non_penduduk_calon->tanggal_lahir, 6, 4);
            $tempatlahir = $suratnikah->non_penduduk_calon->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $suratnikah->non_penduduk_calon->agama->agama;
            if ($suratnikah->non_penduduk_calon->pekerjaan_id == 89) {
                $pekerjaan = $suratnikah->non_penduduk_calon->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $suratnikah->non_penduduk_calon->pekerjaan->pekerjaan;
            }
            $alamat = $suratnikah->non_penduduk_calon->alamat . ' RT. ' . $suratnikah->non_penduduk_calon->alamat_rt . ' RW. ' . $suratnikah->non_penduduk_calon->alamat_rw;
            //kabupaten
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kecamatan;
            }
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kecamatan;
            }
            //desa
            if ($suratnikah->non_penduduk_calon->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $orangtuabapak = $suratnikah->bapak_calon;
            $jenis_kelamin = $suratnikah->non_penduduk_calon->jk->jk;
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkapcalon, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        if ($jenis_kelamin == 'Laki-Laki') {
            $bin_binti = 'Bin';
        }
        if ($jenis_kelamin == 'Perempuan') {
            $bin_binti = 'Binti';
        }
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  ' . $bin_binti, 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(5);
        $pdf->Cell(0, 0, ':     ' . $orangtuabapak, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '7.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);

        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);

    }
    function calontanpabapak($pdf, $id)
    {

        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');
        $desa = $this->desa->find(session('desa'));
        $suratnikah = $this->SuratNikah->find($id);

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

        // jika penduduk

        if ($suratnikah->jenis_calon == 1) {
            $keluarga = $this->keluarga->cekalamat($suratnikah->pribadi_calon->id);
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($suratnikah->pribadi_calon->id);
            $orangtuabapak = $orangtuabapak2->nama;
            $jenis_kelamin = $suratnikah->pribadi_calon->jk->jk;
            if ($suratnikah->pribadi_calon->titel_belakang != '') {
                if ($suratnikah->pribadi_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->titel_depan . ' ' . $suratnikah->pribadi_calon->nama . ', ' . $suratnikah->pribadi_calon->titel_belakang;
                }
                if ($suratnikah->pribadi_calon->titel_depan == '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->nama . ', ' . $suratnikah->pribadi_calon->titel_belakang;
                }
            }
            if ($suratnikah->pribadi_calon->titel_belakang == '') {
                if ($suratnikah->pribadi_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->titel_depan . ' ' . $suratnikah->pribadi_calon->nama . '' . $suratnikah->pribadi_calon->titel_belakang;
                }
                if ($suratnikah->pribadi_calon->titel_depan == '') {
                    $namalengkapcalon = $suratnikah->pribadi_calon->nama . '' . $suratnikah->pribadi_calon->titel_belakang;
                }
            }
            $hari = substr($suratnikah->pribadi_calon->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($suratnikah->pribadi_calon->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($suratnikah->pribadi_calon->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($suratnikah->pribadi_calon->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($suratnikah->pribadi_calon->tanggal_lahir, 6, 4);
            $tempatlahir = $suratnikah->pribadi_calon->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $suratnikah->pribadi_calon->agama->agama;

            if ($suratnikah->pribadi_calon->pekerjaan_id == 89) {
                $pekerjaan = $suratnikah->pribadi_calon->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $suratnikah->pribadi_calon->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
            $perkawinanawal = $suratnikah->pribadi_calon->perkawinan->kawin;
        }
        //jika non penduduk
        if ($suratnikah->jenis_calon == 2) {
            if ($suratnikah->non_penduduk_calon->titel_belakang != '') {
                if ($suratnikah->non_penduduk_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->titel_depan . ' ' . $suratnikah->non_penduduk_calon->nama . ', ' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
                if ($suratnikah->non_penduduk_calon->titel_depan == '' || $suratnikah->non_penduduk_calon->titel_depan == null) {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->nama . ', ' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
            }
            if ($suratnikah->non_penduduk_calon->titel_belakang == '') {
                if ($suratnikah->non_penduduk_calon->titel_depan != '') {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->titel_depan . ' ' . $suratnikah->non_penduduk_calon->nama . '' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
                if ($suratnikah->non_penduduk_calon->titel_depan == '' || $suratnikah->non_penduduk_calon->titel_depan == null) {
                    $namalengkapcalon = $suratnikah->non_penduduk_calon->nama . '' . $suratnikah->non_penduduk_calon->titel_belakang;
                }
            }
            $hari = substr($suratnikah->non_penduduk_calon->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($suratnikah->non_penduduk_calon->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($suratnikah->non_penduduk_calon->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($suratnikah->non_penduduk_calon->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($suratnikah->non_penduduk_calon->tanggal_lahir, 6, 4);
            $tempatlahir = $suratnikah->non_penduduk_calon->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $suratnikah->non_penduduk_calon->agama->agama;
            if ($suratnikah->non_penduduk_calon->pekerjaan_id == 89) {
                $pekerjaan = $suratnikah->non_penduduk_calon->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $suratnikah->non_penduduk_calon->pekerjaan->pekerjaan;
            }
            $alamat = $suratnikah->non_penduduk_calon->alamat . ' RT. ' . $suratnikah->non_penduduk_calon->alamat_rt . ' RW. ' . $suratnikah->non_penduduk_calon->alamat_rw;
            //kabupaten
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kecamatan;
            }
            if ($suratnikah->non_penduduk_calon->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $suratnikah->non_penduduk_calon->desa->kecamatan->kecamatan;
            }
            //desa
            if ($suratnikah->non_penduduk_calon->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            if ($suratnikah->non_penduduk_calon->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $suratnikah->non_penduduk_calon->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $orangtuabapak = $suratnikah->bapak_calon;
            $jenis_kelamin = $suratnikah->non_penduduk_calon->jk->jk;
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkapcalon, 0, '', 'L');
//        $pdf->Ln(4);
//        $pdf->SetFont('Arial', '', 8);
//        if ($jenis_kelamin == 'Laki-Laki') {
//            $bin_binti = 'Bin';
//        }
//        if ($jenis_kelamin == 'Perempuan') {
//            $bin_binti = 'Binti';
//        }
//        $pdf->SetFont('Arial', '', 8);
//        $pdf->SetX(14);
//        $pdf->Cell(25, 0, '2.  ' . $bin_binti, 0, '', 'L');
//        $pdf->SetFont('Arial', 'B', 8);
//        $pdf->Cell(5);
//        $pdf->Cell(0, 0, ':     ' . $orangtuabapak, 0, '', 'L');
//        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);

        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);

    }

    function terdahulu($pdf, $id)
    {

        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');
        $desa = $this->desa->find(session('desa'));
        $suratnikah = $this->SuratNikah->find($id);

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

        // jika penduduk
        if ($suratnikah->jenis_terdahulu == 1) {

            $pribadi = $this->pribadi->find($suratnikah->penduduk_terdahulu);
            $keluarga = $this->keluarga->cekalamat($pribadi->id);
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($pribadi->id);
            $orangtuabapak = $orangtuabapak2->nama;
            $jenis_kelamin = $pribadi->jk->jk;
            if ($pribadi->titel_belakang != '') {
                if ($pribadi->titel_depan != '') {
                    $namalengkapcalon = $pribadi->titel_depan . ' ' . $pribadi->nama . ', ' . $pribadi->titel_belakang;
                }
                if ($pribadi->titel_depan == '') {
                    $namalengkapcalon = $pribadi->nama . ', ' . $pribadi->titel_belakang;
                }
            }
            if ($pribadi->titel_belakang == '') {
                if ($pribadi->titel_depan != '') {
                    $namalengkapcalon = $pribadi->titel_depan . ' ' . $pribadi->nama . '' . $pribadi->titel_belakang;
                }
                if ($pribadi->titel_depan == '') {
                    $namalengkapcalon = $pribadi->nama . '' . $pribadi->titel_belakang;
                }
            }
            $hari = substr($pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $pribadi->agama->agama;

            if ($pribadi->pekerjaan_id == 89) {
                $pekerjaan = $pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $pribadi->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        //jika non penduduk
        if ($suratnikah->jenis_terdahulu == 2) {
            $non_penduduk = $this->non_penduduk->find($suratnikah->penduduk_terdahulu);
            if ($non_penduduk->titel_belakang != '') {
                if ($non_penduduk->titel_depan != '') {
                    $namalengkapcalon = $non_penduduk->titel_depan . ' ' . $non_penduduk->nama . ', ' . $non_penduduk->titel_belakang;
                }
                if ($non_penduduk->titel_depan == '' || $non_penduduk->titel_depan == null) {
                    $namalengkapcalon = $non_penduduk->nama . ', ' . $non_penduduk->titel_belakang;
                }
            }
            if ($non_penduduk->titel_belakang == '') {
                if ($non_penduduk->titel_depan != '') {
                    $namalengkapcalon = $non_penduduk->titel_depan . ' ' . $non_penduduk->nama . '' . $non_penduduk->titel_belakang;
                }
                if ($non_penduduk->titel_depan == '' || $non_penduduk->titel_depan == null) {
                    $namalengkapcalon = $non_penduduk->nama . '' . $non_penduduk->titel_belakang;
                }
            }
            $hari = substr($non_penduduk->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($non_penduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($non_penduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($non_penduduk->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($non_penduduk->tanggal_lahir, 6, 4);
            $tempatlahir = $non_penduduk->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $non_penduduk->agama->agama;
            if ($non_penduduk->pekerjaan_id == 89) {
                $pekerjaan = $non_penduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $non_penduduk->pekerjaan->pekerjaan;
            }
            $alamat = $non_penduduk->alamat . ' RT. ' . $non_penduduk->alamat_rt . ' RW. ' . $non_penduduk->alamat_rw;
            //kabupaten
            if ($non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $non_penduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($non_penduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $non_penduduk->desa->kecamatan->kecamatan;
            }
            if ($non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $non_penduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($non_penduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $non_penduduk->desa->desa;
            }
            if ($non_penduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $non_penduduk->desa->desa;
            }
            if ($non_penduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $non_penduduk->desa->desa;
            }
            if ($non_penduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $non_penduduk->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $orangtuabapak = $suratnikah->nama_bapak_terdahulu;
            $jenis_kelamin = $non_penduduk->jk->jk;

        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkapcalon, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        if ($jenis_kelamin == 'Laki-Laki') {
            $bin_binti = 'Bin';
        }
        if ($jenis_kelamin == 'Perempuan') {
            $bin_binti = 'Binti';
        }
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  ' . $bin_binti, 0, '', 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(5);
        $pdf->Cell(0, 0, ':     ' . $orangtuabapak, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '7.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);

        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);

    }

    function bapakkandung($pdf, $id)
    {

        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');
        $desa = $this->desa->find(session('desa'));
        $suratnikah = $this->SuratNikah->find($id);

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
        // jika penduduk
        if ($suratnikah->jenis_bapak == 1) {
            $pribadi = $this->pribadi->find($suratnikah->penduduk_bapak);

            $keluarga = $this->keluarga->cekalamat($pribadi->id);
            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($pribadi->id);
            $orangtuabapak = $orangtuabapak2->nama;
            if ($pribadi->titel_belakang != '') {
                if ($pribadi->titel_depan != '') {
                    $namalengkapcalon = $pribadi->titel_depan . ' ' . $pribadi->nama . ', ' . $pribadi->titel_belakang;
                }
                if ($pribadi->titel_depan == '') {
                    $namalengkapcalon = $pribadi->nama . ', ' . $pribadi->titel_belakang;
                }
            }
            if ($pribadi->titel_belakang == '') {
                if ($pribadi->titel_depan != '') {
                    $namalengkapcalon = $pribadi->titel_depan . ' ' . $pribadi->nama . '' . $pribadi->titel_belakang;
                }
                if ($pribadi->titel_depan == '') {
                    $namalengkapcalon = $pribadi->nama . '' . $pribadi->titel_belakang;
                }
            }
            $hari = substr($pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $pribadi->agama->agama;

            if ($pribadi->pekerjaan_id == 89) {
                $pekerjaan = $pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $pribadi->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        //jika non penduduk
        if ($suratnikah->jenis_bapak == 2) {
            $nonpenduduk = $this->nonpenduduk->find($suratnikah->penduduk_bapak);
            if ($nonpenduduk->titel_belakang != '') {
                if ($nonpenduduk->titel_depan != '') {
                    $namalengkapcalon = $nonpenduduk->titel_depan . ' ' . $nonpenduduk->nama . ', ' . $nonpenduduk->titel_belakang;
                }
                if ($nonpenduduk->titel_depan == '' || $nonpenduduk->titel_depan == null) {
                    $namalengkapcalon = $nonpenduduk->nama . ', ' . $nonpenduduk->titel_belakang;
                }
            }
            if ($nonpenduduk->titel_belakang == '') {
                if ($nonpenduduk->titel_depan != '') {
                    $namalengkapcalon = $nonpenduduk->titel_depan . ' ' . $nonpenduduk->nama . '' . $nonpenduduk->titel_belakang;
                }
                if ($nonpenduduk->titel_depan == '' || $nonpenduduk->titel_depan == null) {
                    $namalengkapcalon = $nonpenduduk->nama . '' . $nonpenduduk->titel_belakang;
                }
            }
            $hari = substr($nonpenduduk->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($nonpenduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($nonpenduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($nonpenduduk->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($nonpenduduk->tanggal_lahir, 6, 4);
            $tempatlahir = $nonpenduduk->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $nonpenduduk->agama->agama;
            if ($nonpenduduk->pekerjaan_id == 89) {
                $pekerjaan = $nonpenduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $nonpenduduk->pekerjaan->pekerjaan;
            }
            $alamat = $nonpenduduk->alamat . ' RT. ' . $nonpenduduk->alamat_rt . ' RW. ' . $nonpenduduk->alamat_rw;
            //kabupaten
            if ($nonpenduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $nonpenduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($nonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $nonpenduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($nonpenduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $nonpenduduk->desa->kecamatan->kecamatan;
            }
            if ($nonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $nonpenduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($nonpenduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $nonpenduduk->desa->desa;
            }
            if ($nonpenduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $nonpenduduk->desa->desa;
            }
            if ($nonpenduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $nonpenduduk->desa->desa;
            }
            if ($nonpenduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $nonpenduduk->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $orangtuabapak = '';

        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkapcalon, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);

        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);

    }

    function ibukandung($pdf, $id)
    {
        $pdf->SetX(14);
        $pdf->Cell(14, 0, '1.  Nama Lengkap ', 0, '', 'L');
        $desa = $this->desa->find(session('desa'));
        $suratnikah = $this->SuratNikah->find($id);

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

        // jika penduduk
        if ($suratnikah->jenis_ibu == 1) {
            $pribadi = $this->pribadi->find($suratnikah->penduduk_ibu);
            $keluarga = $this->keluarga->cekalamat($pribadi->id);
//            $orangtuabapak2 = $this->orangtua->cekorangtuabapak($pribadi->id);
//            $orangtuabapak = $orangtuabapak2->nama;
            if ($pribadi->titel_belakang != '') {
                if ($pribadi->titel_depan != '') {
                    $namalengkapcalon = $pribadi->titel_depan . ' ' . $pribadi->nama . ', ' . $pribadi->titel_belakang;
                }
                if ($pribadi->titel_depan == '') {
                    $namalengkapcalon = $pribadi->nama . ', ' . $pribadi->titel_belakang;
                }
            }
            if ($pribadi->titel_belakang == '') {
                if ($pribadi->titel_depan != '') {
                    $namalengkapcalon = $pribadi->titel_depan . ' ' . $pribadi->nama . '' . $pribadi->titel_belakang;
                }
                if ($pribadi->titel_depan == '') {
                    $namalengkapcalon = $pribadi->nama . '' . $pribadi->titel_belakang;
                }
            }
            $hari = substr($pribadi->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($pribadi->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($pribadi->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($pribadi->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($pribadi->tanggal_lahir, 6, 4);
            $tempatlahir = $pribadi->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $pribadi->agama->agama;

            if ($pribadi->pekerjaan_id == 89) {
                $pekerjaan = $pribadi->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $pribadi->pekerjaan->pekerjaan;
            }
            $alamat = $keluarga->alamat . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw;
            $alamatlengkap = $statusdesa1 . ' ' . $namadesa . ' ' . $statuskecamatan1 . ' ' . $kecamatan . ' ' . $status1 . ' ' . $kabupaten;
        }
        //jika non penduduk
        if ($suratnikah->jenis_ibu == 2) {
            $nonpenduduk = $this->nonpenduduk->find($suratnikah->penduduk_ibu);

            if ($nonpenduduk->titel_belakang != '') {
                if ($nonpenduduk->titel_depan != '') {
                    $namalengkapcalon = $nonpenduduk->titel_depan . ' ' . $nonpenduduk->nama . ', ' . $nonpenduduk->titel_belakang;
                }
                if ($nonpenduduk->titel_depan == '' || $nonpenduduk->titel_depan == null) {
                    $namalengkapcalon = $nonpenduduk->nama . ', ' . $nonpenduduk->titel_belakang;
                }
            }
            if ($nonpenduduk->titel_belakang == '') {
                if ($nonpenduduk->titel_depan != '') {
                    $namalengkapcalon = $nonpenduduk->titel_depan . ' ' . $nonpenduduk->nama . '' . $nonpenduduk->titel_belakang;
                }
                if ($nonpenduduk->titel_depan == '' || $nonpenduduk->titel_depan == null) {
                    $namalengkapcalon = $nonpenduduk->nama . '' . $nonpenduduk->titel_belakang;
                }
            }
            $hari = substr($nonpenduduk->tanggal_lahir, 0, 2);
            $indo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            if (substr($nonpenduduk->tanggal_lahir, 3, 2) <= 9) {
                $bulan = $indo[substr($nonpenduduk->tanggal_lahir, 4, 1)];
            } else {
                $bulan = $indo[substr($nonpenduduk->tanggal_lahir, 3, 2)];
            }
            $tahun = substr($nonpenduduk->tanggal_lahir, 6, 4);
            $tempatlahir = $nonpenduduk->tempat_lahir . ', ' . $hari . ' ' . $bulan . ' ' . $tahun;
            $agama = $nonpenduduk->agama->agama;
            if ($nonpenduduk->pekerjaan_id == 89) {
                $pekerjaan = $nonpenduduk->pekerjaan_lain->pekerjaan_lain;
            } else {
                $pekerjaan = $nonpenduduk->pekerjaan->pekerjaan;
            }
            $alamat = $nonpenduduk->alamat . ' RT. ' . $nonpenduduk->alamat_rt . ' RW. ' . $nonpenduduk->alamat_rw;
            //kabupaten
            if ($nonpenduduk->desa->kecamatan->kabupaten->status == 1) {
                $statuspemohon = 'Kabupaten';
                $kabupatenpemohon = $nonpenduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            if ($nonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuspemohon = 'Kota';
                $kabupatenpemohon = $nonpenduduk->desa->kecamatan->kabupaten->kabupaten;
            }
            //kecamatan
            if ($nonpenduduk->desa->kecamatan->status == 1) {
                $statuskecamatanpemohon = 'Kecamatan';
                $kecamatanpemohon = $nonpenduduk->desa->kecamatan->kecamatan;
            }
            if ($nonpenduduk->desa->kecamatan->kabupaten->status == 2) {
                $statuskecamatanpemohon = 'Distrik';
                $kecamatanpemohon = $nonpenduduk->desa->kecamatan->kecamatan;
            }
            //desa
            if ($nonpenduduk->desa->status == 1) {
                $statusdesapemohon = 'Kelurahan';
                $namadesapemohon = $nonpenduduk->desa->desa;
            }
            if ($nonpenduduk->desa->status == 2) {
                $statusdesapemohon = 'Desa';
                $namadesapemohon = $nonpenduduk->desa->desa;
            }
            if ($nonpenduduk->desa->status == 3) {
                $statusdesapemohon = 'Kampung';
                $namadesapemohon = $nonpenduduk->desa->desa;
            }
            if ($nonpenduduk->desa->status == 4) {
                $statusdesapemohon = 'Negeri';
                $namadesapemohon = $nonpenduduk->desa->desa;
            }
            $alamatlengkap = $statusdesapemohon . ' ' . $namadesapemohon . ' ' . $statuskecamatanpemohon . ' ' . $kecamatanpemohon . ' ' . $statuspemohon . ' ' . $kabupatenpemohon;
            $orangtuabapak = '';

        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(16);
        $pdf->Cell(120, 0, ':     ' . $namalengkapcalon, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '2.  Tempat,Tgl. Lahir ', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $tempatlahir, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '3.  Warga Negara', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     Warga Negara Indonesia', 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '4.  Agama', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $agama, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '5.  Pekerjaan', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $pekerjaan, 0, '', 'L');
        $pdf->Ln(4);
        $pdf->SetX(14);
        $pdf->Cell(25, 0, '6.  Alamat', 0, '', 'L');
        $pdf->Cell(5);
        $pdf->Cell(120, 0, ':     ' . $alamat, 0, '', 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(3);
        $pdf->SetWidths([85]);
        $pdf->SetAligns(['J']);

        $pdf->SetX(49);
        $pdf->Row3([$alamatlengkap]);

    }
   function pejabat($pdf, $id)
    {
        $desa = $this->desa->find(session('desa'));
        $namadesa = $desa->desa;
        $suratnikah = $this->SuratNikah->find($id);
        if ($suratnikah->penandatangan == 'Atasnama Pimpinan') {
            $idpejabat = 'Sekretaris Organisasi';
            $pejabat = $this->pejabat->cekjabatan($idpejabat);
        } else {
            $pejabat = $this->pejabat->cekjabatan($suratnikah->penandatangan);
        }
        $hari3 = substr($suratnikah->tanggal, 0, 2);
        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (substr($suratnikah->tanggal, 3, 2) <= 9) {
            $bulan3 = $indo3[substr($suratnikah->tanggal, 4, 1)];
        } else {
            $bulan3 = $indo3[substr($suratnikah->tanggal, 3, 2)];
        }
        $tahun3 = substr($suratnikah->tanggal, 6, 4);
        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;

        $pdf->SetFont('Arial', '', 8);


        $pdf->SetX(90);
        $pdf->Cell(0, 10, $namadesa . ', ' . $tempatlahir3, 0, '', 'C');
        $pdf->Ln(4);
        if ($suratnikah->penandatangan == 'Atasnama Pimpinan' || $suratnikah->penandatangan == 'Jabatan Struktural') {
            $pejabatpimpinan = $this->pejabat->cekjabatan('Pimpinan Organisasi');
            $an = 'an.';
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(90);
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
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(90);
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
        if ($suratnikah->penandatangan == 'Jabatan Struktural') {
            $pejabatstruktural = $this->pejabat->find($suratnikah->jabatan_lainnya);

            $pdf->Ln(4);
            $pdf->SetX(90);
            $pdf->Cell(0, 8, 'u.b.', 0, '', 'C');
            $pdf->Ln(2);
            $pdf->SetX(90);
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
        if ($suratnikah->penandatangan != 'Atasnama Pimpinan' && $suratnikah->penandatangan != 'Jabatan Struktural') {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(90);
            if ($suratnikah->penandatangan == 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($suratnikah->penandatangan);
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
            if ($suratnikah->penandatangan == 'Pimpinan Organisasi' && $suratnikah->penandatangan != 'Sekretaris Organisasi') {
                $pejabatsekretaris = $this->pejabat->cekjabatan($suratnikah->penandatangan);
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
        $pdf->Ln(18);

        if ($pejabat != null) {
            $pdf->SetX(90);
            $pdf->SetFont('Arial', 'BU', 8);
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
            $pdf->Ln(4);
            $pdf->SetX(90);
            $pdf->Cell(0, 10, $pejabat->pangkat, 0, '', 'C');
            $pdf->Ln(4);

            if ($pejabat->nip != '') {
                $pdf->SetX(90);
                $pdf->Cell(0, 10, 'NIP.' . $pejabat->nip, 0, '', 'C');
            }
        }

    }

}