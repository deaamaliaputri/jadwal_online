<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Students;
use App\Domain\Contracts\StudentsInterface;
use App\Domain\Contracts\Crudable;


/**
 * Class StudentsRepository
 * @package App\Domain\Repositories
 */
class StudentsRepository extends AbstractRepository implements StudentsInterface, Crudable
{

    /**
     * @var Students
     */
    protected $model;

    /**
     * StudentsRepository constructor.
     * @param Students $students
     */
    public function __construct(Students $students)
    {
        $this->model = $students;
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
       $akun = $this->model
            ->join('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->join('departments', 'students.departments_id', '=', 'departments.id')            
            ->where(function ($query) use ($search) {
                $query->where('students.name', 'like', '%' . $search . '%')
                    ->orWhere('students.nis', 'like', '%' . $search . '%')
                    ->orWhere('kelas.name', 'like', '%' . $search . '%')
                    ->orWhere('departments.name', 'like', '%' . $search . '%');
                    
                })
            ->select('students.*')
            ->paginate($limit)
            
            ->toArray();
        return $akun;
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(array $data)
    {
        // execute sql insert
        return parent::create([
            'name'    => e($data['name']),
            'nis'   => e($data['nis']),
            'kelas_id' => e($data['kelas_id']),
            'departments_id'   => e($data['departments_id'])
        ]);

    }

    /**
     * @param $id
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($id, array $data)
    {
        return parent::update($id, [
           'name'    => e($data['name']),
            'nis'   => e($data['nis']),
            'kelas_id' => e($data['kelas_id']),
            'departments_id'   => e($data['departments_id'])
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

}