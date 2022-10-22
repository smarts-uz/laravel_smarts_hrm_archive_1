<?php
$command = escapeshellcmd("python main.py command");
$output = shell_exec($command);
$out = json_decode($output, true);
var_dump($out);
?>
