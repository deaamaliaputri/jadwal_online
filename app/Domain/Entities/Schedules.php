<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Schedules
 * @package App\Domain\Entities
 */
class Schedules extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'time', 'houre', 'room', 'teachers_id', 'department_id', 'kelas_id',
    ];

 protected $with = ['teachers', 'departments', 'kelas'];

    public function teachers()
    {
        return $this->belongsTo('App\Domain\Entities\Teachers', 'teachers_id');
    }


    public function departments()
    {
        return $this->belongsTo('App\Domain\Entities\Departments', 'departments_id');
    }

    public function kelas()
    {
        return $this->belongsTo('App\Domain\Entities\Kelas', 'kelas_id');
    }
}
