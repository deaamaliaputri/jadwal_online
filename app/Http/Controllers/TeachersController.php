<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teachers\TeachersCreateRequest;
use App\Http\Requests\Teachers\TeachersEditRequest;
use Illuminate\Http\Request;
use App\Domain\Repositories\TeachersRepository;

class TeachersController extends Controller
{

    /**
     * @var TeachersInterface
     */
    protected $teachers;

    /**
     * TeachersController constructor.
     * @param TeachersInterface $teachers
     */
    public function __construct(TeachersRepository $teachers)
    {
        $this->teachers = $teachers;
    }

    /**
     * @api {get} api/teacherss Request Teachers with Paginate
     * @apiName GetTeachersWithPaginate
     * @apiGroup Teachers
     *
     * @apiParam {Number} page Paginate teachers lists
     */
    public function index(Request $request)
    {
        return $this->teachers->paginate(10, $request->input('page'), $column = ['*'], '', $request->input('search'));
    }

    /**
     * @api {get} api/teacherss/id Request Get Teachers
     * @apiName GetTeachers
     * @apiGroup Teachers
     *
     * @apiParam {Number} id id_teachers
     * @apiSuccess {Number} id id_teachers
     * @apiSuccess {Varchar} name name of teachers
     * @apiSuccess {Varchar} address name of address
     * @apiSuccess {Varchar} email email of teachers
     * @apiSuccess {Number} phone phone of teachers
     */
    public function show($id)
    {
        return $this->teachers->findById($id);
    }

    /**
     * @api {post} api/teacherss/ Request Post Teachers
     * @apiName PostTeachers
     * @apiGroup Teachers
     *
     *
     * @apiParam {Varchar} name name of teachers
     * @apiParam {Varchar} email email of teachers
     * @apiParam {Varchar} address email of address
     * @apiParam {Float} phone phone of teachers
     * @apiSuccess {Number} id id of teachers
     */
    public function store(TeachersCreateRequest $request)
    {
        return $this->teachers->create($request->all());
    }

    /**
     * @api {put} api/teacherss/id Request Update Teachers by ID
     * @apiName UpdateTeachersByID
     * @apiGroup Teachers
     *
     *
     * @apiParam {Varchar} name name of teachers
     * @apiParam {Varchar} email email of teachers
     * @apiParam {Varchar} address address of teachers
     * @apiParam {Float} phone phone of teachers
     *
     *
     * @apiError EmailHasRegitered The Email must diffrerent.
     */
    public function update(TeachersEditRequest $request, $id)
    {
        return $this->teachers->update($id, $request->all());
    }

    /**
     * @api {delete} api/teacherss/id Request Delete Teachers by ID
     * @apiName DeleteTeachersByID
     * @apiGroup Teachers
     *
     * @apiParam {Number} id id of teachers
     *
     *
     * @apiError TeachersNotFound The <code>id</code> of the Teachers was not found.
     * @apiError NoAccessRight Only authenticated Admins can access the data.
     */
    public function destroy($id)
    {
        return $this->teachers->delete($id);
    }

}