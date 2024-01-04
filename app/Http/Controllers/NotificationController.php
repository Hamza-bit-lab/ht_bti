<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotificationEmail;
use App\Mail\NotificationMail;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
//use Intervention\Image\Facades\Image;
//use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
//use Nette\Utils\Image;
use Illuminate\Support\Str;


class NotificationController extends Controller
{
    public function notifications() {
        $employees = Employee::all();

        return view('notifications.notificationmail', compact('employees'));
    }

    public function sendNotifications(Request $request)
    {
        $selectedEmployees = $request->input('selected_employees', []);
        $subject = $request->input('subject');
        $message = $request->input('message');
        $employees = Employee::whereIn('id', $selectedEmployees)->get();

        foreach ($employees as $employee) {
            dispatch(new SendNotificationEmail($employee, $message, $subject));
        }

        return redirect()->route('notifications')->with('success', 'Notifications sent successfully.');
    }


//    public function uploadimage(Request $request){
//        try {
//            if ($request->hasFile('upload')) {
//                $file = $request->file('upload');
//
//                // Generate a unique filename using timestamp and a random string
//                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
//
//                // Move the file to the 'media' directory
//                $file->move(public_path('media'), $fileName);
//
//                $url = asset('media', $fileName);
//                return response()->json(['fileName' => $fileName, 'uploaded' => 1, 'url' => $url]);
//            } else {
//                throw new \Exception('File not found in the request.');
//            }
//        } catch (\Exception $e) {
//            return response()->json(['error' => $e->getMessage()]);
//        }
//    }
//
//    public function upload(Request $request){
//        $fileName=$request->file('file')->getClientOriginalName();
//        $path=$request->file('file')->storeAs('uploads', $fileName, 'public');
//        return response()->json(['location'=>"/storage/$path"]);
//
//        /*$imgpath = request()->file('file')->store('uploads', 'public');
//        return response()->json(['location' => "/storage/$imgpath"]);*/
//
//    }
}
