<?php

function get_game_power(string $gamedata): int {
    $red_max = 0;
    $green_max = 0;
    $blue_max = 0;

    $first_group = explode(": ", $gamedata);
    $sets = explode("; ", $first_group[1]);

    forEach ($sets as $set) {
        $colors = explode(", ", $set);
        forEach ($colors as $color_data) {
            $color_parts = explode(" ", $color_data);
            $var_max =  $color_parts[1] . "_max";
            $$var_max = max($color_parts[0],  $$var_max); # LOL
        }
    }
    return $red_max * $green_max * $blue_max;
}

$input_file_name = "./../input.txt";
$input_handle = fopen($input_file_name, "r");
$contents = fread($input_handle, filesize($input_file_name));
fclose($input_handle);

$lines = explode("\n", $contents);
$sum = 0;
forEach ($lines as $line) {
    $sum += get_game_power($line);
}

echo $sum;
