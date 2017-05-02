<?php

namespace App\Http\Controllers;

use App\Http\Requests\Students\StudentsCreateRequest;
use App\Http\Requests\Students\StudentsEditRequest;
use Illuminate\Http\Request;
use App\Domain\Repositories\StudentsRepository;

class StudentsController extends Controller
{

    /**
     * @var StudentsInterface
     */
    protected $students;

    /**
     * StudentsController constructor.
     * @param StudentsInterface $students
     */
    public function __construct(StudentsRepository $students)
    {
        $this->students = $students;
    }

    /**
     * @api {get} api/studentss Request Students with Paginate
     * @apiName GetStudentsWithPaginate
     * @apiGroup Students
     *
     * @apiParam {Number} page Paginate students lists
     */
    public function index(Request $request)
    {
        return $this->students->paginate(10, $request->input('page'), $column = ['*'], '', $request->input('term'));
    }

    /**
     * @api {get} api/studentss/id Request Get Students
     * @apiName GetStudents
     * @apiGroup Students
     *
     * @apiParam {Number} id id_students
     * @apiSuccess {Number} id id_students
     * @apiSuccess {Varchar} name name of students
     * @apiSuccess {Varchar} address name of address
     * @apiSuccess {Varchar} email email of students
     * @apiSuccess {Number} phone phone of students
     */
    public function show($id)
    {
        return $this->students->findById($id);
    }

    /**
     * @api {post} api/studentss/ Request Post Students
     * @apiName PostStudents
     * @apiGroup Students
     *
     *
     * @apiParam {Varchar} name name of students
     * @apiParam {Varchar} email email of students
     * @apiParam {Varchar} address email of address
     * @apiParam {Float} phone phone of students
     * @apiSuccess {Number} id id of students
     */
    public function store(StudentsCreateRequest $request)
    {
        return $this->students->create($request->all());
    }

    /**
     * @api {put} api/studentss/id Request Update Students by ID
     * @apiName UpdateStudentsByID
     * @apiGroup Students
     *
     *
     * @apiParam {Varchar} name name of students
     * @apiParam {Varchar} email email of students
     * @apiParam {Varchar} address address of students
     * @apiParam {Float} phone phone of students
     *
     *
     * @apiError EmailHasRegitered The Email must diffrerent.
     */
    public function update(StudentsEditRequest $request, $id)
    {
        return $this->students->update($id, $request->all());
    }

    /**
     * @api {delete} api/studentss/id Request Delete Students by ID
     * @apiName DeleteStudentsByID
     * @apiGroup Students
     *
     * @apiParam {Number} id id of students
     *
     *
     * @apiError StudentsNotFound The <code>id</code> of the Students was not found.
     * @apiError NoAccessRight Only authenticated Admins can access the data.
     */
    public function destroy($id)
    {
        return $this->students->delete($id);
    }

}