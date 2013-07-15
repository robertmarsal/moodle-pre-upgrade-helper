<?php

/**
 * Did you remember to make the first column something unique in your call to get_records? Duplicate value '9999999999' found in column 'section'.
 * line 898 of /lib/dml/mysqli_native_moodle_database.php: call to debugging()
 * line 1129 of /lib/dml/moodle_database.php: call to mysqli_native_moodle_database->get_records_sql()
 * line 1078 of /lib/dml/moodle_database.php: call to moodle_database->get_records_select()
 * line 1272 of /course/lib.php: call to moodle_database->get_records()
 * line 1632 of /lib/navigationlib.php: call to get_all_sections()
 * line 1692 of /lib/navigationlib.php: call to global_navigation->generate_sections_and_activities()
 * line 37 of /course/format/social/lib.php: call to global_navigation->load_generic_course_sections()
 * line 1609 of /lib/navigationlib.php: call to callback_social_load_content()
 * line 1088 of /lib/navigationlib.php: call to global_navigation->load_course_sections()
 * line 171 of /blocks/navigation/block_navigation.php: call to global_navigation->initialise()
 * line 280 of /blocks/moodleblock.class.php: call to block_navigation->get_content()
 * line 232 of /blocks/moodleblock.class.php: call to block_base->formatted_contents()
 * line 926 of /lib/blocklib.php: call to block_base->get_content_for_output()
 * line 978 of /lib/blocklib.php: call to block_manager->create_block_contents()
 * line 349 of /lib/blocklib.php: call to block_manager->ensure_content_created()
 * line 3 of /theme/base/layout/frontpage.php: call to block_manager->region_has_content()
 * line 685 of /lib/outputrenderers.php: call to include()
 * line 637 of /lib/outputrenderers.php: call to core_renderer->render_page_layout()
 * line ? of unknownfile: call to core_renderer->header()
 * line 1363 of /lib/setuplib.php: call to call_user_func_array()
 * line 95 of /index.php: call to bootstrap_renderer->__call()
 * line 95 of /index.php: call to bootstrap_renderer->header()
 *
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fix_course_sections implements fix {

    public function fix() {

        global $CFG;

        $sql = "SELECT id, COUNT(course), course, section
                FROM {$CFG->prefix}course_sections
                GROUP BY course, section
                HAVING COUNT(course) > 1";

        $csections = get_records_sql($sql);

        $regs = empty($csections) ? 0 : count($csections);
        fdebug($regs.' duplicated sections found in mdl_course_sections', 0);

        if($csections) {
            foreach($csections as $csection) {

                $check_course_sql = "SELECT id
                                     FROM {$CFG->prefix}course c
                                     WHERE c.id = '{$csection->course}'";
                $course = get_records_sql($check_course_sql);

                if($course) {
                    // Replace section with record id
                    $records_sql = "SELECT *
                                    FROM {$CFG->prefix}course_sections
                                    WHERE course = '{$csection->course}' AND section = '{$csection->section}'";

                    $recs = get_records_sql($records_sql);

                    if($recs) {
                        foreach($recs as $rec) {
                            $rec->section = $rec->id;
                            fdebug('Updating the section value of the record '.$rec->id.' to '.$rec->id, 0);
                            if(!update_record('course_sections', $rec)) {
                                fdebug('Could not update the section value of the record '.$rec->id, 1);
                            }
                        }
                    }
                } else {
                    // The course has already been deleted -> remove records
                    fdebug('Removing course section '.$csection->course.' because the course does not exist!', 1);
                    if(!delete_records('course_sections', 'course', $csection->course)) {
                        fdebug('Could not remove the section '.$csection->section.' of the course '.$csection->course);
                    }
                }
            }
        } else {
            fdebug('Nothing to do!', 0);
        }
    }
}

