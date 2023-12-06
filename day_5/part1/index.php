<?php
include_once "./../../functions.php";
error_reporting(E_ERROR | E_PARSE);
CONST NUMBER_OF_MAPS = 7;

$input_file_name = "./../input.txt";
$input_handle = fopen($input_file_name, "r");
$contents = fread($input_handle, filesize($input_file_name));
fclose($input_handle);
$lines = remove_empty(explode("\n", $contents));

clASS CustomMap {
    private array $ranges; // [dest_start, src_start, len]

    public function __construct() {
        $this->ranges = [];
    }

    public function put($destination_range_start, $source_range_start, $range_length) {
        $this->ranges[] = [$destination_range_start, $source_range_start, $range_length];
    }

    public function get($key) {
        forEach ($this->ranges as $range) {
            $destination_range_start = $range[0];
            $source_range_start = $range[1];
            $range_length = $range[2];
            if ($source_range_start <= $key && $key < ($source_range_start + $range_length)) {
                $offset = $key - $source_range_start;
                return $destination_range_start + $offset;
            }
        }
        return null;
    }
}



$maps = [];
for ($i = NUMBER_OF_MAPS; $i --> 0;) {
    $maps[] = new CustomMap();
}


function map_seed_to_location($seed) {
    global $maps;
    $current = $seed;
    for ($i = 0; $i < NUMBER_OF_MAPS; $i++) {
        $current = $maps[$i]->get($current) ?? $current;
    }
    return $current;
}

$num_lines = count($lines);
$seeds = etoi(" ", lstrip($lines[0], "seeds: "));
$map_index = -1; // Will immediately become 0 when we read the first map

for ($i = 1; $i < $num_lines; $i++) {
    $line = $lines[$i];
    if (str_contains($line, "map")) {
        $map_index++;
        continue;
    }
    $nums = etoi(" ", $line);
    $maps[$map_index]->put($nums[0], $nums[1], $nums[2]);
}

$location_current_min = 2147483647;
forEach ($seeds as $seed) {
    $location_current_min = min($location_current_min, map_seed_to_location($seed));
}

echo $location_current_min;
