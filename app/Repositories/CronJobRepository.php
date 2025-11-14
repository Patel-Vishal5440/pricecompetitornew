<?php
namespace App\Repositories;

use App\Models\CronJob;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CronJobRepository
{
    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null);

        Log::info('CronJob DataTables request received', [
            'searchData' => $searchData,
            'user' => auth()->user() ? auth()->user()->id : 'not authenticated'
        ]);

        $cronJobs = CronJob::query()
            ->with(['creator', 'updater'])
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('description', 'like', "%{$searchData}%")
                          ->orWhere('command', 'like', "%{$searchData}%");
                });
            })
            ->latest('created_at');

        try {
            $result = $this->cronJobDataTable($cronJobs);
            Log::info('CronJob DataTables response generated successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('CronJob DataTables Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cronJobDataTable($cronJobs)
    {
        $dataTable = DataTables::of($cronJobs)
            ->addColumn('name', function ($cronJob) {
                return '<div class="userDatatable-content">
                    <strong>' . $cronJob->name . '</strong><br>
                    <small class="text-muted">Created by ' . ($cronJob->creator->name ?? 'Unknown') . '</small>
                </div>';
            })
            ->addColumn('description', function ($cronJob) {
                return '<div class="userDatatable-content">' . Str::limit($cronJob->description, 50) . '</div>';
            })
            ->addColumn('schedule', function ($cronJob) {
                return '<div class="userDatatable-content">
                    <span class="badge-lg rounded px-3 py-1" 
                          style="font-size: 12px; font-weight: 500; color: #0d6efd; background-color: #e7f1ff;">
                        ' . $cronJob->formatted_schedule_time . '
                    </span>
                </div>';
            })
            ->addColumn('command', function ($cronJob) {
                return '<div class="userDatatable-content">
                    <code style="background-color: #f8f9fa; padding: 2px 4px; border-radius: 3px; font-size: 0.875em;">
                        ' . Str::limit($cronJob->command, 30) . '
                    </code>
                </div>';
            })
            ->addColumn('last_run', function ($cronJob) {
                return '<div class="userDatatable-content">
                    <small>' . $cronJob->formatted_last_run . '</small>
                </div>';
            });

        return $dataTable->rawColumns(['name', 'description', 'schedule', 'command', 'last_run'])->make(true);
    }
} 