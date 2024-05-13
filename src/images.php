<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DrongoCam Server</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script defer src="js/bootstrap.bundle.min.js"></script>

    <?php
    if (isset($_GET["download_image"], $_GET["file_path"])) {
        $file_path = $_GET["file_path"];

        header("Content-Description: File Transfer");
        header("Content-Type: image/jpg");
        header("Content-Disposition: attachment; filename=" . basename($file_path));
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Pragma: no-cache");
        header("Content-Length: " . filesize($file_path));
        ob_clean();
        flush();
        readfile($file_path);
    }
    if (isset($_GET["delete_image"], $_GET["file_path"])) {
        $file_path = $_GET["file_path"];

        $db = new SQLite3("database/main.sqlite");
        $query = "DELETE FROM images WHERE file_path = '" . $file_path . "';";
        $result = $db->exec($query);
        $db->close();
        unlink($file_path);
    }
    ?>
</head>

<body>
    <div class="container-fluid p-3 bg-black text-white text-center fw-bold">
        <h1>Welcome to the DrongoCam Server</h1>
    </div>

    <ul class="nav nav-tabs nav-justified bg-dark navbar-dark text-black fw-bold">
        <li class="nav-item">
            <a class="nav-link" href="home.php">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="images.php">Images</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href=data.php>Sensor Data</a>
        </li>
    </ul>

    <div class="container p-1">
        <form action="images.php">
            <div class="row mb-3 mt-3">
                <div class="col">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="text" class="form-control" id="start_date" placeholder="YYYY-MM-DD" name="start_date">
                </div>
                <div class="col">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="text" class="form-control" id="end_date" placeholder="YYYY-MM-DD" name="end_date">
                </div>
            </div>
            <div class="row mb-3 mt-3">
                <div class="col">
                    <label for="start_time" class="form-label">Start Time:</label>
                    <input type="text" class="form-control" id="start_time" placeholder="HH:MM:SS" name="start_time">
                </div>
                <div class="col">
                    <label for="end_time" class="form-label">End Time:</label>
                    <input type="text" class="form-control" id="end_time" placeholder="HH:MM:SS" name="end_time">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div class="table-responsive">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>Image Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>False Trigger</th>
                        <th>Download</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET["start_date"], $_GET["end_date"], $_GET["start_time"], $_GET["end_time"])) {
                        $start_date = strtotime($_GET["start_date"]);
                        $end_date = strtotime($_GET["end_date"]);
                        $start_time = strtotime($_GET["start_time"]);
                        $end_time = strtotime($_GET["end_time"]);
                    } else {
                        $start_date = 0;
                        $end_date = 0;
                        $start_time = 0;
                        $end_time = 0;
                    }

                    $db = new SQLite3("database/main.sqlite");

                    if ($start_date == 0) {
                        $start_date = $db->querySingle("SELECT MIN(date) FROM images;");
                    } else {
                        $start_date = date("Y-m-d", $start_date);
                    }

                    if ($end_date == 0) {
                        $end_date = $db->querySingle("SELECT MAX(date) FROM images;");
                    } else {
                        $end_date = date("Y-m-d", $end_date);
                    }

                    if ($start_time == 0) {
                        $start_time = $db->querySingle("SELECT MIN(time) FROM images;");
                    } else {
                        $start_time = date("H:i:s", $start_time);
                    }

                    if ($end_time == 0) {
                        $end_time = $db->querySingle("SELECT MAX(time) FROM images;");
                    } else {
                        $end_time = date("H:i:s", $end_time);
                    }

                    $query = "SELECT * FROM images WHERE (date BETWEEN '" . $start_date . "' AND '" . $end_date . "') AND (time BETWEEN '" . $start_time . "' AND '" . $end_time . "') ORDER BY date DESC, time Desc;";
                    $result = $db->query($query);

                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        $image_id = $row["image_id"];
                        $date = strtotime($row["date"]);
                        $time = strtotime($row["time"]);
                        $file_name = $row["file_name"];
                        $file_path = $row["file_path"];
                        $false_trigger = $row["false_trigger"];

                        if ($false_trigger) {
                            $false_trigger = "Yes";
                        } else {
                            $false_trigger = "No";
                        }

                        echo "<tr> 
                                <td>" . $file_name . "</td>
                                <td>" . date("Y-m-d", $date) . "</td> 
                                <td>" . date("H:i:s", $time) . "</td>
                                <td>" . $false_trigger . "</td> 
                                <td> <a class='btn btn-success' href='images.php?download_image=1&file_path=" . $file_path . "'>Download</a> </td>
                                <td> <a class='btn btn-danger' href='images.php?delete_image=1&file_path=" . $file_path . "'>Delete</a> </td>
                            </tr>";
                    }
                    $db->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>


</body>

</html>