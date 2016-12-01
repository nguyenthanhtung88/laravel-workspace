#!/usr/bin/php
<?php

$yamlFile = '.framgia_ci.yml';
$resultFile = 'framgia_ci_result.tmp';

if (file_exists($yamlFile)) {
    $result = yaml_parse_file($yamlFile);

    if (isset($result['commands'])) {
        $commands = $result['commands'];

        $success = true;
        foreach ($commands as $command) {
            echo "[+] $command\n";
            $commandResult = execute($command);
            $success = $success && $commandResult;
        }

        $file = fopen($resultFile, 'w');
        fwrite($file, intval($success));
        fclose($file);
    }
}

function execute($cmd) {
    $proc = proc_open($cmd, [['pipe','r'],['pipe','w'],['pipe','w']], $pipes);
    while(($line = fgets($pipes[1])) !== false) {
        fwrite(STDOUT,$line);
    }
    while(($line = fgets($pipes[2])) !== false) {
        fwrite(STDERR,$line);
    }
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    return proc_close($proc);
}
