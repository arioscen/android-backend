<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\Models\Group;
use App\Utils\FirebaseCurl;

class GroupController extends Controller
{
    public function add_to_default(Request $request)
    {
        $token = $request->get('token');
        if ($token) {
            $notification_key = FirebaseCurl::get_default_group_key();

            $params = [
                'operation'             => 'add',
                'notification_key_name' => 'default',
                'notification_key'      => $notification_key,
                'registration_ids'      => [$token]
            ];

            $add_result = FirebaseCurl::notification('post', $params);
            if ($add_result) {
                return response()->json(['result' => true]);        
            }
        }

        return response()->json(['result' => false]);
    }
}
