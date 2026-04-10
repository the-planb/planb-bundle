<?php

if (!function_exists('iterable_to_array')) {
    function iterable_to_array(iterable $input, bool $preserve_keys = true): array
    {
        if ($input instanceof Traversable) {
            return iterator_to_array($input, $preserve_keys);
        }

        return $preserve_keys ? (array)$input : array_values((array)$input);
    }
}

if (!function_exists('array_flatten')) {
    function array_flatten(iterable $input, int $depth = PHP_INT_MAX): array
    {
        $data = [];

        foreach ($input as $item) {
            $item = is_iterable($item) ? iterable_to_array($item) : $item;

            if (!is_array($item)) {
                $data[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : array_flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $data[] = $value;
                }
            }
        }

        return $data;
    }
}
