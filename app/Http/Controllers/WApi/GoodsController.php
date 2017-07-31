<?php

namespace App\Http\Controllers\WApi;

use App\Goods;

class GoodsController extends WApiController
{
    public function hot()
    {
        $this->model = new Goods();
        $lists = $this->model->getHot();
        $data = ['lists' => $lists];
        $this->response($data);
    }
    
    public function rec()
    {
        $this->model = new Goods();
        $lists = $this->model->getRec();
        $data = ['lists' => $lists];
        $this->response($data);
    }
}
