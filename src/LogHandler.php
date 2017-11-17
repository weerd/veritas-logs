<?php

namespace Weerd\VeritasLogs;

use Weerd\VeritasLogs\Parser;
use Illuminate\Support\Facades\Log;

class LogHandler
{
    /**
     * Maximum length of data (in characters) to read.
     *
     * @var string
     */
    protected $maxlength;

    protected $parser;

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
    public function __construct(Parser $parser)
    {
        $this->maxlength = -(config('veritaslogs.maxlength'));
        // $this->maxlength = config('veritaslogs.maxlength') ? -(config('veritaslogs.maxlength')) : 10000;

        // laravel, phoenix, empty, short, simple
        $this->path = storage_path('logs/laravel.log');

        $this->parser = $parser;
    }

    /**
     * [get description]
     * @return [type] [description]
     */
    public function get()
    {
        $data = read_log_file_contents($this->path, $this->maxlength);

        if (! $data || empty($data)) {
            return null;
        }

        return $this->format($data);
    }

    /**
     * [format description]
     * @return [type] [description]
     */
    protected function format($data)
    {

        $data = $this->parser->discardExcess($data);

        // dd(['file' => LogHandler::class, 'data' => $data]); // @remove

        $logs = [];
        $output = [];
        $currentLog = 0;
        $key = null;

        $lines = preg_split("/\\n/", $data);

        // dd(['file' => LogHandler::class, 'data' => $lines]); // @remove

        foreach ($lines as $line) {

            $timestamp = $this->parser->matchTimestamp($line);

            if ($timestamp) {
                $currentLog += 1;
                $key = 'log'.$currentLog;

                $level = $this->parser->matchLevel($line);

                $logs[$key]['timestamp'] = array_shift($timestamp);
                $logs[$key]['level'] = array_shift($level);
                $logs[$key]['output'][] = trim(str_replace([$logs[$key]['timestamp'], $logs[$key]['level']], '', $line));

                // dd(['file' => LogHandler::class, 'line' => $line, 'data' => $logs]); // @remove

                continue;
            }

            if ($this->parser->matchStacktraceOutput($line)) {
                $logs[$key]['stacktrace'][] = $line;

                continue;
            }

            if (! $this->parser->matchStacktraceHeading($line)) {
                $logs[$key]['output'][] = $line;
            }
        };

        // $logs = collect($logs)->map(function ($log) {
        //     $log['output'] = implode("\r\n", $log['output']);

        //     return $log;
        // });

        return $logs;
        // return $logs->all();
    }
}
