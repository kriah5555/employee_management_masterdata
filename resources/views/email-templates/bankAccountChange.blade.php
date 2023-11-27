<?php

use App\Models\User\User;
use App\Models\User\UserBasicDetails;
use App\Models\User\UserBankAccount;

$user = User::find($acoountDetials['user_id']);

// Check if the user exists before proceeding
if ($user) {
    // Now you can use $user in your HTML template.

    $UserBankAccount = $user->userBankDetails($user->id)->get()[0];
    $UserEmail = $user->userContactById($user->id)->get()[0];


    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Bank Account Number Change</title>
    </head>
    <body>
        <h1>Bank Account Number has been Changed</h1>
        <p>We have received your request to change your bank account number.</p>
        <p>Details about the change:</p>
        <ul>
            <li><strong>User Name:</strong> {{ $user->username }}</li>
            <li><strong>Email:</strong> {{ $UserEmail['email'] }}</li>
            <li><strong>New Bank Account Number:</strong> {{ $UserBankAccount['account_number'] }}</li>
        </ul>
        <p>If you did not make this change, please contact our support immediately.</p>
    </body>
    </html>
    <?php
} else {
    // Handle the case where the user is not found, perhaps show an error message.
    echo "User not found";
}
?>
