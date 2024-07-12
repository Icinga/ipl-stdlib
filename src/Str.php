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
     * @param ?string $subject
     *
     * @return string
     */
    public static function camel(?string $subject)
    {
        if ($subject === null) {
            return '';
        }

        $normalized = str_replace(['-', '_'], ' ', $subject);

        return lcfirst(str_replace(' ', '', ucwords(strtolower($normalized))));
    }

    /**
     * Check if the given string starts with the specified substring
     *
     * @param ?string $subject
     * @param string $start
     * @param bool   $caseSensitive
     *
     * @return bool
     */
    public static function startsWith(?string $subject, string $start, bool $caseSensitive = true)
    {
        $subject = $subject ?? '';
        if (! $caseSensitive) {
            return strncasecmp($subject, $start, strlen($start)) === 0;
        }

        return substr($subject, 0, strlen($start)) === $start;
    }

    /**
     * Split string into an array padded to the size specified by limit
     *
     * This method is a perfect fit if you need default values for symmetric array destructuring.
     *
     * @param ?string $subject
     * @param string $delimiter
     * @param int    $limit
     * @param ?string $default
     *
     * @return array<int, ?string>
     */
    public static function symmetricSplit(?string $subject, string $delimiter, int $limit, ?string $default = null)
    {
        if ($subject === null || empty($delimiter)) {
            return array_pad([], $limit, $default);
        }

        return array_pad(explode($delimiter, $subject, $limit), $limit, $default);
    }

    /**
     * Split string into an array and trim spaces
     *
     * @param ?string $subject
     * @param string $delimiter
     * @param ?int    $limit
     *
     * @return array<string>
     */
    public static function trimSplit(?string $subject, string $delimiter = ',', int $limit = null)
    {
        if ($subject === null || empty($delimiter)) {
            return [];
        }

        if ($limit !== null) {
            $exploded = explode($delimiter, $subject, $limit);
        } else {
            $exploded = explode($delimiter, $subject);
        }

        return array_map('trim', $exploded);
    }

    /**
     * Str::limit method truncates the given string to the specified length.
     *
     * This method will replace the truncated part with an ellipsis.
     *
     * @param   string  $subject
     * @param   ?int    $limit Length of the string that will be returned
     * @param   ?string $end Ellipsis that will be added to the truncated string
     *
     * @return  string
     */
    public static function limit(string $subject, ?int $limit = 50, ?string $end = '...'): string
    {
        // If the string is smaller or equal to the limit we simply return it
        if (mb_strwidth($subject, 'UTF-8') <= $limit) {
            return $subject;
        }

        // If the string is longer than the limit we truncate it
        // and add the given end to it.
        return mb_strimwidth($subject, 0, $limit, '', 'UTF-8') . $end;
    }
}
