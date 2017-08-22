<?php

namespace App\Http\Controllers\WApi;

class RecordController extends WApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'record';
    }
    
    public function index()
    {
        $extend = [
            'where' => [
                ['uid', '=', $this->uid]
            ]
        ];
        $lists = $this->getLists($this->table, $extend);
        $data = ['lists'=>$lists];
        $this->response($data);
    }
}
