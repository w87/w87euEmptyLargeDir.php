<?php
/**
 * w87euEmptyLargeDir.php
 * Remove all files and directories (recurrently) from a large directory (many files / huge size).
 * With limit per request, log and statistics of files removed (recursively).
 *
 * @package   w87euEmptyLargeDir.php
 * @version   2025.01.08
 * @see       https://app.w87.eu/codeInfo?app=w87euEmptyLargeDir.php&file=w87euEmptyLargeDir.php
 * @author    Walerian Walawski <https://w87.eu/?contact>
 * @link      https://w87.eu/
 * @license   https://creativecommons.org/licenses/by-sa/4.0/ CC BY-SA 4.0
 * @copyright 20017-2025 SublimeStar.com Walerian Walawski © All Rights Reserved.
 */

// -*- Script access on / off --------------------------------------------------------------------------------------------------

exit('Access OFF');

// -*- PHP settings ------------------------------------------------------------------------------------------------------------

ignore_user_abort(true);
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING);

// -*- HTTP headers ------------------------------------------------------------------------------------------------------------

header('Keep-Alive: timeout=300, max=512', true);
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0', true);
header('Referrer-Policy: unsafe-url', true);
header('X-UA-Compatible: IE=edge', true);
header('Content-type: text/plain');

// -*- Removing ----------------------------------------------------------------------------------------------------------------

$dir = 'tmp'; // Dir. to remove files & subdirs. from (put the script in the parent dir., use just dir. name for padding's sake)
$limit = 5000; // Limit of files / dirs. to remove per one request
$rmBytes = 0; // Bytes removed
$i = 0; // Files / dirs. processed

/**
 * @param string $dir — Directory to remove files / dirs. from
 * @param bool $recursive — Remove files / dirs. recurrently (AND the directory itself!)
 */
function w87euEmptyLargeDir($dir, $recursive = false)
{
    global $i, $limit, $rmBytes;
	$files = array_diff(scandir($dir), array('.','..'));
    $count = count($files);
    $level = count(explode('/', $dir)) - 1;
    $padds = str_repeat("\t", $level);

    echo "\n{$padds}📁 [ DIRECTORY $dir ]\n";
    echo "{$padds}● $count FILES IN THE DIR.\n\n";

	foreach ($files as $file)
	{
        if($limit < 1){
            echo "\n{$padds}LIMIT REACHED!\n";
            return;
        }
        
		if(is_dir("$dir/$file")){
            w87euEmptyLargeDir("$dir/$file", true);
        }else{
            $i++;
            $fs = filesize("$dir/$file");
            unlink("$dir/$file");
            $rmBytes = $rmBytes + $fs;
            $fs = round($fs / 1024 / 1024, 1);
            echo "{$padds}\t→ $i REMOVING\t$fs MB\t$file\n";
        }

        $limit --;
	}

    if($recursive){
        $i++;
        echo "{$padds}\t→ $i REMOVING DIRECTORY $dir\n";
        return rmdir($dir);
    }
}

echo "\n▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬ DELETING FROM „{$dir}” (LIMIT: $limit) ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬\n\n";

w87euEmptyLargeDir($dir);
$fs = round($rmBytes / 1024 / 1024, 1);

echo "\n\n▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬ DELETED $fs MB IN $i FILES ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬\n\n";
