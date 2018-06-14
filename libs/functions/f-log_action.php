<?php

function log_action($action, $message = "") {
    $logfile = SITE_ROOT . 'app' . DS . 'logs' . DS . 'log.txt';
    $new = file_exists($logfile) ? false : true;
    if ($handle = fopen($logfile, 'a')) {
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "{$timestamp} | {$action}: {$message}\n";
        fwrite($handle, $content);
        fclose($handle);
        if ($new) {
            chmod($logfile, 0755);
        }
    } else {
        echo "Could not open log file for writing.";
    }
}

function read_log($logfile) {
    if (file_exists($logfile) && is_readable($logfile) && $handle = fopen($logfile, 'r')) {
        echo '<div class="logfile">' . "\n";
        echo '<ul>' . "\n";
        while (!feof($handle)) {
            $entry = fgets($handle);
            if (trim($entry) != "") {
                echo '<li>' . $entry . '</li>' . "\n";
            }
        }
        echo '</ul>' . "\n";
        echo '</div>' . "\n";
        fclose($handle);
    } else {
        echo 'Could not read from ' . $logfile;
    }
}