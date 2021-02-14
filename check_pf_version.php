#!/usr/local/bin/php -f
<?

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
shell_exec("sudo touch "."{$g['varrun_path']}/pkg.dirty");
$system_pkg_version = get_system_pkg_version(false,false);
shell_exec("sudo rm " . "{$g['varrun_path']}/pkg.dirty");
}

$current_installed_buildtime = trim(file_get_contents("/etc/version.buildtime"));

if ( $system_pkg_version['installed_version'] !== $system_pkg_version['version']) { 
    $additional_info = "WARNING - new version available\n" ;  $exitcode = 1; 
} else { 
    $additional_info = "OK - already at latest version\n" ; $exitcode = 0; 
}
$additional_info .= "Current version: ".$system_pkg_version['installed_version']."\n";
$additional_info .= "Built on: ".$current_installed_buildtime."\n";
$additional_info .= "Remote version: ".$system_pkg_version['version']."\n";

echo $additional_info;
exit ($exitcode);

?>