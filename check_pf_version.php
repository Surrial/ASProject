#!/usr/local/bin/php -f
<?php

# This check pulls the version from the website and compares it
# to the installed version. 
# Many thanks to Atadilo for fixing the code and simplifying it.

# Created 15 Dec 2017
# Modified 12 May 2020

require_once("pkg-utils.inc");
global $g;

if (file_exists("{$g['varrun_path']}/pkg.dirty")) {
$system_pkg_version = get_system_pkg_version(false,false);
} else {
shell_exec("touch "."{$g['varrun_path']}/pkg.dirty");
$system_pkg_version = get_system_pkg_version(false,false);
shell_exec("rm " . "{$g['varrun_path']}/pkg.dirty");
}

$current_installed_buildtime = trim(file_get_contents("/etc/version.buildtime"));

if ( $system_pkg_version['installed_version'] !== $system_pkg_version['version']) { 

    echo "WARNING - new version available\n" . "Installed: ". $system_pkg_version['installed_version'] . "  Latest: " . $system_pkg_version['version'] ;  $exitcode = 1; 

    $command = "echo '" . date("jS F Y h:i:s A") . " ---- Firewall New Version Available ' >> checklog.txt";
    shell_exec($command);

    //check for whether firewall was updated successfully
    $command1 = "echo '" . $system_pkg_version['version'] . "' > data.txt";
    shell_exec($command1);

    echo "\nUpdating Firewall...\n";

    $command2 = "echo '" . date("jS F Y h:i:s A") . " ---- Updating Firewall... ' >> checklog.txt";
    shell_exec($command2);

    shell_exec("pfSense-upgrade -y");

} else { 
    echo "OK - already at latest version\n";
    $upgraded_version = shell_exec("cat /root/data.txt");

    echo "Checking if Firewall has recently been updated...\n";

    if(empty($upgraded_version)) {

        echo "Firewall has not been recently updated!\n";
        $additional_info = "OK - already at latest version\n" ; $exitcode = 0;
        
    } else {
        echo "Patch is tested successfully. Starting patch on actual Firewall\n";

        $command3 = "echo '" . date("jS F Y h:i:s A") . " ---- Patch has been tested successfully... ' >> checklog.txt";
        shell_exec($command3);

        $command4 = "echo '" . date("jS F Y h:i:s A") . " ---- Starting patch on actual Firewall... ' >> checklog.txt";
        shell_exec($command4);

        shell_exec("rm /root/data.txt");
        echo shell_exec("ssh root@192.168.108.145 pfSense-upgrade -y");
        $exitcode = 0;
    }

}
$additional_info .= "Current version: ".$system_pkg_version['installed_version']."\n";
$additional_info .= "Built on: ".$current_installed_buildtime."\n";
$additional_info .= "Remote version: ".$system_pkg_version['version']."\n";

echo $additional_info;
exit ($exitcode);

?>