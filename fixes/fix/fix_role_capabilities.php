<?php

/**
 * Default exception handler: Can not find data record in database table context. Debug: SELECT * FROM {context} WHERE id = ?
 * [array (
 * 0 => '695588',
 * )]
 * line 1272 of /lib/dml/moodle_database.php: dml_missing_record_exception thrown
 * line 1249 of /lib/dml/moodle_database.php: call to moodle_database->get_record_select()
 * line 4890 of /lib/accesslib.php: call to moodle_database->get_record()
 * line 1450 of /lib/accesslib.php: call to context::instance_by_id()
 * line 2506 of /lib/accesslib.php: call to assign_capability()
 * line 557 of /lib/upgradelib.php: call to update_capabilities()
 * line 271 of /lib/upgradelib.php: call to upgrade_plugins_modules()
 * line 1437 of /lib/upgradelib.php: call to upgrade_plugins()
 * line 155 of /admin/cli/upgrade.php: call to upgrade_noncore()
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_role_capabilities implements fix {

    public function fix() {

        global $CFG;

        $sql = "SELECT cap.id, cap.contextid
                FROM {$CFG->prefix}role_capabilities cap
                LEFT JOIN {$CFG->prefix}context ctx ON cap.contextid = ctx.id
                WHERE ctx.id IS NULL ";

        $capabilities = get_records_sql($sql);

        $regs = empty($capabilities) ? 0 : count($capabilities);
        fdebug($regs.' problematic records found in mdl_role_capabilities', 0);

        if($capabilities) {
            foreach($capabilities as $capability) {
                fdebug('Removing capability assignment with id '.$capability->id
                       .' pointint to unexistent context with id '.$capability->contextid, 1);

                if(!delete_records('role_capabilities', 'id', $capability->id)) {
                    fdebug('Could not remove the record with id '.$capability->id.' from mdl_role_capabilities', 1);
                }
            }
        } else {
            fdebug('Nothing to do!', 0);
        }
    }
}
