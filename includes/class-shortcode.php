<?php

namespace Pluginever\WCCS;

class Shortcode {

    public function __construct() {
        add_shortcode( 'wccs_showcase', array( $this, 'shortcode_render_callback' ) );
    }

    public function shortcode_render_callback( $attr ) {
        $attr = wp_parse_args( $attr, array( 'id' => null ) );

        if ( $attr['id'] == null ) {
            return;
        }
        if ( false === get_post_status( $attr['id'] ) ) {
            return;
        }

        $post_id               = intval( $attr['id'] );

        $params = [
            'wccs_featured_categories'    => [],
            'wccs_additional_categories'  => [],
            'wccs_show_block_title'       => '1',
            'wccs_featured_show_title'    => '1',
            'wccs_featured_show_desc'     => '1',
            'wccs_featured_show_button'   => '1',
            'wccs_featured_button_text'   => 'Shop Now',
            'wccs_featured_content_color' => '#fff',
            'wccs_featured_content_bg'    => 'rgba(150,88,138,.9)',
            'wccs_additional_show_title'  => '1',
            'wccs_additional_title_color' => '#000',
            'wccs_additional_content_bg'  => '#fff',
        ];

        foreach ( $params as $key => $val ){
            $meta = get_post_meta( $post_id, $key, true );
            if(false !== $meta ){
                $params[$key] = $meta;
            }
        }

        $featured_categories   = $params['wccs_featured_categories'];
        $additional_categories = $params['wccs_additional_categories'];

        if ( is_array( $additional_categories ) && count( $additional_categories > 6 ) ) {
            $additional_categories = array_slice( $additional_categories, 0, 6 );
        }

        ob_start();

        $additional_cats_width = [];
        $set                   = 0;
        $total                 = 0;


        for ( $i = 0; $i < count( $additional_categories ); $i ++ ) {

            if ( $total >= 6 ) {
                break;
            }
//            $col = rand( 1, 3 );
            $term = wccs_get_term_details( $additional_categories[ $i ], $post_id, 'additional' );

            if ( ! empty( $term['col'] ) ) {
                $col = $term['col'];
            } else {
                $col = 1;
            }

            if ( $set + $col > 3 ) {
                $col = 3 - $set;
            }

            $set += $col;
            if ( $set >= 3 ) {
                $set = 0;
            }

            $total                                                 += $col;
            $additional_cats_width[ $additional_categories[ $i ] ] = "{$col}";
        }
        $additional_categories = $additional_cats_width;
        ?>
        <div class="woo-cs has-border" id="wccs-slider-<?php echo $post_id; ?>">
            <?php if ( !empty($params['wccs_show_block_title']) ): ?>
                <h2 class="woo-cs-heading"><?php echo get_the_title( $post_id ); ?></h2>
            <?php endif; ?>
            <div class="woo-cs-inner">
                <div class="row eq-height">
                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5  woo-cs-left-block">
                        <div class="woo-cs-slider-block">
                            <div class="woo-cs-slider">

                                <?php
                                foreach ( $featured_categories as $featured_category_id ) {
                                    $featured_category = wccs_get_term_details( $featured_category_id, $post_id, 'featured' );
                                    $html              = '<div>';
                                    $html              .= '<a class="woo-cs-slide woo-cs-box-link" href="' . $featured_category['link'] . '">';
                                    $html              .= '<span class="woo-cs-slide-img-container">';
                                    $html              .= '<img src="' . $featured_category['image'] . '" alt="' . $featured_category['title'] . '">';
                                    $html              .= '</a>';
                                    $html              .= '</span>';

                                    if( $params['wccs_featured_show_title'] || $params['wccs_featured_show_desc'] || $params['wccs_featured_show_button'] ){

                                        $html              .= '<span class="woo-cs-cat-details">';

                                        if($params['wccs_featured_show_title'] == '1'){
                                            $html              .= '<span class="woo-cs-cat-title">' . $featured_category['title'] . '</span>';
                                        }

                                        if($params['wccs_featured_show_desc'] == '1'){
                                            $html              .= '<span class="woo-cs-cat-des">' . $featured_category['desc'] . '</span>';
                                        }

                                        if($params['wccs_featured_show_button'] == '1'){
                                            $html              .= '<a href="' . $featured_category['link'] . '" class="woo-cs-cat-button">' . $params['wccs_featured_button_text'] . '</a>';
                                        }

                                        $html              .= '</span>';

                                    }

                                    $html              .= '</div>';
                                    echo $html;
                                }
                                ?>

                            </div>

                        </div>
                        <!--.woo-cs-slider-block-->

                    </div>
                    <!--.woo-cs-left-block-->

                    <div class="show-lg show-md col-xs-12 col-sm-12 col-md-7 col-lg-7 woo-cs-right-block">
                        <div class="row eq-height">

                            <?php
                            $counter = 0;
                            foreach ( $additional_categories as $id => $width ) {
                                $additional_category = wccs_get_term_details( $id, $post_id, 'additional' );
                                $html                = '<div class="center col-xs-12 col-sm-12 col-md-' . ( $width * 4 ) . ' col-lg-' . ( $width * 4 ) . '">';
                                $html                .= '<div class="woo-cs-box">';
                                $html                .= '<a class="woo-cs-slide woo-cs-box-link" href="' . $additional_category['link'] . '">';

                                $html .= '<span class="woo-cs-thumb-container">';
                                $html .= '<img src="' . $additional_category['image'] . '" alt="' . $additional_category['title'] . '" class="woo-cs-image woo-cs-image-thumb">';
                                $html .= '</span>';
                                if( $params['wccs_additional_show_title']){
                                    $html .= '<span class="woo-cs-cat-name">' . $additional_category['title'] . '</span>';
                                }

                                $html .= '</a>';
                                $html .= '</div>';
                                $html .= '</div>';
                                echo $html;
                                $counter ++;
                            }
                            ?>

                        </div>       <!--.plvr-grid-noGutter-equalHeight-->
                    </div>
                    <!--.woo-cs-right-block-->
                </div>

            </div>
        </div>

        <style>
            #wccs-slider-<?php echo $post_id; ?> .woo-cs-right-block .woo-cs-cat-name {
                background: <?php echo $params['wccs_additional_content_bg'];?>;
                color: <?php echo $params['wccs_additional_title_color'];?>;
            }
            #wccs-slider-<?php echo $post_id; ?> .woo-cs-cat-details{
                background: <?php echo $params['wccs_featured_content_bg'];?>;
                color: <?php echo $params['wccs_featured_content_color'];?>;
            }
            #wccs-slider-<?php echo $post_id; ?> .woo-cs-cat-details .woo-cs-cat-button{
                color: <?php echo $params['wccs_featured_content_color'];?>;
                border: 1px solid <?php echo $params['wccs_featured_content_color'];?>;
                background: transparent;
            }
            #wccs-slider-<?php echo $post_id; ?> .woo-cs-cat-details .woo-cs-cat-button:hover{
                color:<?php echo $params['wccs_featured_content_bg'];?>;
                background:<?php echo $params['wccs_featured_content_color'];?> ;
                border: 1px solid <?php echo $params['wccs_featured_content_color'];?>;
            }
            #wccs-slider-<?php echo $post_id; ?> .woo-cs-cat-details .woo-cs-cat-des{
                border-top: 1px solid <?php echo $params['wccs_featured_content_color'];?>;
            }
            #wccs-slider-<?php echo $post_id; ?> .woo-cs-heading{
                border-top: 3px solid <?php echo $params['wccs_featured_content_bg'];?>;
            }
        </style>

        <?php
        $output = ob_get_contents();
        ob_get_clean();

        return $output;
    }
}
