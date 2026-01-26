<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::query()
            ->when($request->user_name, function ($query, $userName) {
                return $query->where('properties->causer_name', 'like', "%{$userName}%");
            })
            ->when($request->date, function ($query, $date) {
                return $query->whereDate('created_at', $date);
            })
            ->when($request->event, function ($query, $event) {
                return $query->where('event', $event);
            })
            ->latest()
            ->paginate(15);

        return view('pages.ActivityLog.index', compact('activities'));
    }

    public function show($id)
    {
        $activity = Activity::findOrFail($id);

        $causer_name = $activity->properties['causer_name']
            ?? ($activity->causer ? $activity->causer->Full_Name : 'System');

        return response()->json([
            'description' => $activity->description,
            'event' => $activity->event,
            'causer' => $causer_name,
            'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            'properties' => $activity->properties,
            'subject_type' => class_basename($activity->subject_type),
            'subject_id' => $activity->subject_id
        ]);
    }
}
