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

$string['pluginname'] = 'Vimeo API';
$string['privacy:metadata'] = 'The Vimeo API local plugin does not store any personal data.';

$string['intro'] = 'This plugin is based on the the Vimeo PHP Library, which you will need to install using Composer. Please visit the <a href="https://github.com/vimeo/vimeo.php" title="Official PHP library for the Vimeo API">Vimeo PHP Library Github page</a> for installation instructions. For more information on the API, consult the <a href="https://developer.vimeo.com/api" title="Vimeo API documentation">Vimeo API documentation</a>.';

$string['caching'] = 'Enable Cache';
$string['caching_desc'] = 'Use the Moodle Cache to avoid having to make API requests. If you need to force an update of data stored in the cache, you can: 1) Purge All Caches, 2) temporarily turn this setting off and reload a page that makes a call to the API, 3) load a page that makes a call to the API with the query param "vimeoapi_cacheupdate" (must be logged in as Site Admin).';

$string['displayorigin'] = 'Display Origin';
$string['displayorigin_desc'] = 'Enable to display whether the data is coming from the Moodle Cache (as opposed to making a request through the Vimeo API). Visible to Site Admins only. For debugging purposes.';

$string['autoloadpath'] = 'Autoload.php Path';
$string['autoloadpath_desc'] = 'Required for retrieving data for private videos/albums. Specify the absolute path to the autoload.php file here.';

$string['client_id'] = 'Client ID';
$string['client_id_desc'] = 'Client identifier, which can be found in your Vimeo API app settings.';

$string['client_secret'] = 'Client Secret';
$string['client_secret_desc'] = 'Client secret, which can be found in your Vimeo API app settings.';

$string['accesstoken'] = 'Access Token';
$string['accesstoken_desc'] = 'You can generate access tokens in your Vimeo API app settings. Make sure you have correctly selected the desired scope permissions';
