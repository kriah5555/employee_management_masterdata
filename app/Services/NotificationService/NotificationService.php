<?php

namespace App\Services\NotificationService;

use Kreait\Firebase\Factory;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use App\Services\Email\MailService;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationService
{
    public function __construct(
        protected UserService $userService,
        protected MailService $mailService
    )
    {
    }

    public function sendNotification($userID ,$title  ,$description )
    {
        try {
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();

            $serverKey = env('FCM_SERVER_KEY');

            // Load Firebase service account credentials from the file
            $serviceAccountPath = env('FIREBASE_CREDENTIALS');
            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

            $factory = (new Factory)->withServiceAccount($serviceAccount);
            $messaging = $factory->createMessaging();

            $deviceTokens = $this->userService->getUserDeviceTokens($userID);

            if ($deviceTokens) {
                $notification = Notification::create($title,$description);

                $messages = [];

                foreach ($deviceTokens as $token) {
                    $messages[] = CloudMessage::withTarget('token', $token)
                        ->withNotification($notification)
                        ->withData(['request_delivery_receipt' => 'true']);
                }

                // Use false as the second argument to actually send the messages
                $responses = $messaging->sendAll($messages, false, ['serverKey' => $serverKey]);

                // Check if the messages were sent successfully
                $success = true;

                foreach ($responses as $response) {
                    if (!empty($response['error'])) {
                        $success = false;
                        Log::error('Failed to send notification. Details: ' . json_encode($response));
                    }
                }

                // Log the result
                if ($success) {
                    Log::info('Notification sent successfully.');
                }

                DB::connection('master')->commit();
                DB::connection('userdb')->commit();

                return response()->json(['success' => $success]);
            } else {
                Log::error('No device token. Skipping notification sending.');
                DB::connection('master')->commit();
                DB::connection('userdb')->commit();
                return response()->json(['success' => false, 'error' => 'No device token available.']);
            }
        } catch (Exception $e) {
            DB::connection('master')->rollback();
            DB::connection('userdb')->rollback();
            Log::error('Exception while sending notification: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
