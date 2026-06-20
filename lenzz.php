<?php
        // http://subinsb.com/convert-bytes-kb-mb-gb-php
        function convertToReadableSize($size) {
                $unit = 1000;
                $base = log($size) / log($unit);
                $suffix = array("", "KB", "MB", "GB", "TB");
                $f_base = floor($base);
                return round(pow($unit, $base - floor($base)), 1) . " " . $suffix[$f_base];
        }
        // Add OpenVPN server status logs here. Only version 2 logs supported.
        $logs[0] = "178.128.22.73:8085/stat/tcp.txt";
        $logs[1] = "178.128.22.73:8085/stat/tcp.txt";
        // -----------------------------
        $CLIENT_LIST = array();
        foreach ($logs as $log) {
                $handle = fopen($log, "r");
                while (!feof($handle)) {
                        $line = fgets($handle);
                        if (substr( $line, 0, 11 ) === "CLIENT_LIST") {
                                array_push($CLIENT_LIST, str_getcsv($line));
                        }
                }
                fclose($handle);
        }
        /* DEBUG PURPOSE
        echo "<pre>";
        print_r($logs);
        print_r($CLIENT_LIST);
        echo "</pre>";
        */
?>
<!-- https://www.w3schools.com/html/html_tables.asp -->
<html>
        <head>
                <meta http-equiv="refresh" content="300">
                <style>
                        table {
                                font-family: arial, sans-serif;
                                border-collapse: collapse;
                                width: 100%;
                        }
                        td, th {
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                        }
                </style>
        </head>
        <body>
                <table>
                        <tr>
                                <th>Name</th>
                                <th>Virtual address</th>
                                <th>Physical address</th>
                                <th>Received</th>
                                <th>Sent</th>
                                <th>Connected since</th>
                        </tr>
<?php
                        foreach ($CLIENT_LIST as $client) {
                                echo "<tr>";
                                echo "<td>$client[1]</td>";
                                echo "<td>$client[3]</td>";
                                echo "<td>", strstr($client[2], ":", true), "</td>";
                                echo "<td>", convertToReadableSize($client[6]), "</td>";
                                echo "<td>", convertToReadableSize($client[5]), "</td>";
                                echo "<td>", date("Y-m-d H:i:s", $client[8]), "</td>";
                                echo "</tr>\n";
                        }
?>
                </table>
                <font face="arial, sans-serif">
                        <center>
                                <br>This page gets reloaded every 5 min.<br>
                                Last update: <b><?php echo date("H:i:s") . " " . date_default_timezone_get() ?></b>
                        </center>
                </font>
        </body>
</html>