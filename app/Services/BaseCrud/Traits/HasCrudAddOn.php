<?php

namespace App\Services\BaseCrud\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait HasCrudAddOn
{
    public $listWebPagination = true;
    public $viewPath = '';

    public $successStoreMsg = "success_create";
    public $successUpdateMsg = "success_updated";
    public $successDestroyMsg = "success_delete";

    public $errorStoreMsg = "error_store";
    public $errorUpdateMsg = "error_update";
    public $errorDestroyMsg = "error_deleted";

    public $redirectSuccessStore = '';
    public $redirectSuccessUpdate = '';

    public $viewList = 'list';
    public $viewCreate = 'create';
    public $viewShow = 'show';
    public $viewEdit = 'edit';
    public $viewPrint = 'print';

    public function __viewList($data)
    {
        return view($this->viewPath . '/' . $this->viewList, $data);
    }

    public function __viewCreate($data)
    {
        return view($this->viewPath . '/' . $this->viewCreate, $data);
    }

    public function __viewShow($data)
    {
        return view($this->viewPath . '/' . $this->viewShow, $data);
    }

    public function __viewEdit($data)
    {
        return view($this->viewPath . '/' . $this->viewEdit, $data);
    }

    public function __viewPrint($data)
    {
        return view($this->viewPath . '/' . $this->viewPrint, $data);
    }

    public function __successList($query)
    {
        $data['list'] = $query;

        $data = $this->__extraDataList($data);

        return $this->__viewList($data);
    }

    public function __successShow()
    {
        $data['row'] = $this->row;

        $data = $this->__extraDataShow($data);

        return $this->__viewShow($data);
    }

    public function __successPrint($query)
    {
        $data['list'] = $query;

        $data = $this->__extraDataList($data);

        return $this->__viewPrint($data);
    }

    public function __redirectSuccess()
    {
        if ($this->redirectSuccessStore) {
            return redirect()->route($this->redirectSuccessStore);
        }

        if ($this->redirectSuccessUpdate) {
            return redirect()->route($this->redirectSuccessUpdate);
        }

        return redirect()->back();
    }

    public function __successStore()
    {
        return $this->__redirectSuccess()->with('success_message', $this->successStoreMsg);
    }

    public function __successUpdate()
    {
        $redirect = $this->__redirectSuccess()->with('success_message', $this->successUpdateMsg);
        return $redirect;
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

    public function __prepareQueryListType()
    {
        $query = $this->query;
        if ($query instanceof \Illuminate\Support\Collection) {
            return $query;
        }

        if ($this->listWebPagination) {
            if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
                $query = $query->paginate($this->paginationPerPage);
                $this->__prepareListPaginationAppend($query);
                return $query;
            }
        } else {
            return $query->get();
        }
    }
}
