<?php

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DataPribadi\KeluargaRepository;
use App\Domain\Repositories\DataPribadi\OrangTuaRepository;
use App\Domain\Repositories\DataPribadi\PribadiRepository;
use App\Domain\Repositories\Master\UserRepository;
use App\Domain\Repositories\Organisasi\OrganisasiRepository;
use App\Domain\Repositories\Pelayanan\NikahRepository;
use App\Domain\Repositories\Penduduk\NonPendudukRepository;
use App\Domain\Repositories\Reverensi\AlamatRepository;
use App\Domain\Repositories\Reverensi\KodeAdministrasiRepository;
use App\Domain\Repositories\Reverensi\LogoRepository;
use App\Domain\Repositories\Reverensi\PejabatRepository;
use App\Domain\Repositories\Sheet\JKRepository;
use App\Domain\Repositories\Wilayah\DesaRepository;
use App\Http\Controllers\Controller;

class CetakLaporanNikah extends Controller
{
    protected $crud;
    protected $paginate;
//330 210
    public $kertas_pjg = 297; // portrait
    public $kertas_lbr = 290; // landscape
    public $kertas_pjg1 = 320; // portrait khusus refrensi

    public $font = 'Arial';
    public $field_font_size = 10;
    public $row_font_size = 8;

    public $butuh = false; // jika perlu fungsi AddPage()
    protected $padding_column = 5;
    protected $default_font_size = 8;
    protected $line = 0;

    public function __construct(
        NikahRepository $NikahRepository,
        PribadiRepository $pribadiRepository,
        NonPendudukRepository $nonPendudukRepository,
        PejabatRepository $pejabatRepository,
        LogoRepository $logoRepository,
        AlamatRepository $alamatRepository,
        DesaRepository $desaRepository,
        KodeAdministrasiRepository $kodeAdministrasiRepository,
        KeluargaRepository $keluargaRepository,
        OrangTuaRepository $orangTuaRepository,
        JKRepository $JKRepository,
        UserRepository $userRepository,
        OrganisasiRepository $organisasiRepository
    )
    {
        $this->Nikah = $NikahRepository;
        $this->pribadi = $pribadiRepository;
        $this->nonpenduduk = $nonPendudukRepository;
        $this->pejabat = $pejabatRepository;
        $this->logo = $logoRepository;
        $this->alamat = $alamatRepository;
        $this->desa = $desaRepository;
        $this->kodeadministrasi = $kodeAdministrasiRepository;
        $this->keluarga = $keluargaRepository;
        $this->orangtua = $orangTuaRepository;
        $this->jk = $JKRepository;
        $this->organisasi = $organisasiRepository;
        $this->user = $userRepository;
        $this->middleware('auth');

    }

    function Headers($pdf)
    {
        $pdf->AddFont('Times-Roman', 'B', 'timesb.php');
        //Put the watermark
        $pdf->SetFont('Arial', 'B', 80);
        $pdf->SetTextColor(128);
        $pdf->RotatedText(100, 200, 'Versi Ujicoba', 24);
    }

    function RotatedText($x, $y, $pdff, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $pdff->Text($x, $y, $pdff);
        $this->Rotate(0);
    }

