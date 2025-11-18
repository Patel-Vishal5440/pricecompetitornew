<?php

namespace App\Http\Controllers;

use App\Models\CronJob;
use App\Repositories\CronJobRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CronJobController extends Controller
{
    protected $cronJobRepository;

    public function __construct(CronJobRepository $cronJobRepository)
    {
        $this->cronJobRepository = $cronJobRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CronJobRepository $cronJobRepository, Request $request)
    {
        $pageTitle = 'Cron Jobs Management';
        $pageDescription = 'Manage system cron jobs and scheduled tasks';

        if (request()->ajax()) {
            $this->cronJobRepository = $cronJobRepository;
            return $this->cronJobRepository->dataSource($request);
        }

        return view('cron-jobs.index', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription
        ]);
    }

    public function toggleStatus(CronJob $cronJob)
    {
        try {
            $cronJob->update([
                'is_active' => !$cronJob->is_active,
                'updated_by' => Auth::id()
            ]);

            $status = $cronJob->is_active ? 'activated' : 'deactivated';
            return redirect()->back()
                ->with('success', "Cron job {$status} successfully!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update cron job status. Please try again.');
        }
    }

    /**
     * Execute the cron job manually.
     */
    public function execute(CronJob $cronJob)
    {
        try {
            // Update last_run and calculate next_run
            $cronJob->update([
                'last_run' => now(),
                'next_run' => $cronJob->calculateNextRun(),
                'updated_by' => Auth::id()
            ]);

            Log::info("Cron job '{$cronJob->name}' executed manually at " . now());

            return redirect()->back()
                ->with('success', 'Cron job executed successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to execute cron job manually', [
                'cron_job_id' => $cronJob->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to execute cron job. Please try again.');
        }
    }

    /**
     * Update schedule times for a cron job.
     */
    public function updateSchedule(Request $request, CronJob $cronJob)
    {
        try {
            Log::info('Update schedule request received', [
                'cron_job_id' => $cronJob->id,
                'request_data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'schedule_times' => 'required|array|min:1',
                'schedule_times.*' => ['required', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
                'timezone' => 'required|string|max:50'
            ], [
                'schedule_times.required' => 'At least one schedule time is required.',
                'schedule_times.*.required' => 'Each schedule time is required.',
                'schedule_times.*.regex' => 'Each schedule time must be in HH:MM format (24-hour).',
                'timezone.required' => 'Timezone is required.'
            ]);

            if ($validator->fails()) {
                Log::warning('Schedule update validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Sort times
            $scheduleTimes = $request->schedule_times;
            sort($scheduleTimes);

            // Remove duplicates
            $scheduleTimes = array_unique($scheduleTimes);
            $scheduleTimes = array_values($scheduleTimes); // Re-index array

            // Update cron job with new schedule times
            // Note: schedule_time column is NOT NULL, so we don't update it
            // The system will use schedule_times for scheduling when available
            $cronJob->update([
                'schedule_times' => $scheduleTimes,
                'timezone' => $request->timezone,
                'updated_by' => Auth::id()
            ]);
            
            // Refresh model and recalculate next_run after schedule update
            $cronJob->refresh();
            $cronJob->update([
                'next_run' => $cronJob->calculateNextRun()
            ]);

            Log::info('Schedule updated successfully', [
                'cron_job_id' => $cronJob->id,
                'schedule_times' => $scheduleTimes,
                'timezone' => $request->timezone
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Schedule times updated successfully!',
                'schedule_times' => $scheduleTimes,
                'timezone' => $request->timezone
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update schedule times', [
                'cron_job_id' => $cronJob->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule times: ' . $e->getMessage()
            ], 500);
        }
    }
}
