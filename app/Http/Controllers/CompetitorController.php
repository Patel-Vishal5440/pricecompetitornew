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
     * @return \Illuminate\View\View
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['status'] = 1;
        $validators = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'website' => 'required|url|max:255',
            'shortname' => 'required|string|max:100',
            'price_class_name' => 'nullable|string|max:255',
            'status' => 'required|in:1,0'
        ]);


        if ($validators->fails()) {
            return redirect()->route('competitor.create')->withErrors($validators)->withInput();
        } else {
            $competitor = new Competitor();
            $competitor->name = $request->name;
            $competitor->website = $request->website;
            $competitor->shortname = $request->shortname;
            $competitor->price_class_name = $request->price_class_name;
            $competitor->status = $request->status;
            $competitor->save();
            return redirect()->route('competitor.list')->with('create', 'Competitor created successfully');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request['status'] = 1;
        $validators = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'website' => 'required|url|max:255',
            'shortname' => 'required|string|max:100',
            'status' => 'required|in:1,0'
        ]);

        if ($validators->fails()) {
            return redirect()->route('competitor.edit', $id)->withErrors($validators)->withInput();
        } else {
            $competitor = Competitor::findOrFail($id);
            $competitor->name = $request->name;
            $competitor->website = $request->website;
            $competitor->shortname = $request->shortname;
            $competitor->price_class_name = $request->price_class_name;
            $competitor->status = $request->status;
            $competitor->save();
            return redirect()->route('competitor.list')->with('update', 'Competitor updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $competitor = Competitor::findOrFail($id);
            
            // Delete the competitor
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