    function Cover($pdf, $id)
    {
        $desa = $this->desa->find(session('desa'));
        $alamat = $this->alamat->cekalamatperdasarkandesa(session('organisasi'));
        $logogambar = $this->logo->getLogokabupatencetak(session('desa'));
        $kodeadministrasi = $this->kodeadministrasi->cekkodeadminbysession();
        $kodeadministrasikearsipan = $this->Nikah->cekkodejenisadministrasicetak();
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
        if ($id == 1) {
            $bulan = 'Januari';
        }
        if ($id == 2) {
            $bulan = 'Februari';
        }
        if ($id == 3) {
            $bulan = 'Maret';
        }
        if ($id == 4) {
            $bulan = 'April';
        }
        if ($id == 5) {
            $bulan = 'Mei';
        }
        if ($id == 6) {
            $bulan = 'Juni';
        }
        if ($id == 7) {
            $bulan = 'Juli';
        }
        if ($id == 8) {
            $bulan = 'Agustus';
        }
        if ($id == 9) {
            $bulan = 'Sebtember';
        }
        if ($id == 10) {
            $bulan = 'Oktober';
        }
        if ($id == 11) {
            $bulan = 'November';
        }
        if ($id == 12) {
            $bulan = 'Desember';
        }
        $pdf->AddPage();
        $pdf->Ln(5);
        $pdf->SetX(10);
        $pdf->SetFont('ARIAL', 'B', 20);
        $pdf->Cell(400, 270, '', 1, 0, '');
        $pdf->Ln(4);
        $pdf->SetX(340);
        if ($kodeadministrasikearsipan != null) {
            $kodeadministrasikearsipanhasil = $kodeadministrasikearsipan;
        } else {
            $kodeadministrasikearsipanhasil = '';
        }
        $pdf->Cell(60, 10, 'Model : ' . $kodeadministrasikearsipanhasil, 1, 0, '');
        $pdf->Ln(15);
        $pdf->SetFont('ARIAL', 'B', 25);
        $pdf->Cell(0, 5, 'LAPORAN REGISTER PELAYANAN ADMINISTRASI', 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 5, 'SURAT KETERANGAN WALI NIKAH', 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('ARIAL', '', 20);
        $pdf->Cell(0, 5, 'BAGIAN BULAN ' . strtoupper($bulan), 0, 0, 'C');
        $pdf->Ln(7);

        if ($logogambar != null) {
            $pdf->Image('app/logo/' . $logogambar->logo, 190, 90, 40, 40);
        }
        $pdf->Ln(110);
        $pdf->SetFont('ARIAL', '', 20);
        $pdf->Cell(0, 0, 'PEMERINTAH ' . $status . ' ' . strtoupper($kabupaten), 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 0, $statuskecamatan . ' ' . strtoupper($kecamatan), 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('ARIAL', 'B', 20);
        $pdf->Cell(0, 0, $statusdesa . ' ' . strtoupper($namadesa), 0, 0, 'C');
        if ($alamat != null) {
            $pdf->Ln(10);
            $pdf->SetFont('ARIAL', '', 15);
            if ($alamat->faxmile != 0) {
                $pdf->Cell(0, 0, 'Alamat:' . $alamat->alamat . ' Telp. ' . $alamat->telepon . ' Fax. ' . $alamat->faxmile, 0, 0, 'C');
            }
            if ($alamat->faxmile == 0) {
                $pdf->Cell(0, 0, 'Alamat:' . $alamat->alamat . ' Telp. ' . $alamat->telepon, 0, 0, 'C');
            }
            $pdf->Ln(5);
            $pdf->Cell(0, 0, 'email: ' . $alamat->email . ' website: ' . $alamat->website, 0, 0, 'C');
        }

        if ($kodeadministrasi != null) {
            $pdf->Ln(10);
            $pdf->SetFont('ARIAL', 'U', 15);

            $pdf->Cell(0, 0, strtoupper($namadesa) . '-' . strtoupper($kodeadministrasi->kode), 0, '', 'C');
        } else {
            $pdf->Ln(10);
            $pdf->SetFont('ARIAL', 'U', 15);
            $pdf->Cell(0, 0, strtoupper($namadesa), 0, '', 'C');
        }
        $organisasi = $this->organisasi->find(session('organisasi'));

        if ($organisasi->is_lock == 0) {
            $this->Headers($pdf);
        }


    }

    function repeatColumn($pdf, $id, $orientasi = '', $column = '', $height = 29.7)
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
            $this->$column($pdf, $id);
        }

        $this->line = $space_bottom;

//        echo $space_bottom . ' + ';
    }

