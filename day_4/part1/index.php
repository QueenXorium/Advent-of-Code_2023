<?php
error_reporting(E_ERROR | E_PARSE);

$input_file_name = "./../input.txt";
$input_handle = fopen($input_file_name, "r");
$contents = fread($input_handle, filesize($input_file_name));
fclose($input_handle);
$lines = explode("\n", $contents);

function remove_empty($array): array {
    $filtered = [];
    foreach ($array as $yn) {
        if ($yn !== "") {
            $filtered[] = $yn;
        }
    }
    return $filtered;
}


$sum = 0;

forEach ($lines as $line) {
    $parts1 = explode(" | ", $line);
    $winning_data = explode(": ",$parts1[0])[1];
    $your_cards = $parts1[1];
    $winning_numbers = remove_empty(preg_split('/\s+/', $winning_data));
    $your_numbers = remove_empty(preg_split('/\s+/', $your_cards));

    $my_remaining_cards = array_diff($your_numbers, $winning_numbers);
    $number_of_winning_cards = count($your_numbers) - count($my_remaining_cards);
    if ($number_of_winning_cards > 0) {
        $sum += pow(2, $number_of_winning_cards-1);
    }
}
echo $sum;