<?php

namespace App\Http\Controllers;

use App\Http\Requests\Departments\DepartmentsCreateRequest;
use App\Http\Requests\Departments\DepartmentsEditRequest;
use Illuminate\Http\Request;
use App\Domain\Contracts\DepartmentsInterface;

class DepartmentsController extends Controller
{

    /**
     * @var DepartmentsInterface
     */
    protected $departments;

    /**
     * DepartmentsController constructor.
     * @param DepartmentsInterface $departments
     */
    public function __construct(DepartmentsInterface $departments)
    {
        $this->departments = $departments;
    }

    /**
     * @api {get} api/departmentss Request Departments with Paginate
     * @apiName GetDepartmentsWithPaginate
     * @apiGroup Departments
     *
     * @apiParam {Number} page Paginate departments lists
     */
    public function index(Request $request)
    {
        return $this->departments->paginate(10, $request->input('page'), $column = ['*'], '', $request->input('search'));
    }

    /**
     * @api {get} api/departmentss/id Request Get Departments
     * @apiName GetDepartments
     * @apiGroup Departments
     *
     * @apiParam {Number} id id_departments
     * @apiSuccess {Number} id id_departments
     * @apiSuccess {Varchar} name name of departments
     * @apiSuccess {Varchar} address name of address
     * @apiSuccess {Varchar} email email of departments
     * @apiSuccess {Number} phone phone of departments
     */
    public function show($id)
    {
        return $this->departments->findById($id);
    }

    /**
     * @api {post} api/departmentss/ Request Post Departments
     * @apiName PostDepartments
     * @apiGroup Departments
     *
     *
     * @apiParam {Varchar} name name of departments
     * @apiParam {Varchar} email email of departments
     * @apiParam {Varchar} address email of address
     * @apiParam {Float} phone phone of departments
     * @apiSuccess {Number} id id of departments
     */
    public function store(DepartmentsCreateRequest $request)
    {
        return $this->departments->create($request->all());
    }

    /**
     * @api {put} api/departmentss/id Request Update Departments by ID
     * @apiName UpdateDepartmentsByID
     * @apiGroup Departments
     *
     *
     * @apiParam {Varchar} name name of departments
     * @apiParam {Varchar} email email of departments
     * @apiParam {Varchar} address address of departments
     * @apiParam {Float} phone phone of departments
     *
     *
     * @apiError EmailHasRegitered The Email must diffrerent.
     */
    public function update(DepartmentsEditRequest $request, $id)
    {
        return $this->departments->update($id, $request->all());
    }

    /**
     * @api {delete} api/departmentss/id Request Delete Departments by ID
     * @apiName DeleteDepartmentsByID
     * @apiGroup Departments
     *
     * @apiParam {Number} id id of departments
     *
     *
     * @apiError DepartmentsNotFound The <code>id</code> of the Departments was not found.
     * @apiError NoAccessRight Only authenticated Admins can access the data.
     */
    public function destroy($id)
    {
        return $this->departments->delete($id);
    }

}