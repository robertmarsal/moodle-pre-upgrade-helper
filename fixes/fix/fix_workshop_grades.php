<?php

/**
 * https://tracker.moodle.org/browse/MDL-28120
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_workshop_grades implements fix {

    public function fix()
    {
        global $CFG;

        $sql = "SELECT id, COUNT(*) AS dupcnt, workshopid, assessmentid, elementno
                FROM mdl_workshop_grades
                GROUP BY workshopid, assessmentid, elementno
                HAVING dupcnt > 1";

        $duplicates = get_records_sql($sql);

        $regs = empty($duplicates) ? 0 : count($duplicates);
        fdebug($regs.' duplicated records found in mdl_workshop_grades', 0);

        if($duplicates) {
            foreach($duplicates as $duplicate) {
                fdebug('Removing duplicate record '.$duplicate->id.' from mdl_workshop_grades', 0);
                if(!delete_records('workshop_grades', 'id', $duplicate->id)) {
                    fdebug('Could not delete the record '.$duplicate->id.' from the table mdl_workshop_grades', 1);
                }
            }
        } else {
            fdebug('Nothing to do!', 0);
        }
    }
}
