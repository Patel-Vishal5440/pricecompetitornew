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
                $scheduleTimes = $cronJob->schedule_times_array;
                $timezone = $cronJob->timezone ?? 'UTC';
                $timezoneAbbr = $timezone === 'UTC' ? 'UTC' :
                               ($timezone === 'America/New_York' ? 'EST' : 
                               ($timezone === 'America/Chicago' ? 'CST' : 
                               ($timezone === 'America/Denver' ? 'MST' : 
                               ($timezone === 'America/Los_Angeles' ? 'PST' : $timezone))));
                
                // Format schedule times display
                // If no schedule times, show default times instead of "Not Scheduled"
                if (empty($scheduleTimes)) {
                    $scheduleTimes = ['12:00', '15:00', '18:00', '21:00'];
                }
                
                // Format times nicely: "12:00, 15:00, 18:00, 21:00" or show count if many
                if (count($scheduleTimes) <= 4) {
                    $scheduleDisplay = implode(', ', $scheduleTimes);
                } else {
                    $scheduleDisplay = implode(', ', array_slice($scheduleTimes, 0, 3)) . ' +' . (count($scheduleTimes) - 3) . ' more';
                }
                $badgeClass = 'bg-primary';
                
                return '<div class="userDatatable-content text-center">
                    <div class="mb-2">
                        <span class="badge rounded-pill px-3 py-2 ' . $badgeClass . '" 
                              style="font-size: 11px; font-weight: 600; color: #fff;">
                            <i class="fas fa-clock me-1"></i>' . $scheduleDisplay . '
                        </span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-globe me-1"></i>' . $timezoneAbbr . '
                        </small>
                    </div>
                </div>';
            })
            ->addColumn('last_run', function ($cronJob) {
                return '<div class="userDatatable-content text-center">
                    <small>' . $cronJob->formatted_last_run . '</small>
                </div>';
            })
            ->addColumn('actions', function ($cronJob) {
                $scheduleTimes = $cronJob->schedule_times_array;
                $timezone = $cronJob->timezone ?? 'UTC';
                
                // Store original schedule times for data attribute (empty array if none)
                $originalScheduleTimes = empty($scheduleTimes) ? [] : $scheduleTimes;
                
                return '<div class="userDatatable-content text-center">
                    <button type="button" class="edit-schedule-btn text-primary" 
                            data-cron-job-id="' . $cronJob->id . '"
                            data-schedule-times=\'' . json_encode($originalScheduleTimes) . '\'
                            data-timezone="' . htmlspecialchars($timezone) . '"
                            title="Edit Schedule Times">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>';
            });

        return $dataTable->rawColumns(['name', 'description', 'schedule', 'last_run', 'actions'])->make(true);
    }
} 