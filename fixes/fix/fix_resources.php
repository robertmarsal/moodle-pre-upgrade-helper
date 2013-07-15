<?php

/**
 * Default exception handler: Can not find data record in database table course. Debug: SELECT id,category FROM {course} WHERE id = ?
 * [array (
 * 0 => '3966',
 * )]
 * line 1272 of /lib/dml/moodle_database.php: dml_missing_record_exception thrown
 * line 1249 of /lib/dml/moodle_database.php: call to moodle_database->get_record_select()
 * line 6272 of /lib/accesslib.php: call to moodle_database->get_record()
 * line 6527 of /lib/accesslib.php: call to context_course::instance()
 * line 6890 of /lib/accesslib.php: call to context_module::instance()
 * line 70 of /lib/resourcelib.php: call to get_context_instance()
 * line 139 of /mod/resource/db/upgradelib.php: call to resourcelib_try_file_migration()
 * line 181 of /mod/resource/db/upgrade.php: call to resource_20_migrate()
 * line 540 of /lib/upgradelib.php: call to xmldb_resource_upgrade()
 * line 271 of /lib/upgradelib.php: call to upgrade_plugins_modules()
 * line 1437 of /lib/upgradelib.php: call to upgrade_plugins()
 * line 155 of /admin/cli/upgrade.php: call to upgrade_noncore()
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_resources implements fix {

    public function fix() {

        global $CFG;

        // Locate resources pointing to unexistent courses
        $sql = "SELECT r.id, r.course
                FROM mdl_resource r
                LEFT JOIN mdl_course c ON r.course = c.id
                WHERE c.id IS NULL";

        $resources = get_records_sql($sql);

        $regs = empty($resources) ? 0 : count($resources);
        fdebug($regs.' problematic records found in mdl_resource', 0);

        if($resources) {
            foreach($resources as $resource) {
                fdebug('Removing resource with id '.$resource->id
                       .' associated to unexistent course '.$resource->course, 1);

                if(!delete_records('resource', 'id', $resource->id)) {
                    fdebug('Could not remove the record '.$resource->id.' from the mdl_resource table', 1);
                }
            }
        }

        // the upgrade has been executed and the table mdl_resource_old exists
        $sql = "SELECT COUNT(*) as old
                FROM information_schema.tables
                WHERE table_schema = '{$CFG->dbname}'
                AND table_name = '{$CFG->prefix}resource_old';";

        $oldresources = get_records_sql($sql);

        if($oldresources) {

            $oldexists = array_shift($oldresources);
            if($oldexists->old == 1) {
                fdebug('Upgrade process has been execute, running alternate process...', 2);

                $sql = "SELECT r.id, r.course
                        FROM mdl_resource_old r
                        LEFT JOIN mdl_course c ON r.course = c.id
                        WHERE c.id IS NULL ";

                $resources_old = get_records_sql($sql);

                $regs = empty($resourcesi_old) ? 0 : count($resources_old);
                fdebug($regs.' problematic records found in mdl_resource_old', 0);

                if($resources_old) {
                   foreach($resources_old as $resource_old) {
                       fdebug('Removing resource with id '.$resource_old->id
                              .' associated to unexistent course '.$resource_old->course, 1);

                       if(!delete_records('resource_old', 'id', $resource_old->id)) {
                           fdebug('Could not remove the record '.$resource_old->id
                                  .' from the table mdl_resource_old', 1);
                       }
                   }
                }
            }
        } else {
            fdebug('Nothing to do!', 0);
        }
    }
}
