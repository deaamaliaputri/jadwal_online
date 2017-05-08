<?php
/**
 * Created by PhpStorm.
 * User: - INDIEGLO -
 * Date: 27/10/2015
 * Time: 8:45
 */

namespace App\Http\Controllers\Cetak;


use App\Domain\Repositories\DepartmentsRepository;
use App\Domain\Repositories\KelasRepository;
use App\Domain\Repositories\SchedulesRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DepartmentsController;

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
        SchedulesRepository $schedulesRepository,
        DepartmentsRepository $departmentsRepository,
        KelasRepository $kelasRepository
    )
    {
        $this->kelas = $kelasRepository;
        $this->jurusan = $departmentsRepository;
        $this->schedules = $schedulesRepository;
        $this->middleware('auth');

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

    function Column($pdf, $id,$id2)
    {
        $pdf->AddFont('Tahoma', '', 'tahoma.php');
        $pdf->AddFont('Tahoma', 'B', 'tahomabd.php');
        $set = $this->butuh;
        if ($set == true) {
            $pdf->AddPage();
        }
        $pdf->SetFont($this->font, 'B', 16);
        $pdf->Ln(10);
        $pdf->Cell(30, 15, 'Jam', 1, 0, 'C');
        $pdf->Cell(30, 15, 'Jam-ke', 1, 0, 'C');
        $pdf->Cell(37, 10, 'Senin', 'TLR', 0, 'C');
        $pdf->Cell(37, 10, 'Selasa', 'TLR', 0, 'C');
        $pdf->Cell(37, 10, 'Rabu', 'TLR', 0, 'C');
        $pdf->Cell(37, 10, 'Kamis', 'TLR', 0, 'C');
        $pdf->Cell(37, 10, 'Jumat', 'TLR', 0, 'C');
        $pdf->Cell(37, 10, 'Sabtu', 'TLR', 0, 'C');
        $pdf->Ln(0);
        $pdf->Ln(10);
        $pdf->SetFont($this->font, '', 10);
        $jumlah = $this->schedules->getByPagecetak($id,$id2);
        $jumlah2 = $this->schedules->getByPagecetak2($id);
        $jumlah3 = $this->schedules->getByPagecetak3($id);
        $jumlah4 = $this->schedules->getByPagecetak4($id);
        $jumlah5 = $this->schedules->getByPagecetak5($id);
        $jumlah6 = $this->schedules->getByPagecetak6($id);
 
        $pdf->Cell(60);
        $pdf->Cell(37, 5, $jumlah[0]->room, 1, 0, 'C');
        $pdf->Cell(37, 5, $jumlah2[0]->room, 1, 0, 'C');
        $pdf->Cell(37, 5, $jumlah3[0]->room, 1, 0, 'C');
        $pdf->Cell(37, 5, $jumlah4[0]->room, 1, 0, 'C');
        $pdf->Cell(37, 5, $jumlah5[0]->room, 1, 0, 'C');
        $pdf->Cell(37, 5, $jumlah6[0]->room, 1, 0, 'C');
        $pdf->Ln(5);

    }

    public function Daftar($id,$id2)
    {
//        array(215, 330)

        $pdf = new PdfClass('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->orientasi = 'L';
        $pdf->AddFont('Arial', '', 'arial.php');

        //Disable automatic page break
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(8, 10, 10);
        $pdf->SetAutoPageBreak(0, 20);
//        $this->Cover($pdf, $id);
        $pdf->AddPage();
        $pdf->SetTitle('Laporan Register Surat Asal Usul');

        $pdf->with_cover = true;
        $pdf->is_footer = false;
        $pdf->set_widths = 80;
        $pdf->set_footer = 25;
//        $this->Column2($pdf);
        $gambar = 'assets/images/logoo.jpg';
        $pdf->Image($gambar, 130, 10, 40, 40);
        $pdf->Ln(45);
        $pdf->AddFont('Tahoma', 'B', 'tahomabd.php');
        $pdf->SetFont('Tahoma', 'BU', 12);
        $pdf->Cell(0, 0, 'SMK NEGERI 1 KEPANJEN', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 0, 'JL. Raya Kedungpedaringan, 651563, Kepanjen, Malang', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 0, 'No. Telp : (0341) 395777', 0, 0, 'C');
        $pdf->Ln(5);
        $kelas = $this->kelas->find($id);
        $jurusan = $this->jurusan->find($id2);
        $pdf->Cell(0, 0, 'Kelas ' . $kelas->name.' '.$jurusan->name, 0, 0, 'C');

        $this->Column($pdf, $id, $id2);
        $pdf->SetFont('Tahoma', '', 13);


        $jumlah = $this->schedules->getByPagecetak($id,$id2);

        $pdf->Cell(30, 14, $jumlah[0]->time, 1, 0, 'C');
        $pdf->Cell(30, 14, $jumlah[0]->hour, 1, 0, 'C');
        $pdf->Cell(37, 7, strtoupper($jumlah[0]->name), 1, 0, 'C');
        $jumlah2 = $this->schedules->getByPagecetak2($id);
        $pdf->Cell(37, 7, strtoupper($jumlah2[0]->name), 1, 0, 'C');
        $jumlah3 = $this->schedules->getByPagecetak3($id);
        $pdf->Cell(37, 7, strtoupper($jumlah3[0]->name), 1, 0, 'C');
        $jumlah4 = $this->schedules->getByPagecetak4($id);
        $pdf->Cell(37, 7, strtoupper($jumlah4[0]->name), 1, 0, 'C');
        $jumlah5 = $this->schedules->getByPagecetak5($id);
        $pdf->Cell(37, 7, strtoupper($jumlah5[0]->name), 1, 0, 'C');
        $jumlah6 = $this->schedules->getByPagecetak6($id);
        $pdf->Cell(37, 7, strtoupper($jumlah6[0]->name), 1, 0, 'C');
        $pdf->Ln(7);
        $pdf->Cell(60);
        $pdf->Cell(37, 7, $jumlah[0]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah2[0]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah3[0]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah4[0]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah5[0]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah6[0]->kode, 1, 0, 'R');
        $pdf->Ln(7);
        $pdf->Cell(30, 7, '09.00 - 10.00', 1, 0, 'C');
        $pdf->Cell(252, 7, 'ISTIRAHAT', 1, 0, 'C');
        $pdf->Ln(7);
        $pdf->Cell(30, 14, $jumlah[1]->time, 1, 0, 'C');
        $pdf->Cell(30, 14, $jumlah[1]->hour, 1, 0, 'C');
        $pdf->Cell(37, 7, strtoupper($jumlah[1]->name), 1, 0, 'C');
        $jumlah2 = $this->schedules->getByPagecetak2($id);
        $pdf->Cell(37, 7, strtoupper($jumlah2[1]->name), 1, 0, 'C');
        $jumlah3 = $this->schedules->getByPagecetak3($id);
        $pdf->Cell(37, 7, strtoupper($jumlah3[1]->name), 1, 0, 'C');
        $jumlah4 = $this->schedules->getByPagecetak4($id);
        $pdf->Cell(37, 7, strtoupper($jumlah4[1]->name), 1, 0, 'C');
        $jumlah5 = $this->schedules->getByPagecetak5($id);
        $pdf->Cell(37, 7, strtoupper($jumlah5[1]->name), 1, 0, 'C');
        $jumlah6 = $this->schedules->getByPagecetak6($id);
        $pdf->Cell(37, 7, strtoupper($jumlah6[1]->name), 1, 0, 'C');
        $pdf->Ln(7);
        $pdf->Cell(60);
        $pdf->Cell(37, 7, $jumlah[1]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah2[1]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah3[1]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah4[1]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah5[1]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah6[1]->kode, 1, 0, 'R');
        $pdf->Ln(7);
        $pdf->Cell(30, 7, '12.00 - 13.00', 1, 0, 'C');
        $pdf->Cell(252, 7, 'ISTIRAHAT', 1, 0, 'C');
        $pdf->Ln(7);
        $pdf->Cell(30, 14, $jumlah[2]->time, 1, 0, 'C');
        $pdf->Cell(30, 14, $jumlah[2]->hour, 1, 0, 'C');
        $pdf->Cell(37, 7, strtoupper($jumlah[2]->name), 1, 0, 'C');
        $jumlah2 = $this->schedules->getByPagecetak2($id);
        $pdf->Cell(37, 7, strtoupper($jumlah2[2]->name), 1, 0, 'C');
        $jumlah3 = $this->schedules->getByPagecetak3($id);
        $pdf->Cell(37, 7, strtoupper($jumlah3[2]->name), 1, 0, 'C');
        $jumlah4 = $this->schedules->getByPagecetak4($id);
        $pdf->Cell(37, 7, strtoupper($jumlah4[2]->name), 1, 0, 'C');
        $jumlah5 = $this->schedules->getByPagecetak5($id);
        $pdf->Cell(37, 7, strtoupper($jumlah5[2]->name), 1, 0, 'C');
        $jumlah6 = $this->schedules->getByPagecetak6($id);
        $pdf->Cell(37, 7, strtoupper($jumlah6[2]->name), 1, 0, 'C');
        $pdf->Ln(7);
        $pdf->Cell(60);
        $pdf->Cell(37, 7, $jumlah[2]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah2[2]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah3[2]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah4[2]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah5[2]->kode, 1, 0, 'R');
        $pdf->Cell(37, 7, $jumlah6[2]->kode, 1, 0, 'R');



        $pdf->Ln(10);
        $pdf->Cell(60, 16, 'Kepala Sekolah', 0, '', 'C');
//        $pdf->SetX(30);
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Ln(20);
        $pdf->Cell(60, 36, 'Drs . R. DIDIK INDRATNO MW,MN', 0, '', 'C');
        $pdf->Ln(5);
        $pdf->Cell(60, 36, 'NIP. 19600717 198703 1 012', 0, '', 'C');

        $pdf->SetFont('Tahoma', '', 13);
        $pdf->Cell(370, -34, 'Wali Kelas', 0, '', 'C');
//        $pdf->SetX(30);
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Ln(30);
        $pdf->Cell(490, -34, 'Drs . R. DIDIK INDRATNO MW,MN', 0, '', 'C');
        $pdf->Ln(5);
        $pdf->Cell(490, -34, 'NIP. 19600717 198703 1 012', 0, '', 'C');



        $tanggal = date('d-m-y');

        $pdf->Output('cetak-data-register-' . $tanggal . '.pdf', 'I');
        exit;
    }
}