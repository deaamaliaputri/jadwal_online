<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Students
 * @package App\Domain\Entities
 */
class Students extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'nis', 'kelas_id', 'departments_id',
    ];

protected $with = ['kelas', 'departments'];

    public function kelas()
    {
        return $this->belongsTo('App\Domain\Entities\Kelas', 'kelas_id');
    }

    public function departments()
    {
        return $this->belongsTo('App\Domain\Entities\Departments', 'departments_id');
    }
}
