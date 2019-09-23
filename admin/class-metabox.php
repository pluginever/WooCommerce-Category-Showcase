<?php

namespace Pluginever\WCCCS;

class Metabox {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'init_featured_cats_settings_metabox' ] );
		add_action( 'admin_init', [ $this, 'init_additional_cats_settings_metabox' ] );

		if ( ! \WC_Category_Showcase::is_pro_installed() ) {
			add_action( 'add_meta_boxes', [ $this, 'init_promotion_metabox' ] );
		}
	}

	/**
	 * Set metabox for featured categories
	 * @since 1.0.3
	 *
	 * @return array
	 */
	public function init_featured_cats_settings_metabox() {
		$metabox = new \Pluginever\Framework\Metabox( 'wccs_featured_categories_metabox' );
		$config  = array(
			'title'        => __( 'Featured Category Settings', 'wc-category-showcase' ),
			'screen'       => 'wccs_showcase',
			'context'      => 'normal',
			'priority'     => 'high',
			'lazy_loading' => 'true',
			'fields'       => array(
				array(
					'type'     => 'select',
					'name'     => 'wccs_featured_categories',
					'label'    => __( 'Select Categories', 'wc-category-showcase' ),
					'value'    => 'all',
					'multiple' => true,
					'select2'  => 'true',
					'sanitize' => 'intval',
					'options'  => $this->get_wc_category_list(),
				),
				array(
					'type'     => 'select',
					'name'     => 'wccs_show_block_title',
					'label'    => __( 'Show Block Title', 'wc-category-showcase' ),
					'sanitize' => 'intval',
					'value'    => '1',
					'options'  => array(
						'1' => __( 'Yes', 'wc-category-showcase' ),
						'0' => __( 'No', 'wc-category-showcase' ),
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
	 * @return array
	 */
	public function init_additional_cats_settings_metabox() {
		$metabox = new \Pluginever\Framework\Metabox( 'wccs_additional_categories_metabox' );
		$config  = array(
			'title'        => __( 'Additional Category Settings', 'wc-category-showcase' ),
			'screen'       => 'wccs_showcase',
			'context'      => 'normal',
			'priority'     => 'high',
			'lazy_loading' => 'true',
			'fields'       => array(
				array(
					'type'     => 'select',
					'name'     => 'wccs_additional_categories',
					'label'    => __( 'Select Categories', 'wc-category-showcase' ),
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

	/**
	 * Show Pro version feature
	 *
	 * since 1.0.0
	 */
	public function init_promotion_metabox() {
		add_meta_box( 'wccs_showcase-promotion', __( 'What More?', 'wc-category-showcase' ), [
			$this,
			'promotion_metabox_callback'
		], 'wccs_showcase', 'side' );
	}

	/**
	 * Show Pro version feature list
	 *
	 * since 1.0.0
	 */
	public function promotion_metabox_callback() {
		?>
		<img src="<?php echo PLVR_WCCS_ASSETS . '/images/promotion.png'; ?>" alt="WOO Category Showcase Pro"
		     style="width: 100%;margin-bottom: 10px;">
		<h4 style="margin: 0;padding: 0;border-bottom: 1px solid #333;"><?php _e( 'Pro Features', 'wc-category-showcase' ); ?></h4>
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

	/**
	 * get Woocommerce product category
	 *
	 * since 1.0.0
	 *
	 * @return array
	 */
	protected function get_wc_category_list() {
		$categories = wccs_get_wc_categories( [ 'number' => 1000 ] );
		$list       = array();

		if ( is_wp_error( $categories ) ) {
			return $list;
		}

		foreach ( $categories as $key => $category ) {
			$list[ $category->term_id ] = $category->name;
		}

		return $list;
	}

}
