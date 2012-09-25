<?php
/*
Plugin Name: Monthchunks
Version: 2.2
Plugin URI: http://justinsomnia.org/2005/04/monthchunks-howto/
Description: Display your monthly archives compactly by year with individual links to each month. Replacement for <code>wp_get_archives('type=monthly')</code>
Author: Justin Watt
Author URI: http://justinsomnia.org/

LICENSE
Copyright 2012 Justin Watt justincwatt@gmail.com

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

function monthchunks( $year_order = "ascending", $month_format = "numeric" ) {
    // get access to wordpress' database object
    global $wpdb;
    $current_month = "";
    $current_year  = "";
    
    // get current year/month if current page is monthly archive
    if ( is_month() ) {
        $current_month = get_the_time( 'n' );
        $current_year  = get_the_time( 'Y' );
    }
    
    // set SQL order by sort order
    if ( $year_order == "descending" ) {
        $year_order = "DESC";
    } else {
        $year_order = "ASC";
    }

    // set format for month display
    if ( $month_format == "alpha" ) {
        $month_format = "LEFT(DATE_FORMAT(post_date, '%M'), 1)";
    } else {
        $month_format = "DATE_FORMAT(post_date, '%c')";
    }

    // get an array of months in which there are posts
    $sql = "
        SELECT DATE_FORMAT(post_date, '%m') as post_month,
        DATE_FORMAT(post_date, '%Y') as post_year,
        $month_format as display_month, 
        DATE_FORMAT(post_date, '%M') as post_month_name
        FROM $wpdb->posts
        WHERE post_status = 'publish'
        GROUP BY post_year, post_month
        HAVING post_year <> '0000'
        ORDER BY post_year $year_order, post_month ASC
    ";
    $months = $wpdb->get_results( $sql );
    
    // group month result objects by year, to ease output
    $years = array();
    foreach ( $months as $month ) {
        $years[$month->post_year][] = $month;
    }

    // each list item will be the year and the months which have blog posts
    foreach ( $years as $year => $months ) {
        // start the list item displaying the year
        print "<li><strong>$year</strong><br />\n";
        
        // loop through each month, creating a link
        // followed by a single space
        foreach ( $months as $month ) {
            if ( $year == $current_year && $month->post_month == $current_month ) {
                // display the current month in bold without a link
                print "<strong title='$month->post_month_name $year'>$month->display_month</strong>\n";
            } else {
                print "<a href='" . get_month_link( $year, $month->post_month ) . "' title='$month->post_month_name $year'>" . $month->display_month . "</a>\n";
            }
        }
        print "</li>\n\n";
    }
}
