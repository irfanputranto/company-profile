<?php

namespace App\Services\BaseCrud\Traits;

use App\Exports\GenericExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as MaatwebsiteExcel;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

trait HasExportData
{
    /** @var list<string> */
    protected array $exportableFormats = ['xlsx', 'csv'];

    public function exportData(Request $request): BinaryFileResponse
    {
        if (! empty($this->abilityPolicyPrint)) {
            $this->authorize($this->abilityPolicyPrint, $this->model);
        }

        $format = strtolower((string) $request->query('format'));

        if (! in_array($format, $this->exportableFormats, true)) {
            abort(Response::HTTP_BAD_REQUEST, 'Format export tidak valid.');
        }

        $this->requestData = $request;
        $this->query = $this->model::query();

        $this->__prepareQueryRelationPrint();
        $this->__prepareQueryList();
        $this->__prepareQuerySearchAblePrint();
        $this->__prepareQueryOptionsList();
        $this->__prepareQuerySortOrderPrint();
        $this->__prepareQueryLimitPrint();

        if ($response = $this->__beforePrint()) {
            return $response;
        }

        $data = $this->__prepareQueryPrintType();
        $this->__prepareLoadRelation($data);

        if (isset($this->resource)) {
            $data = $this->resource::collection($data)->toArray($request);
        } else {
            $data = collect($data)->map(fn ($row): array => $row->toArray())->all();
        }

        $writerType = $format === 'csv' ? MaatwebsiteExcel::CSV : MaatwebsiteExcel::XLSX;

        return Excel::download(
            new ($this->exportExcelClass ?? GenericExport::class)($data),
            $this->exportFilename($format),
            $writerType,
        );
    }

    private function exportFilename(string $extension): string
    {
        $name = $this->exportName ?? class_basename($this->model);

        return str($name)->slug()->append('-export.'.$extension)->toString();
    }
}
