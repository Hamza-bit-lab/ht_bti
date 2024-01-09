<?php

namespace App\Http\Controllers;

use App\Jobs\SendEventEmails;
use App\Models\Employee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function event(){
        return view('events.events');
    }

    public function getAllEvents()
    {
        $events = DB::table('events')->get();

        return response()->json($events);
    }
    public function storeEvents(Request $request)
    {
        $employees = Employee::where('is_employed', 1)->get();

        DB::table('events')->insert([
            'title' => $request->title,
            'start' => $request->start,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        foreach ($employees as $employee) {
            SendEventEmails::dispatch($employee, $request->title, $request->description);
        }

        return response()->json(['message' => 'Event added successfully']);
    }
    public function updateEvent(Request $request, $id)
    {
        DB::table('events')->where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'start' => $request->start,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Event updated successfully']);
    }

    public function deleteEvent($id)
    {
        try {
            DB::table('events')->where('id', $id)->delete();

            return response()->json(['message' => 'Event deleted successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete Event', 'message' => $e->getMessage()], 500);
        }
    }
}
