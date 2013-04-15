<?php
/*
Plugin Name: Monthchunks
Version: 2.4
Plugin URI: http://justinsomnia.org/2005/04/monthchunks-howto/
Description: Concisely display monthly archives by year with links to each month. Replacement for <code>wp_get_archives('type=monthly')</code>
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

function monthchunks( $year_order = "descending", $month_format = "numeric" ) {
	global $wpdb, $wp_locale;
	
	// if current page is monthly archive, get current year/month
	if ( is_month() ) {
		$current_month = get_the_time( 'n' );
		$current_year  = get_the_time( 'Y' );
	} else {
		$current_month = '';
		$current_year  = '';
	}

	// "cache" month names and month display abbreviations
	$month_names = array();
	$month_codes = array(
		'01' => 1,
		'02' => 2,
		'03' => 3,
		'04' => 4,
		'05' => 5,
		'06' => 6,
		'07' => 7,
		'08' => 8,
		'09' => 9,
		'10' => 10,
		'11' => 11,
		'12' => 12
	);

	foreach ( $month_codes as $key => $value ) {
		$month_names[$key] = $wp_locale->get_month( $key );
		if ( $month_format == "alpha" ) {
			// this might not produce a meaningful output for all locales (e.g. Japanese)
			$month_codes[$key] = mb_strtoupper( mb_substr( $month_names[$key], 0, 1 ) );
		} else if ($month_format == "abbreviation") {
			// this will produce larger abbreviations than "alpha"
			$month_codes[$key] = $wp_locale->get_month_abbrev( $month_names[$key] );
		}
	}

	// set SQL order by sort order
	if ( $year_order == "ascending" ) {
		$year_order = "ASC";
	} else {
		$year_order = "DESC";
	}

	// get an array of months in which there are posts
	$sql = "
		SELECT DATE_FORMAT(post_date, '%m') as post_month,
		DATE_FORMAT(post_date, '%Y') as post_year
		FROM $wpdb->posts
		WHERE post_type = 'post'
		AND post_status = 'publish'
		GROUP BY post_year, post_month
		HAVING post_year <> '0000'
		ORDER BY post_year $year_order, post_month ASC
	";
	$archives = $wpdb->get_results( $sql );
	
	// group month archives by year, to ease output
	$years = array();
	foreach ( $archives as $archive ) {
		$years[$archive->post_year][] = $archive->post_month;
	}

	// each list item will be the year and the months which have blog posts
	foreach ( $years as $year => $months ) {
		// start the list item displaying the year
		print "<li><strong>" . esc_html( $year ) . " </strong><br />\n";
		
		// loop through each month, creating a link
		// followed by a single space
		foreach ( $months as $month ) {
			$tooltip = "title='" . esc_attr( $month_names[$month] . ' ' . $year ) . "'";
			$month_link = get_month_link( $year, $month );
			$month_text = esc_html( $month_codes[$month] );

			if ( $year == $current_year && $month == $current_month ) {
				// display the current month in bold without a link
				print "<strong $tooltip>$month_text</strong>\n";
			} else {
				print "<a href='$month_link' $tooltip>$month_text</a>\n";
			}
		}
		print "</li>\n\n";
	}
}
