<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ActivityResource;
use App\Trait\Authorizable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    use Authorizable;
    /**
     * Handle the incoming request.
     */
    public function index(Request $request): JsonResource
    {
        $query      = Activity::with('causer')
                ->latest();

        $data = $this->filter($query, $request)
            ->paginate(20);

        return ActivityResource::collection($data);
    }



    public function download(Request $request)
    {
        $query      = Activity::with('causer')
                ->latest();

        $data = $this->filter($query, $request)
            ->get();

        return ActivityResource::collection($data);
    }



    private function filter($query, $request)
    {
        return $query->when($request->description ?? false, function ($query, $description) {
            $query->where('description', 'like', "%$description%");
        })

            ->when($request->log_name ?? false, function ($query, $logName) {
                $query->where('log_name', 'like', "%$logName%");
            })

            ->when($request->activity ?? false, function ($query, $activity) {
                $query->where('activity_log.properties->activity', 'like', "%$activity%");
            })

            ->when($request->target ?? false, function ($query, $target) {
                $query->where('activity_log.properties->target', 'like', "%$target%");
            })


            ->when($request->start_date && $request->end_date ?? false, function ($query) use ($request) {

                $query->whereBetween('created_at', $this->parseDate(
                    $request->start_date,
                    $request->end_date
                ));
            });
    }


    private function parseDate(string $startDate, string $endDate): array
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate   = Carbon::parse($endDate)->endOfDay();

        return [
            $startDate,
            $endDate,
        ];
    }
}
