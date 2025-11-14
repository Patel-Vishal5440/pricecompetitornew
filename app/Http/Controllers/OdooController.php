<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OdooService;

class OdooController extends Controller
{
    public function authenticate()
    {
        $odooService = new OdooService();
        $userId = $odooService->authenticate();

        return response()->json([
            'odoo_user_id' => $userId
        ]);
    }
}
