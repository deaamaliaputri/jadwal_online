<?php

namespace App\Http\Controllers;

use App\Http\Requests\Daftar\DaftarCreateRequest;
use App\Http\Requests\Daftar\DaftarEditRequest;
use Illuminate\Http\Request;
use App\Domain\Repositories\DaftarRepository;

class DaftarController extends Controller
{

    /**
     * @var DaftarInterface
     */
    protected $daftar;

    /**
     * DaftarController constructor.
     * @param DaftarInterface $daftar
     */
    public function __construct(DaftarRepository $daftar)
    {
        $this->daftar = $daftar;
    }

    /**
     * @api {get} api/daftars Request Daftar with Paginate
     * @apiName GetDaftarWithPaginate
     * @apiGroup Daftar
     *
     * @apiParam {Number} page Paginate daftar lists
     */
    public function index(Request $request)
    {
        return $this->daftar->paginate(10, $request->input('page'), $column = ['*'], '', $request->input('term'));
    }

    /**
     * @api {get} api/daftars/id Request Get Daftar
     * @apiName GetDaftar
     * @apiGroup Daftar
     *
     * @apiParam {Number} id id_daftar
     * @apiSuccess {Number} id id_daftar
     * @apiSuccess {Varchar} name name of daftar
     * @apiSuccess {Varchar} address name of address
     * @apiSuccess {Varchar} email email of daftar
     * @apiSuccess {Number} phone phone of daftar
     */
    public function show($id)
    {
        return $this->daftar->findById($id);
    }

    /**
     * @api {post} api/daftars/ Request Post Daftar
     * @apiName PostDaftar
     * @apiGroup Daftar
     *
     *
     * @apiParam {Varchar} name name of daftar
     * @apiParam {Varchar} email email of daftar
     * @apiParam {Varchar} address email of address
     * @apiParam {Float} phone phone of daftar
     * @apiSuccess {Number} id id of daftar
     */
    public function store(DaftarCreateRequest $request)
    {
        return $this->daftar->create($request->all());
    }

    /**
     * @api {put} api/daftars/id Request Update Daftar by ID
     * @apiName UpdateDaftarByID
     * @apiGroup Daftar
     *
     *
     * @apiParam {Varchar} name name of daftar
     * @apiParam {Varchar} email email of daftar
     * @apiParam {Varchar} address address of daftar
     * @apiParam {Float} phone phone of daftar
     *
     *
     * @apiError EmailHasRegitered The Email must diffrerent.
     */
    public function update(DaftarEditRequest $request, $id)
    {
        return $this->daftar->update($id, $request->all());
    }

    /**
     * @api {delete} api/daftars/id Request Delete Daftar by ID
     * @apiName DeleteDaftarByID
     * @apiGroup Daftar
     *
     * @apiParam {Number} id id of daftar
     *
     *
     * @apiError DaftarNotFound The <code>id</code> of the Daftar was not found.
     * @apiError NoAccessRight Only authenticated Admins can access the data.
     */
    public function destroy($id)
    {
        return $this->daftar->delete($id);
    }
public function getSession()
    {
        if (session('name') == null) {
            return response()->json(
                [
                    'success' => false,
                    'result' => 'redirect'
                ], 401
            );
        }

        return response()->json([
            'success' => true,
            'result' => [
                'name' => session('name'),
                'email' => session('email'),
                'user_id' => session('user_id'),
                'level' => session('level'),

            ]]);
    }
}
