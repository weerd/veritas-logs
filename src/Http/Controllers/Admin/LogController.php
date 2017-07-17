<?php

namespace Weerd\VeritasLogs\Http\Controllers\Admin;

use Weerd\VeritasLogs\LogHandler;
use Weerd\VeritasLogs\Http\Controllers\Controller as BaseController;

class LogController extends BaseController
{
    protected $logHandler;

    public function __construct(LogHandler $logHandler)
    {
        $middlewares = ['web'];

        if (! config('app.env') === 'local') {
            $middlewares[] = 'auth';
        }

        $this->middleware($middlewares);

        $this->logHandler = $logHandler;
    }

    /**
     * [__invoke description]
     *
     * @return \Illuminate\Http\Response
     * @todo Refactor this into smaller methods.
     */
    public function __invoke()
    {
        // Only handling `single` log file setting for now.
        if (! config('app.log') === 'single') {
            return 'Sorry, the current version of Veritas Logs only handles the "single" log file setting in "config/app.php".';
        }

        $logs = $this->logHandler->get();

        if ($logs) {
            $logs = collect($logs)->reverse();
        }

        return view('veritas-logs::logs.admin.show', ['logs' => $logs]);
    }
}
