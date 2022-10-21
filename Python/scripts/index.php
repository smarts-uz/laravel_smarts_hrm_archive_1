<?php
$command = escapeshellcmd('python main.py');
$output = shell_exec($command);
$out = json_decode($output, true);
var_dump($out);
?>
