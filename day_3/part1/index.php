<?php
error_reporting(E_ERROR | E_PARSE);
function array_2d($row_count = 1, $column_count = 1, $default_value = 0) {
    $result = [];
    for ($r = 0; $r < $row_count; ++$r) {
        $result[] = [];
        $outer_array = &$result[$r];
        for ($c = 0; $c < $column_count; ++$c) {
            $outer_array[] = $default_value;
        }
    }
    return $result;
}

/**
 * Assumes $row_max and $col_max are 0
 * @param $row1 int inclusive start row
 * @param $col1 int inclusive start column
 * @param $row2 int inclusive end row
 * @param $col2 int inclusive end column
 * @param $row_max int number of rows, this is the first row index out of bounds
 * @param $col_max int number of columns, this is the first col index out of bounds
 * @return array
 */
function get_border_coordinates($row1, $col1, $row2, $col2, $row_max, $col_max) {
    $row_min = 0;
    $col_min = 0;

    $unbounded_coords = []; // int[][]

    // Corners
    $unbounded_coords[] = [$row1-1, $col1-1]; // Top Left
    $unbounded_coords[] = [$row1-1, $col2+1]; // Top Right
    $unbounded_coords[] = [$row2+1, $col1-1]; // Bottom Left
    $unbounded_coords[] = [$row2+1, $col2+1]; // Bottom Right

    // Top and Bottom
    for ($col = $col1; $col <= $col2; ++$col) {
        $unbounded_coords[] = [$row1-1, $col]; // Top
        $unbounded_coords[] = [$row2+1, $col]; // Bottom
    }

    // Left and Right
    for ($row = $row1; $row <= $row2; ++$row) {
        $unbounded_coords[] = [$row, $col1-1]; // Left
        $unbounded_coords[] = [$row, $col2+1]; // Right
    }

    // Filter coordinates inside global boundaries
    $bounded_coords = [];
    forEach ($unbounded_coords as $coord) {
        $row_index = $coord[0];
        $col_index = $coord[1];
        $within_bounds = ($row_min <= $row_index && $row_index < $row_max) && ($col_min <= $col_index && $col_index < $col_max);
        if ($within_bounds) {
            $bounded_coords[] = $coord;
        }
    }
    return $bounded_coords;
}

$input_file_name = "./../input.txt";
$input_handle = fopen($input_file_name, "r");
$contents = fread($input_handle, filesize($input_file_name));
fclose($input_handle);
$lines = explode("\n", $contents);

define("NUMBER_OF_ROWS", count($lines));
define("NUMBER_OF_COLUMNS", strlen($lines[0]));
const LAST_COLUMN_INDEX = NUMBER_OF_COLUMNS - 1;


$text = array_2d(NUMBER_OF_ROWS, NUMBER_OF_COLUMNS); // char[][]
$is_symbol = array_2d(NUMBER_OF_ROWS, NUMBER_OF_COLUMNS, FALSE); // boolean[][]
$is_number = array_2d(NUMBER_OF_ROWS, NUMBER_OF_COLUMNS, FALSE); // boolean[][]

forEach ($lines as $row => $line) {
    $is_symbol[] = [];
    $is_number[] = [];

    for ($col = 0; $col < NUMBER_OF_COLUMNS; $col++) {
        $char = $line[$col];
        $codepoint = ord($char);

        $char_is_number = 48 <= $codepoint && $codepoint <= 57;
        $char_is_period = 46 == $codepoint;

        $text[$row][$col] = $char;
        $is_number[$row][$col] = $char_is_number;
        $is_symbol[$row][$col] = !($char_is_period || $char_is_number);
    }
}

$number_metadata = []; // value, row, col_start, col_end;

for ($r = 0; $r < NUMBER_OF_ROWS; ++$r) {
    $number_start = -1;
    $number_end = -1;
    for ($c = 0; $c < NUMBER_OF_COLUMNS; ++$c) {
        $current_char = $text[$r][$c];
        $char_is_number = $is_number[$r][$c];
        if ($char_is_number) {
            if ($number_start == -1) {
                $number_start = $c;
            }
            $number_end = $c;
        }
        if (!$char_is_number || ($c == LAST_COLUMN_INDEX))  { // case: End of number
            // If current streak (so break it)
            if ($number_start != -1) {
                $num_val = intval(substr($lines[$r], $number_start, $number_end - $number_start + 1));
                $number_metadata[] = [
                    "value" => $num_val,
                    "row" => $r,
                    "col_start" => $number_start,
                    "col_end" => $number_end
                ];
                $number_start = -1;
                $number_end = -1;
            }
        }
    }
}

function is_part_number(array $num_data): bool {
    global $text, $is_symbol, $is_number;
    $r = $num_data["row"];
    $left_col = $num_data["col_start"];
    $right_col = $num_data["col_end"];
    $coords = get_border_coordinates($num_data["row"], $left_col, $num_data["row"], $right_col, NUMBER_OF_ROWS, NUMBER_OF_COLUMNS);
    forEach ($coords as $coord) {
        if ($is_symbol[$coord[0]][$coord[1]]) {
            return true;
        }
    }
    return false;
}


$sum = 0;
forEach ($number_metadata as $metadatum) {
    if (is_part_number($metadatum)) {
        $sum += $metadatum["value"];
//        echo $metadatum["value"] . "\n";
    }
}

echo $sum . "\n";