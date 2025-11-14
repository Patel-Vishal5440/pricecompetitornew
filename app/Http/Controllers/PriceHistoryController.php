<?php

namespace App\Http\Controllers;

use App\Models\ActivityFeed;
use App\Repositories\PriceHistoryRepository;
use Illuminate\Http\Request;

class PriceHistoryController extends Controller
{
    protected $priceHistoryRepository;

    public function __construct(PriceHistoryRepository $priceHistoryRepository)
    {
        $this->priceHistoryRepository = $priceHistoryRepository;
    }

    public function index(Request $request)
    {
        $pageTitle = 'Price History';
        $pageDescription = 'List of all price changes.';
        
        // If AJAX request, return DataTables response
        if ($request->ajax()) {
            return $this->priceHistoryRepository->dataSource($request);
        }
        
        return view('price_history.index', compact('pageTitle', 'pageDescription'));
    }
}