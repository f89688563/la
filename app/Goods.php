<?php

namespace App;
use Illuminate\Support\Facades\DB;

class Goods extends Base
{
//     protected $table = 'goods';
	
	// 定义关联
	public function _standard()
	{
		return $this->hasMany('App\GoodsStandard', 'gid', 'id');
	}

	public function info($id){
		$info = $this->with('_standard')->where('id', $id)->first()->toArray();
		if ($info){
			if ($standard = $info['_standard']){
				$temp = array();
				foreach ($standard as $v){
					$temp[$v['pid']][] = $v;
				}
				$info['_standard'] = $temp;
			}
			$info = pre($info, 'corver,logo,img');
		}
		return $info;
	}
	
	// 点赞
	public function praise($gid, $uid)
	{
        $praiseModel = DB::table('praise');
        $wPraise = [
            ['gid', '=', $gid],
            ['uid', '=', $uid]
        ];
        if ($praiseModel->where($wPraise)->first())
        {
            $res = $this->where('id', $gid)->decrement('praise');
            $praiseModel->where($wPraise)->delete();
        }
        else
        {
            $res = $this->where('id', $gid)->increment('praise');
            $data = array(
                'cid'=>CID,
                'gid'=>$gid,
                'uid'=>$uid,
                'create_time'=>time(),
            );
            $praiseModel->insert($data);
        }
        $return = [
            'err_code' => $res > 0 ? 0 : -1,
            'err_msg'=> $res > 0 ? '' : '操作异常'
        ];
        return $return;
	}

    public function getRec()
    {
        $where = [
            [ 'cid', '=', CID ],
            [ 'type', '=', 1 ],
            [ 'status', '=', 1 ],
        ];
        $lists = $this->where($where)->orderBy('create_time', 'desc')->limit(8)->get()->toArray();
        $lists = pre($lists);
        return $lists;
    }
    
    public function getHot()
    {
        $where = [
            [ 'cid', '=', CID ],
            [ 'status', '=', 1 ],
        ];
        $lists = $this->where($where)->orderBy('praise', 'desc')->limit(8)->get()->toArray();
        $lists = pre($lists);
        return $lists;
    }
}
