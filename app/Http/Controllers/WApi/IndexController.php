<?php
namespace App\Http\Controllers\WApi;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $data = [
            "results" => [
                'item1', 'item2'
            ]
        ];
        echo json_encode($data);die;
    }
}