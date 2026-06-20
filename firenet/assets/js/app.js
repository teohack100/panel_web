/**
 * Theme: Metrica - Responsive Bootstrap 4 Admin Dashboard
 * Author: Mannatthemes
 * Module/App: Main Js
 */


(function ($) {

    'use strict';

    function hasLegacySideMenu() {
        return !!($('.main-icon-menu .leftmenu-sm-item').length && $('.main-menu-inner .main-icon-menu-pane').length);
    }

    function hasPremiumPanelMenu() {
        return !!($('.left-sidenav .pm-nav-category').length);
    }

    function initSlimscroll() {
        $('.slimscroll').slimscroll({
            height: 'auto',
            position: 'right',
            size: "7px",
            color: '#e0e5f1',
            opacity: 1,
            wheelStep: 5,
            touchScrollStep: 50
        });
    }

    
    function initMetisMenu() {
        if (!$("#main_menu_side_nav").length || typeof $("#main_menu_side_nav").metisMenu !== 'function') {
            return;
        }
        $("#main_menu_side_nav").metisMenu();
    }

    function initLeftMenuCollapse() {
        // Left menu collapse
        $('.button-menu-mobile').on('click', function (event) {
            event.preventDefault();
            $("body").toggleClass("enlarge-menu");
            initSlimscroll();
        });
    }

    function initOutsideMenuClose() {
        return;
    }

    function initEnlarge() {
        if ($(window).width() < 680) {
            $('body').addClass('enlarge-menu');
        } else {
            if ($('body').data('keep-enlarged') != true)
                $('body').removeClass('enlarge-menu');
        }
    }

   

    function initSerach() {
        $('.search-btn').on('click', function () {
            var targetId = $(this).data('target');
            var $searchBar;
            if (targetId) {
                $searchBar = $(targetId);
                $searchBar.toggleClass('open');
            }
        });
    }


    function initMainIconMenu() {
        if (!hasLegacySideMenu()) {
            return;
        }
        $('.main-icon-menu .leftmenu-sm-item').on('click', function(e){
            e.preventDefault();
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
            $('.main-menu-inner').addClass('active');
            var targ = $(this).attr('href');
            $(targ).addClass('active');
            $(targ).siblings().removeClass('active');
        });
    }

    function initTooltipPlugin(){
        $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
        $('[data-toggle="tooltip-custom"]').tooltip({
            template: '<div class="tooltip tooltip-custom" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }
    

    function initActiveMenu() {
        if (hasPremiumPanelMenu() || !hasLegacySideMenu()) {
            return;
        }
        // === following js will activate the menu in left side bar based on url ====
        $(".left-sidenav a").each(function () {
            var pageUrl = window.location.href.split(/[#]/)[0];
            if (this.href == pageUrl) {
                $(this).addClass("active");
                $(this).parent().parent().addClass("in");
                $(this).parent().parent().addClass("mm-show");
                $(this).parent().parent().prev().addClass("active");
                $(this).parent().parent().parent().addClass("active");
                $(this).parent().parent().parent().addClass("mm-active");
                $(this).parent().parent().parent().parent().addClass("in");  
                $(this).parent().parent().parent().parent().parent().addClass("active");  
                $(this).parent().parent().parent().parent().parent().parent().addClass("active");              
                var menu =  $(this).closest('.main-icon-menu-pane').attr('id');
                $("a[href='#"+menu+"']").addClass('active');
                
            }
        });
    }

    

    function init() {
        initSlimscroll();
        initMetisMenu();
        initLeftMenuCollapse();
        initOutsideMenuClose();
        initEnlarge();
        initSerach();
        initMainIconMenu();
        initTooltipPlugin();
        initActiveMenu();
    }

    init();

})(jQuery)
