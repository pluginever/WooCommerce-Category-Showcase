<?php

namespace Pluginever\WCCCS;

class Metabox {

    public function __construct() {
        add_action( 'admin_init', [ $this, 'init_featured_cats_settings_metabox' ] );
        add_action( 'admin_init', [ $this, 'init_additional_cats_settings_metabox' ] );

        if ( ! wc_category_showcase_is_pro_active() ) {
            add_action( 'add_meta_boxes', [ $this, 'init_promotion_metabox' ] );
        }
    }

    /**
     * Set metabox for featured categories
     * @since 1.0.3
     *
     */
    public function init_featured_cats_settings_metabox() {
        $metabox = new \Pluginever\Framework\Metabox( 'wccs_featured_categories_metabox' );
        $config  = array(
            'title'        => __( 'Featured Category Settings', 'wccsp' ),
            'screen'       => 'wccs_showcase',
            'context'      => 'normal',
            'priority'     => 'high',
            'lazy_loading' => 'true',
            'fields'       => array(
                array(
                    'type'     => 'select',
                    'name'     => 'wccs_featured_categories',
                    'label'    => __( 'Select Categories', 'wccsp' ),
                    'value'    => 'all',
                    'multiple' => true,
                    'select2'  => 'true',
                    'sanitize' => 'intval',
                    'options'  => $this->get_wc_category_list(),
                ),
                array(
                    'type'     => 'select',
                    'name'     => 'wccs_show_block_title',
                    'label'    => __( 'Show Block Title', 'wccsp' ),
                    'sanitize' => 'intval',
                    'value'    => '1',
                    'options'  => array(
                        '1' => __( 'Yes', 'wccsp' ),
                        '0' => __( 'No', 'wccsp' ),
                    ),
                ),
            ),
        );
        $metabox->init( apply_filters( 'wccs_featured_metabox_fields', $config ) );
    }

    /**
     * Additional categories
     * @since 1.0.3
     *
     */
    public function init_additional_cats_settings_metabox() {
        $metabox = new \Pluginever\Framework\Metabox( 'wccs_additional_categories_metabox' );
        $config  = array(
            'title'        => __( 'Additional Category Settings', 'wccsp' ),
            'screen'       => 'wccs_showcase',
            'context'      => 'normal',
            'priority'     => 'high',
            'lazy_loading' => 'true',
            'fields'       => array(
                array(
                    'type'     => 'select',
                    'name'     => 'wccs_additional_categories',
                    'label'    => __( 'Select Categories', 'wccsp' ),
                    'value'    => 'all',
                    'multiple' => true,
                    'select2'  => 'true',
                    'sanitize' => 'intval',
                    'options'  => $this->get_wc_category_list(),
                ),
            ),
        );

        $metabox->init( apply_filters( 'wccs_addtional_metabox_fields', $config ) );
    }

    public function init_promotion_metabox() {
        add_meta_box( 'wccs_showcase-promotion', __( 'What More?', 'wccsp' ), [
            $this,
            'promotion_metabox_callback'
        ], 'wccs_showcase', 'side' );
    }

    public function promotion_metabox_callback(){
        ?>
        <img src="<?php echo PLVR_WCCS_ASSETS . '/images/promotion.png'; ?>" alt="WOO Category Showcase Pro"
             style="width: 100%;margin-bottom: 10px;">
        <h4 style="margin: 0;padding: 0;border-bottom: 1px solid #333;"><?php _e( 'Pro Features', 'wccsp' ); ?></h4>
        <ul style="padding-left: 25px;list-style: disc;">
            <li>Custom featured category image</li>
            <li>Custom additional category image</li>
            <li>Custom category title</li>
            <li>Category title show hide</li>
            <li>Category description show hide</li>
            <li>Category button show hide</li>
            <li>Category button custom text</li>
            <li>Custom content color</li>
            <li>Custom content background color</li>
            <li>Custom image column</li>
            <li>And Many More</li>
        </ul>
        <a href="http://bit.ly/woocommerce-category-showcase-pro"
           target="_blank" style="text-align: center;font-weight: bold;">Upgrade To PRO Now</a>
        <?php
    }
    protected function get_wc_category_list() {
        $categories = wccs_get_wc_categories( [ 'number' => 1000 ] );
        $list       = array();
        foreach ( $categories as $key => $category ) {
            $list[ $category->term_id ] = $category->name;
        }

        return $list;
    }

}
