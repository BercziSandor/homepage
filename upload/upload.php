<?php
// Define constants
define('MAX_FILE_SIZE', 10485760);  // 10 MB (in bytes)
define('MAX_ZIP_SIZE', 157286400);  // 150 MB for ZIP files (in bytes)
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'zip', 'pdf', 'docx']);

if (isset($_POST["submit"])) {
    // Create a directory with a name based on the current timestamp
    $message = "";
    $messageError = "";
    $timestampDir = date("Y.m.d_His") . "_" . $_SERVER['REMOTE_ADDR'];
    $timestampDir = date("Y.m.d_His");


    if (count($_FILES["fileToUpload"]["name"]) == 1) {
        // Only one file, use its name without extension as the directory name
        $fileName = $_FILES["fileToUpload"]["name"][0];
        $targetDirectory = "uploads/" . $timestampDir . "_" . $fileName . "/";
    } else {
        // Multiple files, use timestamp and IP as before
        $targetDirectory = "uploads/" . $timestampDir . "/";
    }

    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0700, true); // Create the directory if it doesn't exist
    }

    $uploadOk = 1;
    // Loop through each file in the array of uploaded files
    foreach ($_FILES["fileToUpload"]["name"] as $key => $fileName) {
        $targetFile = $targetDirectory . basename($fileName);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file already exists - no chance
        if (file_exists($targetFile)) {
            $messageError .= $fileName . ": The file already exists.\n";
            $uploadOk = 0;
        }

        // Check file size based on the file type
        if ($fileType == "zip" && $_FILES["fileToUpload"]["size"][$key] > MAX_ZIP_SIZE) {
            $messageError .= $fileName . ": File too big (max. 150MB)\n";
            $uploadOk = 0;
        } elseif ($_FILES["fileToUpload"]["size"][$key] > MAX_FILE_SIZE) {
            $messageError .= $fileName . ": File too big (max. 10MB)\n";
            $uploadOk = 0;
        }

        // Check if the file extension is in the list of allowed extensions
        if (!in_array($fileType, ALLOWED_EXTENSIONS)) {
            $messageError .= "$fileName: File type $fileType not allowed. Allowed extensions: "
                . implode(', ', ALLOWED_EXTENSIONS)
                . "\n";
            $uploadOk = 0;
        }

        if ($uploadOk != 0) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$key], $targetFile)) {
                $message .= "$fileName: upload OK\n";
            } else {
                $messageError .= "$fileName: move_uploaded_file() FAILED\n";
            }
        }
    }

    // Check if the directory is empty and delete it
    if (is_dir($targetDirectory) && count(scandir($targetDirectory)) == 2) {
        // The count of files is 2 because of . and ..
        rmdir($targetDirectory);
    }

    if ($messageError != "") {
        echo '<script>alert(\''
            . $message . '\n'
            . 'There was an error at upload:\n' . $messageError . '\');</script>';
    } else {
        echo '<script>alert(' . $message . ');</script>';
    }

    // Redirect back to the form page
//    echo '<script>window.location = "index.html";</script>';

} //if (isset($_POST["submit"]))

?>
