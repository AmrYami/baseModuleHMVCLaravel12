<?php


namespace App\Services;
use Illuminate\Support\Facades\Redis;


class RealTimeService
{

    public function publishData($userId, $data,$type, $channelName){
        $redis = Redis::connection();
       $res = $redis->publish($channelName, json_encode(['channel'=>$type,'data' => json_encode($data),'user_id' => $userId]));

        \Log::info("Published message to Redis", [
            'channel' => $channelName,
            'message' => json_encode(['channel'=>$type,'data' => json_encode($data),'user_id' => $userId]),
            'response' => $res
        ]);
    }
}

