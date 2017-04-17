<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subjects\SubjectsCreateRequest;
use App\Http\Requests\Subjects\SubjectsEditRequest;
use Illuminate\Http\Request;
use App\Domain\Contracts\SubjectsInterface;

class SubjectsController extends Controller
{

    /**
     * @var SubjectsInterface
     */
    protected $subjects;

    /**
     * SubjectsController constructor.
     * @param SubjectsInterface $subjects
     */
    public function __construct(SubjectsInterface $subjects)
    {
        $this->subjects = $subjects;
    }

    /**
     * @api {get} api/subjectss Request Subjects with Paginate
     * @apiName GetSubjectsWithPaginate
     * @apiGroup Subjects
     *
     * @apiParam {Number} page Paginate subjects lists
     */
    public function index(Request $request)
    {
        return $this->subjects->paginate(10, $request->input('page'), $column = ['*'], '', $request->input('search'));
    }

    /**
     * @api {get} api/subjectss/id Request Get Subjects
     * @apiName GetSubjects
     * @apiGroup Subjects
     *
     * @apiParam {Number} id id_subjects
     * @apiSuccess {Number} id id_subjects
     * @apiSuccess {Varchar} name name of subjects
     * @apiSuccess {Varchar} address name of address
     * @apiSuccess {Varchar} email email of subjects
     * @apiSuccess {Number} phone phone of subjects
     */
    public function show($id)
    {
        return $this->subjects->findById($id);
    }

    /**
     * @api {post} api/subjectss/ Request Post Subjects
     * @apiName PostSubjects
     * @apiGroup Subjects
     *
     *
     * @apiParam {Varchar} name name of subjects
     * @apiParam {Varchar} email email of subjects
     * @apiParam {Varchar} address email of address
     * @apiParam {Float} phone phone of subjects
     * @apiSuccess {Number} id id of subjects
     */
    public function store(SubjectsCreateRequest $request)
    {
        return $this->subjects->create($request->all());
    }

    /**
     * @api {put} api/subjectss/id Request Update Subjects by ID
     * @apiName UpdateSubjectsByID
     * @apiGroup Subjects
     *
     *
     * @apiParam {Varchar} name name of subjects
     * @apiParam {Varchar} email email of subjects
     * @apiParam {Varchar} address address of subjects
     * @apiParam {Float} phone phone of subjects
     *
     *
     * @apiError EmailHasRegitered The Email must diffrerent.
     */
    public function update(SubjectsEditRequest $request, $id)
    {
        return $this->subjects->update($id, $request->all());
    }

    /**
     * @api {delete} api/subjectss/id Request Delete Subjects by ID
     * @apiName DeleteSubjectsByID
     * @apiGroup Subjects
     *
     * @apiParam {Number} id id of subjects
     *
     *
     * @apiError SubjectsNotFound The <code>id</code> of the Subjects was not found.
     * @apiError NoAccessRight Only authenticated Admins can access the data.
     */
    public function destroy($id)
    {
        return $this->subjects->delete($id);
    }

}