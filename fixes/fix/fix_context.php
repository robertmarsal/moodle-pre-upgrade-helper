<?php

/**
 * Fixes the multiple records in the context table problem, by deleting the ones having a NULL path.
 * ---
 * Multiple records found, only one record expected. Debug: SELECT * FROM {context} WHERE contextlevel = ?
 * [array (
 *  0 => 10,
 * )]
 * line 1307 of /lib/dml/moodle_database.php: dml_multiple_records_exception thrown
 * line 1269 of /lib/dml/moodle_database.php: call to moodle_database->get_record_sql()
 * line 1249 of /lib/dml/moodle_database.php: call to moodle_database->get_record_select()
 * line 5632 of /lib/accesslib.php: call to moodle_database->get_record()
 * line 6872 of /lib/accesslib.php: call to context_system::instance()
 * line 654 of /lib/setup.php: call to get_system_context()
 * line 466 of /config.php: call to require_once()
 * line 27 of /admin/cli/mysql_collation.php: call to require()
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_context implements fix {

    function fix() {

       global $CFG;

       $sql = "SELECT id, contextlevel, instanceid
               FROM {$CFG->prefix}context
               WHERE path IS NULL";

       $contexts = get_records_sql($sql);

       $regs = empty($contexts) ? 0 : count($contexts);
       fdebug($regs . ' bad records found in mdl_context', 0);

       if($contexts){
           foreach($contexts as $context) {
               fdebug('Removing contextid '.$context->id
                      .' with contextlevel '.$context->contextlevel
                      .' from the instance '.$context->instanceid, 1);
               if(!delete_records('context', 'id', $context->id)) {
                   fdebug('Could not delete the record '.$context->id.' from mdl_context', 1);
               }
           }
       } else {
           fdebug('Nothing to do!', 0);
       }
    }
}
