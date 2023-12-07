<?php
include_once "./../../functions.php";
error_reporting(E_ERROR | E_PARSE);

$input_file_name = "./../input.txt";
$input_handle = fopen($input_file_name, "r");
$contents = fread($input_handle, filesize($input_file_name));
fclose($input_handle);
$lines = remove_empty(explode("\n", $contents));

$times = list_stoi(remove_empty(preg_split('/\s+/', lstrip($lines[0], "Time:"))));
$distances = list_stoi(remove_empty(preg_split('/\s+/', lstrip($lines[1], "Distance:"))));

$number_of_races = count($times);

/**
 * @param $t int duration in milliseconds
 * @param $d int record distance in millimeters
 * @return int number of ways to win
 */
function number_of_win_combinations(int $t, int $d): int {
    $discriminant = ($t * $t) - ($d << 2);
    $negative_b_over_2 = $t / 2.0;
    $root_over_2 = sqrt($discriminant) / 2;
    $x1 = $negative_b_over_2 - $root_over_2;
    $x2 = $negative_b_over_2 + $root_over_2;
    $discrete_x1 = ceil($x1);
    $discrete_x2 = floor($x2);
    // Add strictly above the insection edgecase logic (Cannot tie the record, must beat):
    if ($discrete_x1 === $x1) {
        $discrete_x1 += 1;
    }
    if ($discrete_x2 === $x2) {
        $discrete_x2 -= 1;
    }
    return $discrete_x2 - $discrete_x1 + 1;
}


$accumulator = 1;

for ($race_number = 0; $race_number < $number_of_races; $race_number++) {
    $max_time = $times[$race_number];
    $distance_record = $distances[$race_number];
    $combinations = number_of_win_combinations($max_time, $distance_record);
    $accumulator *= $combinations;
}

echo $accumulator;

