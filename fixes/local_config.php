<?php

/**
 * @author Robert Boloc <robertboloc@gmail.com>
 * @copyright (c) 2013, Robert Boloc (http://robertboloc.eu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$local_cfg = new stdClass();
// Dirroot of the moodle 1.9.x installation
$local_cfg->dirroot = '/var/www/moodle199urv';
// The fixes to apply
$local_cfg->fixes = array(
    'fix_workshop_grades',
    'fix_context',
    'fix_user_preferences',
    'fix_resources',
    'fix_role_capabilities',
    'fix_course_sections',
    'fix_course_sortorder',
    'fix_jclic_activities',
    'fix_local_capabilities',
    'fix_pma_history',
);
