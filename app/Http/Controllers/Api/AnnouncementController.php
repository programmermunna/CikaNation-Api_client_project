<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data'   => Announcement::latest()->filter($request)->paginate(20),
        ], 200);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'status'  => 'required|boolean',

        ]);

        DB::beginTransaction();
        try {

            $announcement = Announcement::create([
                'number'     => Announcement::max('id') + 1,
                'message'    => $request->message,
                'status'     => $request->status,
                'created_by' => Auth::id(),
            ]);

            activity("Announcement created")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement created successfully",
                    'target' => "$announcement->message",
                ])
                ->log(":causer.name created Announcement $announcement->message.");

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully Announcement Created!!',
                'data'    => $announcement,
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'status'  => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {

            $announcement->update([
                'message'    => $request->message,
                'status'     => $request->status,
            ]);

            activity("Announcement updated")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip,
                    'activity' => "Announcement updated successfully",
                    'target'   => "$announcement->message",
                ])
                ->log(":causer.name updated Announcement $announcement->message.");

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully Announcement Updated!!',
                'data'    => $announcement,
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }


}