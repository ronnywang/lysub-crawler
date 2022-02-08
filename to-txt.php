<?php

foreach (glob(__DIR__ . "/files/*/*/*") as $f) {
    if (preg_match('/.txt$/', $f)) {
        continue;
    }
    if (file_exists($f . '.txt')) {
        continue;
    }
    if (preg_match('#doc$#', $f)) {
        system(sprintf("antiword %s > %s", escapeshellarg($f), escapeshellarg($f . '.txt')));
    }

    error_log($f);
}
