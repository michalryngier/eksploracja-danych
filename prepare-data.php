<?php

$handler = fopen('./abalone.data', 'r');


$rowCount = 0;
while (($line = fgets($handler)) !== false) {
    $rowCount++;   
}

file_put_contents('./abalone.csv', '');
$csvHandler = fopen('./abalone.csv', 'r+');
rewind($handler);

// Create csv for future calculations
for ($i = 0; $i < $rowCount; $i++) {
    $row = explode(',', fgets($handler));
    fputcsv($csvHandler, $row);
}

fclose($csvHandler);

// Wrong data
$csvHandler = fopen('./abalone.csv', 'r+');

file_put_contents('./abalone-output-1.csv', '');
$outputCsv = fopen('./abalone-output-1.csv', 'r+');

// Get numeric columns
$row = fgetcsv($csvHandler);
$numericColumns = [];
foreach ($row as $column => $value) {
    if (is_numeric($value)) {
        $numericColumns[] = $column;
    }
}
rewind($csvHandler);

$errorDataForColumn = [];
foreach ($numericColumns as $col) {
    $columnValues = [];

    for ($i = 0; $i < $rowCount; $i++) {
        $row = fgetcsv($csvHandler);
        $columnValues[] = $row[$col];
    }

    $q3 = stats_stat_percentile($columnValues, 3);
    $sd = stats_standard_deviation($columnValues);

    $upper = $q3 + 3 * $sd;
    $errorDataForColumn[$col] = $upper * 3;

    rewind($csvHandler);
}

$errorDataCounter = 0;

for ($i = 0; $i < $rowCount; $i++) {
    $row = fgetcsv($csvHandler);
    $column = rand(0, count($numericColumns) - 1);

    if (frand(0, 1, 3) < 0.05) {
        $errorDataCounter++;
        $row[$numericColumns[$column]] = $errorDataForColumn[$numericColumns[$column]];
    }

    fputcsv($outputCsv, $row);
}

echo "Errors $errorDataCounter: " . ($errorDataCounter * 100 / $rowCount) . '%' . PHP_EOL;
fclose($handler);
fclose($csvHandler);

// Random empty data
$csvHandler = fopen('./abalone-output-1.csv', 'r+');

file_put_contents('./abalone-output-2.csv', '');
$outputCsv = fopen('./abalone-output-2.csv', 'r+');

$emptyDataCount = 0;
for ($i = 0; $i < $rowCount; $i++) {
    $row = fgetcsv($csvHandler);

    if (frand(0, 1, 3) < 0.05) {
        $randCol = rand(0, count($row) - 1);
        $row[$randCol] = '';
        $emptyDataCount++;
    }

    fputcsv($outputCsv, $row);
}

echo "Empty $emptyDataCount: " . ($emptyDataCount * 100 / $rowCount) . '%' . PHP_EOL;

fclose($outputCsv);
fclose($csvHandler);


function frand($min, $max, $decimals = 0) {
    $scale = pow(10, $decimals);
    return mt_rand($min * $scale, $max * $scale) / $scale;
}