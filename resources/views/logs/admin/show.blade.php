{{-- {{ dd($logs) }} --}}
@extends('veritas-logs::layouts.admin.master')

@section('styles')
<style>
    .logs {
        margin: 3rem 0;
    }
    .card + .card {
        margin-top: 2rem;
    }
    .card-block {
        overflow-x: scroll;
    }
    .timestamp {
        color: #888;
        font-size: smaller;
    }
    .stacktrace {
        font-family: Monaco, "Courier New", "Courier";
        font-size: x-small;
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    .stacktrace li {
        /*overflow-x: scroll;*/
        padding: 1rem;
        /*white-space: nowrap;*/
    }
    .stacktrace li + li {
        border-top: 1px solid #ccc;
    }
    .card-header {
        position: relative;
    }
    .card-header [data-toggle="collapse"] {
        text-decoration: none;
    }
    .card-header [data-toggle="collapse"]::before {
        content: '\25BC';
    }
    .card-header [data-toggle="collapse"].collapsed::before {
        content: '\25B6';
    }
    .card-header .badge {
        position: absolute;
        top: .75rem;
        right: 1.25rem;
    }
</style>
@endsection

@section('content')
    <div class="container">
        <h1>Logs</h1>

        <p class="lead">
            The following list shows the latest logs for <strong>{{ config('app.name', 'the application') }}</strong> running on the <strong>{{ ucfirst(config('app.env')) }}</strong> environment.
        </p>

        <div class="logs">
            @if($logs)
                @foreach($logs as $log)
                    <div class="card">
                        <div class="card-header">
                            <em class="timestamp">{{ $log['timestamp'] }}</em>
                        </div>

                        <div class="card-block">
                            <h2 class="card-title h5">{{ $log['logLevel'] }}</h2>

                            <p class="card-title">
                                @if(isset($log['message']))
                                    <code>{{ $log['message'] }}</code>
                                @endif

                                @if(isset($log['output']))
                                    <pre><code>{{ $log['output'] }}</code></pre>
                                @endif
                            </p>

                            @if(isset($log['stacktrace']))
                                <div id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="card">
                                        <div class="card-header justify-content-between" role="tab" id="headingOne">
                                            <h3 class="mb-0 h6">
                                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="{{ '#collapse'.$loop->index }}" aria-expanded="true" aria-controls="{{ 'collapse'.$loop->index }}">
                                                    Stacktrace
                                                </a>
                                                <span class="badge badge-pill badge-default">{{ count($log['stacktrace']) }}</span>
                                            </h3>
                                        </div>

                                        <div id="{{ 'collapse'.$loop->index }}" class="collapse" role="tabpanel" aria-labelledby="headingOne">
                                            <ul class="stacktrace">
                                                @foreach($log['stacktrace'] as $line)
                                                    <li>{{ $line }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            @else
                <div class="card">
                    <div class="card-block">
                        <p>No logs were found.</p>
                    </div>
                </div>
            @endif
        </div>

    </div>
@endsection
