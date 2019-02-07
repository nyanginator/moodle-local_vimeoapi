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
 * Helper functions for local_vimeoapi
 *
 * @package    local_vimeoapi
 * @copyright  Nicholas Yang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use Vimeo\Vimeo;

/**
 * Get admin login status based on capability, instead of using is_admin().
 *
 * @return bool Whether currently logged in user has site:config capability.
 */
function local_vimeoapi_logged_in_as_admin() {
    return has_capability('moodle/site:config', context_system::instance());
}

/**
 * Checks for the query param "vimeoapi_updatecache" in the URL and returns
 * true only if logged in as Site Admin. A true value signals that the cache
 * should be refreshed.
 *
 * @return bool Whether query param "vimeoapi_updatecache" exists in URL.
 */
function local_vimeoapi_force_cache_update_from_queryparam() {
    // Only Site Admins allowed to do this
    if (local_vimeoapi_logged_in_as_admin()) {
        if (isset($_GET['vimeoapi_updatecache'])) {
            return true;
        }
    }

    return false;
}

/**
 * Helper function to display a message about whether data retrieved was from
 * Moodle Cache. Also outputs a message if autoload.php can't be found based
 * on the path set in the config. This will only return a value if logged in
 * as Site Admin.
 *
 * @param bool $gotcached Decides which message to display.
 * @return string Message about data origin (cached or not).
 */
function local_vimeoapi_display_origin($gotcached) {
    $msg = '';

    // Only Site Admins allowed to do this. Display Origin config also required.
    if (local_vimeoapi_logged_in_as_admin() && get_config('local_vimeoapi', 'displayorigin')) {
        // The cache message to display
        $msg = $gotcached ? '[from cache]' : '[NOT from cache]';

        // For uncached, display mesage if autoload.php can't be found.
        if (!$gotcached && empty(local_vimeoapi_get_autoload_path())) {
            $msg = '[check config: autoload.php not found]';
        }

        return '<span class="small"><em><b>' . $msg . '</b></em></span>';
    }

    return $msg;
}

/**
 * Helper function to get the autoload.php path from the config settings.
 *
 * @return Absolute path to autoload.php. Empty string if not found.
 */
function local_vimeoapi_get_autoload_path() {
    // Trim off trailing slash and then make sure it ends with "autoload.php".
    $autoloadpath = rtrim(get_config('local_vimeoapi', 'autoloadpath'), '/');
    $autoloadsuffix = (substr($autoloadpath, -strlen('autoload.php')) === 'autoload.php') ? '' : '/autoload.php';
    $autoloadpath .= $autoloadsuffix;

    if (!file_exists($autoloadpath)) {
        return '';
    }

    return $autoloadpath;
}

/**
 * Initializations for getting data through Vimeo API framework. Call this
 * function to setup the Vimeo object for making requests.
 *
 * @return Vimeo object. Null if autoload.php not found.
 */
function local_vimeoapi_init_vimeo_api() {
    $autoloadpath = local_vimeoapi_get_autoload_path();
    if (!empty($autoloadpath)) {
        // Load the Vimeo PHP Library
        require_once($autoloadpath);

        // Get values from config setting
        $client_id = get_config('local_vimeoapi', 'client_id');
        $client_secret = get_config('local_vimeoapi', 'client_secret');
        $accesstoken = get_config('local_vimeoapi', 'accesstoken');

        $lib = new Vimeo($client_id, $client_secret, $accesstoken);

        // Return the Vimeo object
        return $lib;
    }

    return null;
}

/**
 * Returns field value of a specific Vimeo album.
 *
 * @param stdClass $albumid Vimeo album ID.
 * @param string $field Name of field to retrieve.
 * @return string Value of the requested album field. Empty string if no value.
 */
function local_vimeoapi_get_album_field($albumid, $field) {
    $fieldvalue = '';
    $albumid = trim($albumid);
    if ($albumid !== '') {
        try {
            // Vimeo API is required for getting album information
            $lib = local_vimeoapi_init_vimeo_api();
            if ($lib !== null) {
                $response = $lib->request("/me/albums/$albumid?fields=$field");
                if (isset($response['body'][$field])) {
                    $fieldvalue = $response['body'][$field];
                }
            }
        }
        catch (Exception $e) {
        }
    }

    return $fieldvalue;
}

/**
 * Returns field value of a specific Vimeo video.
 *
 * @param stdClass $videoid Vimeo video ID.
 * @param string $field Name of field to retrieve.
 * @param bool $trydirecturl Try to avoid API call by accessing direct URL.
 * @return string Value of the requested video field. Empty string if no value.
 */
