<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class BaseController extends Controller
{
    public $model;
    public $table;
    public $cid;
    public $uid;
    
    public function __construct()
    {
        $this->cid = request('cid');
        $this->uid = request('uid');
        $this->cid and define('CID', $this->cid);
        $this->uid and define('UID', $this->uid);
    }
    
    public function getLists($table, $extend=[])
    {
        $model = DB::table($table);
        
        $default = [
            'join'      => '',
            'select'    =>  [ '*' ],
            'orderBy'   =>  ['id', 'desc'],
            'paginate'  => 10,
        ];
        $extend = array_merge($default, $extend);
        
        $multArray = [ 'orderBy', 'select', 'join' ];
        foreach ($extend as $k=>$v)
        {
            if (!$v) continue;
            if (in_array($k, $multArray)) {
                call_user_func_array(array($model, $k), $v);
            } else {
                call_user_func(array($model, $k), $v);
            }
        }
        $data = $model->get()->toJson();
        return json_decode($data, true);
    }
    
    public function show($id)
    {
        $info = DB::table($this->table)->where('id', $id)->first();
        $data = [ 'info' => $info ];
        $this->response($data);
    }
    
    public function index()
    {
        $lists = DB::table($this->table)->get()->toArray();
        $data = [ 'err_code'=>0, 'lists'=>$lists ];
        $this->response($data);
    }
    
    public function create()
    {
        
    }
    
    public function store()
    {
        
    }
    
    public function edit($id)
    {
        
    }
    
    public function update($id)
    {
        
    }
    
    public function destroy($id)
    {
        $res = DB::table($this->table)->where('id', $id)->delete();
        $data = [ 'err_code'=>$res > 0 ? 0 : -1 ];
        $this->response($data);
    }
    
    public function response($data=[])
    {
        $default = [
            'err_code' => 0
        ];
        $data = array_merge($default, $data);
        echo json_encode($data);die;
    }
    
}
