#!/usr/bin/env php
<?php
date_default_timezone_set('Europe/Amsterdam');
define('DATA_FILE', 'data.js');

// Group results by x seconds
define('GROUP_BY_SECONDS', 60);

$fp = fopen('php://stdin', 'r');
if (!$fp) {
    throw new RuntimeException('Failed to open stdin');
}

file_put_contents(DATA_FILE, 'function data_chart() { return "Date,Requests/second\n"+' . PHP_EOL);

$currentDay = null;
$hits = 0;
$lineNr = 0;
while (false !== ($line = fgets($fp, 4096))) {
    // Get the timestamp from the loglines
    if (preg_match('~\[(.*)\] \"(.*)\" (\d+) (\d+)~', $line, $fields) < 1) {
        continue;
    }

    $timestamp = strtotime($fields[1]);
    $timespan = strftime('%F %H:%M:%S', $timestamp -= $timestamp % GROUP_BY_SECONDS);
    if ($currentDay !== $timespan) {
        if (null !== $currentDay) {
            printf("%s\t%u\t%.2f/second" . PHP_EOL, $currentDay, $hits, $hits/GROUP_BY_SECONDS);
            file_put_contents(DATA_FILE, sprintf('"%s,%.2f\n"+' . PHP_EOL, $currentDay, $hits/GROUP_BY_SECONDS), FILE_APPEND);
        }
        $currentDay = $timespan;
        $hits = 0;
    }
    $hits++;
}
file_put_contents(DATA_FILE, '"";}', FILE_APPEND);

if (!feof($fp)) {
    throw new RuntimeException('Stopped when not at the end of the logfile');
}
fclose($fp);
