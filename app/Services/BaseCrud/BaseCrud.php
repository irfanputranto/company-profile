<?php

namespace App\Services\BaseCrud;

use App\Http\Controllers\Controller;
use App\Services\BaseCrud\Traits\HasCrudHooks;
use App\Services\BaseCrud\Traits\HasCrudPrepareQuery;
use App\Services\BaseCrud\Traits\HasCrudSuccessResult;
use App\Services\BaseCrud\Traits\HasDBSafe;
use App\Services\BaseCrud\Traits\HasExportData;
use App\Services\BaseCrud\Traits\HasImportData;
use Illuminate\Http\Request;

class BaseCrud extends Controller
{
    use HasCrudHooks, HasCrudPrepareQuery, HasCrudSuccessResult, HasDBSafe, HasExportData, HasImportData;

    public $model;

    public $resource;

    public $row;

    public $searchAble = [];

    public $modelKey = 'id';

    public $storeValidator;

    public $updateValidator;

    public $relationList = [];

    public $relationShow = [];

    public $relationPrint = [];

    public $lockRelationParam = false;

    public $paginationPerPage = 10;

    public $defaultOrder = 'id';

    public $defaultSort = 'asc';

    public $requestData;

    public $query;

    public $cacheInMinute = 10;

    public $enableBulkDelete = true;

    public $enableForceDelete = false;

    public $abilityPolicyIndex = 'viewAny';

    public $abilityPolicyShow = 'view';

    public $abilityPolicyStore = 'create';

    public $abilityPolicyUpdate = 'update';

    public $abilityPolicyDelete = 'delete';

    public $abilityPolicyBulkDelete = 'bulkDelete';

    public $abilityPolicyForceDelete = 'forceDelete';

    public $abilityPolicyBulkForceDelete = 'bulkForceDelete';

    public $abilityPolicyPrint = 'print';

    // untuk settingan export
    public $exportPdfView;

    public $exportName;

    public $exportPdfPaper = 'a4';

    public $exportPdfOrientation = 'portrait';

    public $exportExcelClass;

    public $exportDocxHeaders = [];
    // untuk settingan export

    public $pagination = [
        'current_page' => 1,
        'from' => 0,
        'last_page' => 1,
        'per_page' => 10,
        'to' => 0,
        'total' => 0,
    ];

    public function index(Request $request)
    {
        if (! empty($this->abilityPolicyIndex)) {
            $this->authorize($this->abilityPolicyIndex, $this->model);
        }

        $this->requestData = $request;

        if ($ress = $this->__prepareCacheResult()) {
            return $ress;
        }

        $this->query = $this->model::query();

        $this->__prepareQueryRelationList();

        $this->__prepareQueryList();

        $this->__prepareQuerySearchAbleList();

        $this->__prepareQueryOptionsList();

        if ($ress = $this->__beforeList()) {
            return $ress;
        }

        $this->__prepareQuerySortOrderList();

        $this->__prepareQueryLimitList();

        $query = $this->__prepareQueryListType();

        $this->__prepareLoadRelation($query);

        return $this->__successList($query);
    }

    public function store(Request $request)
    {
        return $this->DBSafe(
            function () {
                if (! empty($this->abilityPolicyStore)) {
                    $this->authorize($this->abilityPolicyStore, $this->model);
                }

                $req = app($this->storeValidator);

                $this->requestData = $req;

                $dt = new $this->model;

                $data = $req->validated();

                $data = $this->__prepareDataStore($data);

                if ($ress = $this->__beforeStore()) {
                    return $ress;
                }

                $dt->fill($data);

                $dt->save();

                $this->row = $dt;

                if ($ress = $this->__afterStore()) {
                    return $ress;
                }

                $this->__prepareLoadRelation($this->row);

                return $this->__successStore();
            }
        );
    }

    public function show(Request $request, $id)
    {
        $this->requestData = $request;

        if ($ress = $this->__prepareCacheResult()) {
            return $ress;
        }

        $this->query = $this->model::where($this->modelKey, $id);

        $this->__prepareQueryRelationShow();

        $this->__prepareQueryRowShow();

        $this->row = $this->query->firstOrFail();

        if (! empty($this->abilityPolicyShow)) {
            $this->authorize($this->abilityPolicyShow, $this->row);
        }

        $this->__prepareLoadRelation($this->row);

        if ($ress = $this->__beforeShow()) {
            return $ress;
        }

        return $this->__successShow();
    }

