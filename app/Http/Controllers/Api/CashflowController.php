<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cashflow\StoreCashflowRequest;
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


    /**
     * Create new Cashflow
     */
    public function store(StoreCashflowRequest $request)
    {
        try{
            $data = Cashflow::create($request->validated());
            
            return response()->json([
                'status'  => 'success',
                'message' => 'Cashflow created successfully',
                'data'    => new CashflowResource($data)
            ],200);

        }catch(\Exception $error){
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ],500);
        }
    }
}