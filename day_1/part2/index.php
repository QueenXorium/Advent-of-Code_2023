<?php

$word_to_number_map = [
    "one" => 1,
    "two" => 2,
    "three" => 3,
    "four" => 4,
    "five" => 5,
    "six" => 6,
    "seven" => 7,
    "eight" => 8,
    "nine" => 9
];

function get_calibration_value(string $text): int {
    global $word_to_number_map; // Global for minor efficiency improvement lol
    $codepoint_0 = 48;
    $codepoint_9 = 57;
    $matches = [];
    $text_size = strlen($text);
    FOR ($i = 0; $i < $text_size; $i++):
        // If it is a number do fast match and continue on
        $v = $text[$i];
        $codepoint = ord($v);
        IF ($codepoint_0 <= $codepoint && $codepoint <= $codepoint_9):
            $matches[] = $codepoint - $codepoint_0;
            continue;
        ENDIF;

        // Check if any words are in range and equal to a slice
        FOREACH ($word_to_number_map as $word => $number):
            $word_length = strlen($word);
            IF ($i + $word_length <= $text_size):
                $text_slice = substr($text, $i, $word_length);
                IF ($text_slice === $word):
                    $matches[] = $number;
                    break;
                ENDIF;
            ENDIF;
        ENDFOREACH;
    ENDFOR;

    $number_of_matches = count($matches);
    IF ($number_of_matches == 0):
        return 0;
    ENDIF;

    return ($matches[0] * 10) + $matches[$number_of_matches - 1];
}

$input_file_name = "./../input.txt";
$input_handle = fopen($input_file_name, "r");
$contents = fread($input_handle, filesize($input_file_name));
fclose($input_handle);

$lines = explode("\n", $contents);
$sum = 0;
forEach ($lines as $line) {
    $sum += get_calibration_value($line);
}

echo $sum;
