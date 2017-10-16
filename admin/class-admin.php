<?php

namespace Pluginever\WCCCS;

class Admin {
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
        add_action( 'add_meta_boxes', array( $this, 'builder_metabox' ) );

        //ajax
        add_action( 'wp_ajax_nopriv_wccs_add_term', array( $this, 'wccs_add_term' ) );
        add_action( 'wp_ajax_wccs_add_term', array( $this, 'wccs_add_term' ) );

        add_action( 'wp_ajax_nopriv_wccs_remove_term', array( $this, 'wccs_remove_term' ) );
        add_action( 'wp_ajax_wccs_remove_term', array( $this, 'wccs_remove_term' ) );

        add_filter( 'manage_wccs_showcase_posts_columns', array( $this, 'set_shortocode_column' ) );
        add_filter( 'manage_wccs_showcase_posts_custom_column', array( $this, 'shortocode_column_data' ), 10, 2 );

        add_action('save_post', array( $this, 'save_meta'));
    }

    public function load_admin_scripts() {
        $suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
        wp_register_style( 'wc-category-showcase-admin', PLVR_WCCS_ADMIN_ASSETS . "/css/wc-category-showcase-admin{$suffix}.css", [], date( 'i' ) );
        wp_register_script( 'wc-category-showcase-admin', PLVR_WCCS_ADMIN_ASSETS . "/js/wc-category-showcase-admin{$suffix}.js", [ 'jquery', 'wp-color-picker' ], date( 'i' ), true );
        wp_register_script( 'wc-color-picker', PLVR_WCCS_ADMIN_ASSETS . "/js/alpha-color-picker.js", [ 'jquery','wp-color-picker' ], date( 'i' ), true );
        global $post;
        wp_localize_script( 'wc-category-showcase-admin', 'wccs', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'wccs_admin_action' ),
            'post_id' => $post->ID,
            'additional_max' => 6,
            'additional_selected' => count(get_post_meta( $post->ID, '_wccs_additional_categories', true )),
        ] );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'wc-category-showcase-admin' );
        wp_enqueue_script( 'wc-category-showcase-admin' );
        wp_enqueue_script( 'wc-color-picker' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_style( 'thickbox' );
    }

    public function builder_metabox() {
        add_meta_box( 'wccs-builder-metabox', __( 'Showcase Builder', 'wc-category-showcase' ), array(
            $this,
            'wcss_builder_metabox_callback'
        ), 'wccs_showcase' );
    }

    public function wcss_builder_metabox_callback( $post ) {
        add_thickbox();
        wp_enqueue_media();
        ?>
        <div class="wccs-admin-builder plvr">
            <h3 class="pl-10 mb-0"><?php _e('Featured Categories', 'wc-category-showcase'); ?></h3>
            <p class="pl-10"><?php _e('Add unlimited number of featured category. Adding more then 1 category will turn featured categories into slider. (Actual image size may be different on the public side of the site.)', 'wc-category-showcase'); ?></p>
            <div class="featured-categories">
                <ul class="featured-categories-list">
                    <?php
                    $featured_categories = get_post_meta( $post->ID, '_wccs_featured_categories', true );
                    foreach ( $featured_categories as $featured_cat ) {
                        $description = (strlen($featured_cat['desc']) > 100) ? substr($featured_cat['desc'],0,100).'...' : $featured_cat['desc'];

                        $html = '<li>';
                        $html .= '<img src="' . $featured_cat['image'] . '" alt="">';
                        $html .= '<span class="category-details">';
                        $html .= '<span class="category-name">' . $featured_cat['name'] . '</span>';
                        $html .= '<span class="category-description">' . wpautop($description) . '</span>';
                        $html .= '</span>';
                        $html .= '<a href="#" class="tool-link tool-link-settings wccs-edit-item" data-scope="featured" data-post-id="' . $post->ID . '" data-term-id="' . $featured_cat['term_id'] . '"><i class="dashicons dashicons-admin-generic"></i></a>';
                        $html .= '<a href="#" class="tool-link tool-link-edit wccs-remove-item"  data-scope="featured" data-post-id="' . $post->ID . '" data-term-id="' . $featured_cat['term_id'] . '"><i class="dashicons dashicons-trash"></i></a>';
                        $html .= '</li>';
                        echo $html;
                    }
                    ?>
                    <li class="add-new-slide">
                        <img class="ajax-loading-icon" src="<?php echo PLVR_WCCS_ASSETS . '/images/spinner.gif'; ?>" alt="">
                        <div class="tools">
                            <div class="form-group">
                                <label class="control-label" for="selected-term-id"><?php _e('Select New Category:', 'wc-category-showcase'); ?></label>
                                <select name="selected-term-id"  class="form-control selected-term-id">
                                    <?php
                                    $categories = wccs_get_wc_categories();
                                    foreach ( $categories as $term_id => $name ) {
                                        echo "<option value='$term_id'>$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <a href="#" class="button button-secondary add-new-category" data-scope="featured"><?php _e('Add', 'wc-category-showcase'); ?></a>
                        </div>
                    </li>
                </ul>
            </div>


            <h3 class="pl-10 mb-0"><?php _e('Additional Categories', 'wc-category-showcase');?></h3>
            <p class="pl-10"><?php _e('You can add 6 different category as additional categories. (Actual image size may be different on the public side of the site.)', 'wc-category-showcase'); ?></p>
            <div class="additional-categories">
                <ul class="featured-categories-list">
                    <?php
                    $additional_categories = get_post_meta( $post->ID, '_wccs_additional_categories', true );
                    foreach ( $additional_categories as $additional_cat ) {
                        $html = '<li>';
                        $html .= '<img src="' . $additional_cat['image'] . '" alt="">';
                        $html .= '<a href="#" class="tool-link tool-link-edit wccs-remove-item" data-scope="additional" data-post-id="' . $post->ID . '" data-term-id="' . $additional_cat['term_id'] . '"><i class="dashicons dashicons-trash"></i></a>';
                        $html .= '</li>';
                        echo $html;
                    }
                    ?>



                    <li class="add-new-slide">
                        <img class="ajax-loading-icon" src="<?php echo PLVR_WCCS_ASSETS . '/images/spinner.gif'; ?>" alt="">
                        <div class="tools">
                            <div class="form-group">
                                <label class="control-label" for="email"><?php _e('Select Category:', 'wc-category-showcase'); ?></label>
                                <select name="selected-term-id"  class="form-control selected-term-id">
                                    <?php
                                    $categories = wccs_get_wc_categories();
                                    foreach ( $categories as $term_id => $name ) {
                                        echo "<option value='$term_id'>$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <a href="#" class="button button-secondary add-new-category" data-scope="additional"><?php _e('Add', 'wc-category-showcase'); ?></a>
                        </div>
                    </li>

                </ul>
            </div>


            <div class="form-group pl-10">
                <label for=""><?php _e('Show Title ', 'wc-category-showcase'); ?></label>
                <select name="wcss_show_title" class="form-control maxw-200">
                    <option value="yes"><?php _e('Yes', 'wc-category-showcase'); ?> </option>
                    <option value="no"><?php _e('No', 'wc-category-showcase'); ?> </option>
                </select>
            </div>

        </div>


        <div id="wccs-modal-window" style="display:none;">
            <div class="plvr wccs-modal-window">
                <form>
                    <div class="form-group">
                        <label for="category-name"><?php _e('Category Image', '');?>:</label>
                        <div class="category-image-wrp">
                            <img class="category-image" src="http://plugineverdemo.dev/wp-content/uploads/2017/02/full-sleeve.jpg" alt="">
                            <input id="wccs-category-imageid" type="hidden" value="http://plugineverdemo.dev/wp-content/uploads/2017/02/full-sleeve.jpg">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category-color"><?php _e('Color', '');?>:</label>
                        <input type="text" class="color-picker" data-alpha="false" data-default-color="rgba(0,0,0,0.85)"  id="category-color" value="#fff"/>
                    </div>

                    <div class="form-group">
                        <label for="category-bgcolor"><?php _e('Background Color', '');?>:</label>
                        <input type="text" class="color-picker" data-alpha="true" data-default-color="rgba(0,0,0,0.85)" id="category-bgcolor" value="rgba(209,0,55,0.7)"/>
                    </div>

                    <div class="form-group">
                        <label for="category-name"><?php _e('Category Name', '');?>:</label>
                        <input type="text" class="form-control" id="category-name">
                    </div>
                    <div class="form-group">
                        <label for="category-desc"><?php _e('Category Description', '');?>:</label>
                        <textarea class="form-control" name="category-desc" id="category-desc" cols="30" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>
        <a href="#TB_inline?width=400&height=500&inlineId=wccs-modal-window" class="thickbox">View the WordPress Codex!</a>
        <?php
    }


    public function wccs_add_term() {
        error_log( print_r( $_POST, true ) );

        if ( ! wp_verify_nonce( $_POST['nonce'], "wccs_admin_action" ) ) {
            exit( "No naughty business please" );
        }

        $term = wccs_get_term_details( $_POST['term_id'] );

        if ( ! $term ) {
            wp_send_json_error();
        }
        if( !in_array($_POST['scope'], array('featured', 'additional')) ) return;

        $meta_key = sanitize_key("_wccs_{$_POST['scope']}_categories");

        $saved_categories = get_post_meta( $_POST['post_id'], $meta_key, true );

        if ( ! $saved_categories ) {
            $saved_categories = array();
        }

        if( $_POST['scope'] == 'additional' && count($saved_categories) >= 6 ){
            wp_send_json_error(array('msg' => __('Reached Maximum limit', '')));
        }

        $term_ids = wp_list_pluck( $saved_categories, 'term_id' );

        if ( in_array( $_POST['term_id'], $term_ids ) ) {
            wp_send_json_error( array( 'msg' => __( 'Category already added', '' ) ) );
        }

        $saved_categories [] = $term;
        $saved_categories    = array_filter( $saved_categories );
        update_post_meta( $_POST['post_id'], $meta_key, $saved_categories );

        $key = $this->search_array_by_term_id($_POST['term_id'], $saved_categories );

        wp_send_json_success( array( 'index' => $key, 'category' => $saved_categories[ $key ] ) );


        wp_die();
    }


    public function wccs_remove_term() {

        if ( ! wp_verify_nonce( $_POST['nonce'], "wccs_admin_action" ) ) {
            exit( "No naughty business please" );
        }
        $term_id = $_POST['term_id'];


        if( !in_array($_POST['scope'], array('featured', 'additional')) ) return;

        $meta_key = sanitize_key("_wccs_{$_POST['scope']}_categories");

        $saved_categories = get_post_meta( $_POST['post_id'], $meta_key, true );


        $term_ids            = wp_list_pluck( $saved_categories, 'term_id' );

        if ( ! in_array( $_POST['term_id'], $term_ids ) ) {
            wp_send_json_error( array( 'msg' => __( 'Category not in selection', '' ) ) );
        }

        foreach ( $saved_categories as $key => $category ) {

            if( (int) $category['term_id'] == (int) $term_id ){
                unset($saved_categories[$key]);
            };
        }

        $saved_categories = array_filter( $saved_categories );
        update_post_meta( $_POST['post_id'], $meta_key, $saved_categories );
        wp_send_json_success();

        wp_die();
    }


    public function set_shortocode_column( $columns ) {
        unset( $columns['date'] );
        $columns['shortcode'] = __( 'Shortcode', 'woocatslider' );
        $columns['date']      = __( 'Date', 'woocatslider' );

        return $columns;
    }

    public function shortocode_column_data( $column, $post_id ) {
        switch ( $column ) {

            case 'shortcode' :
                echo "<code>[wccs_showcase id='{$post_id}']</code>";
                break;

        }

    }

    public function save_meta($post_id){
        if( ! 'wccs_showcase' == get_post_type( $post_id )) return ;
        if( ! current_user_can('edit_post') ) return ;
        if( define('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        update_post_meta( $post_id, '_wcss_show_title',  $_POST['wcss_show_title']);
    }

    protected function search_array_by_term_id($term_id, $array) {
        foreach ($array as $key => $val) {
            if ($val['term_id'] == $term_id) {
                return $key;
            }
        }
        return null;
    }
}

new Admin();
