<?php

shell_exec("ifconfig em2 down");

$command = "echo '" . date("jS F Y h:i:s A") . " ---- NAT Disabled ' >> checklog.txt;
shell_exec($command);

?>