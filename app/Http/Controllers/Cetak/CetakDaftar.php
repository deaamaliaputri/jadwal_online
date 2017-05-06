<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\SchedulesRepository;
use App\Http\Controllers\Controller;

class CetakDaftar extends Controller
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
        SchedulesRepository $schedulesRepository
    )
    {
        $this->schedules = $schedulesRepository;
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
        $pdf->AddPage();
        $pdf->Ln(5);
        $pdf->SetX(10);
        $pdf->SetFont('ARIAL', 'B', 20);
        $pdf->Cell(400, 270, '', 1, 0, '');
        $pdf->Ln(4);
        $pdf->SetX(340);
        $pdf->Ln(15);
        $pdf->SetFont('ARIAL', 'B', 25);
        $pdf->Cell(0, 5, 'LAPORAN REGISTER PELAYANAN ADMINISTRASI', 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 5, 'SURAT ASAL-USUL KELAHIRAN', 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('ARIAL', '', 20);
        $pdf->Ln(7);
$gambar = 'assets/images/logo.png';
            $pdf->Image($gambar, 190, 90, 40, 40);
        $pdf->Ln(110);
        $pdf->SetFont('ARIAL', '', 20);
        // $pdf->Cell(0, 0, 'PEMERINTAH ' . $status . ' ' . strtoupper($kabupaten), 0, 0, 'C');
        $pdf->Ln(10);
        // $pdf->Cell(0, 0, $statuskecamatan . ' ' . strtoupper($kecamatan), 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('ARIAL', 'B', 20);
        // $pdf->Cell(0, 0, $statusdesa . ' ' . strtoupper($namadesa), 0, 0, 'C');
        // if ($alamat != null) {
        //     $pdf->Ln(10);
        //     $pdf->SetFont('ARIAL', '', 15);
        //     if ($alamat->faxmile != 0) {
        //         $pdf->Cell(0, 0, 'Alamat:' . $alamat->alamat . ' Telp. ' . $alamat->telepon . ' Fax. ' . $alamat->faxmile, 0, 0, 'C');
        //     }
        //     if ($alamat->faxmile == 0) {
        //         $pdf->Cell(0, 0, 'Alamat:' . $alamat->alamat . ' Telp. ' . $alamat->telepon, 0, 0, 'C');
        //     }
        //     $pdf->Ln(5);
        //     $pdf->Cell(0, 0, 'email: ' . $alamat->email . ' website: ' . $alamat->website, 0, 0, 'C');
        // }

        // if ($kodeadministrasi != null) {
        //     $pdf->Ln(10);
        //     $pdf->SetFont('ARIAL', 'U', 15);

        //     $pdf->Cell(0, 0, strtoupper($namadesa) . '-' . strtoupper($kodeadministrasi->kode), 0, '', 'C');
        // } else {
        //     $pdf->Ln(10);
        //     $pdf->SetFont('ARIAL', 'U', 15);
        //     $pdf->Cell(0, 0, strtoupper($namadesa), 0, '', 'C');
        // }
        // $organisasi = $this->organisasi->find(session('organisasi'));

        // if ($organisasi->is_lock == 0) {
        //     $this->Headers($pdf);
        // }


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
        $pdf->Cell(25, 20, 'Jam', 1, 0, 'C');
        $pdf->Cell(20, 19, 'Jam-ke', 'TLR', 0, 'C');
        $pdf->Cell(40, 17, 'Senin', 'TLR', 0, 'C');
        $pdf->Cell(40, 17, 'Selasa', 'TLR', 0, 'C');
        $pdf->Cell(40, 17, 'Rabu', 'TLR', 0, 'C');
        $pdf->Cell(40, 17, 'Kamis', 'TLR', 0, 'C');
        $pdf->Cell(40, 17, 'Jumat', 'TLR', 0, 'C');
        $pdf->Cell(40, 17, 'Sabtu', 'TLR', 0, 'C');
        $pdf->Ln(10);
        $pdf->Cell(25);
         $pdf->Ln(10);
        $pdf->SetFont($this->font, '', 10);
        $pdf->Cell(25, 5, '(1)', 1, 0, 'C');
        $pdf->Cell(20, 5, '(2)', 1, 0, 'C');
        $pdf->Cell(40, 5, '(3)', 1, 0, 'C');
        $pdf->Cell(25, 5, '(4)', 1, 0, 'C');
        $pdf->Cell(70, 5, '(5)', 1, 0, 'C');
        $pdf->Cell(40, 5, '(6)', 1, 0, 'C');
        $pdf->Cell(38, 5, '(7)', 1, 0, 'C');
        $pdf->Cell(32, 5, '(8)', 1, 0, 'C');
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
        $pdf->Cell(0, 10, 'SURAT KETERANGAN ASAL USUL KELAHIRAN', 0, 0, 'C');
        $pdf->Ln(10);
    }

    public function Daftar($id)
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
        $pdf->SetTitle('Laporan Register Surat Asal Usul');

        $pdf->with_cover = true;
        $pdf->is_footer = true;
        $pdf->set_widths = 80;
        $pdf->set_footer = 25;
        $this->Column2($pdf);
        $this->Column($pdf, $id);
        $jumlah = $this->schedules->getByPagecetak($id);



        $pdf->SetAligns(['C', 'C', 'C', 'C', 'JC', 'C', 'C', 'C', 'C', 'C', 'C']);
        $pdf->SetWidths([25, 20, 40, 25, 70, 40, 38, 32, 30, 30, 30]);
        $pdf->SetFont('Tahoma', '', 10);

        if ($jumlah == null) {

        } else {
            foreach ($jumlah as $row) {
                $this->butuh = true;
                
                    $pdf->Row([$row->time, $row->hour, $row->name , '']);

               
                $this->repeatColumn($pdf, $id, 'L', 'Column');

            }
//             $tanggal = $this->asalusul->getByPagelimittanggal($id);
//             if ($tanggal != null) {
//                 $pejabatsekretaris = $this->pejabat->cekjabatan('Pimpinan Organisasi');

//                 if ($pejabatsekretaris != null) {
//                     $this->butuh = true;

//                     $pdf->Ln(20);
//                     if ($pdf->y >= '240') {
//                         $pdf->Ln(40);

//                     }
//                     if ($tanggal != null) {
//                         $hari3 = substr($tanggal->tanggal, 0, 2);
//                         $indo3 = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
//                         if (substr($tanggal->tanggal, 3, 2) <= 9) {
//                             $bulan3 = $indo3[substr($tanggal->tanggal, 4, 1)];
//                         } else {
//                             $bulan3 = $indo3[substr($tanggal->tanggal, 3, 2)];
//                         }
//                         $tahun3 = substr($tanggal->tanggal, 6, 4);
//                         $tempatlahir3 = $hari3 . ' ' . $bulan3 . ' ' . $tahun3;
//                         $pdf->SetAligns(['C', 'C']);
//                         $pdf->SetWidths([250, 70]);
//                         $pdf->Row2(['', $namadesa . ', ' . $tempatlahir3]);
//                     }
//                     if ($pejabatsekretaris != null) {
//                         if ($pejabatsekretaris->keterangan != '') {
//                             $keteraganjabatan5 = $pejabatsekretaris->keterangan . ' ';
//                         }
//                         if ($pejabatsekretaris->keterangan == '') {
//                             $keteraganjabatan5 = '';
//                         }
//                         $pdf->SetFont('Arial', 'B', 10);
//                         $pdf->Row2(['', $keteraganjabatan5 . strtoupper($pejabatsekretaris->jabatan . ' ' . $namadesa . ',')]);
//                     }


//                     $pdf->Ln(25);

//                     if ($pejabatsekretaris != null) {
// //                    $pdf->SetX(200);
//                         $pdf->SetFont('Arial', 'BU', 10);

//                         if ($pejabatsekretaris->titel_belakang != '' && $pejabatsekretaris->titel_depan != '') {
//                             $pdf->Row2(['', $pejabatsekretaris->titel_depan . ' ' . $pejabatsekretaris->nama . ', ' . $pejabatsekretaris->titel_belakang]);
//                         } else if ($pejabatsekretaris->titel_belakang == '' && $pejabatsekretaris->titel_depan != '') {
//                             $pdf->Row2(['', $pejabatsekretaris->titel_depan . ' ' . $pejabatsekretaris->nama]);
//                         } else if ($pejabatsekretaris->titel_belakang != '' && $pejabatsekretaris->titel_depan == '') {
//                             $pdf->Row2(['', $pejabatsekretaris->nama . ', ' . $pejabatsekretaris->titel_belakang]);
//                         } else if ($pejabatsekretaris->titel_belakang == '' && $pejabatsekretaris->titel_depan == '') {
//                             $pdf->Row2(['', $pejabatsekretaris->nama]);
//                         }
//                         $pdf->SetFont('Arial', '', 10);
//                         $pdf->Row2(['', $pejabatsekretaris->pangkat]);
//                         if ($pejabatsekretaris->nip != '') {
//                             $pdf->Row2(['', 'NIP.' . $pejabatsekretaris->nip]);


//                         }
//                     }
                // }
            }
        
//        $pdf->set_line = false;

        $tanggal = date('d-m-y');

        $pdf->Output('cetak-data-register-' . $tanggal . '.pdf', 'I');
        exit;
    }
}