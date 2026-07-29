<?php

namespace App\Services\BaseCrud;

use App\Services\BaseCrud\Traits\HasCrudAddOn;
use App\Services\BaseCrud\Traits\HasCrudExtraData;

class BaseWebCrud extends BaseCrud
{
    use HasCrudAddOn, HasCrudExtraData;

    public function create()
    {
        if (! empty($this->abilityPolicyStore)) {
            $this->authorize($this->abilityPolicyStore, $this->model);
        }

        if ($ress = $this->__beforeCreate()) {
            return $ress;
        }

        $data['row'] = $this->row;

        $data = $this->__extraDataCreate($data);

        return $this->__viewCreate($data);
    }

    public function edit($id)
    {
        $this->query = $this->model::where($this->modelKey, $id);

        $this->__prepareQueryRelationShow($this->query);

        $this->__prepareQueryRowShow($this->query);

        $this->row = $this->query->firstOrFail();

        $data['row'] = $this->row;

        if (! empty($this->abilityPolicyUpdate)) {
            $this->authorize($this->abilityPolicyUpdate, $this->row);
        }

        if ($ress = $this->__beforeEdit()) {
            return $ress;
        }

        $data = $this->__extraDataShow($data);

        return $this->__viewEdit($data);
    }
}
