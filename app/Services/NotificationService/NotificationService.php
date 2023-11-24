<?php

namespace App\Services\NotificationService;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationService
{
    public function sendNotification($token)
    {
        try {
            $serverKey = env('FCM_SERVER_KEY');

            // Load Firebase service account credentials from the file
            $serviceAccountPath = env('FIREBASE_CREDENTIALS');
            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

            $factory = (new Factory)->withServiceAccount($serviceAccount);
            $messaging = $factory->createMessaging();


            $notification = Notification::create('Test Notification', 'Hi How are you jjjjjjjjjjj');

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData(['request_delivery_receipt' => 'true']); // Request delivery receipt

            // Use false as the second argument to actually send the message
            $response = $messaging->send($message, false, ['serverKey' => $serverKey]);

            // Check if the message was sent successfully
            $success = empty($response['error']);

            // Log the result
            if ($success) {
                Log::info('Notification sent successfully.');
            } else {
                Log::error('Failed to send notification. Details: ' . json_encode($response));
            }

            return response()->json(['success' => $success]);
        } catch (\Exception $e) {
            // Log any exceptions
            Log::error('Exception while sending notification: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
