<?php

namespace App\Http\Requests\Schedules;

use App\Http\Requests\Request;

/**
 * Class UserCreateRequest
 *
 * @package App\Http\Requests\User
 */
class SchedulesCreateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Declaration an attributes
     *
     * @var array
     */
    protected $attrs = [
        'time'    => 'Time',
        'hour'   => 'Hour',
        'room' => 'Room',
        'teachers_id'   => 'Teachers_id',
        'departemenst_id'   => 'Departments_id',
        'kelas_id'   => 'Kelas_id'
        
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        'time'    => 'required|max:225',
        'hour'   => 'required|max:225',
        'room' => 'required|max:225',
        'teachers_id'   => 'required|max:225',
        'departemenst_id'   => 'required|max:225',
        'kelas_id'   => 'required|max:225'
        ];
    }

    /**
     * @param $validator
     *
     * @return mixed
     */
    public function validator($validator)
    {
        return $validator->make($this->all(), $this->container->call([$this, 'rules']), $this->messages(), $this->attrs);
    }

}
