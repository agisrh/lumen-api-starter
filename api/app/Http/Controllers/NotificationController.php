<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        # Validasi
        $this->validate($request, [
            'token'        => 'required|String',
            'title'        => 'required|string',
            'message'      => 'required|string'
        ]);

        $token   = $request->token;
        $title   = $request->title;
        $message = $request->message;
        $data    = array("id" => "1");

        $fcm = FCM_Notif($token, $title, $message, $data);


        return respondWithData(true, 200, 'Send notification', $fcm);



    }
}