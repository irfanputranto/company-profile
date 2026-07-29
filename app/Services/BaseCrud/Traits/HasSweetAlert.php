<?php

namespace App\Services\BaseCrud\Traits;

trait HasSweetAlert
{
    public function __successStore()
    {
        return $this->__redirectSuccess()->with('success_message', $this->successStoreMsg);
    }

    public function __successUpdate()
    {
        return $this->__redirectSuccess()->with('success_message', $this->successUpdateMsg);
    }

    public function __successDestroy()
    {
        return $this->__redirectSuccess()->with('success_message', $this->successDestroyMsg);
    }

    public function __errorStore()
    {
        return back()->withInput()->with('error_message', $this->errorStoreMsg);
    }

    public function __errorUpdate()
    {
        return back()->withInput()->with('error_message', $this->errorUpdateMsg);
    }

    public function __errorDestroy()
    {
        return back()->withInput()->with('error_message', $this->errorDestroyMsg);
    }
}
