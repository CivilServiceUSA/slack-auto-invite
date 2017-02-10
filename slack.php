<?php
error_reporting(E_ALL);
require('config.php');

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$response = array();
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
$invite = isset($_REQUEST['invite']) ? $_REQUEST['invite'] : null;
$valid_invite = null;

/**
 * See if the Invite Code is Valid
 */
foreach (INVITE_TOKENS as $key => $value) {
    if (strtolower($key) === strtolower($invite)) {
        $valid_invite = $value;
        break;
    }
}

/**
 * Make API Call
 * @param $url - URL for Slack
 * @param $data - Array of Data
 * @return mixed
 */
function makeApiCall ($url, $data) {
    $ch = curl_init();
    $fields = http_build_query($data);

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

/**
 * Check if Email and Invite Code is Valid
 */
if (filter_var($email, FILTER_VALIDATE_EMAIL) && $valid_invite) {

    $signup = makeApiCall(SLACK_URL . '/api/users.admin.invite', array(
        'email' => $email,
        'token' => SLACK_TOKEN,
        'set_active' => true,
        'resend' => true
    ));

    if ($signup && $signup['ok'] === true) {
        $response['success'] = true;

        // Send Message to Admin
        makeApiCall(SLACK_URL . '/api/chat.postMessage', array(
            'channel' => 'D3EJFKP6W',
            'token' => SLACK_TOKEN,
            'text' => $email . ' just used ' . $valid_invite['slack_name'] . '\'s Invite Code to Join our Slack Team.',
            'as_user' => false,
            'link_names' => true,
            'username' => 'New Member Notification'
        ));

        // Send Message to Owner of Invite Code
        makeApiCall(SLACK_URL . '/api/chat.postMessage', array(
            'channel' => $valid_invite['slack_ims_id'],
            'token' => SLACK_TOKEN,
            'text' => $email . ' just used your Invite Code to Join our Slack Team.',
            'as_user' => false,
            'username' => 'New Member Notification'
        ));
    } else {
        $errorKeys = array(
            'already_invited' => 'Invite Already Sent',
            'already_in_team' => 'You are already on our team',
            'sent_recently' => 'Invite Email Recently Sent',
            'user_disabled' => 'Account Deactivated',
            'missing_scope' => 'Invalid API Token'
        );

        $response['success'] = false;
        $response['error'] = $errorKeys[$signup['error']];
    }
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['success'] = false;
    $response['error'] = 'Invalid Email Address';
} else if (!$valid_invite) {
    $response['success'] = false;
    $response['error'] = 'Invalid Invite Code';
} else {
    $response['success'] = false;
    $response['error'] = 'An Unknown Error has Occurred';
}

exit(json_encode($response));
