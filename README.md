# Moodle - Vimeo API
https://github.com/nyanginator/moodle-local_vimeoapi

Setup and use the Vimeo API through a Moodle local plugin. 

Table of Contents
=================
* [What This Plugin Does](#what-this-plugin-does)
* [Install](#install)
* [Usage](#usage)
  * [Admin Configuration](#admin-configuration)
  * [Setup](#setup)
  * [Retrieving Data Through the API](#retrieving-data-through-the-api)
* [Notes](#notes)
* [Uninstall](#uninstall)
* [Contact](#contact)

What This Plugin Does
=====================
This is a Moodle local plugin that helps to setup and utilize the Vimeo API for getting data from Vimeo videos and albums. Currently, this plugin supports retrieving album durations, video durations, and video thumbnail links. Data is stored in the Moodle Cache for faster retrieval later.

Install
=======
Create the folder `local/vimeoapi` in your Moodle installation and copy the contents of this repository there. Login as the Moodle admin and proceed through the normal installation of this new plugin. If the plugin is not automatically found, you may have to go to Site Administration > Notifications.

This plugin is based on the [Vimeo PHP Library](https://github.com/vimeo/vimeo.php), which you should install using Composer. For more information on the Vimeo API, consult the [Vimeo API documentation](https://developer.vimeo.com/api).

Usage
=====

Admin Configuration
-------------------
Settings for this plugin can be found at Site Administration > Plugins > Local Plugins > Vimeo API.

![Admin Configuration](https://raw.githubusercontent.com/nyanginator/moodle-local_vimeoapi/master/screenshots/admin-config.jpg)

* **Enable Cache** - Cache any data retrieved using the Vimeo API in the Moodle Cache. When this is disabled, you can override this setting on any specific page by just adding the query param `vimeoapi_updatecache`.
* **Display Origin** - Append a string denoting whether the requested data was retrieved from the cache.
* **Autoload.php Path** - Specify the path to the `autoload.php` file, either the `autoload.php` in the Composer-generated vendor directory, or the `autoload.php` in `vimeo/vimeo-api`.
* **Client ID** - Find your client ID in the settings of your Vimeo API App.
* **Client Secret** - Generate client secrets your Vimeo API App.
* **Access Token** - Generate access tokens with predefined permission scopes in your Vimeo API App.

Setup
-----
1. Locate the directory where the `autoload.php` code for loading the Vimeo API is located. Copy the absolute path of this directory over to this plugin's settings in Moodle.
2. Go to the [Vimeo Developer site](https://developer.vimeo.com) and click on **Create App**. This App will basically serve as your entry point into Vimeo.
3. In the App's settings, you will see the client identifier. Copy this over to this plugin's settings in Moodle.
4. A client secret should have already been generated for you. Copy it over to this plugin's settings in Moodle.
5. Read about scopes [here](https://developer.vimeo.com/api/authentication#understanding-the-auth-process). Check off the desired scopes you want and then click on **Generate** to get an access token. Copy the token value into this plugin's settings in Moodle.

Retrieving Data Through the API
-------------------------------
The intended use of this plugin is from within your own PHP code. This allows you full control over what, where, and how to display retrieved data. Here is a basic example:

```php
require_once($CFG->dirroot . '/local/vimeoapi/lib.php');
    
$video_duration = local_vimeoapi_get_video_duration(117526873);
$video_thumb = local_vimeoapi_get_video_thumb(117526873);

echo 'Video duration: ' . $video_duration . '<br>';
echo 'Video thumbnail link: ' . $video_thumb . '<br>';
```
The above code outputs:

```
Video duration: 2 mins 13 secs
Video thumbnail link: https://i.vimeocdn.com/video/504257437_640.jpg 
```

Notes
=====
* Vimeo limits the number of API calls you can make per hour. This is why it is important to avoid making API calls when not absolutely necessary and to utilize the Moodle Cache. Aside from this limitation, too many API calls can slow down your site.
* If you are having trouble retrieving data, check the location of the vendor directory, the scope of the access token you are using, and whether the cache needs to be refreshed.
* Thumbnails retrieved using the API have the sizes small, medium, and large defined as images having widths 100px, 200px, and 640px, respectively. This is set in the `getVideoThumb()` function of `locallib.php`. To see what other sizes are available, take a look at the `$available_sizes` array.
* For public videos, the plugin will try to avoid making API calls by accessing the data from `https://vimeo.com/api/v2/video/{vimeo_id}.php`. Consequently, if you only need access to public video information, it is not necessary to create an App in Vimeo, and no need for the client ID, client secret, and access token.
* Currently, only 3 functions are implemented:
  - `local_vimeo_api_get_album_duration()`
  - `local_vimeoapi_get_video_duration()`
  - `local_vimeoapi_get_video_thumb()`

   Because they are the only ones I have use for at the moment. More can easily be added in `lib.php` by following these functions as templates.
* For public video data that is accessible through `https://vimeo.com/api/v2/video/$vimeoid.php`, these are the valid variables:
  - description
  - duration
  - height
  - tags
  - title
  - upload_date
  - url
  - user_id
  - user_name
  - user_portrait_huge
  - user_portrait_large
  - user_portrait_medium
  - user_portrait_small
  - user_url
  - stats_number_of_comments
  - stats_number_of_likes
  - stats_number_of_plays
  - width
* To get private video data (and album data), you must call the Vimeo API. Refer to the [Vimeo API Reference](https://developer.vimeo.com/api/reference) to determine what endpoint to use in the `$lib->request()` call. Once you have the object, you can do a `print_r` to see what data is available. Try to [request only required parameters](https://developer.vimeo.com/api/common-formats#working-with-parameters) to reduce response time.

Uninstall
=========
Uninstall by going to Site Administration > Plugins > Plugins Overview and using the Uninstall link for the `local/vimeoapi` plugin.

Contact
=======
Nicholas Yang  
https://nyanginator.com
