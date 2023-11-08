<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Trait\Authorizable;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use Authorizable;

    public function index(Request $request)
    {
        $Attendance = Attendance::latest()
        ->filter($request)
        ->paginate(AppConstant::PAGINATION);

        return AttendanceResource::collection($Attendance);
    }
}
