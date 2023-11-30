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

    public function sendNotification(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return Response::json(['error' => 'Token is required.'], 400);
        }

        $success = $this->notificationService->sendNotification($token);

        return Response::json(['success' => $success]);
    }
}
