<?php

namespace Weerd\VeritasLogs;

use Illuminate\Support\Facades\Log;

class LogHandler
{
    /**
     * Maximum length of data (in characters) to read.
     *
     * @var string
     */
    protected $maxlength;

    /**
     * Path to the log file location.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a Log Handler instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxlength = -(config('veritaslogs.maxlength'));
        // $this->maxlength = config('veritaslogs.maxlength') ? -(config('veritaslogs.maxlength')) : 10000;

        $this->path = storage_path('logs/laravel.log');
    }

    /**
     * [get description]
     * @return [type] [description]
     */
    public function get()
    {
        // dump($this->maxlength);

        $data = read_log_file_contents($this->path, $this->maxlength);

        if (! $data || empty($data)) {
            return null;
        }

        // @TODO: need to clean up recursive function logic!
        $data = $this->parse($data);

        if (! $data) {
            // dump('nope');
            return $this->expandedGet();
        }

        // dd($data);

        return $data;
    }

    protected function expandedGet()
    {
        $this->maxlength = $this->maxlength * 2;

        return $this->get();
    }

    /**
     * [parse description]
     * @return [type] [description]
     */
    protected function parse($data)
    {
        $logs = [];
        $output = [];
        $currentLog = 0;
        $key = null;

        $lines = preg_split("/\\n/", $data);

        // dd($lines);

        foreach ($lines as $line) {

            $timestamp = $this->matchTimestamp($line);

            if ($timestamp) {
                $currentLog += 1;
                $key = 'log'.$currentLog;

                $logLevel = $this->matchLogLevel($line);

                $logs[$key]['timestamp'] = array_shift($timestamp);
                $logs[$key]['logLevel'] = array_shift($logLevel);
                $logs[$key]['message'] = trim(str_replace([$logs[$key]['timestamp'], $logs[$key]['logLevel']], '', $line));

                continue;
            }

            if ($currentLog) {
                $stacktraceItem = $this->matchStacktraceItem($line);

                if ($stacktraceItem) {
                    $logs[$key]['stacktrace'][] = $line;

                    continue;
                }
            }

            if ($currentLog) {
                $output[] = $line;
            }
        };

        if ($output) {
            array_unshift($output, $logs[$key]['message']);

            $logs[$key]['output'] = implode("\r\n" , $output);

            unset($logs[$key]['message']);
        }

        // dd($logs);

        return $logs;
    }

    protected function matchTimestamp($string)
    {
        $output = [];

        preg_match("/\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\]/", $string, $output);

        return $output;
    }

    protected function matchLogLevel($string)
    {
        $output = [];

        preg_match("/[A-Za-z]+\.[A-Za-z]+:/", $string, $output);

        return $output;
    }

    protected function matchStacktraceItem($string)
    {
        return preg_match("/#\d+/", $string);
    }
}
