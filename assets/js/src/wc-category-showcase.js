/**
 * WooCommerce Category Showcase
 * http://pluginever.com
 *
 * Copyright (c) 2017 PluginEver
 * Licensed under the GPLv2+ license.
 */

/*jslint browser: true */
/*global jQuery:false */

window.WC_Category_Showcase = (function(window, document, $, undefined){
	'use strict';

	var app = {};

	app.init = function() {
	    var slider = $('.woo-cs-slider');
        slider.on('init', function(){
            $(this).closest('.woo-cs').addClass('initialized');
        });

        slider.slick({
            autoplay: true,
            dots: false,
            speed: 500
        });



    $(window).load(function() {
    //     var stHeight = $('.slick-track').height();
    //     $('.slick-slide').css('height',stHeight + 'px' );
    });

	};

    // var $slick_carousel = $('.woo-cs-slider');
    // $slick_carousel.on('init', function() {
    //     $slick_carousel.css('display', 'block');
    // });

    $(document).ready( app.init );

	return app;

})(window, document, jQuery);

