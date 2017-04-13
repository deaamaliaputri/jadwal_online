<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact
 * @package App\Domain\Entities
 */
class Contact extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'time', 'houre', 'room', 'teachers_id', 'department_id', 'class_id',
    ];

}
