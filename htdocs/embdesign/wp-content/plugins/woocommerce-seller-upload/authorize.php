<?php
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig(plugin_dir_path(__FILE__) . 'credentials.json');
$client->addScope(Google_Service_Drive::DRIVE);
$client->setRedirectUri(admin_url('admin.php?page=drive-auth'));
$client->setAccessType('offline');
$client->setPrompt('consent');

if (!isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    echo "<h2>🔐 Connect to Google Drive</h2>";
    echo "<a href='$auth_url' class='button button-primary'>Click here to connect</a>";
} else {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['access_token'])) {
        file_put_contents(plugin_dir_path(__FILE__) . 'token.json', json_encode($token));
        echo "<h2>✅ Google Drive connected successfully!</h2>";
    } else {
        echo "<h2>❌ Failed to get access token</h2><pre>" . print_r($token, true) . "</pre>";
    }
}
