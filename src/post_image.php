<?php
$db_path = "database/main.sqlite";
$api_key_value = "f29b28e9-5215-44db-9257-84d4e46d6371";
$api_key = $file_path = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = format_data($_POST["api_key"]);
    $file_path = "images/" . format_data($_POST["file_name"]);
    $file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $upload_good = 1;

    if ($api_key != $api_key_value) {
        echo "The incorrect API key was provided.\n";
        $upload_good = 0;
    }

    if (file_exists($file_path)) {
        echo "The file already exists.\n";
        $upload_good = 0;
    }

    if ($file_type != "jpg" && $file_type != "jpeg") {
        echo "The file must be a jpg or jpeg.\n";
        $upload_good = 0;
    }

    if ($upload_good == 0) {
        echo "The file was not uploaded.\n";
    } else {
        $image = fopen($file_path, "wb");
        if (fwrite($image, $_POST["file"])) {
            echo "The file " . htmlspecialchars($_POST["file_name"]) . " has been uploaded.\n";
        } else {
            echo "There was an error during the file upload.\n";
        }
    }
}

function format_data($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}