    public function update(Request $request, $id)
    {
        return $this->DBSafe(
            function () use ($id) {
                $req = app($this->updateValidator);

                $this->requestData = $req;

                $this->query = $this->model::where($this->modelKey, $id);

                $this->__prepareQueryRowUpdate();

                $this->row = $this->query->firstOrFail();

                if (! empty($this->abilityPolicyUpdate)) {
                    $this->authorize($this->abilityPolicyUpdate, $this->row);
                }

                $data = $req->validated();

                $data = $this->__prepareDataUpdate($data);

                if ($ress = $this->__beforeUpdate()) {
                    return $ress;
                }

                $this->row->fill($data);

                $this->row->save();

                if ($ress = $this->__afterUpdate()) {
                    return $ress;
                }

                $this->__prepareLoadRelation($this->row);

                return $this->__successUpdate();
            }
        );
    }

    public function destroy(Request $request, $id)
    {
        return $this->DBSafe(
            function () use ($id, $request) {

                $ids = $request->input('ids');

                if (! empty($ids) && $this->enableBulkDelete) {
                    return $this->bulkDestroy($request);
                }

                $isForceDelete = $request->input('force') == true || $request->input('force') == 'true';
                if ($this->enableForceDelete && $isForceDelete) {
                    return $this->forceDestroy($id);
                }

                $this->query = $this->model::where($this->modelKey, $id);

                $this->__prepareQueryRowDestroy();

                $this->row = $this->query->firstOrFail();

                if (! empty($this->abilityPolicyDelete)) {
                    $this->authorize($this->abilityPolicyDelete, $this->row);
                }

                if ($ress = $this->__beforeDestroy()) {
                    return $ress;
                }

                $this->row->delete();

                if ($ress = $this->__afterDestroy()) {
                    return $ress;
                }

                return $this->__successDestroy();
            }
        );
    }

    public function bulkDestroy(Request $request)
    {
        return $this->DBSafe(
            function () use ($request) {

                $ids = $request->input('ids');

                $isForceDelete = $request->input('force') == true || $request->input('force') == 'true';
                if ($this->enableForceDelete && $isForceDelete) {
                    return $this->bulkForceDestroy($ids);
                }

                $this->query = $this->model::whereIn($this->modelKey, $ids);

                $this->__prepareQueryBulkDestroy();

                if (! empty($this->abilityPolicyBulkDelete)) {
                    $this->authorize($this->abilityPolicyBulkDelete, [$this->model, ['ids' => $ids]]);
                }

                if ($ress = $this->__beforeBulkDestroy()) {
                    return $ress;
                }

                $this->query->delete();

                if ($ress = $this->__afterBulkDestroy()) {
                    return $ress;
                }

                return $this->__successBulkDestroy();
            }
        );
    }

    public function forceDestroy($id)
    {
        return $this->DBSafe(
            function () use ($id) {
                $this->query = $this->model::withTrashed()->where($this->modelKey, $id);

                $this->__prepareQueryRowDestroy();

                $this->row = $this->query->firstOrFail();

                if (! empty($this->abilityPolicyForceDelete)) {
                    $this->authorize($this->abilityPolicyForceDelete, $this->row);
                }

                if ($ress = $this->__beforeForceDestroy()) {
                    return $ress;
                }

                $this->row->forceDelete();

                if ($ress = $this->__afterForceDestroy()) {
                    return $ress;
                }

                return $this->__successDestroy();
            }
        );
    }

    public function bulkForceDestroy($ids)
    {
        return $this->DBSafe(
            function () use ($ids) {

                $this->query = $this->model::withTrashed()->whereIn($this->modelKey, $ids); // withTrashed()

                $this->__prepareQueryBulkDestroy();

                if (! empty($this->abilityPolicyBulkForceDelete)) {
                    $this->authorize($this->abilityPolicyBulkForceDelete, [$this->model, ['ids' => $ids]]);
                }

                if ($ress = $this->__beforeBulkForceDestroy()) { // Hook baru
                    return $ress;
                }

                $this->query->forceDelete();

                if ($ress = $this->__afterBulkForceDestroy()) { // Hook baru
                    return $ress;
                }

                return $this->__successBulkDestroy();
            }
        );
    }

    public function print(Request $request)
    {
        if (! empty($this->abilityPolicyPrint)) {
            $this->authorize($this->abilityPolicyPrint, $this->model);
        }

        $this->requestData = $request;

        if ($ress = $this->__prepareCacheResult()) {
            return $ress;
        }

        $this->query = $this->model::query();

        $this->__prepareQueryRelationPrint();

        $this->__prepareQueryList();

        $this->__prepareQuerySearchAblePrint();

        $this->__prepareQueryOptionsList();

        if ($ress = $this->__beforePrint()) {
            return $ress;
        }

        $this->__prepareQuerySortOrderPrint();

        $this->__prepareQueryLimitPrint();

        $query = $this->__prepareQueryPrintType();

        $this->__prepareLoadRelation($query);

        return $this->__successPrint($query);
    }
}
