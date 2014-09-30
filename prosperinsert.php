<?php
/*
Plugin Name: ProsperInsert
Description: Easily place products within pages and posts.
Version: 1.0
Author: Prosperent Brandon
Author URI: http://prosperent.com
Plugin URI: http://community.prosperent.com/forumdisplay.php?35-Wordpress-Plugin-Suite
License: GPLv3

    Copyright 2012  Prosperent Brandon  (email : brandon@prosperent.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Default caching time for products (in seconds)
if (!defined( 'PROSPERINSERT_CACHE_PRODS'))
    define( 'PROSPERINSERT_CACHE_PRODS', 604800 );
// Default caching time for trends and coupons (in seconds)
if (!defined( 'PROSPERINSERT_CACHE_COUPS'))
    define( 'PROSPERINSERT_CACHE_COUPS', 3600 );

if (!defined( 'WP_CONTENT_DIR'))
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if (!defined('PROSPERINSERT_URL'))
    define('PROSPERINSERT_URL', plugin_dir_url(__FILE__));
if (!defined('PROSPERINSERT_PATH'))
    define('PROSPERINSERT_PATH', plugin_dir_path(__FILE__));
if (!defined('PROSPERINSERT_BASENAME'))
    define('PROSPERINSERT_BASENAME', plugin_basename(__FILE__));
if (!defined('PROSPERINSERT_FOLDER'))
    define('PROSPERINSERT_FOLDER', plugin_basename(dirname(__FILE__)));
if (!defined('PROSPERINSERT_FILE'))
    define('PROSPERINSERT_FILE', basename((__FILE__)));
if (!defined('PROSPERINSERT_CACHE'))
	define('PROSPERINSERT_CACHE', WP_CONTENT_DIR . '/prosperent_cache');
if (!defined('PROSPERINSERT_INCLUDE'))
	define('PROSPERINSERT_INCLUDE', PROSPERINSERT_PATH . 'includes');
if (!defined('PROSPERINSERT_MODEL'))
	define('PROSPERINSERT_MODEL', PROSPERINSERT_INCLUDE . '/models');
if (!defined('PROSPERINSERT_VIEW'))
	define('PROSPERINSERT_VIEW', PROSPERINSERT_INCLUDE . '/views');
if (!defined('PROSPERINSERT_IMG'))
	define('PROSPERINSERT_IMG', PROSPERINSERT_URL . 'includes/img');
if (!defined('PROSPERINSERT_JS'))
	define('PROSPERINSERT_JS', PROSPERINSERT_URL . 'includes/js');
if (!defined('PROSPERINSERT_CSS'))
	define('PROSPERINSERT_CSS', PROSPERINSERT_URL . 'includes/css');
if (!defined('PROSPERINSERT_THEME'))
	define('PROSPERINSERT_THEME', WP_CONTENT_DIR . '/prosperent-themes');

//error_reporting(0);   
	
require_once(PROSPERINSERT_INCLUDE . '/ProsperIndexController.php');

if(is_admin())
{
	require_once(PROSPERINSERT_INCLUDE . '/ProsperAdminController.php');
}

