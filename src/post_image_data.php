<?php
$db_path = "database/main.sqlite";
$api_key_value = "f29b28e9-5215-44db-9257-84d4e46d6371";
$api_key = $current_date = $current_time = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first = true;
    $api_key = format_data($_POST["api_key"]);
    $current_date = format_data($_POST["current_date"]);
    $current_time = format_data($_POST["current_time"]);

    if ($api_key == $api_key_value) {
        $db = new SQLite3($db_path);
        if (!$db) {
            echo "The connection to the database failed: " . $db->lastErrorMsg() . "\n";
        } else {
            for ($i = 0; $i < count($_POST["date"]); $i++) {
                $file_path = "images/" . $_POST["file_name"][$i];

                if (file_exists($file_path)) {
                    $query = "INSERT INTO images (date, time, file_name, file_path, false_trigger)
                    VALUES ('" . $_POST["date"][$i] . "', '" . $_POST["time"][$i] . "', '" . $_POST["file_name"][$i] . "', '" . $file_path . "',
                    '" . $_POST["false_trigger"][$i] . "')";

                    $result = $db->exec($query);
                    if (!$result) {
                        echo "The data could not be inserted into the database: " . $db->lastErrorMsg() . "\n";
                    } else {
                        echo "The record was created successfully.\n";
                    }

                    if ($first) {
                        $query = "INSERT INTO update_log (date, time)
                        VALUES ('" . $current_date . "', '" . $current_time[$i] . "')";
                        $result = $db->exec($query);
                        $first = false;
                    }
                } else {
                    echo "The image file does not exist.\n";
                }
            }
            $db->close();
        }
    } else {
        echo "The incorrect API key was provided\n";
    }
} else {
    echo "No data was received.\n";
}

function format_data($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
