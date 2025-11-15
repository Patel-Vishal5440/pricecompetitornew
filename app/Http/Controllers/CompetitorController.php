<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Competitor;
use Illuminate\Validation\Rule;
use App\Repositories\CompetitorRepository;

class CompetitorController extends Controller
{

    /**
     * Display a competitor list of the resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(CompetitorRepository $competitorRepository, Request $request)
    {
        $pageTitle = 'Competitor List';
        $pageDescription = 'Manage and view all competitors';

        if (request()->ajax()) {
            return $competitorRepository->dataSource($request);
        }

        return view('competitor.competitors', compact(
            'pageTitle',
            'pageDescription'
        ));
    }



    /**
     * Display a add new competitor of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $pageTitle = 'Create new Competitor';
        $pageDescription = 'Some description for the page';
        return view('competitor.new_competitor', compact('pageTitle', 'pageDescription'));
    }

    /**
     * Store a newly created competitor resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'website' => 'required|url|max:255',
                'shortname' => 'required|string|max:100',
                'price_class_name' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $competitor = Competitor::create([
                'name' => $request->name,
                'website' => $request->website,
                'shortname' => $request->shortname,
                'price_class_name' => $request->price_class_name,
                'status' => $request->status ?? 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Competitor created successfully',
                'competitor' => $competitor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create competitor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $pageTitle = 'Edit Competitor';
        $pageDescription = 'Some description for the page';
        $competitor = Competitor::findOrFail($id);
        return view('competitor.new_competitor', compact('pageTitle', 'pageDescription', 'competitor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $competitor = Competitor::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'website' => 'required|url|max:255',
                'shortname' => 'required|string|max:100',
                'price_class_name' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $competitor->update([
                'name' => $request->name,
                'website' => $request->website,
                'shortname' => $request->shortname,
                'price_class_name' => $request->price_class_name,
                'status' => $request->status ?? $competitor->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Competitor updated successfully',
                'competitor' => $competitor
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Competitor not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update competitor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $competitor = Competitor::findOrFail($id);
            $competitor->delete();
            
            // If AJAX request, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Competitor deleted successfully!',
                    'deleted_id' => $id
                ]);
            }
            
            return redirect()->route('competitor.list')->with('delete', 'Competitor deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Competitor not found.'
                ], 404);
            }
            
            return redirect()->route('competitor.list')->with('error', 'Competitor not found.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete competitor: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('competitor.list')->with('error', 'Failed to delete competitor. Please try again.');
        }
    }


}
