<?php

namespace App\Helpers;

/**
 * Class ArrayHelper
 * @package App\Helpers
 */
class ArrayHelper
{
    /**
     * @param  array  $array
     * @param  string  $prefix
     *
     * @return array
     */
    public static function flatten(array $array, $prefix = ''): array
    {
        $result = [];
        array_walk($array, static function ($value, $key) use ($prefix, &$result) {
            $path = $prefix ? "$prefix.$key" : $key;
            if (is_array($value)) {
                $result = array_merge($result, self::flatten($value, $path));
            } else {
                $result[$path] = $value;
            }
        });

        return $result;
    }

    /**
     * @param  string  $path
     * @param $value
     * @param  string  $keySeparator
     *
     * @return array
     */
    public static function setValueForPath(string $path, $value, string $keySeparator = '.'): array
    {
        $pathsTree = [];

        $pathParts = array_reverse(explode($keySeparator, $path));
        $pathTree = [];

        foreach ($pathParts as $key => $pathPart) {
            if (empty($pathPart)) {
                continue;
            }

            $pathTree = ($key === 0 ? [$pathPart => $value] : [$pathPart => $pathTree]);
        }

        return array_merge_recursive($pathsTree, $pathTree);
    }
}
