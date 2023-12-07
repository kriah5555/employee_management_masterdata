<?php

namespace App\Http\Controllers\NotificationController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Services\NotificationService\NotificationService;


class NotificationController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function sendNotificationTo($userID, $title, $description)
    {
        try {
            $success = $this->notificationService->sendNotification($userID, $title, $description);

            return response()->json(['success' => $success]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function sendNotification()
    {
        $userIDs = [1,1,1]; // Array of user IDs
        $titles = ['Login Notification', 'Another Notification', 'Yet Another Notification'];
        $descriptions = ['Hi, you have logged in 30 mins late', 'This is another notification', 'And here is one more'];

        $responses = [];

        foreach ($userIDs as $key => $userID) {
            $title = $titles[$key] ?? '';
            $description = $descriptions[$key] ?? ''; 

            $responses[] = $this->sendNotificationTo($userID, $title, $description);
        }

        return response()->json(['responses' => $responses]);
    }
}
