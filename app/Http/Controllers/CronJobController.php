<?php

namespace App\Http\Controllers;

use App\Models\CronJob;
use App\Repositories\CronJobRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
            // Here you would implement the actual command execution
            // For now, we'll just update the last_run timestamp
            $cronJob->update([
                'last_run' => now(),
                'updated_by' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', 'Cron job executed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to execute cron job. Please try again.');
        }
    }
}
