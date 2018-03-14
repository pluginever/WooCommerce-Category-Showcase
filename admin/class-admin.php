<?php

namespace Pluginever\WCCCS;

class Admin {
    public function __construct() {
        add_filter( 'manage_wccs_showcase_posts_columns', array( $this, 'set_shortocode_column' ) );
        add_filter( 'manage_wccs_showcase_posts_custom_column', array( $this, 'shortocode_column_data' ), 10, 2 );

        add_action( 'save_post', array( $this, 'save_meta' ) );
    }

    /**
     * Add shortcode admin column
     *
     * @param $columns
     *
     * @return mixed
     */
    public function set_shortocode_column( $columns ) {
        unset( $columns['date'] );
        $columns['shortcode'] = __( 'Shortcode', 'woocatslider' );
        $columns['date']      = __( 'Date', 'woocatslider' );

        return $columns;
    }

    /**
     * Show shortcode admin column
     *
     * @param $column
     * @param $post_id
     */
    public function shortocode_column_data( $column, $post_id ) {
        switch ( $column ) {

            case 'shortcode' :
                echo "<code>[wccs_showcase id='{$post_id}']</code>";
                break;

        }

    }

    /**
     * Save meta value
     *
     * @param $post_id
     */
    public function save_meta( $post_id ) {
//        wp_die(var_dump($_POST));
//        if ( 'wccs_showcase' !== get_post_type( $post_id ) ) {
//            return;
//        }
//        if ( ! current_user_can( 'edit_post' ) ) {
//            return;
//        }
//        if ( define( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
//            return;
//        }
//
//        update_post_meta( $post_id, '_wcss_show_title', sanitize_key( $_POST['wcss_show_title'] ) );
    }

    /**
     * Search if term exist in the array
     *
     * @param $term_id
     * @param $array
     *
     * @return int|null|string
     */
    protected function search_array_by_term_id( $term_id, $array ) {
        foreach ( $array as $key => $val ) {
            if ( $val['term_id'] == $term_id ) {
                return $key;
            }
        }

        return null;
    }
}

new Admin();
