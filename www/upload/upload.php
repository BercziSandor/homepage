<?php
// Define constants
define('MAX_NORMAL_SIZE', 10 * 1048576);  // 10 MB (in bytes)
define('MAX_BIG_SIZE', 150 * 1048576);  // 150 MB for ZIP files (in bytes)
define('NORMAL_FILE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'docx']);
define('BIG_FILE_EXTENSIONS', ['zip', 'apk']);
define('ALLOWED_EXTENSIONS', array_merge(NORMAL_FILE_EXTENSIONS, BIG_FILE_EXTENSIONS));
$response = [
    'success' => false,
    'messages' => []
];

if (isset($_FILES["fileToUpload"])) {
    date_default_timezone_set('Europe/Budapest');
    $timestampDir = date("Y.m.d_His");

    // Consider removing or modifying this block to handle your directory naming logic
    if (count($_FILES["fileToUpload"]["name"]) === 1) {
        // Only one file, use its name without extension as the directory name
        $fileName = $_FILES["fileToUpload"]["name"][0];
        $targetDirectory = "uploads/" . $timestampDir . "_" . pathinfo($fileName, PATHINFO_FILENAME) . "/";
    } else {
        // Multiple files
        $targetDirectory = "uploads/" . $timestampDir . "/";
    }


    foreach ($_FILES["fileToUpload"]["name"] as $key => $fileName) {
        $uploadAllowed = false;
        $targetFile = $targetDirectory . basename($fileName);
        $fileType = strtolower(trim(pathinfo($targetFile, PATHINFO_EXTENSION)));

        if (file_exists($targetFile)) {
            $response['messages'][] = "$fileName: The file already exists.";
        } elseif (!in_array($fileType, ALLOWED_EXTENSIONS)) {
            $response['messages'][] = "$fileName: File type $fileType not allowed. Allowed extensions: " . implode(', ', ALLOWED_EXTENSIONS);
        } elseif (in_array($fileType, BIG_FILE_EXTENSIONS)  && $_FILES["fileToUpload"]["size"][$key] > MAX_BIG_SIZE ) {
            $response['messages'][] = "$fileName: File too big (max. 150MB)";
        } elseif (in_array($fileType, NORMAL_FILE_EXTENSIONS) && $_FILES["fileToUpload"]["size"][$key] > MAX_NORMAL_SIZE) {
            $response['messages'][] = "$fileName: File too big (max. 10MB)";
        } elseif (!is_file($_FILES["fileToUpload"]["tmp_name"][$key])) {
            $response['messages'][] = "$fileName: Internal error.";
        } else {
            $uploadAllowed = true;
        }

        if ($uploadAllowed) {
            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0700, true); // Create the directory if it doesn't exist
            }
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$key], $targetFile)) {
                $response['messages'][] = "$fileName: Upload OK";
                $response['success'] = true; // Set success to true if at least one file is successfully uploaded
            } else {
                $response['messages'][] = "$fileName: move_uploaded_file() FAILED";
            }
        }
    }

    // Optionally, you can check if the directory is empty and delete it if necessary
    // This could be the case if all file uploads failed
} else {
    $response['messages'][] = "No files were uploaded.";
}

header('Content-Type: application/json');
echo json_encode($response);
