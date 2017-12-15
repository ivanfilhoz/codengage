<?php

namespace App\Tool;

class Search
{
    private $list = [];

    public function __construct(array $list)
    {
        $this->list = $list;
    }

    public function byKey(string $term, array $keys)
    {
        return array_filter($this->list, function ($item) use ($term, $keys) {
            foreach ($keys as $key) {
                if (stripos($item[$key], $term) !== false) {
                    return true;
                }
            }
            return false;
        });
    }
}
