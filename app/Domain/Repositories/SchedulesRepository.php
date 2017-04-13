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
            'name'    => e($data['name']),
            'email'   => e($data['email']),
            'address' => e($data['address']),
            'phone'   => e($data['phone'])
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
            'email'   => e($data['email']),
            'address' => e($data['address']),
            'phone'   => e($data['phone'])
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