<?php

namespace App;

class Address extends Base
{
    //
    public function concat()
    {
        $info = $this->toArray();
        extract($info);
        $info['address'] = $province.$city.$district.$detail;
        unset($info['province']);
        unset($info['city']);
        unset($info['district']);
        unset($info['detail']);
        return $info;
    }
}
