<?php

namespace App\Services\BaseCrud\Traits;

trait HasCrudExtraData
{
    public function __extraData($data)
    {
        return $data;
    }

    public function __extraDataList($data)
    {
        return $this->__extraData($data);
    }

    public function __extraDataCreate($data)
    {
        return $this->__extraData($data);
    }

    public function __extraDataShow($data)
    {
        return $this->__extraData($data);
    }

    public function __extraDataEdit($data)
    {
        return $this->__extraData($data);
    }
}
