<?php

namespace Weerd\VeritasLogs;

class Parser
{
    /**
     * Discard excess log data prior to closest log timestamp.
     *
     * @param  string $string
     * @return null|string
     */
    public function discardExcess($string)
    {
        $matches = $this->matchTimestamp($string);

        if (! $matches) {
            return null;
        }

        $startPosition = strpos($string, array_shift($matches));

        return substr($string, $startPosition);
    }

    /**
     * Match regex pattern for log level indicator.
     *
     * @param  string $string
     * @return array
     */
    public function matchLevel($string)
    {
        preg_match("/[A-Za-z]+\.[A-Za-z]+:/", $string, $output);

        return $output;
    }

    /**
     * Match regex pattern for stacktrace heading.
     *
     * @param  string $string
     * @return array
     */
    public function matchStacktraceHeading($string)
    {
        preg_match("/\[?[sS]tack\s?[tT]race\]?:?/", $string, $output);

        return $output;
    }

    /**
     * Match regex pattern for stacktrace output.
     *
     * @param  string $string
     * @return array
     */
    public function matchStacktraceOutput($string)
    {
        preg_match("/#\d+/", $string, $output);

        return $output;
    }

    /**
     * Match regex pattern for log timestamp.
     *
     * @param  string $string
     * @return array
     */
    public function matchTimestamp($string)
    {
        preg_match("/\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\]/", $string, $output);

        return $output;
    }
}
