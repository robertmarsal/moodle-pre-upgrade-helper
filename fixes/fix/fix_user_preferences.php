<?php

/**
 * Default exception handler: DDL sql execution error Debug: Duplicate entry '16295-forum_displaymode' for key 'mdl_userpref_usenam_uix'
 * CREATE UNIQUE INDEX mdl_userpref_usenam_uix ON mdl_user_preferences (userid, name)
 * line 400 of /lib/dml/moodle_database.php: ddl_change_structure_exception thrown
 * line 749 of /lib/dml/mysqli_native_moodle_database.php: call to moodle_database->query_end()
 * line 88 of /lib/ddl/database_manager.php: call to mysqli_native_moodle_database->change_database_structure()
 * line 75 of /lib/ddl/database_manager.php: call to database_manager->execute_sql()
 * line 892 of /lib/ddl/database_manager.php: call to database_manager->execute_sql_arr()
 * line 5396 of /lib/db/upgrade.php: call to database_manager->add_index()
 * line 1394 of /lib/upgradelib.php: call to xmldb_main_upgrade()
 * line 150 of /admin/cli/upgrade.php: call to upgrade_core()
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_user_preferences implements fix {

    function fix() {

        global $CFG;

        // Search for duplicates
        $sql = "SELECT userid, name
                FROM {$CFG->prefix}user_preferences up
                GROUP BY up.userid, up.name
                HAVING (COUNT(userid) > 1)";
        $multiples = get_records_sql($sql);

        if($multiples) {

            fdebug('Found '.count($multiples).' users with duplicated records', 0);

            foreach($multiples as $multiple){

                $sql = "SELECT id, userid, name, value
                        FROM {$CFG->prefix}user_preferences up
                        WHERE up.userid = {$multiple->userid}
                        AND up.name = '".$multiple->name."'
                        ORDER BY id";

                $duplicates = get_records_sql($sql);

                if($duplicates) {

                    // Keep the record with the greater id
                    $greatest = array_pop($duplicates);

                    fdebug('Cleaning the records of the user '.$greatest->userid, 0);

                    // Remove duplicates
                    foreach($duplicates as $duplicate) {
                        if($duplicate->id != $greatest->id) {
                            fdebug('Removing user preference '.
                                    $duplicate->name.' with id '.$duplicate->id.
                                    ' and value '.$duplicate->value, 1);
                            if(!delete_records('user_preferences', 'id', $duplicate->id)) {
                                fdebug('Could not delete the record with id '.$duplicate->id, 1);
                            }
                        }
                    }
                }
            }
        } else {
            fdebug('Nothing to do!', 0);
        }
    }
}
