<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = "notifications";
    protected $fillable = [
        'id',
        'user_id',
        'notification_description',
        'url',
        'read',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
    
    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public static function createNotification($recipientIds, $description, $url) {
        try {
            foreach($recipientIds as $recipientId){
                $insertNotification = new Notification([
                    'user_id'                  => $recipientId,
                    'notification_description'  => $description,
                    'url'                      => $url,
                    'read'                     => 0,
                    'created_by'               => Auth::user()->id,
                    'created_at'               => Carbon::now(),
                    'updated_by'               => Auth::user()->id,
                    'updated_at'               => Carbon::now(),
                ]);
                $insertNotification->save();
            }
            $returnData = [
                'success' => true,
                'message' => 'Notification created',
            ];
            return $returnData;
        } catch (Exception $e) {
            $returnData = [
                'success' => false,
                'message' => 'Notification can not created',
            ];
            return $returnData;
        }
    }
}

