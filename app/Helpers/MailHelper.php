<?php
use Illuminate\Support\Facades\Mail;
use App\Mail\MailService;
use App\Exceptions;
use App\Http\Controllers\Notification\NotificationController;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * [sendMail description]
 * @param  string $viewName                   [name of the view file, body of the mail]
 * @param  array  $toAddress                  [to whome we need to send, array of emails or just single email]
 * @param  array  $ccAddress                  [array/single emails to add in css]
 * @param  array  $subject                    [subject name to send email]
 * @param  array  $attachements               [attachements in array: filePath and fileName is must]
 * @param  array  $tokensArray                [values of tokens with key as token]
 * @return int/string                         [status of the mail]
 */
function sendMail($viewName = '', $toAddress = [], $ccAddress = [], $subject = [], $attachements = [], $tokensArray = [])
{
    $siteEnvironment = env('SITE_ENVIRONMENT') ?? 'LOCAL'; //NOSONAR

    try {
        Mail::to($finalTodoAddress)
            ->cc($finalCcAddress)
            // ->bcc(ABSOLUTE_YOU_SYSTEM_MAIL)
            ->send(new MailService($subject, $viewName, $attachements, $tokensArray));
        return ['status' => 200, 'message' => 'mail sent successfully.!'];
    } catch (Exception $e) {
        return ['status' => 500, 'message' => [$e->getMessage()]];
    }
}

/**
 * [sendNotification description]
 * @param  array $notifyData [
 * 'triggered_by'
 *   user_to_notify ,
 *   type_ref_id,
 *    message
 *]
 * @return [type]             [description]
 */
function sendNotification($notifyData)
{
    $notify = new NotificationController();
    return $notify->createNotifications($notifyData);
}