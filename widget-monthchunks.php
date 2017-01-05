<?php

    if ( ! defined( 'MONTHCHUNKS_VERSION' ) ) {
        header( 'Status: 403 Forbidden' );
        header( 'HTTP/1.1 403 Forbidden' );
        exit();
    }

    class Monthchunks_Widget extends WP_Widget {

        private $MONTHCHUNKS_WIDGET_TITLE;

        function __construct() {
            parent::__construct('wp-monthchunks-widget', __('MonthChunks - Blog Archives', 'monthchunks') );
            $this->MONTHCHUNKS_WIDGET_TITLE = __( 'Blog archive', 'monthchunks');
        }

        function widget($args, $instance) {

                extract($args);

                $title = apply_filters( 'widget_title', $instance['title'] );
                $years_order = $instance['years_order'];
                $mode = $instance['mode'];

                echo $before_widget;

                if( ! empty( $title ) ) {
                    echo $before_title, $title, $after_title;
                }

                echo '<ul>' ;

                $monthchunks = new MonthChunks( $years_order, $mode );
                $monthchunks->get();

                echo '</ul>',$after_widget;
        }

        public function form( $instance ) {

            if ( isset( $instance[ 'title' ] ) ) {
                $title = $instance[ 'title' ];
            }
            else {
                $title = $this->MONTHCHUNKS_WIDGET_TITLE;
            }

            if ( isset( $instance[ 'years_order' ] ) ) {
                $years_order = $instance[ 'years_order' ];
            }
            else {
                $years_order = MONTHCHUNKS_YEARS_ORDER;
            }

            if ( isset( $instance[ 'mode' ] ) ) {
                $mode = $instance[ 'mode' ];
            }
            else {
                $mode = MONTHCHUNKS_MODE;
            }

            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
                <input class="widefat" 
                       id="<?php echo $this->get_field_id( 'title' ); ?>" 
                       name="<?php echo $this->get_field_name( 'title' ); ?>" 
                       type="text" value="<?php echo esc_attr( $title ); ?>" />
                
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('years_order'); ?>"><?php _e('Years order', 'monthchunks'); ?></label>
                <select name="<?php echo $this->get_field_name('years_order'); ?>" id="<?php echo $this->get_field_id('years_order'); ?>" class="widefat">
                <?php
                $order_options = array(
                    'ascending' => __( 'Ascending' , 'monthchunks' ), 
                    'descending' => __( 'Descending', 'monthchunks' )
                );
                foreach ($order_options as $option => $option_string) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $years_order == $option ? ' selected="selected"' : '', '>', $option_string , '</option>';
                }
                ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('mode'); ?>"><?php _e('Mode', 'monthchunks'); ?></label>
                <select name="<?php echo $this->get_field_name('mode'); ?>" id="<?php echo $this->get_field_id('mode'); ?>" class="widefat">
                <?php
                $mode_options = array( 
                    'numeric' => __( 'Numeric' , 'monthchunks' ), 
                    'alpha' => __( 'Alpha', 'monthchunks' ),
                    'abbreviation' => __( 'Abbreviation', 'monthchunks' )
                );
                foreach ($mode_options as $option => $option_string) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $mode == $option ? ' selected="selected"' : '', '>', $option_string , '</option>';
                }
                ?>
                </select>
            </p>
            <?php 
        }

        public function update( $new_instance, $old_instance ) {
            $instance = array();

            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['years_order'] = strip_tags( $new_instance['years_order'] );
            $instance['mode'] = strip_tags( $new_instance['mode'] );

            return array_merge( $old_instance, $instance);
        }
    }

    function register_monthchunks_widget() {
        register_widget( 'MonthChunks_Widget' );
    }
    add_action( 'widgets_init', 'register_monthchunks_widget' );
