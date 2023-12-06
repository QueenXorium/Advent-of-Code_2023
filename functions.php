<?php
function remove_empty($array): array {
    $filtered = [];
    foreach ($array as $yn) {
        if ($yn !== "") {
            $filtered[] = $yn;
        }
    }
    return $filtered;
}

function list_stoi($original_list) {
    $output = [];
    for ($i = 0, $len = count($original_list); $i < $len; $i++) {
        $output[] = (int) $original_list[$i];
    }
    return $output;
}

/**
 * Explode to int
 * @return array of exploded integers
 */
function etoi(string $delimeter, string $input): array {
    return list_stoi(explode($delimeter, $input));
}

function lstrip(string $s, $to_strip): string {
    return substr($s, strlen($to_strip));
}

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

function array_1d($size, $default_value = 0) {
    $result = [];
    for ($r = 0; $r < $size; ++$r) {
        $result[] = $default_value;
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

function add_all(&$result_list, &$elements_to_add) {
    foreach ($elements_to_add as &$e) {
        $result_list[] = &$e;
    }
}