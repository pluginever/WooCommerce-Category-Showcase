<?php
function wccs_update_1_0_3() {
    $args = array(
        'post_type' => 'wccs_showcase',
        'order'     => 'ASC'
    );

    $the_query = new WP_Query( $args );
    while ( $the_query->have_posts() ) : $the_query->the_post();
        $id = get_the_ID();
        $featured_categories   = get_post_meta( $id, '_wccs_featured_categories', true );
        $additional_categories = get_post_meta( $id, '_wccs_additional_categories', true );
        if( $featured_categories ){
            $f_cats = wp_list_pluck($featured_categories, 'term_id');
            delete_post_meta($id, '_wccs_featured_categories');
            update_post_meta($id, 'wccs_featured_categories', $f_cats);
        }

        if( $additional_categories ){
            $a_cats = wp_list_pluck($additional_categories, 'term_id');
            delete_post_meta($id, '_wccs_additional_categories');
            update_post_meta($id, 'wccs_additional_categories', $a_cats);
        }
    endwhile;

    wp_reset_query();

}

wccs_update_1_0_3();
