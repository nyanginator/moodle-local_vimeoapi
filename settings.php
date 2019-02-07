<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    local_vimeoapi
 * @copyright  Nicholas Yang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (is_siteadmin()) {
    $settings = new admin_settingpage('local_vimeoapi', get_string('pluginname', 'local_vimeoapi'));

    $settings->add(new admin_setting_heading('local_vimeoapi/pluginname', '', new lang_string('intro', 'local_vimeoapi')));

    $caching_setting = new admin_setting_configcheckbox('local_vimeoapi/caching', get_string('caching', 'local_vimeoapi'), get_string('caching_desc', 'local_vimeoapi'), 1);
    $settings->add($caching_setting);

    $displayorigin = new admin_setting_configcheckbox('local_vimeoapi/displayorigin', get_string('displayorigin', 'local_vimeoapi'), get_string('displayorigin_desc', 'local_vimeoapi'), 0);
    $settings->add($displayorigin);

    $settings->add(new admin_setting_configtext('local_vimeoapi/autoloadpath', get_string('autoloadpath', 'local_vimeoapi'), get_string('autoloadpath_desc', 'local_vimeoapi'), '', PARAM_RAW));

    $settings->add(new admin_setting_configtext('local_vimeoapi/client_id', get_string('client_id', 'local_vimeoapi'), get_string('client_id_desc', 'local_vimeoapi'), '', PARAM_RAW));
    $settings->add(new admin_setting_configtext('local_vimeoapi/client_secret', get_string('client_secret', 'local_vimeoapi'), get_string('client_secret_desc', 'local_vimeoapi'), '', PARAM_RAW));
    $settings->add(new admin_setting_configtext('local_vimeoapi/accesstoken', get_string('accesstoken', 'local_vimeoapi'), get_string('accesstoken_desc', 'local_vimeoapi'), '', PARAM_RAW));

    $ADMIN->add('localplugins', $settings);
}
