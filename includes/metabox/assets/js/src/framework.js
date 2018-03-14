/**
 * Pluginever Framework
 * https://pluginever.com/
 *
 * Copyright (c) 2017 PluginEver
 * Licensed under the GPLv2+ license.
 */

/*jslint browser: true */
/*global jQuery:false */

window.Pluginever_Framework = (function(window, document, $, undefined){
	'use strict';

	var app = {};

	app.init = function() {
	    //initialize select 2
        $('.plvr-select2').select2();

        $('.plvr-tooltip').tooltipster({
            // fixedWidth: 0,
            maxWidth: 300,
        });


        //condition
        $('.plvr-conditional').conditionize();

        //lazy loading
        setTimeout(function () {
            $('.plvr-lazy-loading').removeClass('loading').addClass('loaded');
        }, 1000);
	};

	$(document).ready( app.init );

	return app;

})(window, document, jQuery);

