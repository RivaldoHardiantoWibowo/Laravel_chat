<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        $data["friends"] = User::whereNot("id", auth()->user()->id)->get();

        return view("chat", $data);
    }

    public function saveMessage(Request $request){
        $roomId = $request->roomId;
        $userId = auth()->user()->id;
        $message = $request->message;

            broadcast(new SendMessage($roomId, $userId, $message));
            Message::create([
                'room_id' => $roomId,
                'user_id' => $userId,
                'message' => $message
            ]);

            return response([
                "status" => true,
                "message" => "Message success stored"
            ]);
    }

    public function loadMessage($roomId) {
        $message = Message::where("room_id", $roomId)->orderBy("updated_at", "asc")->get();

        return response([
            "status" => true,
            "data" => $message
        ]);
    }
}
