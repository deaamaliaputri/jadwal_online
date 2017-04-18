<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kelas\KelasCreateRequest;
use App\Http\Requests\Kelas\KelasEditRequest;
use Illuminate\Http\Request;
use App\Domain\Repositories\KelasRepository;

class KelasController extends Controller
{

    /**
     * @var KelasInterface
     */
    protected $kelas;

    /**
     * KelasController constructor.
     * @param KelasInterface $kelas
     */
    public function __construct(KelasRepository $kelas)
    {
        $this->kelas = $kelas;
    }

    /**
     * @api {get} api/kelass Request Kelas with Paginate
     * @apiName GetKelasWithPaginate
     * @apiGroup Kelas
     *
     * @apiParam {Number} page Paginate kelas lists
     */
    public function index(Request $request)
    {
        return $this->kelas->paginate(10, $request->input('page'), $column = ['*'], '', $request->input('search'));
    }

    /**
     * @api {get} api/kelass/id Request Get Kelas
     * @apiName GetKelas
     * @apiGroup Kelas
     *
     * @apiParam {Number} id id_kelas
     * @apiSuccess {Number} id id_kelas
     * @apiSuccess {Varchar} name name of kelas
     * @apiSuccess {Varchar} address name of address
     * @apiSuccess {Varchar} email email of kelas
     * @apiSuccess {Number} phone phone of kelas
     */
    public function show($id)
    {
        return $this->kelas->findById($id);
    }

    /**
     * @api {post} api/kelass/ Request Post Kelas
     * @apiName PostKelas
     * @apiGroup Kelas
     *
     *
     * @apiParam {Varchar} name name of kelas
     * @apiParam {Varchar} email email of kelas
     * @apiParam {Varchar} address email of address
     * @apiParam {Float} phone phone of kelas
     * @apiSuccess {Number} id id of kelas
     */
    public function store(KelasCreateRequest $request)
    {
        return $this->kelas->create($request->all());
    }

    /**
     * @api {put} api/kelass/id Request Update Kelas by ID
     * @apiName UpdateKelasByID
     * @apiGroup Kelas
     *
     *
     * @apiParam {Varchar} name name of kelas
     * @apiParam {Varchar} email email of kelas
     * @apiParam {Varchar} address address of kelas
     * @apiParam {Float} phone phone of kelas
     *
     *
     * @apiError EmailHasRegitered The Email must diffrerent.
     */
    public function update(KelasEditRequest $request, $id)
    {
        return $this->kelas->update($id, $request->all());
    }

    /**
     * @api {delete} api/kelass/id Request Delete Kelas by ID
     * @apiName DeleteKelasByID
     * @apiGroup Kelas
     *
     * @apiParam {Number} id id of kelas
     *
     *
     * @apiError KelasNotFound The <code>id</code> of the Kelas was not found.
     * @apiError NoAccessRight Only authenticated Admins can access the data.
     */
    public function destroy($id)
    {
        return $this->kelas->delete($id);
    }

}