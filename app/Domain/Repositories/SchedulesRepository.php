<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Schedules;
use App\Domain\Contracts\SchedulesInterface;
use App\Domain\Contracts\Crudable;


/**
 * Class SchedulesRepository
 * @package App\Domain\Repositories
 */
class SchedulesRepository extends AbstractRepository implements SchedulesInterface, Crudable
{

    /**
     * @var Schedules
     */
    protected $model;

    /**
     * SchedulesRepository constructor.
     * @param Schedules $schedules
     */
    public function __construct(Schedules $schedules)
    {
        $this->model = $schedules;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @param int $limit
     * @param int $page
     * @param array $column
     * @param string $field
     * @param string $search
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate($limit = 10, $page = 1, array $column = ['*'], $field, $search = '')
    {
        // query to aql
        if (session('level') == 0) {

            $akun = $this->model
                ->join('departments', 'schedules.departments_id', '=', 'departments.id')
                ->join('kelas', 'schedules.kelas_id', '=', 'kelas.id')
                ->join('teachers', 'schedules.wali_kelas', '=', 'teachers.id')
                ->join('subjects', 'schedules.subjects_id', '=', 'subjects.id')
                ->where(function ($query) use ($search) {
                    $query->where('schedules.hari', 'like', '%' . $search . '%')
                        ->orWhere('departments.name', 'like', '%' . $search . '%')
                        ->orWhere('teachers.name', 'like', '%' . $search . '%')
                        ->orWhere('kelas.name', 'like', '%' . $search . '%')
                        ->orWhere('subjects.name', 'like', '%' . $search . '%');
                })
                ->select('schedules.*')
                ->paginate($limit)
                ->toArray();

            return $akun;
        }
        if (session('level') == 1) {

            // $akun = Schedules::selectRaw('ANY_VALUE(id) as id_schedules,ANY_VALUE(kelas_id),ANY_VALUE(departments_id) ')->groupBy('kelas_id','departments_id')->orderBy('kelas_id', 'DESC')->get();
            $akun = $this->model->select('*')->groupBy('kelas_id', 'departments_id')->orderBy('kelas_id', 'DESC')->get();
            //  dump($akun);
            $result = [];
            foreach ($akun as $key => $value) {
                $result[] = $value->id;
            }

            // --> Flatten  array
            $array_id = [];
            $array_length = count($result);
            for ($i = 0; $i <= $array_length - 1; $i++) {
                array_push($array_id, $result[$i]);
            };

            $spp = $this->model
                ->join('departments', 'schedules.departments_id', '=', 'departments.id')
                ->join('kelas', 'schedules.kelas_id', '=', 'kelas.id')
                ->join('teachers', 'schedules.wali_kelas', '=', 'teachers.id')
                ->join('subjects', 'schedules.subjects_id', '=', 'subjects.id')
                ->whereIn('schedules.id', $array_id)
                ->where(function ($query) use ($search) {
                    $query->where('schedules.hari', 'like', '%' . $search . '%')
                        ->orWhere('departments.name', 'like', '%' . $search . '%')
                        ->orWhere('teachers.name', 'like', '%' . $search . '%')
                        ->orWhere('kelas.name', 'like', '%' . $search . '%')
                        ->orWhere('subjects.name', 'like', '%' . $search . '%');
                })
                ->select('schedules.*')
                ->paginate($limit)
                ->toArray();
            return $spp;
        }
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(array $data)
    {
        // execute sql insert
        Schedules::create([
            'time' => e($data['time']),
            'hour' => e($data['hour']),
            'room' => e($data['room']),
            'hari' => e($data['hari']),
            'wali_kelas' => e($data['wali_kelas']),
            'teachers_id' => e($data['teachers_id']),
            'departments_id' => e($data['departments_id']),
            'kelas_id' => e($data['kelas_id']),
            'subjects_id' => e($data['subjects_id']),
        ]);
        Schedules::create([
            'hari' => e($data['hari']),
            'wali_kelas' => e($data['wali_kelas']),
            'departments_id' => e($data['departments_id']),
            'kelas_id' => e($data['kelas_id']),
            'time' => e($data['time_2']),
            'hour' => e($data['hour_2']),
            'room' => e($data['room_2']),
            'subjects_id' => e($data['subjects_id_2']),
            'teachers_id' => e($data['teachers_id_2']),
        ]);
        Schedules::create([
            'hari' => e($data['hari']),
            'wali_kelas' => e($data['wali_kelas']),
            'departments_id' => e($data['departments_id']),
            'kelas_id' => e($data['kelas_id']),
            'time' => e($data['time_3']),
            'hour' => e($data['hour_3']),
            'room' => e($data['room_3']),
            'subjects_id' => e($data['subjects_id_3']),
            'teachers_id' => e($data['teachers_id_3']),
        ]);

        return response()->json(['created' => true], 200);

    }

    /**
     * @param $id
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($id, array $data)
    {
        return parent::update($id, [
            'time' => e($data['time']),
            'hour' => e($data['hour']),
            'room' => e($data['room']),
            'hari' => e($data['hari']),
            'wali_kelas' => e($data['wali_kelas']),
            'teachers_id' => e($data['teachers_id']),
            'departments_id' => e($data['departments_id']),
            'kelas_id' => e($data['kelas_id']),
            'subjects_id' => e($data['subjects_id'])

        ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {
        return parent::delete($id);
    }


    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findById($id, array $columns = ['*'])
    {
        return parent::find($id, $columns);
    }

    public function getByPagecetak($id, $id2)
    {

        // query to aql
        $AsalUsul = $this->model
            ->join('departments', 'schedules.departments_id', '=', 'departments.id')
            ->join('kelas', 'schedules.kelas_id', '=', 'kelas.id')
            ->join('teachers', 'schedules.teachers_id', '=', 'teachers.id')
            ->join('subjects', 'schedules.subjects_id', '=', 'subjects.id')
            ->where('kelas.id', $id)
            ->where('departments.id', $id2)
            ->where('schedules.hari', 'Senin')
            ->orderBy('schedules.time', 'asc')
            ->select(
                'schedules.time',
                'schedules.room',
                'schedules.hour',
                'schedules.hari',
                'schedules.wali_kelas',
                'teachers.kode',
                'subjects.name')
            ->get();

        return $AsalUsul;
    }

    public function getByPagecetak2($id)
    {

        // query to aql
        $AsalUsul = $this->model
            ->join('departments', 'schedules.departments_id', '=', 'departments.id')
            ->join('kelas', 'schedules.kelas_id', '=', 'kelas.id')
            ->join('teachers', 'schedules.teachers_id', '=', 'teachers.id')
            ->join('subjects', 'schedules.subjects_id', '=', 'subjects.id')
            ->where('kelas.id', $id)
            ->where('schedules.hari', 'Selasa')
            ->orderBy('schedules.time', 'asc')
            ->select(
                'schedules.id',
                'schedules.time',
                'schedules.room',
                'schedules.hour',
                'schedules.hari',
                'teachers.kode',
                'subjects.name')
            ->get();

        return $AsalUsul;
    }

    public function getByPagecetak3($id)
    {

        // query to aql
        $AsalUsul = $this->model
            ->join('departments', 'schedules.departments_id', '=', 'departments.id')
            ->join('kelas', 'schedules.kelas_id', '=', 'kelas.id')
            ->join('teachers', 'schedules.teachers_id', '=', 'teachers.id')
            ->join('subjects', 'schedules.subjects_id', '=', 'subjects.id')
            ->where('kelas.id', $id)
            ->where('schedules.hari', 'Rabu')
            ->orderBy('schedules.time', 'asc')
            ->select(
                'schedules.id',
                'schedules.time',
                'schedules.room',
                'schedules.hour',
                'schedules.hari',
                'teachers.kode',
                'subjects.name')
            ->get();

        return $AsalUsul;
    }

    public function getByPagecetak4($id)
    {

        // query to aql
        $AsalUsul = $this->model
            ->join('departments', 'schedules.departments_id', '=', 'departments.id')
            ->join('kelas', 'schedules.kelas_id', '=', 'kelas.id')
            ->join('teachers', 'schedules.teachers_id', '=', 'teachers.id')
            ->join('subjects', 'schedules.subjects_id', '=', 'subjects.id')
            ->where('kelas.id', $id)
            ->where('schedules.hari', 'Kamis')
            ->orderBy('schedules.time', 'asc')
            ->select(
                'schedules.id',
                'schedules.time',
                'schedules.room',
                'schedules.hour',
                'schedules.hari',
                'teachers.kode',
                'subjects.name')
            ->get();

        return $AsalUsul;
    }

    public function getByPagecetak5($id)
    {

        // query to aql
        $AsalUsul = $this->model
            ->join('departments', 'schedules.departments_id', '=', 'departments.id')
            ->join('kelas', 'schedules.kelas_id', '=', 'kelas.id')
            ->join('teachers', 'schedules.teachers_id', '=', 'teachers.id')
            ->join('subjects', 'schedules.subjects_id', '=', 'subjects.id')
            ->where('kelas.id', $id)
            ->where('schedules.hari', 'Jumat')
            ->orderBy('schedules.time', 'asc')
            ->select(
                'schedules.id',
                'schedules.time',
                'schedules.room',
                'schedules.hour',
                'schedules.hari',
                'teachers.kode',
                'subjects.name')
            ->get();

        return $AsalUsul;
    }

    public function getByPagecetak6($id)
    {

        // query to aql
        $AsalUsul = $this->model
            ->join('departments', 'schedules.departments_id', '=', 'departments.id')
            ->join('kelas', 'schedules.kelas_id', '=', 'kelas.id')
            ->join('teachers', 'schedules.teachers_id', '=', 'teachers.id')
            ->join('subjects', 'schedules.subjects_id', '=', 'subjects.id')
            ->where('kelas.id', $id)
            ->where('schedules.hari', 'Sabtu')
            ->orderBy('schedules.time', 'asc')
            ->select(
                'schedules.id',
                'schedules.time',
                'schedules.room',
                'schedules.hour',
                'schedules.hari',
                'teachers.kode',
                'subjects.name')
            ->get();

        return $AsalUsul;
    }

    public function getcekcetak($id, $id2)
    {

        $ceksenin = $this->model
            ->where('kelas_id', $id)
            ->where('departments_id', $id2)
            ->where('hari', 'Senin')
            ->whereNull('deleted_at')
            ->count();
        if ($ceksenin == 3) {
            $cekselasa = $this->model
                ->where('kelas_id', $id)
                ->where('departments_id', $id2)
                ->where('hari', 'Selasa')
                ->whereNull('deleted_at')
                ->count();
            if ($cekselasa == 3) {
                $cekrabu = $this->model
                    ->where('kelas_id', $id)
                    ->where('departments_id', $id2)
                    ->where('hari', 'Rabu')
                    ->whereNull('deleted_at')
                    ->count();
                if ($cekrabu == 3) {
                    $cekkamis = $this->model
                        ->where('kelas_id', $id)
                        ->where('departments_id', $id2)
                        ->where('hari', 'Kamis')
                        ->whereNull('deleted_at')
                        ->count();
                    if ($cekkamis == 3) {
                        $cekjumat = $this->model
                            ->where('kelas_id', $id)
                            ->where('departments_id', $id2)
                            ->where('hari', 'Jumat')
                            ->whereNull('deleted_at')
                            ->count();

                        if ($cekjumat == 3) {
                            $ceksabtu = $this->model
                                ->where('kelas_id', $id)
                                ->where('departments_id', $id2)
                                ->where('hari', 'Sabtu')
                                ->whereNull('deleted_at')
                                ->count();
                            if ($ceksabtu == 3) {
                                return response()->json(
                                    [
                                        'success' => true,
                                        'result' => 'Data Dapat di Cetak',
                                    ]
                                );

                            } else {
                                if ($ceksabtu < 3) {
                                    $cek =  3 - $ceksabtu;
                                    return response()->json(
                                        [
                                            'success' => false,
                                            'result' => 'Cek Kembali Schedules Pada Hari Sabtu kurang dari '.$cek .' pelajaran',
                                        ]
                                    );
                                }
                                else{
                                    $cek =   $ceksabtu  - 3;
                                    return response()->json(
                                        [
                                            'success' => false,
                                            'result' => 'Cek Kembali Schedules Pada Hari Sabtu Lebih dari '.$cek .' pelajaran',
                                        ]
                                    );
                                }

                            }
                        } else {
                            if ($cekjumat < 3) {
                                $cek =  3 - $cekjumat;
                                return response()->json(
                                    [
                                        'success' => false,
                                        'result' => 'Cek Kembali Schedules Pada Hari Jumat kurang dari '.$cek .' pelajaran',
                                    ]
                                );
                            }
                            else{
                                $cek =   $cekjumat  - 3;
                                return response()->json(
                                    [
                                        'success' => false,
                                        'result' => 'Cek Kembali Schedules Pada Hari Jumat Lebih dari '.$cek .' pelajaran',
                                    ]
                                );
                            }
                        }

                    } else {
                        if ($cekkamis < 3) {
                            $cek =  3 - $cekkamis ;
                            return response()->json(
                                [
                                    'success' => false,
                                    'result' => 'Cek Kembali Schedules Pada Hari Kamis kurang dari '.$cek .' pelajaran',
                                ]
                            );
                        }
                        else{
                            $cek =   $cekkamis  - 3;
                            return response()->json(
                                [
                                    'success' => false,
                                    'result' => 'Cek Kembali Schedules Pada Hari Kamis Lebih dari '.$cek .' pelajaran',
                                ]
                            );
                        }
                    }
                } else {
                    if ($cekrabu < 3) {
                        $cek =  3 - $cekrabu ;
                        return response()->json(
                            [
                                'success' => false,
                                'result' => 'Cek Kembali Schedules Pada Hari Rabu kurang dari '.$cek .' pelajaran',
                            ]
                        );
                    }
                    else{
                        $cek =   $cekrabu  - 3;
                        return response()->json(
                            [
                                'success' => false,
                                'result' => 'Cek Kembali Schedules Pada Hari Rabu Lebih dari '.$cek .' pelajaran',
                            ]
                        );
                    }
                }

            } else {
                if ($cekselasa < 3) {
                    $cek =  3 - $cekselasa ;
                    return response()->json(
                        [
                            'success' => false,
                            'result' => 'Cek Kembali Schedules Pada Hari Selasa kurang dari '.$cek .' pelajaran',
                        ]
                    );
                }
                else{
                    $cek =   $cekselasa  - 3;
                    return response()->json(
                        [
                            'success' => false,
                            'result' => 'Cek Kembali Schedules Pada Hari Selasa Lebih dari '.$cek .' pelajaran',
                        ]
                    );
                }
            }
        } else {
            if ($ceksenin < 3) {
                $cek =  3 - $ceksenin ;
                return response()->json(
                    [
                        'success' => false,
                        'result' => 'Cek Kembali Schedules Pada Hari Senin kurang dari '.$cek .' pelajaran',
                    ]
                );
            }
            else{
                $cek =   $ceksenin  - 3;
                return response()->json(
                    [
                        'success' => false,
                        'result' => 'Cek Kembali Schedules Pada Hari Senin Lebih dari '.$cek .' pelajaran',
                    ]
                );
            }
        }


    }
}