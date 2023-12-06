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

    public function getRanges() {
        $out = [];
        forEach ($this->ranges as $range) {
            $out[] = [
                "source_start" => $range[1],
                "destination_start" => $range[0],
                "length" => $range[2]
            ];
        }
        return $out;
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

class ClosedInterval {
    public int $low;
    public int $high;

    public static function ofLength($low, $length) {
        return new ClosedInterval($low, $low + $length - 1);
    }

    public function __construct($low, $high) {
        $this->low = $low;
        $this->high = $high;
    }

    public static function relativeComplement(ClosedInterval $a, ClosedInterval $b) {
        return ClosedInterval::difference($a, $b);
    }

    /**
     * Return 0, 1, or 2 ranges in $a that are not present in $b. AKA The relativeComplement
     * @param ClosedInterval $a
     * @param ClosedInterval $b
     * @return void
     */
    public static function difference(ClosedInterval $a, ClosedInterval $b): array {
        /* case where A is <= B
        BBBBBB
        AAAAAA
        or
        BBBBBBB
          AA    */
        if ($a->isSubsetOf($b)) {
            return [];
        }

        // case left part of A extends over
        //    BBBBBBB
        // AAAAAAAAAAAA
        // ^^^
        $ranges = [];
        if ($a->low < $b->low) {
            $ranges[] = new ClosedInterval($a->low, $b->low - 1);
        }

        // case right part of A extends over
        //    BBBBBBB
        // AAAAAAAAAAAA
        //           ^^
        if ($a->high > $b->high) {
            $ranges[] = new ClosedInterval($b->high + 1, $a->high);
        }
        return $ranges;
    }

    /**
     * Return the intersection of $a and $b or NULL if no intersection.
     * @param ClosedInterval $a
     * @param ClosedInterval $b
     * @return ClosedInterval|null
     */
    public static function intersection(ClosedInterval $a, ClosedInterval $b): ?ClosedInterval {
        $c_low = max($a->low, $b->low);
        $c_high = min($a->high, $b->high);
        if ($c_low <= $c_high) {
            return new ClosedInterval($c_low, $c_high);
        }
        return null;
    }

    public function length(): int {
        return $this->high - $this->low;
    }

    public function isSupersetOf(ClosedInterval $that) {
        return $this->low <= $that->low && $that->high <= $this->high;
    }

    public function isSubsetOf(ClosedInterval $that) {
        return $that->low <= $this->low && $this->high <= $that->high;
    }

    public function contains(int $x) {
        return $this->low <= $x && $x <= $this->high;
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
$initial_seeds = etoi(" ", lstrip($lines[0], "seeds: "));
$initial_number_of_seeds = count($initial_seeds);

$seeds = [];
for ($i = 0; $i < $initial_number_of_seeds; $i += 2) {
    $seeds[] = [$initial_seeds[$i], $initial_seeds[$i+1]];
}

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
$seed_ranges = [];
forEach ($seeds as $seed_data) {
    $seed_ranges[] = ClosedInterval::ofLength($seed_data[0], $seed_data[1]);
}


$function_arguments = $seed_ranges;

forEach ($maps as $map):
    $function_images = [];

    forEach (/*List<ClosedInterval*/ $function_arguments as /*ClosedInterval*/ $function_argument):
        /* List<ClosedInterval> of Ranges of the image */
        $image_ranges = [];

        $function_argument_ranges = [$function_argument];
        while (count($function_argument_ranges)):
            $argument_range = array_pop($function_argument_ranges);
            $found = false;
            forEach ($map->getRanges() as $mapCustomRange):
                $source_start = $mapCustomRange["source_start"];
                $image_start = $mapCustomRange["destination_start"];

                $mapRange = ClosedInterval::ofLength($source_start, $mapCustomRange["length"]);
                $intersection = ClosedInterval::intersection($argument_range, $mapRange);
                $differences = ClosedInterval::difference($argument_range, $mapRange);
                if ($intersection !== null) {
                    $found = true;
                    $map_offset = $intersection->low - $source_start;
                    $image_offset_start = $image_start + $map_offset;
                    $image_ranges[] = new ClosedInterval($image_offset_start, $image_offset_start + $intersection->length());
                    add_all($function_argument_ranges, $differences);
                }
            endForEach;
            /* Base case - no mapping applied */
            if ($found === false) {
                $image_ranges[] = $argument_range;
            }
        endWhile;
        add_all($function_images, $image_ranges);
    endForEach;
    $function_arguments = $function_images;
endForEach;

$current_min_image = 2147483647;
forEach ($function_images as $imageRange) {
    $current_min_image = min($current_min_image, $imageRange->low);
}

echo $current_min_image;
