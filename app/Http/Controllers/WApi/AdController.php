<?php

namespace App\Http\Controllers\WApi;

use App\Ad;

class AdController extends WApiController
{
    public function __construct()
    {
        $this->model = new Ad();
        parent::__construct();
    }
    
    public function index()
    {
        $fields = 'id';
        $extend = [
            'select'    =>  $fields
        ];
        $data = $this->getLists($this->model, $extend);
        $data = pre($data, 'corver', img_pre());
        $this->response(['lists'=>$data]);
    }
}
