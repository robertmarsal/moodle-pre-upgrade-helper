<?php

/**
 * !!Out of range value for column 'sortorder' at row 3
 * ALTER TABLE `mdl_course` MODIFY COLUMN `sortorder` bigint(10) signed NOT NULL DEFAULT '0'
 * Error code: ddlexecuteerror !!
 * !! Stack trace: * line 432 of /lib/dml/moodle_database.php: ddl_change_structure_exception thrown
 * line 860 of /lib/dml/mysqli_native_moodle_database.php: call to moodle_database-&gt;query_end()
 * line 139 of /lib/db/upgradelib.php: call to mysqli_native_moodle_database-&gt;change_database_structure()
 * line 232 of /lib/db/upgrade.php: call to upgrade_mysql_fix_unsigned_columns()
 * line 1493 of /lib/upgradelib.php: call to xmldb_main_upgrade()
 * line 153 of /admin/cli/upgrade.php: call to upgrade_core()
 * !!
 *
 * The problem is that the course table contains values of the sortorder fiels greater than the greatest signed
 * BIGINT.
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_course_sortorder implements fix {

    public function fix() {

        global $CFG;

        $SIGNED_BIG_INT = 9223372036854775807;

        $sql = "SELECT id, sortorder
                FROM {$CFG->prefix}course c
                WHERE c.sortorder > '".$SIGNED_BIG_INT."'";

        $unsigned = get_records_sql($sql);

        $regs = empty($unsigned) ? 0 : count($unsigned);
        fdebug($regs.' greater records found in mdl_course', 0);

        if($unsigned) {
            fdebug('Recalculating the sortorder value to be in range', 1);
            if(!execute_sql('UPDATE mdl_course SET sortorder=ID + 1000', false)) {
                fdebug('Operation failed!', 1);
            }
        } else {
            fdebug('Nothing to do!', 0);
        }
    }
}
