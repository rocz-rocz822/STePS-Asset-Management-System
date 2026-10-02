<?php

namespace App\Http\Controllers;

use App\Enums\ReportType;
use App\Exports\GenericExport;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index(): View
    {
        return view('reports.index', [
            'types' => ReportType::cases(),
        ]);
    }

    public function show(string $type, Request $request): View
    {
        $reportType = ReportType::tryFrom($type) ?? abort(404);

        $data = $this->reportService->generate($reportType->value, $request);

        $filterOptions = match ($reportType) {
            ReportType::ByCategory => ['categories' => \App\Models\Category::orderBy('name')->get()],
            ReportType::ByStatus => ['statuses' => \App\Enums\AssetStatus::cases()],
            ReportType::ByCondition => ['conditions' => \App\Enums\AssetCondition::cases()],
            ReportType::ByLocation => ['locations' => \App\Models\Location::orderBy('building')->get()],
            default => [],
        };

        return view('reports.show', ['type' => $reportType, 'data' => $data, ...$filterOptions]);
    }

    public function export(string $type, string $format, Request $request): Response
    {
        $reportType = ReportType::tryFrom($type) ?? abort(404);

        $data = $this->reportService->generate(
            $reportType->value,
            $request
        );

        $filename = Str::slug($data['title']) . '-' . now()->format('Ymd-His');

        activity('report')
            ->causedBy($request->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'browser' => $request->userAgent(),
                'format' => strtoupper($format),
            ])
            ->log("Exported \"{$data['title']}\" as " . strtoupper($format));

        return match ($format) {
            'pdf' => Pdf::loadView('reports.pdf', $data)
                ->download("{$filename}.pdf"),

            'excel' => Excel::download(
                new GenericExport($data['headers'], $data['rows']),
                "{$filename}.xlsx"
            ),

            'csv' => Excel::download(
                new GenericExport($data['headers'], $data['rows']),
                "{$filename}.csv",
                ExcelFormat::CSV
            ),

            default => abort(404, 'Unsupported export format.'),
        };
    }
}