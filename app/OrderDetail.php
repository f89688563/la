<?php
namespace App;

class OrderDetail extends Base
{
//     protected $table = 'order_detail';
    protected $fillable = [
        'oid', 'gid', 'grid', 'sids', 'standard', 'name', 'price', 'jf', 'rebate', 'corver', 'count', 'create_time'
    ];
//     protected $guarded = [];
}
