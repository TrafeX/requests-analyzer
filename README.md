# Requests/second analyzer
Simple tool to be used on CLI for getting the requests/second out of a log file.
It outputs the requests/second and writes them to a JavaScript file.
Open chart.html to see the requests/second in a chart (no webserver required)

## Examples

    Analyze logfile:
    cat /tmp/http.log | ./analyze.php

    Realtime analyzing:
    ssh <server> tail -f <logfile> | ./analyze.php
