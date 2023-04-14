<?php

$handler = fopen('./abalone.data', 'r');


$rowCount = 0;
while (($line = fgets($handler)) !== false) {
    $rowCount++;   
}

file_put_contents('./abalone.csv', '');
$csvHandler = fopen('./abalone.csv', 'r+');
rewind($handler);

// set empty data
for ($i = 0; $i < $rowCount; $i++) {
    $row = explode(',', fgets($handler));
    if (rand(0, 10) / 10 < 0.05) {
        $randCol = floor(rand(0, count($row)));
        $row[$randCol] = '';
    }

    fputcsv($csvHandler, $row, ';');
}



// wrong data
for ($i = 0; $i < $rowCount; $i++) {
    $row = explode(',', fgets($handler));
    if (rand(0, 10) / 10 < 0.08) {
        $randCol = floor(rand(0, count($row)));
        if (is_numeric($row[$randCol])) {
            $row[$randCol] = $row[$randCol] * 1000;
        }
    }

    fputcsv($csvHandler, $row, ';');
}


fclose($handler);
fclose($csvHandler);
