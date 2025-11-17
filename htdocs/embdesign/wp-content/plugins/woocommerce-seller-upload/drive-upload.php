<?php
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

function upload_file_to_google_drive($file) {
    if (empty($file) || !isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        error_log('WCSU: drive-upload.php: Invalid file data or temporary file missing. File: ' . print_r($file, true));
        return false;
    }

    try {
        $client = new Google_Client();
        $client->setAuthConfig(plugin_dir_path(__FILE__) . 'credentials.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        // Load saved OAuth token
        $tokenPath = plugin_dir_path(__FILE__) . 'token.json';
        if (!file_exists($tokenPath)) {
            error_log('WCSU: token.json not found. Please authenticate first.');
            return false;
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);

        // Refresh token if expired
        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            } else {
                error_log('WCSU: No refresh token available.');
                return false;
            }
        }

        $service = new Google_Service_Drive($client);

        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => basename($file['name'])
        ]);

        $content = file_get_contents($file['tmp_name']);

        $uploadedFile = $service->files->create($fileMetadata, [
            'data' => $content,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        $fileId = $uploadedFile->id;

        // Make the file public
        $permission = new Google_Service_Drive_Permission([
            'type' => 'anyone',
            'role' => 'reader'
        ]);
        $service->permissions->create($fileId, $permission);

        // Return a direct download link
        $downloadUrl = "https://drive.google.com/uc?export=download&id=$fileId";
        return $downloadUrl;

    } catch (Exception $e) {
        error_log('WCSU: Google Drive upload error: ' . $e->getMessage());
        return false;
    }
}
