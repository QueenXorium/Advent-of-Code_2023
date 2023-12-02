<?php

function get_score(string $gamedata): int {
    $max_counts = [
        "red" => 12,
        "green" => 13,
        "blue" => 14
    ];

    $first_group = explode(": ", $gamedata);
    $game_number = (int) (explode("Game ", $first_group[0])[1]);
    $sets = explode("; ", $first_group[1]);

    $impossible = false;
    forEach ($sets as $set) {
        $colors = explode(", ", $set);
        forEach ($colors as $color_data) {
            $color_parts = explode(" ", $color_data);
            $amount = $color_parts[0];
            $color = $color_parts[1];
            if ($amount > $max_counts[$color]) {
                $impossible = true;
                break;
            }
        }
    }
    return $impossible ? 0 : $game_number;
}

$input_file_name = "./../input.txt";
$input_handle = fopen($input_file_name, "r");
$contents = fread($input_handle, filesize($input_file_name));
fclose($input_handle);

$lines = explode("\n", $contents);
$sum = 0;
forEach ($lines as $line) {
    $sum += get_score($line);
}

echo $sum;
