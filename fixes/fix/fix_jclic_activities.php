<?php

/**
 * !!! Database table 'jclic_activities'' contains unsigned column 'total_time' with 1 values that are out of allowed range, upgrade can not continue. !!!
 * !!
 * Error code: notlocalisederrormessage !!
 * !! Stack trace: * line 130 of /lib/db/upgradelib.php: moodle_exception thrown
 * line 232 of /lib/db/upgrade.php: call to upgrade_mysql_fix_unsigned_columns()
 * line 1493 of /lib/upgradelib.php: call to xmldb_main_upgrade()
 * line 153 of /admin/cli/upgrade.php: call to upgrade_core()
 * !!
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_jclic_activities implements fix {

    public function fix() {

        global $CFG;

        $SIGNED_MEDIUM_INT = 8388607;

        //8388607 = biggest SIGNED MEDIUMINT
        $sql = "SELECT id, total_time
                FROM {$CFG->prefix}jclic_activities
                WHERE total_time > '".$SIGNED_MEDIUM_INT."'";

        $unsigned = get_records_sql($sql);

        $regs = empty($unsigned) ? 0 : count($unsigned);
        fdebug($regs.' problematic registers found in mdl_course', 0);

        if($unsigned) {
            foreach($unsigned as $usigned) {
                $usigned->total_time = $SIGNED_MEDIUM_INT;
                fdebug('Updating total_time field to max allowed', 0);
                if(!update_record('jclic_activities', $usigned)) {
                    fdebug('Could not update the total_time field of the record with id '.$usigned->id, 1);
                }
            }
        } else {
            fdebug('Nothing to do!', 0);
        }
    }
}
