<?php

namespace ipl\Stdlib;

use InvalidArgumentException;
use Traversable;
use stdClass;

/**
 * Detect and return the PHP type of the given subject
 *
 * If subject is an object, the name of the object's class is returned, otherwise the subject's type.
 *
 * @param   $subject
 *
 * @return  string
 */
function get_php_type($subject)
{
    if (is_object($subject)) {
        return get_class($subject);
    } else {
        return gettype($subject);
    }
}

/**
 * Get the array value of the given subject
 *
 * @param   array|object|Traversable   $subject
 *
 * @return  array
 *
 * @throws  InvalidArgumentException   If subject type is invalid
 */
function arrayval($subject)
{
    if (is_array($subject)) {
        return $subject;
    }

    if ($subject instanceof stdClass) {
        return (array) $subject;
    }

    if ($subject instanceof Traversable) {
        // Works for generators too
        return iterator_to_array($subject);
    }

    throw new InvalidArgumentException(sprintf(
        'arrayval expects arrays, objects or instances of Traversable. Got %s instead.',
        get_php_type($subject)
    ));
}

/**
 * Format the given input array as CSV string and return it
 *
 * The input array is always expected to be an array of rows.
 * The keys of the first row will be automatically used as the header row.
 *
 * @param   iterable    $data
 * @param   string      $delimiter  Field delimiter
 * @param   string      $enclosure  Field enclosure
 * @param   string      $escape     Escape character
 *
 * @return  string
 *
 * @throws  \InvalidArgumentException
 */
function str_putcsv($data, $delimiter = ',', $enclosure = '"', $escape = '\\')
{
    $fp = fopen('php://temp', 'r+b');

    if (! is_iterable($data)) {
        throw new \InvalidArgumentException(sprintf(
            'str_putcsv expects arrays or instances of Traversable. Got %s instead.',
            get_php_type($data)
        ));
    }

    foreach ($data as $row) {
        fputcsv($fp, array_keys($row), $delimiter, $enclosure, $escape);

        break;
    }

    foreach ($data as $row) {
        fputcsv($fp, $row, $delimiter, $enclosure, $escape);
    }

    rewind($fp);
    $csv = stream_get_contents($fp);
    fclose($fp);
    $csv = rtrim($csv, "\n"); // fputcsv adds a newline

    return $csv;
}
