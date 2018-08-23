/* Start Menu */
(function ($) {
    var index = 0;
    $.fn.menumaker = function (options) {
        var istartupsmenu = jQuery(this),
            settings = jQuery.extend({
                title: "",
                breakpoint: 1024,
                format: "dropdown",
                sticky: false
            }, options);
        return this.each(function () {
            istartupsmenu.prepend('<div id="menu-button" class="fa fa-bars" aria-hidden="true">' + settings.title + '</div>');
            jQuery(this).find("#menu-button").on('click', function () {
                jQuery(this).toggleClass('menu-opened');
                var mainmenu = jQuery(this).next('ul');
                if (mainmenu.hasClass('open')) {
                    mainmenu.slideToggle().removeClass('open');
                } else {
                    jQuery('ul.MobileMenu').slideToggle().addClass('open');
                    if (settings.format === "dropdown") {
                        mainmenu.find('ul').show();
                    }
                }
            });
            istartupsmenu.find('li ul').parent().addClass('has-sub');
            istartupsmenu.find('li ul').addClass('sub-menu');
            multiTg = function () {
                istartupsmenu.find(".has-sub").prepend('<span class="submenu-button fa fa-plus"></span>');
                istartupsmenu.find('.submenu-button').on('click', function () {
                    jQuery(this).toggleClass('submenu-opened');
                    if (jQuery(this).siblings('ul').hasClass('open')) {
                        jQuery(this).siblings('ul').slideToggle().removeClass('open');
                    } else {
                        jQuery(this).siblings('ul').slideToggle().addClass('open');
                    }
                });
            };
            if (settings.format === 'multitoggle') multiTg();
            else istartupsmenu.addClass('dropdown');
            if (settings.sticky === true) istartupsmenu.css('position', 'fixed');
            resizeFix = function () {
                if (jQuery(window).width() > 1024) {
                    istartupsmenu.find('ul').show();

                }
                if (jQuery(window).width() <= 1024) {
                    istartupsmenu.find('ul').hide().removeClass('open');
                    jQuery('#menu-button').removeClass('menu-opened');
                    jQuery('.submenu-button').removeClass('submenu-opened');
                }
            };
            resizeFix();
            return jQuery(window).on('resize', resizeFix);
        });
    };
})(jQuery);
(function ($) {
    jQuery(document).ready(function () {
        jQuery(document).ready(function () {
            jQuery("#istartupsmenu").menumaker({
                title: "",
                format: "multitoggle"
            });
        });
        /** Set Position of Sub-Menu **/
        var wapoMainWindowWidth = jQuery(window).width();
        jQuery('#istartupsmenu ul ul li').mouseenter(function () {
            var subMenuExist = jQuery(this).find('.sub-menu').length;
            if (subMenuExist > 0) {
                var subMenuWidth = jQuery(this).find('.sub-menu').width();
                var subMenuOffset = jQuery(this).find('.sub-menu').parent().offset().left + subMenuWidth;
                if ((subMenuWidth + subMenuOffset) > wapoMainWindowWidth) {
                    jQuery(this).find('.sub-menu').removeClass('submenu-left');
                    jQuery(this).find('.sub-menu').addClass('submenu-right');
                } else {
                    jQuery(this).find('.sub-menu').removeClass('submenu-right');
                    jQuery(this).find('.sub-menu').addClass('submenu-left');
                }
            }
        });
    });
})(jQuery);
/*Mobile Nav*/
function resize() {
    if (jQuery(window).width() <= 1024) {
        jQuery('#istartupsmenu > ul').addClass('MobileMenu');
    } else {
        jQuery('#istartupsmenu > ul').removeClass('MobileMenu');
    }
}
jQuery(document).ready(function () {
    jQuery(window).resize(resize);
    resize();
});
/*Side Menu*/
(function ($) {
    var index = 0;
    $.fn.istartups = function (options) {
        var istartupsside = jQuery(this),
            settings = jQuery.extend({
                title: "",
                breakpoint: 5000,
                format: "dropdown",
                sticky: false
            }, options);
        return this.each(function () {
            istartupsside.prepend('<div id="menu-button" class="fa fa-bars" aria-hidden="true">' + settings.title + '</div>');
            jQuery(this).find("#menu-button").on('click', function () {
                jQuery(this).toggleClass('menu-opened');
                var mainmenu = jQuery(this).next('ul');
                if (mainmenu.hasClass('open')) {
                    mainmenu.removeClass('open');
                } else {
                    jQuery('#istartupsside > ul').addClass('open');
                }
            });
            istartupsside.find('li ul').parent().addClass('has-sub');
            istartupsside.find('li ul').addClass('sub-menu');
            multiTg = function () {
                istartupsside.find(".has-sub").prepend('<span class="submenu-button fa fa-plus"></span>');
                istartupsside.find('.submenu-button').on('click', function () {
                    jQuery(this).toggleClass('submenu-opened');
                    if (jQuery(this).siblings('ul').hasClass('open')) {
                        jQuery(this).siblings('ul').slideToggle().removeClass('open');
                    } else {
                        jQuery(this).siblings('ul').slideToggle().addClass('open');
                    }
                });
            };
            if (settings.format === 'multitoggle') multiTg();
            else istartupsside.addClass('dropdown');
            if (settings.sticky === true) istartupsside.css('position', 'fixed');
        });
    };
})(jQuery);
(function ($) {
    jQuery(document).ready(function () {
        jQuery(document).ready(function () {
            jQuery("#istartupsside").istartups({
                title: "",
                format: "multitoggle"
            });
            var foundActive = false,
                activeElement, linePosition = 0,
                width = 0,
                menuLine = jQuery("#istartupsside #menu-line"),
                lineWidth, defaultPosition, defaultWidth;
            jQuery("#istartupsside > ul > li").each(function () {
                if (jQuery(this).hasClass('current-menu-item')) {
                    activeElement = jQuery(this);
                    foundActive = true;
                }
            });
            if (foundActive != true) {
                activeElement = jQuery("#istartupsside > ul > li").first();
            }
            if (foundActive == true) {
                activeElement = jQuery("#istartupsside > ul > li").first();
            }
        });
    });
})(jQuery);
