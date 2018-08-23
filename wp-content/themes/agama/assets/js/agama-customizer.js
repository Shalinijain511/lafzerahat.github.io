(function ($) {
    
	"use strict";
	
    if ('undefined' !== typeof themevision) {
        var upgrade = $('<a class="themevision-link"></a>')
            .attr('href', themevision.URL)
            .attr('target', '_blank')
            .text(themevision.Label)
            .css({
                'display': 'inline-block',
                'background-color': 'rgb(254, 102, 99)',
                'color': '#fff',
                'text-transform': 'uppercase',
                'margin-top': '6px',
                'padding': '3px 6px',
                'font-size': '9px',
                'letter-spacing': '1px',
                'line-height': '1.5',
                'clear': 'both'
            })
		;
        setTimeout(function () {
            $('.preview-notice').append(upgrade);
            $('.customize-panel-back').css('height', '97px');
        }, 200);
        // Remove accordion click event
        $('.themevision-link').on('click', function (e) {
            e.stopPropagation();
        });
    }
	
	if( 'undefined' !== typeof themevision ) {
		var support = $('<a class="themevision-support"></a>')
			.attr('href', themevision.sup_url)
			.attr('target', '_blank')
			.text(themevision.sup_dev)
			.css({
				'display': 'inline-block',
                'background-color': 'rgb(139, 193, 32)',
                'color': '#fff',
                'text-transform': 'uppercase',
                'margin-top': '6px',
				'margin-left': '6px',
                'padding': '3px 6px',
                'font-size': '9px',
                'letter-spacing': '1px',
                'line-height': '1.5',
                'clear': 'both'
			})
		;
		setTimeout(function () {
            $('.preview-notice').append(support);
            $('.customize-panel-back').css('height', '97px');
        }, 200);
        // Remove accordion click event
        $('.themevision-support').on('click', function (e) {
            e.stopPropagation();
        });
	}
	
})(jQuery);