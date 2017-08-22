<?php
namespace App\Http\Controllers\WApi;

use Illuminate\Support\Facades\DB;
use App\Car;
use phpDocumentor\Reflection\Types\This;

class CarController extends WApiController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->model = new Car();
        $this->table = 'car';
    }
    
    public function index()
    {
        $uid = request('uid');
        $extend = [
            'join'      => [ 'goods as g', 'c.gid', '=', 'g.id' ],
            'orderBy'   => [ 'c.create_time', 'desc' ],
            'where'     => [ ['c.uid', '=', $uid] ],
            'select'    => [ 'c.id', 'c.standard', 'c.count', 'c.grid', 'g.name', 'g.corver', 'g.id as gid', 'g.price' ],
        ];
        
        $data = $this->getLists('car as c', $extend);
        $this->response(['lists'=>pre($data, 'corver')]);
    }
    
    public function store()
    {
        $data = [
            'cid'   => CID,
            'uid'   => request('uid'),
            'gid'   => request('gid'),
            'sids'  => request('sids'),
            'detail'    => request('detail'),
            'standard'  => request('standard'),
            'count'     => request('count'),
            'create_time'   => time()
        ];
        
        $res = $this->model->toCar($data);
        $this->response($data);
    }
    
    public function update($id)
    {
        $count = request('count');
        $res = DB::table('car')->where('id', $id)->increment('count', $count);
        $data = [ 'err_code'=>$res > 0 ? 0 : -1 ];
        $this->response($data);
    }
    
}
