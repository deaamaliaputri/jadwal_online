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
        return parent::paginate($limit, $page, $column, 'room', $search);
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(array $data)
    {
        // execute sql insert
        return parent::create([
             'time'    => '2016-08-09',
            'hour'   => '2016-08-09',
            'room' => e($data['room']),
            'teachers_id'   => 0,
            'departments_id'   => e($data['departments_id']),
            'kelas_id'   => e($data['kelas_id']),
            'subjects_id'   => e($data['subjects_id']),
            'teachers_id'   => 0,
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
            'time'    => e($data['time']),
            'hour'   => e($data['hour']),
            'room' => e($data['room']),
            'teachers_id'   => e($data['teachers_id']),
            'departments_id'   => e($data['departments_id']),
            'kelas_id'   => e($data['kelas_id']),
        'subjects_id'   => e($data['subjects_id'])
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