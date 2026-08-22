<?php
/**
 * Google Drive API Service
 */

require_once __DIR__ . '/../vendor/autoload.php';

class GoogleDriveService {
    private $client;
    private $service;
    private $folderId;

    public function __construct() {
        $this->folderId = GOOGLE_DRIVE_FOLDER_ID;
        $credentialsPath = __DIR__ . '/../' . GOOGLE_SERVICE_ACCOUNT_JSON;

        $this->client = new \Google\Client();
        $this->client->setApplicationName(SITE_NAME . ' Reports Service');
        $this->client->setScopes([\Google\Service\Drive::DRIVE_READONLY]);
        
        if (file_exists($credentialsPath)) {
            $this->client->setAuthConfig($credentialsPath);
        } else {
            // Check if it's an absolute path
            if (file_exists(GOOGLE_SERVICE_ACCOUNT_JSON)) {
                $this->client->setAuthConfig(GOOGLE_SERVICE_ACCOUNT_JSON);
            } else {
                throw new Exception("Google Service Account JSON file not found.");
            }
        }
        
        $this->service = new \Google\Service\Drive($this->client);
    }

    /**
     * Search for a file by exact name inside the configured folder
     */
    public function findFileByName($fileName) {
        if (empty($this->folderId)) {
            throw new Exception("Google Drive Folder ID is not configured.");
        }

        // Search query: exact name and inside the specified folder
        $query = sprintf("name = '%s' and '%s' in parents and trashed = false", 
            str_replace("'", "\'", $fileName), 
            $this->folderId
        );

        $optParams = [
            'q' => $query,
            'spaces' => 'drive',
            'fields' => 'files(id, name, mimeType, size)'
        ];

        $results = $this->service->files->listFiles($optParams);
        $files = $results->getFiles();

        if (count($files) > 0) {
            return $files[0];
        }

        return null;
    }

    /**
     * Download the file content
     */
    public function downloadFile($fileId) {
        $response = $this->service->files->get($fileId, ['alt' => 'media']);
        return $response->getBody()->getContents();
    }
}
