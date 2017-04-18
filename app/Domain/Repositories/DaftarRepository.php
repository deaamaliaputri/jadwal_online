<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Daftar;
use App\Domain\Contracts\DaftarInterface;
use App\Domain\Contracts\Crudable;


/**
 * Class DaftarRepository
 * @package App\Domain\Repositories
 */
class DaftarRepository extends AbstractRepository implements DaftarInterface, Crudable
{

    /**
     * @var Daftar
     */
    protected $model;

    /**
     * DaftarRepository constructor.
     * @param Daftar $daftar
     */
    public function __construct(Daftar $daftar)
    {
        $this->model = $daftar;
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
        return parent::paginate($limit, $page, $column, 'name', $search);
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(array $data)
    {
        // execute sql insert
        return parent::create([
            'name'    => e($data['time']),
            'email'   => e($data['hour']),
            'password' => e($data['room']),
            'phone'   => e($data['teachers_id']),
            'status'   => e($data['department_id']),
            'level'   => e($data['kelas_id']),
            'nip'   => e($data['kelas_id'])
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
            'name'    => e($data['time']),
            'email'   => e($data['hour']),
            'password' => e($data['room']),
            'phone'   => e($data['teachers_id']),
            'status'   => e($data['department_id']),
            'level'   => e($data['kelas_id']),
            'nip'   => e($data['kelas_id'])
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