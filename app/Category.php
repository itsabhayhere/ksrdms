<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Category extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','price',
    ];
    public function categorySubmit($req)
    {
        DB::beginTransaction();
       $currentTime = date('Y-m-d H:i:s');
 
        $submiteInfo = DB::table('categories')->insertGetId([
            'dairyId' => $req->dairyId,
            'name' => $req->categoryName,
            'price' => $req->categoryPrice,
            'created_at' => $currentTime,
        ]);

        if ($submiteInfo == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: Category');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }
    
        DB::commit();

        Session::flash('msg', 'Category added Successfuly');
        Session::flash('alert-class', 'alert-success');
        return true;
    }
    public function categoryEditSubmit($req)
    {
        DB::beginTransaction();

        $currentTime = date('Y-m-d H:i:s');

        $pro = DB::table('categories')->where(['id' => $req->categoryId])->update([
            'name' => $req->categoryName,
            'price' => $req->categoryPrice,
            'updated_at' => $currentTime,
        ]);
        if ($pro == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: CATEGORY_UPDATE');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        DB::commit();

        Session::flash('msg', 'Category rate updated successfuly');
        Session::flash('alert-class', 'alert-success');
        return true;
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
