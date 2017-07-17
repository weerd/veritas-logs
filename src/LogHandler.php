<?php

namespace Weerd\VeritasLogs;

use Illuminate\Support\Facades\Log;

class LogHandler
{
    /**
     * [$maxlength description]
     * @var [type]
     */
    protected $maxlength;

    /**
     * [$path description]
     * @var [type]
     */
    protected $path;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->maxlength = -(config('veritaslogs.maxlength'));

        $this->path = storage_path('logs/laravel.log');
    }

    /**
     * [get description]
     * @return [type] [description]
     */
    public function get()
    {
        $data = $this->find();

        if (! $data || empty($data)) {
            return null;
        }

        return $this->parse($data);
    }

    /**
     * [find description]
     * @return [type] [description]
     */
    protected function find()
    {
        if (! file_exists($this->path)) {
            return null;
        }

        try {
            return file_get_contents($this->path, false, null, $this->maxlength);
        } catch (\ErrorException $error) {
            Log::error($error);

            return file_get_contents($this->path, false, null);
        }
    }

    /**
     * [parse description]
     * @return [type] [description]
     */
    protected function parse($data)
    {
        $logs = [];
        $currentLog = 0;
        $key = null;

        $lines = preg_split("/\\n/", $data);

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
                }
            }
        };

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
