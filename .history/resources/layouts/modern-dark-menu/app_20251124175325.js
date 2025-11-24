var App = function () {

    var MediaSize = {
        xl: 1200,
        lg: 992,
        md: 991,
        sm: 576
    };

    var Dom = {
        main: document.querySelector('html, body'),
        id: {
            container: document.querySelector("#container"),
        },
        class: {
            navbar: document.querySelector(".navbar"),
            overlay: document.querySelector('.overlay'),
            search: document.querySelector('.toggle-search'),
            searchOverlay: document.querySelector('.search-overlay'),
            searchForm: document.querySelector('.search-form-control'),
            mainContainer: document.querySelector('.main-container'),
            mainHeader: document.querySelector('.header.navbar')
        }
    };


    /*
    |--------------------------------------------------------------------------
    | Safe Scroll Category
    |--------------------------------------------------------------------------
    */
    var categoryScroll = {
        scrollCat: function () {

            var sidebarWrapper = document.querySelector('.sidebar-wrapper li.active');

            if (!sidebarWrapper) return;   // SAFE FIX

            var sidebarWrapperTop = sidebarWrapper.offsetTop - 12;

            setTimeout(() => {

                const scroll = document.querySelector('.menu-categories');
                if (!scroll) return;       // SAFE FIX

                scroll.scrollTop = sidebarWrapperTop;

            }, 50);
        }
    };


    /*
    |--------------------------------------------------------------------------
    | Toggle Functions
    |--------------------------------------------------------------------------
    */
    var toggleFunction = {

        sidebar: function () {

            var sidebarCollapseEle = document.querySelectorAll('.sidebarCollapse');

            sidebarCollapseEle.forEach(el => {

                if (!el) return;

                el.addEventListener('click', function (sidebar) {

                    sidebar.preventDefault();

                    let getSidebar = document.querySelector('.sidebar-wrapper');
                    if (!getSidebar) return;       // SAFE FIX

                    Dom.class.mainContainer.classList.toggle("sidebar-closed");
                    Dom.class.mainHeader.classList.toggle('expand-header');
                    Dom.class.mainContainer.classList.toggle("sbar-open");

                    if (Dom.class.overlay)
                        Dom.class.overlay.classList.toggle('show');

                    if (Dom.main)
                        Dom.main.classList.toggle('sidebar-noneoverflow');
                });
            });
        },


        onToggleSidebarSubmenu: function () {

            const sidebar = document.querySelector('.sidebar-wrapper');
            if (!sidebar) return;      // SAFE FIX

            ['mouseenter', 'mouseleave'].forEach(function (ev) {

                sidebar.addEventListener(ev, function () {

                    // all submenu elements must be checked
                    const activeMenu = document.querySelector('li.menu.active');
                    if (!activeMenu) return;

                    const submenu = activeMenu.querySelector('.submenu');
                    if (!submenu) return;

                    if (ev === 'mouseenter') {
                        submenu.classList.add('show');
                    } else {
                        submenu.classList.remove('show');
                    }

                });

            });

        },


        overlay: function () {

            const dismiss = document.querySelector('#dismiss');
            const overlay = document.querySelector('.overlay');

            if (!dismiss || !overlay) return;  // SAFE FIX

            dismiss.addEventListener('click', function () {
                Dom.class.mainContainer.classList.add('sidebar-closed');
                Dom.class.mainContainer.classList.remove('sbar-open');
                overlay.classList.remove('show');
                Dom.main.classList.remove('sidebar-noneoverflow');
            });
        },


        search: function () {

            if (!Dom.class.search) return; // SAFE FIX

            Dom.class.search.addEventListener('click', function () {
                Dom.class.search.classList.add('show-search');
                if (Dom.class.searchOverlay)
                    Dom.class.searchOverlay.classList.add('show');
            });

            if (Dom.class.searchOverlay) {
                Dom.class.searchOverlay.addEventListener('click', function () {
                    Dom.class.search.classList.remove('show-search');
                    Dom.class.searchOverlay.classList.remove('show');
                });
            }

            const closeBtn = document.querySelector('.search-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function (event) {
                    event.stopPropagation();
                    Dom.class.search.classList.remove('show-search');
                    if (Dom.class.searchOverlay)
                        Dom.class.searchOverlay.classList.remove('show');
                    document.querySelector('.search-form-control').value = '';
                });
            }
        },


        themeToggle: function (layoutName) {

            var togglethemeEl = document.querySelector('.theme-toggle');
            if (!togglethemeEl) return; // SAFE FIX

            togglethemeEl.addEventListener('click', function () {

                var getLocalStorage = sessionStorage.getItem("theme");
                if (!getLocalStorage) return;  // SAFE FIX
                var parseObj = JSON.parse(getLocalStorage);

                if (!parseObj?.settings?.layout) return; // SAFE FIX

                var isDark = parseObj.settings.layout.darkMode;

                parseObj.settings.layout.darkMode = !isDark;
                sessionStorage.setItem("theme", JSON.stringify(parseObj));

                if (parseObj.settings.layout.darkMode) {
                    document.body.classList.add('dark');
                } else {
                    document.body.classList.remove('dark');
                }
            });
        }

    };



    /*
    |--------------------------------------------------------------------------
    | Inbuilt functions (all protected now)
    |--------------------------------------------------------------------------
    */
    var inBuiltfunctionality = {

        mainCatActivateScroll: function () {
            if (!document.querySelector('.menu-categories')) return;
            new PerfectScrollbar('.menu-categories');
        },

        notificationScroll: function () {
            if (!document.querySelector('.notification-scroll')) return;
            new PerfectScrollbar('.notification-scroll');
        },

        preventScrollBody: function () {
            var els = document.querySelectorAll('#sidebar, .user-profile-dropdown .dropdown-menu, .notification-dropdown .dropdown-menu,  .language-dropdown .dropdown-menu');
            els.forEach(el => {
                if (!el) return;
                el.addEventListener('mousewheel', e => e.preventDefault());
                el.addEventListener('DOMMouseScroll', e => e.preventDefault());
            });
        },

        searchKeyBind: function () {
            if (!Dom.class.search) return;
        },

        bsTooltip: function () {
            var els = document.querySelectorAll('.bs-tooltip');
            els.forEach(el => new bootstrap.Tooltip(el));
        },

        bsPopover: function () {
            var els = document.querySelectorAll('.bs-popover');
            els.forEach(el => new bootstrap.Popover(el));
        },

        onCheckandChangeSidebarActiveClass: function () {
            const active = document.querySelector('.sidebar-wrapper li.menu.active [aria-expanded="true"]');
            if (active)
                active.setAttribute('aria-expanded', 'false');
        },

        MaterialRippleEffect: function () {
            let btns = document.querySelectorAll('button.btn, a.btn');
            btns.forEach(btn => btn.classList.add('_effect--ripple'));
            if (document.querySelector('._effect--ripple')) {
                Waves.attach('._effect--ripple', 'waves-light');
                Waves.init();
            }
        }
    };


    /*
    |--------------------------------------------------------------------------
    | Mobile / Desktop Handling
    |--------------------------------------------------------------------------
    */
    var _mobileResolution = {
        onRefresh: function () {
            if (window.innerWidth <= MediaSize.md) {
                categoryScroll.scrollCat();
                toggleFunction.sidebar();
            }
        }
    };

    var _desktopResolution = {
        onRefresh: function () {
            if (window.innerWidth > MediaSize.md) {
                categoryScroll.scrollCat();
                toggleFunction.sidebar();
                toggleFunction.onToggleSidebarSubmenu();
            }
        }
    };


    /* ---------------------------------------------------------------------- */

    return {
        init: function (Layout) {

            toggleFunction.overlay();
            toggleFunction.search();
            toggleFunction.themeToggle(Layout);

            _desktopResolution.onRefresh();
            _mobileResolution.onRefresh();

            inBuiltfunctionality.mainCatActivateScroll();
            inBuiltfunctionality.notificationScroll();
            inBuiltfunctionality.preventScrollBody();
            inBuiltfunctionality.searchKeyBind();
            inBuiltfunctionality.bsTooltip();
            inBuiltfunctionality.bsPopover();
            inBuiltfunctionality.onCheckandChangeSidebarActiveClass();
            inBuiltfunctionality.MaterialRippleEffect();
        }
    };

}();


window.addEventListener('load', function () {
    App.init('layout');
});
