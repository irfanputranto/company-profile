<?php

namespace App\Services\BaseCrud\Traits;

use App\Imports\Base\BaseImport;
use App\Support\SecureUploadRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait HasImportData
{
    /**
     * Import Data function
     *
     * @param  array  $validation
     */
    public function importDataToArray(Request $request, $validation = [])
    {
        $request->validate(['file' => SecureUploadRules::spreadsheet()]);

        $import = new BaseImport($validation);
        $import = $import->toArray($request->file('file'));
        $excel_data = $import[0];
        $validator = Validator::make($excel_data, $validation['validation'] ?? [], $validation['message'] ?? []);
        if ($validator->fails()) {
            $errors = $validator->messages()->get('*');
            $response = [];
            foreach ($errors as $key => $value) {
                $err = explode('.', $key);
                $response[$err[0]] = $excel_data[$err[0]];
            }

            return ['status' => false, 'data' => $response, 'message' => head($errors)];
        }

        return ['status' => true, 'data' => $excel_data];
    }

    /**
     * Store Import function
     *
     * @param [array|object] $items
     * @param  bool  $isReplace
     * @param [type] $id
     * @return void
     */
    public function __querySaveImport(array $items, $isReplace = false, $id = null)
    {
        return $this->DBSafe(
            function () use ($items, $isReplace, $id) {
                $dt = new $this->model;
                if ($isReplace && $id != null) {
                    $dt = $this->model::where('id', $id)->first();
                }

                $items = $this->__prepareImportStore($items);

                $dt->fill($items);

                $dt->save();

                if (! $this->__afterImportStore($dt, $items)) {
                    return false;
                }

                return $dt;
            }
        );
    }

    public function __storeImports(array $items, $isReplace = false, $id = null)
    {
        foreach ($items['data'] as $key => $value) {
            $this->row[$key] = $this->__querySaveImport($value, $isReplace, $id);
        }

        return $this->row;
    }

    public function __prepareImportStore($item)
    {
        return $item;
    }

    public function __afterImportStore($dt, $items)
    {
        return true;
    }
    // end import data;
}
