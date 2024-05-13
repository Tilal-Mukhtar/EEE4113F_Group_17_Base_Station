<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DrongoCam Server</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <?php
    if (isset($_GET["start_date"], $_GET["end_date"], $_GET["start_time"], $_GET["end_time"], $_GET["optradio"])) {
        $start_date = strtotime($_GET["start_date"]);
        $end_date = strtotime($_GET["end_date"]);
        $start_time = strtotime($_GET["start_time"]);
        $end_time = strtotime($_GET["end_time"]);
        $option = $_GET["optradio"];
    } else {
        $start_date = 0;
        $end_date = 0;
        $start_time = 0;
        $end_time = 0;
        $option = "option1";
    }

    $db = new SQLite3("database/main.sqlite");

    if ($start_date == 0) {
        $start_date = $db->querySingle("SELECT MIN(date) FROM sensor_data;");
    } else {
        $start_date = date("Y-m-d", $start_date);
    }

    if ($end_date == 0) {
        $end_date = $db->querySingle("SELECT MAX(date) FROM sensor_data;");
    } else {
        $end_date = date("Y-m-d", $end_date);
    }

    if ($start_time == 0) {
        $start_time = $db->querySingle("SELECT MIN(time) FROM sensor_data;");
    } else {
        $start_time = date("H:i:s", $start_time);
    }

    if ($end_time == 0) {
        $end_time = $db->querySingle("SELECT MAX(time) FROM sensor_data;");
    } else {
        $end_time = date("H:i:s", $end_time);
    }

    if ($option == "option3") {
        $query = "DELETE FROM sensor_data WHERE (date BETWEEN '" . $start_date . "' AND '" . $end_date . "') AND (time BETWEEN '" . $start_time . "' AND '" . $end_time . "');";
        $result = $db->exec($query);

        $query = "SELECT * FROM sensor_data ORDER BY date DESC, time DESC;";
    }
    if ($option == "option2") {
        $query = "SELECT * FROM sensor_data WHERE (date BETWEEN '" . $start_date . "' AND '" . $end_date . "') AND (time BETWEEN '" . $start_time . "' AND '" . $end_time . "') ORDER BY date DESC, time Desc;";
        $result = $db->query($query);

        $file_path = "database/sensor_data.csv";
        $file = fopen($file_path, "w");
        $columns = array("sensor_data_id", "date", "time", "temperature (°C)", "humidity (%)", "battery temperature (°C)", "battery humidity (%)", "battery voltage (V)", "battery current (A)");
        fputcsv($file, $columns);
        while ($row = $result->fetchArray(SQLITE3_NUM)) {
            fputcsv($file, $row);
        }
        fclose($file);

        header("Content-Description: File Transfer");
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=sensor_data.csv");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Pragma: no-cache");
        header("Content-Length: " . filesize($file_path));
        ob_clean();
        flush();
        readfile($file_path);

        $query = "SELECT * FROM sensor_data WHERE (date BETWEEN '" . $start_date . "' AND '" . $end_date . "') AND (time BETWEEN '" . $start_time . "' AND '" . $end_time . "') ORDER BY date DESC, time DESC;";
    }
    if ($option == "option1") {
        $query = "SELECT * FROM sensor_data WHERE (date BETWEEN '" . $start_date . "' AND '" . $end_date . "') AND (time BETWEEN '" . $start_time . "' AND '" . $end_time . "') ORDER BY date DESC, time DESC;";
    }
    $db->close();
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
            <a class="nav-link" href="images.php">Images</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href=data.php>Sensor Data</a>
        </li>
    </ul>

    <div class="container p-1">
        <form action="data.php">
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
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" id="radio1" name="optradio" value="option1" checked>Search
                <label class="form-check-label" for="radio1"></label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" id="radio2" name="optradio" value="option2">Download
                <label class="form-check-label" for="radio2"></label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" id="radio3" name="optradio" value="option3">Delete
                <label class="form-check-label" for="radio3"></label>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div class="table-responsive">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>Data Entry</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Temperature (°C)</th>
                        <th>Humidity (%)</th>
                        <th>Battery Temperature (°C)</th>
                        <th>Battery Humidity (%)</th>
                        <th>Battery Voltage (V)</V>
                        </th>
                        <th>Battery Current (A)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $db = new SQLite3("database/main.sqlite");
                    $result = $db->query($query);
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        $sensor_data_id = $row["sensor_data_id"];
                        $date = strtotime($row["date"]);
                        $time = strtotime($row["time"]);
                        $temperature = $row["temperature"];
                        $humidity = $row["humidity"];
                        $battery_temperature = $row["battery_temperature"];
                        $battery_humidity = $row["battery_humidity"];
                        $battery_voltage = $row["battery_voltage"];
                        $battery_current = $row["battery_current"];

                        echo "<tr> 
                                <td>" . $sensor_data_id . "</td>
                                <td>" . date("Y-m-d", $date) . "</td> 
                                <td>" . date("H:i:s", $time) . "</td>
                                <td>" . $temperature . "</td> 
                                <td>" . $humidity . "</td> 
                                <td>" . $battery_temperature . "</td> 
                                <td>" . $battery_humidity . "</td> 
                                <td>" . $battery_voltage . "</td> 
                                <td>" . $battery_current . "</td> 
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