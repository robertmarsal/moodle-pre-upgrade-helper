<?php

/**
 * The mdl_pma_history table contains a table with a column named table. This can cause the
 * upgrade to stop, when converting the tables to utf8.
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_pma_history implements fix {

    function fix() {

        global $CFG;

        $sql = "ALTER TABLE {$CFG->prefix}pma_history
                CONVERT TO CHARACTER SET utf8
                COLLATE utf8_general_ci";

        fdebug('Converting table mdl_pma_history to utf8_general_ci', 0);

        if(!execute_sql($sql, false)) {
            fdebug('Could not change the collation of the mdl_pma_history to utf8_general_ci', 1);
        } else {
            fdebug('Collation changed!', 0);
        }
    }
}