    function Column($pdf, $id)
    {
        $pdf->AddFont('Tahoma', '', 'tahoma.php');
        $pdf->AddFont('Tahoma', 'B', 'tahomabd.php');
        $set = $this->butuh;
        if ($set == true) {
            $pdf->AddPage();
        }
        if ($id == 1) {
            $bulan = 'Januari';
        }
        if ($id == 2) {
            $bulan = 'Februari';
        }
        if ($id == 3) {
            $bulan = 'Maret';
        }
        if ($id == 4) {
            $bulan = 'April';
        }
        if ($id == 5) {
            $bulan = 'Mei';
        }
        if ($id == 6) {
            $bulan = 'Juni';
        }
        if ($id == 7) {
            $bulan = 'Juli';
        }
        if ($id == 8) {
            $bulan = 'Agustus';
        }
        if ($id == 9) {
            $bulan = 'Sebtember';
        }
        if ($id == 10) {
            $bulan = 'Oktober';
        }
        if ($id == 11) {
            $bulan = 'November';
        }
        if ($id == 12) {
            $bulan = 'Desember';
        }
        $pdf->SetFont('Tahoma', 'B', 10);
        $pdf->Cell(0, 10, 'BULAN                       : ' . strtoupper($bulan), 0, 0, 'L');
        $pdf->SetFont($this->font, 'B', $this->field_font_size);
        $pdf->Ln(10);
        $pdf->Cell(25, 20, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(20, 19, 'Nomor', 'TLR', 0, 'C');
        $pdf->Cell(80, 10, 'Wali Nikah', 1, 0, 'C');
        $pdf->Cell(80, 10, 'Pengantin Wanita', 1, 0, 'C');
        $pdf->Cell(30, 18, 'Hubungan Wali', 'TLR', 0, 'C');
        $pdf->Cell(25, 18, 'Status Wali', 'TLR', 0, 'C');
        $pdf->Cell(30, 18, 'Alasan Wali', 'TLR', 0, 'C');
        $pdf->Cell(30, 20, 'Pencatat Nikah', 1, 0, 'C');
        $pdf->Cell(30, 20, 'Penandatangan', 1, 0, 'C');
        $pdf->Cell(25, 20, 'Operator', 1, 0, 'C');
        $pdf->Cell(25, 20, 'Keterangan', 1, 0, 'C');
        $pdf->Ln(10);
        $pdf->Cell(25);
        $pdf->Cell(20, 10, 'Register', 'BLR', 0, 'C');
        $pdf->Cell(40, 10, 'Nama Wali Nikah', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Alamat Domisili', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Nama Pengantin', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Alamat Domisili', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Nikah', 'BLR', 0, 'C');
        $pdf->Cell(25, 10, 'Nikah', 'BLR', 0, 'C');
        $pdf->Cell(30, 10, 'Hakim', 'BLR', 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFont($this->font, '', 10);
        $pdf->Cell(25, 5, '(1)', 1, 0, 'C');
        $pdf->Cell(20, 5, '(2)', 1, 0, 'C');
        $pdf->Cell(40, 5, '(3)', 1, 0, 'C');
        $pdf->Cell(40, 5, '(4)', 1, 0, 'C');
        $pdf->Cell(40, 5, '(5)', 1, 0, 'C');
        $pdf->Cell(40, 5, '(6)', 1, 0, 'C');
        $pdf->Cell(30, 5, '(7)', 1, 0, 'C');
        $pdf->Cell(25, 5, '(8)', 1, 0, 'C');
        $pdf->Cell(30, 5, '(9)', 1, 0, 'C');
        $pdf->Cell(30, 5, '(10)', 1, 0, 'C');
        $pdf->Cell(30, 5, '(11)', 1, 0, 'C');
        $pdf->Cell(25, 5, '(12)', 1, 0, 'C');
        $pdf->Cell(25, 5, '(13)', 1, 0, 'C');
        $pdf->Ln(5);

    }

    function Column2($pdf)
    {
        $set = $this->butuh;
        if ($set == true) {
            $pdf->AddPage();
        }
        $pdf->AddFont('Tahoma', 'B', 'tahomabd.php');
        $pdf->SetFont('Tahoma', 'B', 12);
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'LAPORAN BUKU REGISTER PELAYANAN ADMINISTRASI', 0, '', 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'SURAT KETERANGAN WALI NIKAH', 0, 0, 'C');
        $pdf->Ln(10);
    }

    public function LaporanNikah($id)
    {
//        array(215, 330)

        $pdf = new PdfClass('L', 'mm', 'A3');
        $pdf->AliasNbPages();
        $pdf->orientasi = 'L';
        $pdf->AddFont('Arial', '', 'arial.php');

        //Disable automatic page break
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(0, 20);
        $this->Cover($pdf, $id);
        $pdf->AddPage();
        $pdf->SetTitle('Laporan Register Surat Nikah');

        $pdf->with_cover = true;
        $pdf->is_footer = true;
        $pdf->set_widths = 80;
        $pdf->set_footer = 25;
        $this->Column2($pdf);
        $this->Column($pdf, $id);
        $jumlah = $this->Nikah->getByPagecetak($id);

        $desa = $this->desa->find(session('desa'));


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

        $pdf->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C']);
        $pdf->SetWidths([25, 20, 40, 40, 40, 40, 30, 25, 30, 30, 30,25,25 ]);
        $pdf->SetFont('Tahoma', '', 10);

        if ($jumlah == null) {

        } else {
            foreach ($jumlah as $row) {
                $this->butuh = true;
                $namapejabat = $this->pejabat->cekjabatan($row->penandatangan);
                if ($namapejabat != null) {
                    $namapejabatna = $namapejabat->nama;
                }
                if ($namapejabat == null) {
                    $namapejabatna = '';
                }
                $namapejabat1 = $this->pejabat->find($row->pejabat_nikah);
                if ($namapejabat1 != null) {
                    $namapejabatna1 = $namapejabat1->nama;
                }
                if ($namapejabat1 == null) {
                    $namapejabatna1 = '';
                }
                if ($row->jenis_penduduk == 1) {
                    $namawali = $row->pribadi->nama;
                    $keluarga = $this->keluarga->cekalamat($row->penduduk_id);
                    $alamatregional = $keluarga->alamat . '' . ' RT. ' . $keluarga->alamat_rt . ' RW. ' . $keluarga->alamat_rw . ' ' . $statusdesa . ' ' . $namadesa . ' ' . $statuskecamatan . ' ' . $kecamatan . ' ' . $status . ' ' . $kabupaten;
                }
                if ($row->jenis_penduduk == 2) {
                    $namawali = $row->non_penduduk->nama;
                    //kabupaten
                    if ($row->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                        $statuskabupatennonpenduduk = 'Kabupaten';
                        $kabupatennonpenduduk = $row->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
                    }
                    if ($row->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                        $statuskabupatennonpenduduk = 'Kota';
                        $kabupatennonpenduduk = $row->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
                    }
                    //kecamatan
                    if ($row->non_penduduk->desa->kecamatan->status == 1) {
                        $statuskecamatannonpenduduk = 'Kecamatan';
                        $kecamatannonpenduduk = $row->non_penduduk->desa->kecamatan->kecamatan;
                    }
                    if ($row->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                        $statuskecamatannonpenduduk = 'Distrik';
                        $kecamatannonpenduduk = $row->non_penduduk->desa->kecamatan->kecamatan;
                    }
                    //desa
                    if ($row->non_penduduk->desa->status == 1) {
                        $statusdesanonpenduduk = 'Kelurahan';
                        $namadesanonpenduduk = $row->non_penduduk->desa->desa;
                    }
                    if ($row->non_penduduk->desa->status == 2) {
                        $statusdesanonpenduduk = 'Desa';
                        $namadesanonpenduduk = $row->non_penduduk->desa->desa;
                    }
                    if ($row->non_penduduk->desa->status == 3) {
                        $statusdesanonpenduduk = 'Kampung';
                        $namadesanonpenduduk = $row->non_penduduk->desa->desa;
                    }
                    if ($row->non_penduduk->desa->status == 4) {
                        $statusdesanonpenduduk = 'Negeri';
                        $namadesanonpenduduk = $row->non_penduduk->desa->desa;
                    }
                    $alamatregional = $row->non_penduduk->alamat . '|' . ' RT ' . $row->non_penduduk->alamat_rt . '| RW ' . $row->non_penduduk->alamat_rw . ' ' . $statusdesanonpenduduk . ' ' . $namadesanonpenduduk . ' ' . $statuskecamatannonpenduduk . ' ' . $kecamatannonpenduduk . ' ' . $statuskabupatennonpenduduk . ' ' . $kabupatennonpenduduk;
                }
                
                if ($row->jenis_pengantin == 1) {
                    $namapengantin = $row->pribadi->nama;
                    $keluargapengantin = $this->keluarga->cekalamat($row->penduduk_pengantin);
                    $alamatregionalpengantin = $keluargapengantin->alamat . '' . ' RT. ' . $keluargapengantin->alamat_rt . ' RW. ' . $keluargapengantin->alamat_rw . ' ' . $statusdesa . ' ' . $namadesa . ' ' . $statuskecamatan . ' ' . $kecamatan . ' ' . $status . ' ' . $kabupaten;
                }
                if ($row->jenis_pengantin == 2) {
                    $namapengantin = $row->non_penduduk->nama;
                    //kabupaten
                    if ($row->non_penduduk->desa->kecamatan->kabupaten->status == 1) {
                        $statuskabupatennonpenduduk1 = 'Kabupaten';
                        $kabupatennonpenduduk1 = $row->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
                    }
                    if ($row->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                        $statuskabupatennonpenduduk1 = 'Kota';
                        $kabupatennonpenduduk1 = $row->non_penduduk->desa->kecamatan->kabupaten->kabupaten;
                    }
                    //kecamatan
                    if ($row->non_penduduk->desa->kecamatan->status == 1) {
                        $statuskecamatannonpenduduk1 = 'Kecamatan';
                        $kecamatannonpenduduk1 = $row->non_penduduk->desa->kecamatan->kecamatan;
                    }
                    if ($row->non_penduduk->desa->kecamatan->kabupaten->status == 2) {
                        $statuskecamatannonpenduduk1 = 'Distrik';
                        $kecamatannonpenduduk1 = $row->non_penduduk->desa->kecamatan->kecamatan;
                    }
                    //desa
                    if ($row->non_penduduk->desa->status == 1) {
                        $statusdesanonpenduduk1 = 'Kelurahan';
                        $namadesanonpenduduk1 = $row->non_penduduk->desa->desa;
                    }
                    if ($row->non_penduduk->desa->status == 2) {
                        $statusdesanonpenduduk1 = 'Desa';
                        $namadesanonpenduduk1 = $row->non_penduduk->desa->desa;
                    }
                    if ($row->non_penduduk->desa->status == 3) {
                        $statusdesanonpenduduk1 = 'Kampung';
                        $namadesanonpenduduk1 = $row->non_penduduk->desa->desa;
                    }
                    if ($row->non_penduduk->desa->status == 4) {
                        $statusdesanonpenduduk1 = 'Negeri';
                        $namadesanonpenduduk1 = $row->non_penduduk->desa->desa;
                    }
                    $alamatregionalpengantin = $row->non_penduduk->alamat . '|' . ' RT ' . $row->non_penduduk->alamat_rt . '| RW ' . $row->non_penduduk->alamat_rw . ' ' . $statusdesanonpenduduk1 . ' ' . $namadesanonpenduduk1 . ' ' . $statuskecamatannonpenduduk1 . ' ' . $kecamatannonpenduduk1 . ' ' . $statuskabupatennonpenduduk1 . ' ' . $kabupatennonpenduduk1;
                }
                $users = $this->user->find($row->user_id);
                         $pdf->Row([$row->tanggal, $row->no_reg, $row->nik . $namawali, $alamatregional, $row->nik_pengantin.$namapengantin,$alamatregionalpengantin, $row->hubungan_wali,$row->status_wali,$row->alasan_wali,$namapejabatna ,$namapejabatna1,$users->name,'']);

//                $this->butuh = true;
                $organisasi = $this->organisasi->find(session('organisasi'));

                if ($organisasi->is_lock == 0) {
                    $this->Headers($pdf);
                }

                $this->repeatColumn($pdf, $id, 'L', 'Column');

            }

            $tanggal = $this->Nikah->getByPagelimittanggal($id);
            if ($tanggal != null) {
                $pejabatsekretaris = $this->pejabat->cekjabatan('Pimpinan Organisasi');

                if ($pejabatsekretaris != null) {
                    $this->butuh = true;

                    $pdf->Ln(20);
                    if ($pdf->y >= '240') {
                        $pdf->Ln(40);

                    }
                    if ($tanggal != null) {
                        $hari3 = substr($tanggal->tanggal, 0, 2);
                        $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
                        if (substr($tanggal->tanggal, 3, 2) <= 9) {
                            $bulan3 = $indo3[substr($tanggal->tanggal, 4, 1)];
                        } else {
                            $bulan3 = $indo3[substr($tanggal->tanggal, 3, 2)];
                        }
                        $tahun3 = substr($tanggal->tanggal, 6, 4);
                        $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;
                        $pdf->SetAligns(['C', 'C']);
                        $pdf->SetWidths([250, 70]);
                        $pdf->Row2(['', $namadesa . ', ' . $tempatlahir3]);
                    }
                    if ($pejabatsekretaris != null) {
                        if ($pejabatsekretaris->keterangan != '') {
                            $keteraganjabatan5 = $pejabatsekretaris->keterangan . ' ';
                        }
                        if ($pejabatsekretaris->keterangan == '') {
                            $keteraganjabatan5 = '';
                        }
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Row2(['', $keteraganjabatan5 . strtoupper($pejabatsekretaris->jabatan . ' ' . $namadesa . ',')]);
                    }


                    $pdf->Ln(25);

                    if ($pejabatsekretaris != null) {
//                    $pdf->SetX(200);
                        $pdf->SetFont('Arial', 'BU', 10);

                        if ($pejabatsekretaris->titel_belakang != '' && $pejabatsekretaris->titel_depan != '') {
                            $pdf->Row2(['', $pejabatsekretaris->titel_depan . ' ' . $pejabatsekretaris->nama . ', ' . $pejabatsekretaris->titel_belakang]);
                        } else if ($pejabatsekretaris->titel_belakang == '' && $pejabatsekretaris->titel_depan != '') {
                            $pdf->Row2(['', $pejabatsekretaris->titel_depan . ' ' . $pejabatsekretaris->nama]);
                        } else if ($pejabatsekretaris->titel_belakang != '' && $pejabatsekretaris->titel_depan == '') {
                            $pdf->Row2(['', $pejabatsekretaris->nama . ', ' . $pejabatsekretaris->titel_belakang]);
                        } else if ($pejabatsekretaris->titel_belakang == '' && $pejabatsekretaris->titel_depan == '') {
                            $pdf->Row2(['', $pejabatsekretaris->nama]);
                        }
                        $pdf->SetFont('Arial', '', 10);
                        $pdf->Row2(['', $pejabatsekretaris->pangkat]);
                        if ($pejabatsekretaris->nip != '') {
                            $pdf->Row2(['', 'NIP.' . $pejabatsekretaris->nip]);


                        }
                    }
                }
            }
        }
//        $pdf->set_line = false;

        $tanggal = date('d-m-y');

        $pdf->Output('cetak-data-register-' . $tanggal . '.pdf', 'I');
        exit;
    }
}