<?php
/*
Plugin Name: monthchunks
Version: 2.1
Plugin URI: http://justinsomnia.org/2005/04/monthchunks-howto/
Description: Display your monthly archives compactly by year with individual links to each month. Replacement for <code>wp_get_archives('type=monthly')</code>
Author: Justin Watt
Author URI: http://justinsomnia.org/

INSTRUCTIONS

1) Save this file as monthchunks.php in /path/to/wordpress/wp-content/plugins/ 
2) Activate "monthchunks" from the Wordpress control panel
3) In your sidebar.php template file, replace wp_get_archives('type=monthly'); with monthchunks();


CHANGELOG

2.1
added year_order and month_format options
added title="month_name year" attribute (aka tooltips) to the month links 
limited visible archives to posts with post_status = 'publish'
revised pretty html output slightly
added semifix for year = "0000" bug

2.0
removed <ul></ul> output to make monthchunks more of a drop-in replacement for wp_get_archives()
added logic to de-link from current month
sort years in chronlogical order
don't print separator space after last month of year

1.2
used $wpdb->posts instead of wp_posts as table name

1.1
used wordpress's get_month_link() function to output link to monthly archive (thanks raphaële)

1.0
inital version
turned custom_archive function into monthchunks plugin (thanks jackson)


LICENSE

monthchunks.php
Copyright (C) 2006 Justin Watt
justincwatt@gmail.com
http://justinsomnia.org/

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

function monthchunks($year_order = "ascending", $month_format = "numeric")
{
    // get access to wordpress' database object
    global $wpdb;
    $current_month = "";
    $current_year  = "";
    
    // get current year/month if current page is monthly archive
    if (is_month())
    {
        $current_month = get_the_time('n');
        $current_year  = get_the_time('Y');
    }
    
    // set SQL order by sort order
    if ($year_order == "descending")
    {
        $year_order = "DESC";
    }
    else
    {
        $year_order = "ASC";
    }

    // set format for month display
    if ($month_format == "alpha")
    {
        $month_format = "LEFT(DATE_FORMAT(post_date, '%M'), 1)";
    }
    else
    {
        $month_format = "DATE_FORMAT(post_date, '%c')";
    }

    // get an array of the years in which there are posts
    $wpdb->query("SELECT DATE_FORMAT(post_date, '%Y') as post_year
                  FROM $wpdb->posts
                  WHERE post_status = 'publish'
                  GROUP BY post_year
                  HAVING post_year <> '0000'
                  ORDER BY post_year $year_order");
    $years = $wpdb->get_col();
    
    // each list item will be the year and the months which have blog posts
    foreach($years as $year)
    {
        // get an array of months for the current year without leading zero
        // sort by month with leading zero
        $months = $wpdb->get_results("SELECT DATE_FORMAT(post_date, '%c') as post_month, 
                                      $month_format AS display_month, 
                                      DATE_FORMAT(post_date, '%M') as post_month_name
                                      FROM $wpdb->posts
                                      WHERE DATE_FORMAT(post_date, '%Y') = $year
                                      AND post_status = 'publish'
                                      GROUP BY DATE_FORMAT(post_date, '%m')
                                      ORDER BY post_date");

        // start the list item displaying the year
        print "<li><strong>$year</strong><br />\n";
        
        // loop through each month, creating a link
        // followed by a single space
        $month_count = count($months);
        $i = 0;
        foreach($months as $month)
        {
            // display the current month in bold without a link
            if ($year == $current_year && $month->post_month == $current_month)
            {
                print "<strong title='$month->post_month_name $year'>$month->display_month</strong>";
            }
            else
            {
                print "<a href='" . get_month_link($year, $month->post_month) . "' title='$month->post_month_name $year'>" . $month->display_month . "</a>";
            }

            if ($i < $month_count-1)
            {
                print " \n";
            }
            $i++;
        }

        //end the year list item
        print "</li>\n\n";
    }
}

?>