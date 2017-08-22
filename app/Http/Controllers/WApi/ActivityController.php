<?php

namespace App\Http\Controllers\WApi;

use App\Activity;

class ActivityController extends WApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Activity();
        $this->table = 'activity';
    }
    
    public function index()
    {
        $share = request('share', 0);
        $extend = [
            'where' => [
                ['cid', '=', $this->cid],
                ['share', '=', $share]
            ]
        ];
        $lists = $this->getLists($this->table, $extend);
        
        $data = ['lists'=>pre($lists, 'logo')];
        $this->response($data);
    }
    
    public function show($id)
    {
        $info = $this->model->find($id)->toArray();
        $data = ['info'=>pre($info, 'logo')];
        $this->response($data);
    }
    
    
    
}

