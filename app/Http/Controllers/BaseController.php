<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    public $model;
    
    public function __construct()
    {
    }
    
    public function getLists($model='', $extend=[])
    {
        
        $default = [
            'orderBy'   =>  'id desc',
            'select'    =>  '*',
        ];
        
        $extend = array_merge($default, $extend);
        
        foreach ($extend as $k=>$v)
        {
            call_user_func(array($model, $k), $v);
        }
        $data = $model->get()->toArray();
        return $data;
        
    }
    
    public function index()
    {
        $this->response();
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
        
    }
    
    public function response($data=[])
    {
        $default = [
            'err_code' => 0
        ];
        $data = array_merge($data, $default);
        echo json_encode($data);die;
    }
    
}
