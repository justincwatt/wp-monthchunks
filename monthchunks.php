<?php
/*
Plugin Name: monthchunks
Version: 1.0
Plugin URI: http://justinsomnia.org/2005/04/monthchunks-howto/
Description: Display your monthly archives by year with individual links to each month.
Author: Justin Watt
Author URI: http://justinsomnia.org/

Save this file as monthchunks.php in /path/to/wordpress/wp-content/plugins/ and activate from the Wordpress control panel. Edit the header and html structure as necessary to suit your blog.
*/

function monthchunks()
{
    // get access to wordpress' database object
    global $wpdb;

    // uncomment this line if you want to print a header above your archives
    //print "\n\n<h1>monthchunks</h1>\n";
    
    // get an array of the years in which there are posts
    $wpdb->query("SELECT DATE_FORMAT(post_date, '%Y') as post_year
                  FROM wp_posts
                  GROUP BY post_year
                  ORDER BY DATE_FORMAT(post_date, '%Y') desc");
    $years = $wpdb->get_col();
    
    // begin unordered list
    // each list item will be the year and the months which have blog posts
    print "<ul>\n";

    foreach($years as $year)
    {
        // get an array of months for the current year
        $wpdb->query("SELECT DATE_FORMAT(post_date, '%c') as post_month
                      FROM wp_posts
                      WHERE DATE_FORMAT(post_date, '%Y') = $year
                      GROUP BY post_month
                      ORDER BY DATE_FORMAT(post_date, '%m')");
        $months = $wpdb->get_col();

        // start the list item displaying the year
        // followed by a line break
        print "\t<li><strong>" . $year . "</strong><br>\n\t";
        
        // loop through each month, creating a link
        // followed by a single space
        // the link url will be: http://example.com/year/month (adjust if necessary)
        foreach($months as $month)
        {
            print "<a href='" . get_settings('home') . "/" . $year . "/" . $month . "/'>" . $month . "</a> ";
        }

        //end the list item for the given year
        print "</li>\n";
    }

    // end the unordered list
    print "</ul>\n\n";
}

?>