<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private array $updatedInstance = [];

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
     * Update Multiple Records.
     */
    public function update(UpdateAnnouncementRequest $request)
    {
        
        DB::beginTransaction();
        try {
            
            foreach($request->announcements as $attribute){
                $announcement = Announcement::find($attribute['id']);
                $announcement->update([
                    'message' => $attribute['message'],
                    'status'  => $attribute['status'],
                ]);

                $this->updatedInstance[] = $announcement;


                activity("Announcement updated")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement updated successfully",
                    'target' => "$announcement->message",
                ])
                ->log(":causer.name updated Announcement $announcement->message.");
            }
            


            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully Announcement Updated!!',
                'data'    => $this->updatedInstance
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'announcements' => 'required|array|min:1',
            'announcements.*' => 'exists:announcements,id'
        ]);

        DB::beginTransaction();
        try {
           
            $announcements = Announcement::whereIn('id',$request->announcements);
            
            $announcements->get()->each(function($announcement){
                activity("Announcement deleted")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement deleted successfully",
                    'target' => "$announcement->message",
                ])
                ->log(":causer.name deleted Announcement $announcement->message.");
            });


            $announcements->delete();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully Announcement Deleted!!',
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