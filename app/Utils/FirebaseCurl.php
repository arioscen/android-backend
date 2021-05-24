<?php

namespace App\Utils;

use Ixudra\Curl\Facades\Curl;
use App\Models\Group;

class FirebaseCurl
{
    public static function send($params = array()) {
        $response = Curl::to(env('FCM_URL', 'https://fcm.googleapis.com/fcm/') . 'send')
            ->withHeader('Content-Type: application/json')
            ->withHeader('Authorization: key=' . env('FIREBASE_API_KEY'))
            ->withData($params)->asJsonRequest()
            ->post();
        $response_decode = json_decode($response, true);
        if (is_array($response_decode)) {
            if (array_key_exists('success', $response_decode)) {
                if ($response_decode['success'] > 0) {
                    return true;
                }
            }
        }

        return false;
    }
    
    public static function notification($method, $params = array()) {
        $response = Curl::to(env('FCM_URL', 'https://fcm.googleapis.com/fcm/') . 'notification')
        ->withHeader('Content-Type: application/json')
        ->withHeader('Authorization: key=' . env('FIREBASE_API_KEY'))
        ->withHeader('project_id: ' . env('FIREBASE_SENDER_ID'));
        if ($method == 'post') {
            $response = $response->withData($params)->asJsonRequest()
            ->post();
        } else if ($method == 'get') {
            $response = $response->withData($params)
            ->get();
        }

        $response_decode = json_decode($response, true);
        if (is_array($response_decode) && array_key_exists('notification_key', $response_decode)) {
            return $response_decode['notification_key'];
        }

        return false;
    }

    public static function get_default_group_key()
    {
        $default_group_name = 'default';
        $default_group = Group::where('name', $default_group_name)->first();
        if ($default_group) {
            return $default_group->notification_key;
        } else {
            $params = [
                'notification_key_name' => $default_group_name
            ];

            $notification_key = self::notification('get', $params);
            
            Group::create([
                    'name'             => $default_group_name,
                    'notification_key' => $notification_key
                ]);
            
            return $notification_key;
        }

        return false;
    }
}
