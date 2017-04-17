<?php

namespace App\Http\Controllers;

use App\Http\Requests\Schedules\SchedulesCreateRequest;
use App\Http\Requests\Schedules\SchedulesEditRequest;
use Illuminate\Http\Request;
use App\Domain\Contracts\SchedulesInterface;

class SchedulesController extends Controller
{

    /**
     * @var SchedulesInterface
     */
    protected $schedules;

    /**
     * SchedulesController constructor.
     * @param SchedulesInterface $schedules
     */
    public function __construct(SchedulesInterface $schedules)
    {
        $this->schedules = $schedules;
    }

    /**
     * @api {get} api/scheduless Request Schedules with Paginate
     * @apiName GetSchedulesWithPaginate
     * @apiGroup Schedules
     *
     * @apiParam {Number} page Paginate schedules lists
     */
    public function index(Request $request)
    {
        return $this->schedules->paginate(10, $request->input('page'), $column = ['*'], '', $request->input('search'));
    }

    /**
     * @api {get} api/scheduless/id Request Get Schedules
     * @apiName GetSchedules
     * @apiGroup Schedules
     *
     * @apiParam {Number} id id_schedules
     * @apiSuccess {Number} id id_schedules
     * @apiSuccess {Varchar} name name of schedules
     * @apiSuccess {Varchar} address name of address
     * @apiSuccess {Varchar} email email of schedules
     * @apiSuccess {Number} phone phone of schedules
     */
    public function show($id)
    {
        return $this->schedules->findById($id);
    }

    /**
     * @api {post} api/scheduless/ Request Post Schedules
     * @apiName PostSchedules
     * @apiGroup Schedules
     *
     *
     * @apiParam {Varchar} name name of schedules
     * @apiParam {Varchar} email email of schedules
     * @apiParam {Varchar} address email of address
     * @apiParam {Float} phone phone of schedules
     * @apiSuccess {Number} id id of schedules
     */
    public function store(SchedulesCreateRequest $request)
    {
        return $this->schedules->create($request->all());
    }

    /**
     * @api {put} api/scheduless/id Request Update Schedules by ID
     * @apiName UpdateSchedulesByID
     * @apiGroup Schedules
     *
     *
     * @apiParam {Varchar} name name of schedules
     * @apiParam {Varchar} email email of schedules
     * @apiParam {Varchar} address address of schedules
     * @apiParam {Float} phone phone of schedules
     *
     *
     * @apiError EmailHasRegitered The Email must diffrerent.
     */
    public function update(SchedulesEditRequest $request, $id)
    {
        return $this->schedules->update($id, $request->all());
    }

    /**
     * @api {delete} api/scheduless/id Request Delete Schedules by ID
     * @apiName DeleteSchedulesByID
     * @apiGroup Schedules
     *
     * @apiParam {Number} id id of schedules
     *
     *
     * @apiError SchedulesNotFound The <code>id</code> of the Schedules was not found.
     * @apiError NoAccessRight Only authenticated Admins can access the data.
     */
    public function destroy($id)
    {
        return $this->schedules->delete($id);
    }

}