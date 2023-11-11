<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CashflowResource;
use App\Models\Cashflow;
use App\Trait\Authorizable;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    use Authorizable;
    
    public function index(Request $request)
    {
        $cashflows = Cashflow::latest()
        ->filter($request)
        ->paginate(AppConstant::PAGINATION);

        return CashflowResource::collection($cashflows);
    }
}