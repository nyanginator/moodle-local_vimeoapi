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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/vimeoapi/locallib.php');

/**
 * Gets an album's duration. Checks Moodle Cache first. If it's not there,
 * get it through the Vimeo API.
 *
 * @param stdClass $albumid Vimeo album ID.
 * @param string $format
 *     hms     (X hrs Y mins Z secs)
 *     ::      (X:Y:Z)
 *     seconds (Z secs)
 * @return string Formatted string showing the album's duration.
 */
function local_vimeoapi_get_album_duration($albumid, $format = 'hms') {
    if (empty($albumid))
        return;

    // First check Moodle Cache
    $cache = cache::make('local_vimeoapi', 'albumdurations');
    $duration = $cache->get($albumid);
    $gotcached = true;

    // Override cache retrieval if disabled in settings
    if (!get_config('local_vimeoapi', 'caching') || local_vimeoapi_force_cache_update_from_queryparam()) {
        unset($duration);
    }

    // Connect to Vimeo only if duration hasn't been Moodle-cached
    if (empty($duration)) {
        $duration = local_vimeoapi_get_album_field($albumid, 'duration');

        // Cache the duration for this album
        $cache->set($albumid, $duration);

        $gotcached = false;
    }

    return local_vimeoapi_format_seconds($duration, $format) . ' ' . local_vimeoapi_display_origin($gotcached);
}

/**
 * Gets a video's duration. Checks Moodle Cache first. If it's not there,
 * get it through the Vimeo API.
 *
 * @param stdClass $videoid Vimeo video ID.
 * @param string $format
 *     hms     (X hrs Y mins Z secs)
 *     ::      (X:Y:Z)
 *     seconds (Z secs)
 * @return string Formatted string showing the video's duration.
 */
function local_vimeoapi_get_video_duration($videoid, $format = 'hms') {
    if (empty($videoid))
        return;

    // First check Moodle Cache
    $cache = cache::make('local_vimeoapi', 'videodurations');
    $duration = $cache->get($videoid);
    $gotcached = true;

    // Override cache retrieval if disabled in settings
    if (!get_config('local_vimeoapi', 'caching') || local_vimeoapi_force_cache_update_from_queryparam()) {
        unset($duration);
    }

    // Connect to Vimeo only if duration hasn't been Moodle-cached
    if (empty($duration)) {
        $duration = local_vimeoapi_get_video_field($videoid, 'duration');

        // Cache the duration for this video
        $cache->set($videoid, $duration);

        $gotcached = false;
    }

    return local_vimeoapi_format_seconds($duration, $format) . ' ' . local_vimeoapi_display_origin($gotcached);
}

/**
 * Gets a video's thumbnail image link. Checks Moodle Cache first. If it's not
 * there, get it through the Vimeo API.
 *
 * @param stdClass $videoid Vimeo video ID.
 * @param string $size Requested thumbnail size: small/medium/large
 * @param bool $displayorigin Override displaying of origin.
 * @return string Image link of video thumbnail.
 */
function local_vimeoapi_get_video_thumb($videoid, $size = 'large', $displayorigin = true) {
    if (empty($videoid))
        return;

    // First check Moodle Cache
    $cache = cache::make('local_vimeoapi', 'videothumbs');
    $thumb = $cache->get($videoid);
    $gotcached = true;

    // Override cache retrieval if disabled in settings
    if (!get_config('local_vimeoapi', 'caching') || local_vimeoapi_force_cache_update_from_queryparam()) {
        unset($thumb);
    }

    // Connect to Vimeo only if thumb hasn't been Moodle-cached
    if (empty($thumb)) {
        $thumb = local_vimeoapi_get_video_thumb_helper($videoid, $size);

        // Cache the thumb for this video
        $cache->set($videoid, $thumb);

        $gotcached = false;
    }

    if ($displayorigin) {
    return $thumb . ' ' . local_vimeoapi_display_origin($gotcached);
    }
    else {
        return $thumb;
    }
}
