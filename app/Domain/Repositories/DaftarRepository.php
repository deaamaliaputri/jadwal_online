<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Daftar;
use App\Domain\Contracts\DaftarInterface;
use App\Domain\Contracts\Crudable;


/**
 * Class DaftarRepository
 * @package App\Domain\Repositories
 */
class DaftarRepository extends AbstractRepository implements DaftarInterface, Crudable
{

    /**
     * @var Daftar
     */
    protected $model;

    /**
     * DaftarRepository constructor.
     * @param Daftar $daftar
     */
    public function __construct(Daftar $daftar)
    {
        $this->model = $daftar;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @param int $limit
     * @param int $page
     * @param array $column
     * @param string $field
     * @param string $search
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate($limit = 10, $page = 1, array $column = ['*'], $field, $search = '')
    {
        // query to aql
        $akun = $this->model
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
                })
        
            ->paginate($limit)
            ->toArray();
        return $akun;
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(array $data)
    {
        // execute sql insert
        return parent::create([
            'name'    => e($data['name']),
            'email'   => e($data['email']),
            'password' => bcrypt(e($data['password'])),
            'phone'   => e($data['phone']),
            'status'   => e($data['status']),
            'level'   => e($data['level']),
            'nip'   => e($data['nip'])
        ]);
    }

    

    /**
     * @param $id
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($id, array $data)
    {
        return parent::update($id, [
            'name'    => e($data['name']),
            'email'   => e($data['email']),
            'phone'   => e($data['phone']),
            'status'   => e($data['status']),
            'level'   => e($data['level']),
            'nip'   => e($data['nip'])
        ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {
        return parent::delete($id);
    }


    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findById($id, array $columns = ['*'])
    {
        return parent::find($id, $columns);
    }

public function updatePassword(array $data)  
   {  
       try {  
           $user = $this->model->find(session('user_id'));  
           if ($user) {  
               $old_password = $user->password;  
 
               if (\Hash::check($data['old_password'], $old_password)) {  
                   // flush cache with tags  
      
                   $user->password = bcrypt($data['new_password']);  
                   $user->save();  
 
                   return $this->updateSuccess($data);  
               }  
 
               return [  
                   'success' => false,  
                   'result' => [  
                       'message' => 'Password lama tidak cocok.',  
                   ],  
               ];  
           }  
 
           return [  
               'success' => false,  
               'result' => [  
                   'message' => 'Data tidak ditemukan',  
               ],  
           ];  
       } catch (\Exception $e) {  
           // store errors to log  
           \Log::error('class : ' . UserRepository::class . ' method : put | ' . $e);  
 
           return $this->createError();  
       }  
   }  
}