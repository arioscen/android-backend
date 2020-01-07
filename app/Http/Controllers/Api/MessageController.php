<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\FirebaseCurl;

class MessageController extends Controller
{
    public function send_to_default(Request $request)
    {
        $notification_key = FirebaseCurl::get_default_group_key();
        if ($notification_key) {
            $content = $request->json()->all();
            $title = array_key_exists('title', $content) ? $content['title'] : '';
            $body = array_key_exists('body', $content) ? $content['body'] : '';
            
            $params = [
                'to' => $notification_key,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ];            

            $send_result = FirebaseCurl::send($params);
            if ($send_result) {
                return response()->json(['result' => true]);        
            }
        }

        return response()->json(['result' => false]);
    }
}