function local_vimeoapi_get_video_field($videoid, $field, $trydirecturl = true) {
    $fieldvalue = '';
    $videoid = trim($videoid);
    if ($videoid !== '') {
        try {
            // First try to get the data from the direct URL
            $url = "https://vimeo.com/api/v2/video/$videoid.php";
            if (local_vimeoapi_get_http_response_code($url) === "200" && $trydirecturl) {
                $hash = unserialize(file_get_contents($url));

                if (isset($hash[0][$field])) {
                    $fieldvalue = $hash[0][$field];
                }
            }
            // If direct URL doesn't work (i.e. a private video), then use API,
            // which has a limited number of requests per hour
            else {
                $lib = local_vimeoapi_init_vimeo_api();
                if ($lib !== null) {
                    $response = $lib->request("/me/videos/$videoid?fields=$field");
                    if (isset($response['body'][$field])) {
                        $fieldvalue = $response['body'][$field];
                    }
                }
            }
        }
        catch (Exception $e) {
        }
    }

    return $fieldvalue;
}

/**
 * Returns thumbnail of a specific Vimeo video.
 *
 * @param stdClass $videoid Vimeo video ID.
 * @param string $size Requested thumbnail size: small/medium/large.
 * @param bool $trydirecturl Try to avoid API call by accessing direct URL.
 * @return string Image link of the requested video's thumbnail. Empty string if not found.
 */
function local_vimeoapi_get_video_thumb_helper($videoid, $size = 'large', $trydirecturl = true) {
    $thumbnail = '';
    $videoid = trim($videoid);
    if ($videoid !== '') {
        // First try to get the data from the direct URL
        $url = "https://vimeo.com/api/v2/video/$videoid.php";
        if (local_vimeoapi_get_http_response_code($url) === "200" && $trydirecturl) {
            $hash = unserialize(file_get_contents($url));

            if (isset($hash[0]['thumbnail_' . $size])) {
                $thumbnail = $hash[0]['thumbnail_' . $size];
            }
        }
        // If direct URL doesn't work (i.e. a private video), then use the API,
        // which has a limited number of requests per hour
        else {
            try {
                $lib = local_vimeoapi_init_vimeo_api();
                if ($lib !== null) {
                    $response = $lib->request("/me/videos/$videoid?fields=pictures.sizes");
                    if (isset($response['body']['pictures']['sizes'])) {
                        $thumbnail = '';

                        // Get thumbnail info for available sizes
                        $thumbs = $response['body']['pictures']['sizes'];
                        $available_sizes = array_column($thumbs, 'width');

                        // Define what small/medium/large sizes are
                        $smallwidth = $available_sizes[0];  // 100px
                        $mediumwidth = $available_sizes[1]; // 200px
                        $largewidth = $available_sizes[3];  // 640px

                        foreach ($thumbs as $thumb) {
                            $thumbnail = $thumb['link'];

                            if (
                                ($size === 'small' && $thumb['width'] == $smallwidth) ||
                                ($size === 'medium' && $thumb['width'] == $mediumwidth) ||
                                ($size === 'large' && $thumb['width'] == $largewidth)
                            ) {
                                break;
                            }
                        }
                    }
                }
            }
            catch (Exception $e) {
            }

        }
    }

    return $thumbnail;
}

/**
 * Use this to check if a URL is valid (value of "200" is OK).
 *
 * @param string $url URL to check
 * @return string Response code of given URL.
 */
function local_vimeoapi_get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}

/**
 * Formats # of seconds as a string of "X hrs, X mins, X secs". Zero values
 * are not displayed.
 *
 * @param int $seconds Number of seconds
 * @param string $format
 *     hms     (X hrs Y mins Z secs)
 *     ::      (X:Y:Z)
 *     seconds (Z secs)
 * @return string Formatted time string
 */
function local_vimeoapi_format_seconds($seconds, $format = 'hms') {
    if (empty($seconds))
        return;

    // If $seconds is not a number, just return it back
    if (!is_numeric($seconds)) {
        return $seconds;
    }

    $hours = floor($seconds / 3600);
    $mins = floor($seconds / 60 % 60);
    $secs = floor($seconds % 60);

    if ($format === 'hms') {
        return format_time($seconds);
    }
    else if ($format === '::') {
        if ($hours > 0 && $mins > 0 && $secs > 0)
            return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        else if ($mins > 0 && $secs > 0)
            return sprintf('%02d:%02d', $mins, $secs);
        else
            return sprintf('00:%02d', $secs);
    }
    else if ($format === 'seconds') {
        $secstring = ' ' . $seconds . ' sec';
        if ($secs > 1) { $secstring .= 's'; }
        return $secstring;
    }
}
