<?php

function filter_numbers(string $text): string {
    $codepoint_0 = 48;
    $codepoint_9 = 57;
    $filtered_result = "";
    $text_size = strlen($text);
    for ($i = 0; $i < $text_size; $i++) {
        $v = $text[$i];
        $codepoint = ord($v);
        if ($codepoint_0 <= $codepoint && $codepoint <= $codepoint_9) {
            $filtered_result .= $v;
        }
    }
    return $filtered_result;
}

$input_file_name = "./../input.txt";
$input_handle = fopen($input_file_name, "r");
$contents = fread($input_handle, filesize($input_file_name));
fclose($input_handle);

$lines = explode("\n", $contents);
$sum = 0;
forEach ($lines as $line) {
    $filtered_numbers = filter_numbers($line);
    if (strlen($filtered_numbers) > 0) {
        $sum += (int)($filtered_numbers[0] . $filtered_numbers[-1]);
    }
}

echo $sum;
