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
        'time', 'hour', 'room', 'subjects_id', 'teachers_id', 'departments_id', 'kelas_id', 'hari', 'wali_kelas'
    ];

 protected $with = ['subjects', 'departments', 'teachers', 'kelas','wali_kelass'];

    public function subjects()
    {
        return $this->belongsTo('App\Domain\Entities\Subjects', 'subjects_id');
    }

    public function teachers()
    {
        return $this->belongsTo('App\Domain\Entities\Teachers', 'teachers_id');
    }
    public function wali_kelass()
    {
        return $this->belongsTo('App\Domain\Entities\Teachers', 'wali_kelas');
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
