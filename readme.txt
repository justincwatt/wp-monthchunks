=== Monthchunks ===
Contributors: justincwatt
Donate link: http://justinsomnia.org/2005/04/monthchunks-howto/
Tags: archives
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Compactly display monthly archives by year with links to each month.

== Description ==

Display monthly archives by year with links to each month in a compact format
similar to the following:

    2010
    6 7 8 9 10 11 12
    
    2011
    1 2 3 4 5 6 7 8 9 10 11 12
    
    2012
    1 2 3 4 5 6 7 8 9

Serves as a drop in replacement for `wp_get_archives( array( 'type' => 'monthly' )`.

== Installation ==

1. Extract the zip file, drop the contents in your wp-content/plugins/ directory, and then activate from the Plugins page.
1. Edit your theme file (e.g. sidebar.php) and replace WordPress template tag `wp_get_archives();` with `monthchunks();`

== Frequently Asked Questions ==

= What if I want the years to be displayed in descending order (2012, 2011, 2010...) instead of ascending (the default)? =

The monthchunks function takes two optional parameters and the first is `year_order`, which accepts two string values: `"ascending"` or `"descending"`.

= What if I want the months to be displayed using the first letters of the names of the month (J, F, M...) instead of numbers (the default)? =

The monthchunks function takes two optional parameters and the second is `month_format`, which accepts two string values: `"numeric"` or `"alpha"`.

== Screenshots ==

1. This is how Monthchunks looks on the Twenty Eleven theme. Here you can see that four years of archives (up to 48 links, eventually) take up only 200 vertical pixels of precious sidebar real estate.

== Changelog ==
= 2.2 =
* Generate output with a single SQL query, instead of N+1, where N was the number of years of post archives

= 2.1 =
* Add year_order and month_format options
* Add `title="month_name year"` attribute (aka tooltips) to the month links 
* Lmited visible archives to posts with post_status = 'publish'
* Revise pretty html output slightly
* Add semifix for year = "0000" bug

= 2.0 =
* Remove `<ul></ul>` output to make monthchunks more of a drop-in replacement for `wp_get_archives()`
* Add logic to de-link from current month
* Sort years in chronlogical order
* Don't print separator space after last month of year

= 1.2 =
* Use `$wpdb->posts` instead of `wp_posts` as table name

= 1.1 =
* Use WordPress' `get_month_link()` function to output link to monthly archive (thanks raphaële)

= 1.0 =
* Initial version

== Upgrade Notice ==
= 2.2 =
Improved performance of SQL queries

= 2.0 =
Monthchunks is now a drop-in replacement for `wp_get_archives()`

= 1.0 =
Initial version
