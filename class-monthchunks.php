<?php

    if ( ! defined( 'MONTHCHUNKS_VERSION' ) ) {
        header( 'Status: 403 Forbidden' );
        header( 'HTTP/1.1 403 Forbidden' );
        exit();
    }

    class MonthChunks {

        private $year_order;
        private $month_format;
        private $month_names = array();

        private $current_month;
        private $current_year;

        public function __construct( $year_order , $month_format ) {

            if ( !empty( $year_order ) ) {
                $this->year_order = $year_order;
            } else {
                $this->year_order = MONTHCHUNKS_MODE;
            }

            if ( !empty( $month_format ) ) {
                $this->month_format = $month_format;
            } else {
                $this->month_format = MONTHCHUNKS_MODE;
            }

            $this->month_names = $this->get_month_names( $this->month_format );

            // if current page is monthly archive, get current year/month
            if ( is_month() ) {
                $this->current_month = get_the_time( 'n' );
                $this->current_year  = get_the_time( 'Y' );
            } else {
                $this->current_month = '';
                $this->current_year  = '';
            }
        }

        public function get() {

            $years = $this->get_all_posts_by_year( $this->year_order );

            // each list item will be the year and the months which have blog posts
            foreach ( $years as $year => $months ) {
                // start the list item displaying the year
                print "<li><strong>" . esc_html( $year ) . " </strong><br />\n";

                // loop through each month, creating a link
                // followed by a single space
                foreach ( $months as $month ) {
                    $this->print_month( $month, $year );
                }
                print "</li>\n\n";
            }
        }

        private function get_month_names($month_format) {

            global $wp_locale;

            // "cache" month names and month display abbreviations
            $month_names = array();

            foreach ( $this->month_codes as $key => $value ) {
                $month_names[$key] = $wp_locale->get_month( $key );
                if ( $month_format == "alpha" ) {
                    // this might not produce a meaningful output for all locales (e.g. Japanese)
                    $this->month_codes[$key] = mb_strtoupper( mb_substr( $month_names[$key], 0, 1 ) );
                } else if ($month_format == "abbreviation") {
                    // this will produce larger abbreviations than "alpha"
                    $this->month_codes[$key] = $wp_locale->get_month_abbrev( $month_names[$key] );
                }
            }

            return $month_names;
        }

        private function get_all_posts_by_year( $year_order ) {

            global $wpdb;

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

            $years = $this->group_posts( $archives );

            return $years;
        }

        private function group_posts ( $archives ) {
            // group month archives by year, to ease output
            $years = array();
            foreach ( $archives as $archive ) {
                $years[$archive->post_year][] = $archive->post_month;
            }

            return $years;
        }

        private function print_month ( $month, $year ) {

            $tooltip = "title='" . esc_attr( $this->month_names[$month] . ' ' . $year ) . "'";
            $month_link = get_month_link( $year, $month );
            $month_text = esc_html( $this->month_codes[$month] );

            if ( $year == $this->current_year && $month == $this->current_month ) {
                // display the current month in bold without a link
                print "<strong $tooltip>$month_text</strong>\n";
            } else {
                print "<a href='$month_link' $tooltip>$month_text</a>\n";
            }

        }

        private $month_codes = array(
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
    }
?>
