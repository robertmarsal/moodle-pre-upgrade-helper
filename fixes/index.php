<?php

/**
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Local requires
require_once __DIR__ . 'local_config.php';
require_once __DIR__ . 'fix/fix.php';
require_once __DIR__ . 'lib/color.php';

// Moodle requires
global $local_cfg;
require_once $local_cfg->dirroot.'/config.php';

// Activate debug display
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

// Run the fixes
foreach($local_cfg->fixes as $fix_class) {
    require_once (dirname(__FILE__).'/fix/'.$fix_class.'.php');

    fdebug('Running fix '.$fix_class);

    $fix = new $fix_class();
    $fix->fix();

    fdebug('End of fix '.$fix_class);
}

/**
 * Displays shell debug messages
 *
 * @param $message - message to show
 * @param $codi - color code of the message:
 *      0 - green
 *      1 - red
 */
function fdebug($message, $code = 99) {

    $c = new Color();

    $dmessage = date('d M Y H:i:s').' '.$message;

    switch($code){
        // success
        case 0: $dmessage = $c($dmessage)->green();
            break;
        // error
        case 1: $dmessage = $c($dmessage)->red();
            break;
        // info
        case 2: $dmessage = $c($dmessage)->blue();
            break;
    }

    mtrace($dmessage);
}
