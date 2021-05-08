<?php
/*
Plugin Name: Monthchunks
Version: 3.1.2
Plugin URI: http://justinsomnia.org/2005/04/monthchunks-howto/
Description: Concisely display monthly archives by year with links to each month. Replacement for <code>wp_get_archives('type=monthly'). Widget support.</code>
Author: Justin Watt, Xavi Ivars
Author URI: http://justinsomnia.org/
Text Domain: monthchunks
Domain Path: /languages
*/

/*
LICENSE
Copyright 2012 Justin Watt justincwatt@gmail.com
          2013 Xavi Ivars xavi.ivars@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'MONTHCHUNKS_VERSION', '3.1' );

if ( ! defined( 'MONTHCHUNKS_FILE' ) ) {
	define( 'MONTHCHUNKS_FILE', __FILE__ );
}

if ( ! class_exists( 'MonthChunks' ) ) {

    define( 'MONTHCHUNKS_YEARS_ORDER', 'descending' );
    define( 'MONTHCHUNKS_MODE', 'numeric' );

    function monthchunks_load_textdomain() {
        load_plugin_textdomain( 'monthchunks', false, dirname( plugin_basename( MONTHCHUNKS_FILE ) ) . '/languages/' );
    }
    add_action( 'plugins_loaded', 'monthchunks_load_textdomain' );

    require_once 'class-monthchunks.php';
    require_once 'widget-monthchunks.php';

    // Function for backwards compatibility
    function monthchunks( $year_order = "descending", $month_format = "numeric" ) {
        $mchunks = new MonthChunks($year_order, $month_format);
        $mchunks->get();
    }
}
