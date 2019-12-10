<?php

namespace ipl\Stdlib;

/**
 * Collection of string manipulation functions
 */
class Str
{
    /**
     * Convert the given string to camel case
     *
     * The given string may be delimited by the following characters: '_' (underscore), '-' (dash), ' ' (space).
     *
     * @param string $subject
     *
     * @return string
     */
    public static function camel($subject)
    {
        $normalized = str_replace(['-', '_'], ' ', $subject);

        return lcfirst(str_replace(' ', '', ucwords(strtolower($normalized))));
    }

    /**
     * Split string into an array and trim spaces
     *
     * @param string $subject
     * @param string $delimiter
     * @param int    $limit
     *
     * @return array
     */
    public static function trimSplit($subject, $delimiter = ',', $limit = null)
    {
        if ($limit !== null) {
            $exploded = explode($delimiter, $subject, $limit);
        } else {
            $exploded = explode($delimiter, $subject);
        }

        return array_map('trim', $exploded);
    }
}
