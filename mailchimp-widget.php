<?php
/*
Plugin Name: MailChimp Widget
Plugin URI: https://github.com/kalchas
Description: 
Author: James Lafferty
Version: 0.7
Author URI: https://github.com/kalchas
License: GPL2
*/

/*  Copyright 2010  James Lafferty  (email : james@nearlysensical.com)

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

require_once('lib/mcapi.class.php');
require_once('lib/ns_mc_plugin.class.php');
require_once('lib/ns_widget_mailchimp.class.php');

/**
 * Get the plugin object. All the bookkeeping and other setup stuff happens here.
 */

$ns_mc_plugin = NS_MC_Plugin::get_instance();

register_deactivation_hook(__FILE__, array(&$ns_mc_plugin, 'remove_options'));

?>