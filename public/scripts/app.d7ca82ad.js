'use strict';

/**
 * @ngdoc overview
 * @name minovateApp
 * @description
 * # minovateApp
 *
 * Main module of the application.
 */

  /*jshint -W079 */

var app = angular
  .module('minovateApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngSanitize',
    'ngTouch',
    'ngMessages',
    'picardy.fontawesome',
    'ui.bootstrap',
    'ui.router',
    'ui.utils',
    'angular-loading-bar',
    'angular-momentjs',
    'FBAngular',
    'lazyModel',
    'toastr',
    'angularBootstrapNavTree',
    'oc.lazyLoad',
    'ui.select',
    'ui.tree',
    'textAngular',
    'colorpicker.module',
    'angularFileUpload',
    'ngImgCrop',
    'datatables',
    'datatables.bootstrap',
    'datatables.colreorder',
    'datatables.colvis',
    'datatables.tabletools',
    'datatables.scroller',
    'datatables.columnfilter',
    'ui.grid',
    'ui.grid.resizeColumns',
    'ui.grid.edit',
    'ui.grid.moveColumns',
    'ngTable',
    'smart-table',
    'angular-flot',
    'angular-rickshaw',
    'easypiechart',
    'uiGmapgoogle-maps',
    'ui.calendar',
    'ngTagsInput',
    'pascalprecht.translate',
    'ngMaterial',
    'localytics.directives',
    'leaflet-directive',
    'wu.masonry',
    'ipsum',
    'angular-intro',
    'dragularModule'
  ])
  .run(['$rootScope', '$state', '$stateParams', function($rootScope, $state, $stateParams) {
    $rootScope.$state = $state;
    $rootScope.$stateParams = $stateParams;
    $rootScope.$on('$stateChangeSuccess', function(event, toState) {

      event.targetScope.$watch('$viewContentLoaded', function () {

        angular.element('html, body, #content').animate({ scrollTop: 0 }, 200);

        setTimeout(function () {
          angular.element('#wrap').css('visibility','visible');

          if (!angular.element('.dropdown').hasClass('open')) {
            angular.element('.dropdown').find('>ul').slideUp();
          }
        }, 200);
      });
      $rootScope.containerClass = toState.containerClass;
    });
  }])

  .config(['uiSelectConfig', function (uiSelectConfig) {
    uiSelectConfig.theme = 'bootstrap';
  }])

  //angular-language
  .config(['$translateProvider', function($translateProvider) {
    $translateProvider.useStaticFilesLoader({
      prefix: 'languages/',
      suffix: '.json'
    });
    $translateProvider.useLocalStorage();
    $translateProvider.preferredLanguage('en');
    $translateProvider.useSanitizeValueStrategy(null);
  }])

  .config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise('/app/dashboard');

    $stateProvider

    .state('app', {
      abstract: true,
      url: '/app',
      templateUrl: 'views/tmpl/app.html'
    })
    //dashboard
    .state('app.dashboard', {
      url: '/dashboard',
      controller: 'DashboardCtrl',
      templateUrl: 'views/tmpl/dashboard.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/datatables/datatables.bootstrap.min.css',
            'scripts/vendor/datatables/datatables.bootstrap.min.css'
          ]);
        }]
      }
    })
    //mail
    .state('app.mail', {
      abstract: true,
      url: '/mail',
      controller: 'MailCtrl',
      templateUrl: 'views/tmpl/mail/mail.html'
    })
    //mail/inbox
    .state('app.mail.inbox', {
      url: '/inbox',
      controller: 'MailInboxCtrl',
      templateUrl: 'views/tmpl/mail/inbox.html'
    })
    //mail/compose
    .state('app.mail.compose', {
      url: '/compose',
      controller: 'MailComposeCtrl',
      templateUrl: 'views/tmpl/mail/compose.html'
    })
    //mail/single
    .state('app.mail.single', {
      url: '/single',
      controller: 'MailSingleCtrl',
      templateUrl: 'views/tmpl/mail/single.html'
    })
    //ui
    .state('app.ui', {
      url: '/ui',
      template: '<div ui-view></div>'
    })
    //ui/typography
    .state('app.ui.typography', {
      url: '/typography',
      controller: 'TypographyCtrl',
      templateUrl: 'views/tmpl/ui/typography.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/google-code-prettify/prettify.css',
            'scripts/vendor/google-code-prettify/sons-of-obsidian.css',
            'scripts/vendor/google-code-prettify/prettify.js'
          ]);
        }]
      }
    })
    //ui/lists
    .state('app.ui.lists', {
      url: '/lists',
      controller: 'ListsCtrl',
      templateUrl: 'views/tmpl/ui/lists.html'
    })
    //ui/buttons&icons
    .state('app.ui.buttons-icons', {
      url: '/buttons-icons',
      controller: 'ButtonsIconsCtrl',
      templateUrl: 'views/tmpl/ui/buttons-icons.html'
    })
    //ui/navs&accordions
    .state('app.ui.navs', {
      url: '/navs',
      controller: 'NavsCtrl',
      templateUrl: 'views/tmpl/ui/navs.html'
    })
    //ui/modals
    .state('app.ui.modals', {
      url: '/modals',
      controller: 'ModalsCtrl',
      templateUrl: 'views/tmpl/ui/modals.html'
    })
    //ui/tiles
    .state('app.ui.tiles', {
      url: '/tiles',
      controller: 'TilesCtrl',
      templateUrl: 'views/tmpl/ui/tiles.html'
    })
    //ui/portlets
    .state('app.ui.portlets', {
      url: '/portlets',
      controller: 'PortletsCtrl',
      templateUrl: 'views/tmpl/ui/portlets.html'
    })
    //ui/grid
    .state('app.ui.grid', {
      url: '/grid',
      controller: 'GridCtrl',
      templateUrl: 'views/tmpl/ui/grid.html'
    })
    //ui/widgets
    .state('app.ui.widgets', {
      url: '/widgets',
      controller: 'WidgetsCtrl',
      templateUrl: 'views/tmpl/ui/widgets.html'
    })
    //ui/alerts & notifications
    .state('app.ui.alerts', {
      url: '/alerts',
      controller: 'AlertsCtrl',
      templateUrl: 'views/tmpl/ui/alerts.html'
    })
    //ui/general
    .state('app.ui.general', {
      url: '/general',
      controller: 'GeneralCtrl',
      templateUrl: 'views/tmpl/ui/general.html'
    })
    //ui/tree
    .state('app.ui.tree', {
      url: '/tree',
      controller: 'TreeCtrl',
      templateUrl: 'views/tmpl/ui/tree.html'
    })
    //ui/masonry
    .state('app.ui.masonry', {
      url: '/masonry',
      controller: 'UiMasonryCtrl',
      templateUrl: 'views/tmpl/ui/masonry.html'
    })
    //ui/dragula
    .state('app.ui.dragula', {
      url: '/dragula',
      controller: 'UiDragulaCtrl',
      templateUrl: 'views/tmpl/ui/dragula.html'
    })
    //material
    .state('app.material', {
      url: '/material',
      template: '<div ui-view></div>'
    })
    //material/autocomplete
    .state('app.material.autocomplete', {
      url: '/autocomplete',
      controller: 'mtAutocompleteCtrl',
      templateUrl: 'views/tmpl/material/autocomplete.html'
    })
    //material/bottom-sheet
    .state('app.material.bottom-sheet', {
      url: '/bottom-sheet',
      controller: 'mtBottomSheetCtrl',
      templateUrl: 'views/tmpl/material/bottom-sheet.html'
    })
    //material/buttons
    .state('app.material.buttons', {
      url: '/buttons',
      controller: 'mtButtonsCtrl',
      templateUrl: 'views/tmpl/material/buttons.html'
    })
    //material/cards
    .state('app.material.cards', {
      url: '/cards',
      controller: 'mtCardsCtrl',
      templateUrl: 'views/tmpl/material/cards.html'
    })
    //material/checkbox
    .state('app.material.checkbox', {
      url: '/checkbox',
      controller: 'mtCheckboxCtrl',
      templateUrl: 'views/tmpl/material/checkbox.html'
    })
    //material/chips
    .state('app.material.chips', {
      url: '/chips',
      controller: 'mtChipsCtrl',
      templateUrl: 'views/tmpl/material/chips.html'
    })
    //material/content
    .state('app.material.content', {
      url: '/content',
      controller: 'mtContentCtrl',
      templateUrl: 'views/tmpl/material/content.html'
    })
    //material/dialog
    .state('app.material.dialog', {
      url: '/dialog',
      controller: 'mtDialogCtrl',
      templateUrl: 'views/tmpl/material/dialog.html'
    })
    //material/divider
    .state('app.material.divider', {
      url: '/divider',
      controller: 'mtDividerCtrl',
      templateUrl: 'views/tmpl/material/divider.html'
    })
    //material/fab-speed-dial
    .state('app.material.fab-speed-dial', {
      url: '/fab-speed-dial',
      controller: 'mtFabSpeedDialCtrl',
      templateUrl: 'views/tmpl/material/fab-speed-dial.html'
    })
    //material/fab-toolbar
    .state('app.material.fab-toolbar', {
      url: '/fab-toolbar',
      controller: 'mtFabToolbarCtrl',
      templateUrl: 'views/tmpl/material/fab-toolbar.html'
    })
    //material/grid-list
    .state('app.material.grid-list', {
      url: '/grid-list',
      controller: 'mtGridListCtrl',
      templateUrl: 'views/tmpl/material/grid-list.html'
    })
    //material/inputs
    .state('app.material.inputs', {
      url: '/inputs',
      controller: 'mtInputsCtrl',
      templateUrl: 'views/tmpl/material/inputs.html'
    })
    //material/list
    .state('app.material.list', {
      url: '/list',
      controller: 'mtListCtrl',
      templateUrl: 'views/tmpl/material/list.html'
    })
    //material/menu
    .state('app.material.menu', {
      url: '/menu',
      controller: 'mtMenuCtrl',
      templateUrl: 'views/tmpl/material/menu.html'
    })
    //material/progress-circular
    .state('app.material.progress-circular', {
      url: '/progress-circular',
      controller: 'mtProgressCircularCtrl',
      templateUrl: 'views/tmpl/material/progress-circular.html'
    })
    //material/progress-linear
    .state('app.material.progress-linear', {
      url: '/progress-linear',
      controller: 'mtProgressLinearCtrl',
      templateUrl: 'views/tmpl/material/progress-linear.html'
    })
    //material/radio-button
    .state('app.material.radio-button', {
      url: '/radio-button',
      controller: 'mtRadioButtonCtrl',
      templateUrl: 'views/tmpl/material/radio-button.html'
    })
    //material/select
    .state('app.material.select', {
      url: '/select',
      controller: 'mtSelectCtrl',
      templateUrl: 'views/tmpl/material/select.html'
    })
    //material/sidenav
    .state('app.material.sidenav', {
      url: '/sidenav',
      controller: 'mtSidenavCtrl',
      templateUrl: 'views/tmpl/material/sidenav.html'
    })
    //material/slider
    .state('app.material.slider', {
      url: '/slider',
      controller: 'mtSliderCtrl',
      templateUrl: 'views/tmpl/material/slider.html'
    })
    //material/subheader
    .state('app.material.subheader', {
      url: '/subheader',
      controller: 'mtSubheaderCtrl',
      templateUrl: 'views/tmpl/material/subheader.html'
    })
    //material/swipe
    .state('app.material.swipe', {
      url: '/swipe',
      controller: 'mtSwipeCtrl',
      templateUrl: 'views/tmpl/material/swipe.html'
    })
    //material/switch
    .state('app.material.switch', {
      url: '/switch',
      controller: 'mtSwitchCtrl',
      templateUrl: 'views/tmpl/material/switch.html'
    })
    //material/tabs
    .state('app.material.tabs', {
      url: '/tabs',
      controller: 'mtTabsCtrl',
      templateUrl: 'views/tmpl/material/tabs.html'
    })
    //material/toast
    .state('app.material.toast', {
      url: '/toast',
      controller: 'mtToastCtrl',
      templateUrl: 'views/tmpl/material/toast.html'
    })
    //material/toolbar
    .state('app.material.toolbar', {
      url: '/toolbar',
      controller: 'mtToolbarCtrl',
      templateUrl: 'views/tmpl/material/toolbar.html'
    })
    //material/tooltip
    .state('app.material.tooltip', {
      url: '/tooltip',
      controller: 'mtTooltipCtrl',
      templateUrl: 'views/tmpl/material/tooltip.html'
    })
    //material/whiteframe
    .state('app.material.whiteframe', {
      url: '/whiteframe',
      controller: 'mtWhiteframeCtrl',
      templateUrl: 'views/tmpl/material/whiteframe.html'
    })
    //shop
    .state('app.shop', {
      url: '/shop',
      template: '<div ui-view></div>'
    })
    //shop/orders
    .state('app.shop.orders', {
      url: '/orders',
      controller: 'OrdersCtrl',
      templateUrl: 'views/tmpl/shop/orders.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/datatables/datatables.bootstrap.min.css',
            'scripts/vendor/datatables/Pagination/input.js',
            'scripts/vendor/datatables/ColumnFilter/jquery.dataTables.columnFilter.js'
          ]);
        }]
      }
    })
    //shop/products
    .state('app.shop.products', {
      url: '/products',
      controller: 'ProductsCtrl',
      templateUrl: 'views/tmpl/shop/products.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/datatables/datatables.bootstrap.min.css',
            'scripts/vendor/datatables/Pagination/input.js',
            'scripts/vendor/datatables/ColumnFilter/jquery.dataTables.columnFilter.js'
          ]);
        }]
      }
    })
    //shop/invoices
    .state('app.shop.invoices', {
      url: '/invoices',
      controller: 'InvoicesCtrl',
      templateUrl: 'views/tmpl/shop/invoices.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/datatables/datatables.bootstrap.min.css',
            'scripts/vendor/datatables/Pagination/input.js',
            'scripts/vendor/datatables/ColumnFilter/jquery.dataTables.columnFilter.js'
          ]);
        }]
      }
    })
    //shop/single-order
    .state('app.shop.single-order', {
      url: '/single-order',
      controller: 'SingleOrderCtrl',
      templateUrl: 'views/tmpl/shop/single-order.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/datatables/datatables.bootstrap.min.css',
            'scripts/vendor/datatables/Pagination/input.js',
            'scripts/vendor/datatables/ColumnFilter/jquery.dataTables.columnFilter.js'
          ]);
        }]
      }
    })
    //shop/single-product
    .state('app.shop.single-product', {
      url: '/single-product',
      controller: 'SingleProductCtrl',
      templateUrl: 'views/tmpl/shop/single-product.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/datatables/datatables.bootstrap.min.css',
            'scripts/vendor/datatables/Pagination/input.js',
            'scripts/vendor/datatables/ColumnFilter/jquery.dataTables.columnFilter.js',
            'scripts/vendor/touchspin/jquery.bootstrap-touchspin.js',
            'scripts/vendor/touchspin/jquery.bootstrap-touchspin.css',
            'scripts/vendor/magnific/magnific-popup.css',
            'scripts/vendor/magnific/jquery.magnific-popup.min.js'
          ]);
        }]
      }
    })
    //shop/single-invoice
    .state('app.shop.single-invoice', {
      url: '/single-invoice',
      controller: 'SingleInvoiceCtrl',
      templateUrl: 'views/tmpl/shop/single-invoice.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/datatables/datatables.bootstrap.min.css',
            'scripts/vendor/datatables/Pagination/input.js',
            'scripts/vendor/datatables/ColumnFilter/jquery.dataTables.columnFilter.js'
          ]);
        }]
      }
    })
    //forms
    .state('app.forms', {
      url: '/forms',
      template: '<div ui-view></div>'
    })
    //forms/common
    .state('app.forms.common', {
      url: '/common',
      controller: 'FormsCommonCtrl',
      templateUrl: 'views/tmpl/forms/common.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/slider/bootstrap-slider.js',
            'scripts/vendor/touchspin/jquery.bootstrap-touchspin.js',
            'scripts/vendor/touchspin/jquery.bootstrap-touchspin.css',
            'scripts/vendor/filestyle/bootstrap-filestyle.min.js'
          ]);
        }]
      }
    })
    //forms/validate
    .state('app.forms.validate', {
      url: '/validate',
      controller: 'FormsValidateCtrl',
      templateUrl: 'views/tmpl/forms/validate.html'
    })
    //forms/wizard
    .state('app.forms.wizard', {
      url: '/wizard',
      controller: 'FormWizardCtrl',
      templateUrl: 'views/tmpl/forms/wizard.html'
    })
    //forms/upload
    .state('app.forms.upload', {
      url: '/upload',
      controller: 'FormUploadCtrl',
      templateUrl: 'views/tmpl/forms/upload.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/filestyle/bootstrap-filestyle.min.js'
          ]);
        }]
      }
    })
    //forms/imgcrop
    .state('app.forms.imgcrop', {
      url: '/imagecrop',
      controller: 'FormImgCropCtrl',
      templateUrl: 'views/tmpl/forms/imgcrop.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/filestyle/bootstrap-filestyle.min.js'
          ]);
        }]
      }
    })
    //tables
    .state('app.tables', {
      url: '/tables',
      template: '<div ui-view></div>'
    })
    //tables/bootstrap
    .state('app.tables.bootstrap', {
      url: '/bootstrap',
      controller: 'TablesBootstrapCtrl',
      templateUrl: 'views/tmpl/tables/bootstrap.html'
    })
    //tables/datatables
    .state('app.tables.datatables', {
      url: '/datatables',
      controller: 'TablesDatatablesCtrl',
      templateUrl: 'views/tmpl/tables/datatables.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/datatables/ColReorder/css/dataTables.colReorder.min.css',
            'scripts/vendor/datatables/ColReorder/js/dataTables.colReorder.min.js',
            'scripts/vendor/datatables/Responsive/dataTables.responsive.css',
            'scripts/vendor/datatables/Responsive/dataTables.responsive.js',
            'scripts/vendor/datatables/ColVis/css/dataTables.colVis.min.css',
            'scripts/vendor/datatables/ColVis/js/dataTables.colVis.min.js',
            'scripts/vendor/datatables/TableTools/css/dataTables.tableTools.css',
            'scripts/vendor/datatables/TableTools/js/dataTables.tableTools.js',
            'scripts/vendor/datatables/datatables.bootstrap.min.css'
          ]);
        }]
      }
    })
    //tables/uiGrid
    .state('app.tables.ui-grid', {
      url: '/ui-grid',
      controller: 'TablesUiGridCtrl',
      templateUrl: 'views/tmpl/tables/ui-grid.html'
    })
    //tables/ngTable
    .state('app.tables.ng-table', {
      url: '/ng-table',
      controller: 'TablesNgTableCtrl',
      templateUrl: 'views/tmpl/tables/ng-table.html'
    })
    //tables/smartTable
    .state('app.tables.smart-table', {
      url: '/smart-table',
      controller: 'TablesSmartTableCtrl',
      templateUrl: 'views/tmpl/tables/smart-table.html'
    })
    //tables/fooTable
    .state('app.tables.footable', {
      url: '/footable',
      controller: 'TablesFootableCtrl',
      templateUrl: 'views/tmpl/tables/footable.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/footable/dist/footable.all.min.js',
            'scripts/vendor/footable/css/footable.core.min.css'
          ]);
        }]
      }
    })
    //charts
    .state('app.charts', {
      url: '/charts',
      controller: 'ChartsCtrl',
      templateUrl: 'views/tmpl/charts.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/flot/jquery.flot.resize.js',
            'scripts/vendor/flot/jquery.flot.orderBars.js',
            'scripts/vendor/flot/jquery.flot.stack.js',
            'scripts/vendor/flot/jquery.flot.pie.js',
            'scripts/vendor/gaugejs/gauge.min.js'
          ]);
        }]
      }
    })
    //layouts
    .state('app.layouts', {
      url: '/layouts',
      template: '<div ui-view></div>'
    })
    //layouts/boxed
    .state('app.layouts.boxed', {
      url: '/boxed',
      controller: 'BoxedlayoutCtrl',
      templateUrl: 'views/tmpl/layouts/boxed.html',
      containerClass: 'boxed-layout'
    })
    //layouts/fullwidth
    .state('app.layouts.fullwidth', {
      url: '/fullwidth',
      controller: 'FullwidthlayoutCtrl',
      templateUrl: 'views/tmpl/layouts/fullwidth.html'
    })
    //layouts/sidebar-sm
    .state('app.layouts.sidebar-sm', {
      url: '/sidebar-sm',
      controller: 'SidebarsmlayoutCtrl',
      templateUrl: 'views/tmpl/layouts/sidebar-sm.html',
      containerClass: 'sidebar-sm-forced sidebar-sm'
    })
    //layouts/sidebar-xs
    .state('app.layouts.sidebar-xs', {
      url: '/sidebar-xs',
      controller: 'SidebarxslayoutCtrl',
      templateUrl: 'views/tmpl/layouts/sidebar-xs.html',
      containerClass: 'sidebar-xs-forced sidebar-xs'
    })
    //layouts/offcanvas
    .state('app.layouts.offcanvas', {
      url: '/offcanvas',
      controller: 'OffcanvaslayoutCtrl',
      templateUrl: 'views/tmpl/layouts/offcanvas.html',
      containerClass: 'sidebar-offcanvas'
    })
    //layouts/hz-menu
    .state('app.layouts.hz-menu', {
      url: '/hz-menu',
      controller: 'HzmenuCtrl',
      templateUrl: 'views/tmpl/layouts/hz-menu.html',
      containerClass: 'hz-menu'
    })
    //layouts/rtl-layout
    .state('app.layouts.rtl', {
      url: '/rtl',
      controller: 'RtlCtrl',
      templateUrl: 'views/tmpl/layouts/rtl.html',
      containerClass: 'rtl'
    })
    //maps
    .state('app.maps', {
      url: '/maps',
      template: '<div ui-view></div>'
    })
    //maps/vector
    .state('app.maps.vector', {
      url: '/vector',
      controller: 'VectorMapCtrl',
      templateUrl: 'views/tmpl/maps/vector.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/jqvmap/jqvmap/jquery.vmap.min.js',
            'scripts/vendor/jqvmap/jqvmap/maps/jquery.vmap.world.js',
            'scripts/vendor/jqvmap/jqvmap/maps/jquery.vmap.usa.js',
            'scripts/vendor/jqvmap/jqvmap/maps/jquery.vmap.europe.js',
            'scripts/vendor/jqvmap/jqvmap/maps/jquery.vmap.germany.js'
          ]);
        }]
      }
    })
    //maps/google
    .state('app.maps.google', {
      url: '/google',
      controller: 'GoogleMapCtrl',
      templateUrl: 'views/tmpl/maps/google.html'
    })
    //maps/leaflet
    .state('app.maps.leaflet', {
      url: '/leaflet',
      controller: 'LeafletMapCtrl',
      templateUrl: 'views/tmpl/maps/leaflet.html'
    })
    //calendar
    .state('app.calendar', {
      url: '/calendar',
      controller: 'CalendarCtrl',
      templateUrl: 'views/tmpl/calendar.html'
    })
    //app core pages (errors, login,signup)
      .state('core', {
      abstract: true,
      url: '/core',
      template: '<div ui-view></div>'
    })
    //login
    .state('core.login', {
      url: '/login',
      controller: 'LoginCtrl',
      templateUrl: 'views/tmpl/pages/login.html'
    })
    //signup
    .state('core.signup', {
      url: '/signup',
      controller: 'SignupCtrl',
      templateUrl: 'views/tmpl/pages/signup.html'
    })
    //forgot password
    .state('core.forgotpass', {
      url: '/forgotpass',
      controller: 'ForgotPasswordCtrl',
      templateUrl: 'views/tmpl/pages/forgotpass.html'
    })
    //page 404
    .state('core.page404', {
      url: '/page404',
      templateUrl: 'views/tmpl/pages/page404.html'
    })
    //page 500
    .state('core.page500', {
      url: '/page500',
      templateUrl: 'views/tmpl/pages/page500.html'
    })
    //page offline
    .state('core.page-offline', {
      url: '/page-offline',
      templateUrl: 'views/tmpl/pages/page-offline.html'
    })
    //locked screen
    .state('core.locked', {
      url: '/locked',
      templateUrl: 'views/tmpl/pages/locked.html'
    })
    //example pages
    .state('app.pages', {
      url: '/pages',
      template: '<div ui-view></div>'
    })
    //gallery page
    .state('app.pages.gallery', {
      url: '/gallery',
      controller: 'GalleryCtrl',
      templateUrl: 'views/tmpl/pages/gallery.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/mixitup/jquery.mixitup.js',
            'scripts/vendor/magnific/magnific-popup.css',
            'scripts/vendor/magnific/jquery.magnific-popup.min.js'
          ]);
        }]
      }
    })
    //timeline page
    .state('app.pages.timeline', {
      url: '/timeline',
      controller: 'TimelineCtrl',
      templateUrl: 'views/tmpl/pages/timeline.html'
    })
    //chat page
    .state('app.pages.chat', {
      url: '/chat',
      controller: 'ChatCtrl',
      templateUrl: 'views/tmpl/pages/chat.html'
    })
    //search results
    .state('app.pages.search-results', {
      url: '/search-results',
      controller: 'SearchResultsCtrl',
      templateUrl: 'views/tmpl/pages/search-results.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/slider/bootstrap-slider.js'
          ]);
        }]
      }
    })
    //profile page
    .state('app.pages.profile', {
      url: '/profile',
      controller: 'ProfileCtrl',
      templateUrl: 'views/tmpl/pages/profile.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/filestyle/bootstrap-filestyle.min.js'
          ]);
        }]
      }
    })
    //intro page
    .state('app.pages.intro', {
      url: '/intro',
      controller: 'IntroPageCtrl',
      templateUrl: 'views/tmpl/pages/intro.html'
    })
    //documentation
    .state('app.help', {
      url: '/help',
      controller: 'HelpCtrl',
      templateUrl: 'views/tmpl/help.html'
    });
  }]);


'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the minovateApp
 */
app
  .controller('MainCtrl', function ($scope, $http, $translate) {

    $scope.main = {
      title: 'Minovate',
      settings: {
        navbarHeaderColor: 'scheme-default',
        sidebarColor: 'scheme-default',
        brandingColor: 'scheme-default',
        activeColor: 'default-scheme-color',
        headerFixed: true,
        asideFixed: true,
        rightbarShow: false
      }
    };

    $scope.ajaxFaker = function(){
      $scope.data=[];
      var url = 'http://www.filltext.com/?rows=10&fname={firstName}&lname={lastName}&delay=5&callback=JSON_CALLBACK';

      $http.jsonp(url).success(function(data){
        $scope.data=data;
        angular.element('.tile.refreshing').removeClass('refreshing');
      });
    };

    $scope.changeLanguage = function (langKey) {
      $translate.use(langKey);
      $scope.currentLanguage = langKey;
    };
    $scope.currentLanguage = $translate.proposedLanguage() || $translate.use();
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:navCollapse
 * @description
 * # navCollapse
 * # sidebar navigation dropdown collapse
 */
app
  .directive('navCollapse', function ($timeout) {
    return {
      restrict: 'A',
      link: function($scope, $el) {

        $timeout(function(){

          var $dropdowns = $el.find('ul').parent('li'),
            $a = $dropdowns.children('a'),
            $notDropdowns = $el.children('li').not($dropdowns),
            $notDropdownsLinks = $notDropdowns.children('a'),
            app = angular.element('.appWrapper'),
            sidebar = angular.element('#sidebar'),
            controls = angular.element('#controls');

          $dropdowns.addClass('dropdown');

          var $submenus = $dropdowns.find('ul >.dropdown');
          $submenus.addClass('submenu');

          $a.append('<i class="fa fa-plus"></i>');

          $a.on('click', function(event) {
            if (app.hasClass('sidebar-sm') || app.hasClass('sidebar-xs') || app.hasClass('hz-menu')) {
              return false;
            }

            var $this = angular.element(this),
              $parent = $this.parent('li'),
              $openSubmenu = angular.element('.submenu.open');

            if (!$parent.hasClass('submenu')) {
              $dropdowns.not($parent).removeClass('open').find('ul').slideUp();
            }

            $openSubmenu.not($this.parents('.submenu')).removeClass('open').find('ul').slideUp();
            $parent.toggleClass('open').find('>ul').stop().slideToggle();
            event.preventDefault();
          });

          $dropdowns.on('mouseenter', function() {
            sidebar.addClass('dropdown-open');
            controls.addClass('dropdown-open');
          });

          $dropdowns.on('mouseleave', function() {
            sidebar.removeClass('dropdown-open');
            controls.removeClass('dropdown-open');
          });

          $notDropdownsLinks.on('click', function() {
            $dropdowns.removeClass('open').find('ul').slideUp();
          });

          var $activeDropdown = angular.element('.dropdown>ul>.active').parent();

          $activeDropdown.css('display', 'block');
        });

      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:slimScroll
 * @description
 * # slimScroll
 */
app
  .directive('slimscroll', function () {
    return {
      restrict: 'A',
      link: function ($scope, $elem, $attr) {
        var off = [];
        var option = {};

        var refresh = function () {
          if ($attr.slimscroll) {
            option = $scope.$eval($attr.slimscroll);
          } else if ($attr.slimscrollOption) {
            option = $scope.$eval($attr.slimscrollOption);
          }

          angular.element($elem).slimScroll({ destroy: true });

          angular.element($elem).slimScroll(option);
        };

        var registerWatch = function () {
          if ($attr.slimscroll && !option.noWatch) {
            off.push($scope.$watchCollection($attr.slimscroll, refresh));
          }

          if ($attr.slimscrollWatch) {
            off.push($scope.$watchCollection($attr.slimscrollWatch, refresh));
          }

          if ($attr.slimscrolllistento) {
            off.push($scope.$on($attr.slimscrolllistento, refresh));
          }
        };

        var destructor = function () {
          angular.element($elem).slimScroll({ destroy: true });
          off.forEach(function (unbind) {
            unbind();
          });
          off = null;
        };

        off.push($scope.$on('$destroy', destructor));

        registerWatch();
      }
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:SparklineCtrl
 * @description
 * # sparklineCtrl navbar
 * Controller of the minovateApp
 */
app
  .controller('SparklineCtrl', function ($scope) {
    $scope.navChart1 = {
      data: [5, 8, 3, 4, 6, 2, 1, 9, 7],
      options: {
        type: 'bar',
        barColor: '#92424e',
        barWidth: '6px',
        height: '36px'
      }
    };
    $scope.navChart2 = {
      data: [2, 4, 5, 3, 8, 9, 7, 3, 5],
      options: {
        type: 'bar',
        barColor: '#397193',
        barWidth: '6px',
        height: '36px'
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:sparkline
 * @description
 * # sparkline
 */
app
  .directive('sparkline', [
  function() {
    return {
      restrict: 'A',
      scope: {
        data: '=',
        options: '='
      },
      link: function($scope, $el) {
        var data = $scope.data,
            options = $scope.options,
            chartResize,
            chartRedraw = function() {
              return $el.sparkline(data, options);
            };
        angular.element(window).resize(function() {
          clearTimeout(chartResize);
          chartResize = setTimeout(chartRedraw, 200);
        });
        return chartRedraw();
      }
    };
  }
]);

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:DashboardCtrl
 * @description
 * # DashboardCtrl
 * Controller of the minovateApp
 */
app
  .controller('DashboardCtrl', function($scope,$http){
    $scope.page = {
      title: 'Dashboard',
      subtitle: 'Place subtitle here...'
    };

    $scope.getUsers = function(){
      $scope.data=[];
      var url = 'http://www.filltext.com/?rows=10&fname={firstName}&lname={lastName}&delay=3&callback=JSON_CALLBACK';

      $http.jsonp(url).success(function(data){
          $scope.data=data;
      });
    };

    $scope.getUsers();
  })

  .controller('StatisticsChartCtrl', function ($scope) {

    $scope.dataset = [{
      data: [[1,15],[2,40],[3,35],[4,39],[5,42],[6,50],[7,46],[8,49],[9,59],[10,60],[11,58],[12,74]],
      label: 'Unique Visits',
      points: {
        show: true,
        radius: 4
      },
      splines: {
        show: true,
        tension: 0.45,
        lineWidth: 4,
        fill: 0
      }
    }, {
      data: [[1,50],[2,80],[3,90],[4,85],[5,99],[6,125],[7,114],[8,96],[9,130],[10,145],[11,139],[12,160]],
      label: 'Page Views',
      bars: {
        show: true,
        barWidth: 0.6,
        lineWidth: 0,
        fillColor: { colors: [{ opacity: 0.3 }, { opacity: 0.8}] }
      }
    }];

    $scope.options = {
      colors: ['#e05d6f','#61c8b8'],
      series: {
        shadowSize: 0
      },
      legend: {
        backgroundOpacity: 0,
        margin: -7,
        position: 'ne',
        noColumns: 2
      },
      xaxis: {
        tickLength: 0,
        font: {
          color: '#fff'
        },
        position: 'bottom',
        ticks: [
          [ 1, 'JAN' ], [ 2, 'FEB' ], [ 3, 'MAR' ], [ 4, 'APR' ], [ 5, 'MAY' ], [ 6, 'JUN' ], [ 7, 'JUL' ], [ 8, 'AUG' ], [ 9, 'SEP' ], [ 10, 'OCT' ], [ 11, 'NOV' ], [ 12, 'DEC' ]
        ]
      },
      yaxis: {
        tickLength: 0,
        font: {
          color: '#fff'
        }
      },
      grid: {
        borderWidth: {
          top: 0,
          right: 0,
          bottom: 1,
          left: 1
        },
        borderColor: 'rgba(255,255,255,.3)',
        margin:0,
        minBorderMargin:0,
        labelMargin:20,
        hoverable: true,
        clickable: true,
        mouseActiveRadius:6
      },
      tooltip: true,
      tooltipOpts: {
        content: '%s: %y',
        defaultTheme: false,
        shifts: {
          x: 0,
          y: 20
        }
      }
    };
  })

  .controller('ActualStatisticsCtrl',function($scope){
    $scope.easypiechart = {
      percent: 100,
      options: {
        animate: {
          duration: 3000,
          enabled: true
        },
        barColor: '#418bca',
        scaleColor: false,
        lineCap: 'round',
        size: 140,
        lineWidth: 4
      }
    };
    $scope.easypiechart2 = {
      percent: 75,
      options: {
        animate: {
          duration: 3000,
          enabled: true
        },
        barColor: '#e05d6f',
        scaleColor: false,
        lineCap: 'round',
        size: 140,
        lineWidth: 4
      }
    };
    $scope.easypiechart3 = {
      percent: 46,
      options: {
        animate: {
          duration: 3000,
          enabled: true
        },
        barColor: '#16a085',
        scaleColor: false,
        lineCap: 'round',
        size: 140,
        lineWidth: 4
      }
    };
  })

  .controller('BrowseUsageCtrl', function ($scope) {

    $scope.donutData = [
      {label: 'Chrome', value: 25, color: '#00a3d8'},
      {label: 'Safari', value: 20, color: '#2fbbe8'},
      {label: 'Firefox', value: 15, color: '#72cae7'},
      {label: 'Opera', value: 5, color: '#d9544f'},
      {label: 'Internet Explorer', value: 10, color: '#ffc100'},
      {label: 'Other', value: 25, color: '#1693A5'}
    ];

    $scope.oneAtATime = true;

    $scope.status = {
      isFirstOpen: true,
      tab1: {
        open: true
      },
      tab2: {
        open: false
      },
      tab3: {
        open: false
      }
    };

  })

  .controller('RealtimeLoadCtrl', function($scope, $interval){

    $scope.options1 = {
      renderer: 'area',
      height: 133
    };

    $scope.seriesData = [ [], []];
    var random = new Rickshaw.Fixtures.RandomData(50);

    for (var i = 0; i < 50; i++) {
      random.addData($scope.seriesData);
    }

    var updateInterval = 800;

    $interval(function() {
      random.removeData($scope.seriesData);
      random.addData($scope.seriesData);
    }, updateInterval);

    $scope.series1 = [{
      name: 'Series 1',
      color: 'steelblue',
      data: $scope.seriesData[0]
    }, {
      name: 'Series 2',
      color: 'lightblue',
      data: $scope.seriesData[1]
    }];

    $scope.features1 = {
      hover: {
        xFormatter: function(x) {
          return new Date(x * 1000).toUTCString();
        },
        yFormatter: function(y) {
          return Math.floor(y) + '%';
        }
      }
    };
  })

  .controller('ProjectProgressCtrl', function($scope, DTOptionsBuilder, DTColumnDefBuilder){
    $scope.projects = [{
      title: 'Graphic layout for client',
      priority: {
        value: 1,
        title: 'High Priority'
      },
      status: 42,
      chart: {
        data: [1,3,2,3,5,6,8,5,9,8],
        color: '#cd97eb'
      }
    },{
      title: 'Make website responsive',
      priority: {
        value: 3,
        title: 'Low Priority'
      },
      status: 89,
      chart: {
        data: [2,5,3,4,6,5,1,8,9,10],
        color: '#a2d200'
      }
    },{
      title: 'Clean html/css/js code',
      priority: {
        value: 1,
        title: 'High Priority'
      },
      status: 23,
      chart: {
        data: [5,6,8,2,1,6,8,4,3,5],
        color: '#ffc100'
      }
    },{
      title: 'Database optimization',
      priority: {
        value: 2,
        title: 'Normal Priority'
      },
      status: 56,
      chart: {
        data: [2,9,8,7,5,9,2,3,4,2],
        color: '#16a085'
      }
    },{
      title: 'Database migration',
      priority: {
        value: 3,
        title: 'Low Priority'
      },
      status: 48,
      chart: {
        data: [3,5,6,2,8,9,5,4,3,2],
        color: '#1693A5'
      }
    },{
      title: 'Email server backup',
      priority: {
        value: 2,
        title: 'Normal Priority'
      },
      status: 10,
      chart: {
        data: [7,8,6,4,3,5,8,9,10,7],
        color: '#3f4e62'
      }
    }];

    $scope.dtOptions = DTOptionsBuilder.newOptions().withBootstrap();
    $scope.dtColumnDefs = [
      DTColumnDefBuilder.newColumnDef(0),
      DTColumnDefBuilder.newColumnDef(1),
      DTColumnDefBuilder.newColumnDef(2),
      DTColumnDefBuilder.newColumnDef(3),
      DTColumnDefBuilder.newColumnDef(4).notSortable()
    ];
  });


'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:collapseSidebarSm
 * @description
 * # collapseSidebarSm
 */
app
  .directive('collapseSidebar', function ($rootScope) {
    return {
      restrict: 'A',
      link: function postLink(scope, element) {

        var app = angular.element('.appWrapper'),
            $window = angular.element(window),
            width = $window.width();

        var removeRipple = function() {
          angular.element('#sidebar').find('.ink').remove();
        };

        var collapse = function() {

          width = $window.width();

          if (width < 992) {
            app.addClass('sidebar-sm');
          } else {
            app.removeClass('sidebar-sm sidebar-xs');
          }

          if (width < 768) {
            app.removeClass('sidebar-sm').addClass('sidebar-xs');
          } else if (width > 992){
            app.removeClass('sidebar-sm sidebar-xs');
          } else {
            app.removeClass('sidebar-xs').addClass('sidebar-sm');
          }

          if (app.hasClass('sidebar-sm-forced')) {
            app.addClass('sidebar-sm');
          }

          if (app.hasClass('sidebar-xs-forced')) {
            app.addClass('sidebar-xs');
          }

        };

        collapse();

        $window.resize(function() {
          if(width !== $window.width()) {
            var t;
            clearTimeout(t);
            t = setTimeout(collapse, 300);
            removeRipple();
          }
        });

        element.on('click', function(e) {
          if (app.hasClass('sidebar-sm')) {
            app.removeClass('sidebar-sm').addClass('sidebar-xs');
          }
          else if (app.hasClass('sidebar-xs')) {
            app.removeClass('sidebar-xs');
          }
          else {
            app.addClass('sidebar-sm');
          }

          app.removeClass('sidebar-sm-forced sidebar-xs-forced');
          app.parent().removeClass('sidebar-sm sidebar-xs');
          removeRipple();
          e.preventDefault();
        });

      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:ripple
 * @description
 * # ripple
 */
app
  .directive('ripple', function () {
    return {
      restrict: 'A',
      link: function(scope, element) {
        var parent, ink, d, x, y;

        angular.element(element).find('>li>a').click(function(e){
          parent = angular.element(this).parent();

          if(parent.find('.ink').length === 0) {
            parent.prepend('<span class="ink"></span>');
          }

          ink = parent.find('.ink');
          //incase of quick double clicks stop the previous animation
          ink.removeClass('animate');

          //set size of .ink
          if(!ink.height() && !ink.width())
          {
            //use parent's width or height whichever is larger for the diameter to make a circle which can cover the entire element.
            d = Math.max(parent.outerWidth(), parent.outerHeight());
            ink.css({height: d, width: d});
          }

          //get click coordinates
          //logic = click coordinates relative to page - parent's position relative to page - half of self height/width to make it controllable from the center;
          x = e.pageX - parent.offset().left - ink.width()/2;
          y = e.pageY - parent.offset().top - ink.height()/2;

          //set the position and add class .animate
          ink.css({top: y+'px', left: x+'px'}).addClass('animate');

          setTimeout(function(){
            angular.element('.ink').remove();
          }, 600);
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:NavCtrl
 * @description
 * # NavCtrl
 * Controller of the minovateApp
 */
app
  .controller('NavCtrl', function ($scope) {
    $scope.oneAtATime = false;

    $scope.status = {
      isFirstOpen: true,
      isSecondOpen: true,
      isThirdOpen: true
    };

  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:pageLoader
 * @description
 * # pageLoader
 */
app
  .directive('pageLoader', [
    '$timeout',
    function ($timeout) {
      return {
        restrict: 'AE',
        template: '<div class="dot1"></div><div class="dot2"></div>',
        link: function (scope, element) {
          element.addClass('hide');
          scope.$on('$stateChangeStart', function() {
            element.toggleClass('hide animate');
          });
          scope.$on('$stateChangeSuccess', function (event) {
            event.targetScope.$watch('$viewContentLoaded', function () {
              $timeout(function () {
                element.toggleClass('hide animate');
              }, 600);
            });
          });
        }
      };
    }
  ]);

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:DaterangepickerCtrl
 * @description
 * # DaterangepickerCtrl
 * Controller of the minovateApp
 */
app
  .controller('DaterangepickerCtrl', function ($scope, $moment) {
    $scope.startDate = $moment().subtract(1, 'days').format('MMMM D, YYYY');
    $scope.endDate = $moment().add(31, 'days').format('MMMM D, YYYY');
    $scope.rangeOptions = {
      ranges: {
        Today: [$moment(), $moment()],
        Yesterday: [$moment().subtract(1, 'days'), $moment().subtract(1, 'days')],
        'Last 7 Days': [$moment().subtract(6, 'days'), $moment()],
        'Last 30 Days': [$moment().subtract(29, 'days'), $moment()],
        'This Month': [$moment().startOf('month'), $moment().endOf('month')],
        'Last Month': [$moment().subtract(1, 'month').startOf('month'), $moment().subtract(1, 'month').endOf('month')]
      },
      opens: 'left',
      startDate: $moment().subtract(29, 'days'),
      endDate: $moment(),
      parentEl: '#content'
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:daterangepicker
 * @description
 * # daterangepicker
 */
app
  .directive('daterangepicker', function() {
    return {
      restrict: 'A',
      scope: {
        options: '=daterangepicker',
        start: '=dateBegin',
        end: '=dateEnd'
      },
      link: function(scope, element) {
        element.daterangepicker(scope.options, function(start, end) {
          scope.start = start.format('MMMM D, YYYY');
          scope.end = end.format('MMMM D, YYYY');
          scope.$apply();
        });
      }
    };
  });


'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:BoxedlayoutCtrl
 * @description
 * # BoxedlayoutCtrl
 * Controller of the minovateApp
 */
app
  .controller('BoxedlayoutCtrl', function ($scope) {
    $scope.page = {
      title: 'Boxed Layout',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:FullwidthlayoutCtrl
 * @description
 * # FullwidthlayoutCtrl
 * Controller of the minovateApp
 */
app
  .controller('FullwidthlayoutCtrl', function ($scope) {
    $scope.page = {
      title: 'Full-width Layout',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:SidebarsmlayoutCtrl
 * @description
 * # SidebarsmlayoutCtrl
 * Controller of the minovateApp
 */
app
  .controller('SidebarsmlayoutCtrl', function ($scope) {
    $scope.page = {
      title: 'Small Sidebar Layout',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:SidebarxslayoutCtrl
 * @description
 * # SidebarxslayoutCtrl
 * Controller of the minovateApp
 */
app
  .controller('SidebarxslayoutCtrl', function ($scope) {
    $scope.page = {
      title: 'Extra-small Sidebar Layout',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:HzmenuCtrl
 * @description
 * # HzmenuCtrl
 * Controller of the minovateApp
 */
app
  .controller('HzmenuCtrl', function ($scope) {
     $scope.page = {
      title: 'Horizontal menu Layout',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:RtlCtrl
 * @description
 * # RtlCtrl
 * Controller of the minovateApp
 */
app
  .controller('RtlCtrl', function ($scope) {
    $scope.page = {
      title: 'RTL Layout',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:TypographyCtrl
 * @description
 * # TypographyCtrl
 * Controller of the minovateApp
 */
app
  .controller('TypographyCtrl', function ($scope) {
    $scope.page = {
      title: 'Typography',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:TileControlClose
 * @description
 * # TileControlClose
 */
app
  .directive('tileControlClose', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element) {
        var tile = element.parents('.tile');

        element.on('click', function() {
          tile.addClass('closed').fadeOut();
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:tileControlToggle
 * @description
 * # tileControlToggle
 */
app
  .directive('tileControlToggle', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element) {
        var tile = element.parents('.tile');

        element.on('click', function(){
          tile.toggleClass('collapsed');
          tile.children().not('.tile-header').slideToggle(150);
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:tileControlRefresh
 * @description
 * # tileControlRefresh
 */
app
  .directive('tileControlRefresh', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element) {
        var tile = element.parents('.tile');
        var dropdown = element.parents('.dropdown');

        element.on('click', function(){
          tile.addClass('refreshing');
          dropdown.trigger('click');
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:tileControlFullscreen
 * @description
 * # tileControlFullscreen
 */
app
  .directive('tileControlFullscreen', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element) {
        var dropdown = element.parents('.dropdown');

        element.on('click', function(){
          dropdown.trigger('click');
        });

      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:prettyprint
 * @description
 * # prettyprint
 */
/* jshint ignore:start */
app
  .directive('prettyprint', function () {
    return {
      restrict: 'C',
      link: function postLink(scope, element) {
        element.html(prettyPrintOne(element.html(),'',true));
      }
    };
  });
/* jshint ignore:end */

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ListsCtrl
 * @description
 * # ListsCtrl
 * Controller of the minovateApp
 */
app
  .controller('ListsCtrl', function ($scope) {

    $scope.page = {
      title: 'Lists',
      subtitle: 'Place subtitle here...'
    };

    $scope.list = [
      {
        id: 1,
        title: 'Item 1',
        items: []
      }, {
        id: 2,
        title: 'Item 2',
        items: [
          {
            id: 21,
            title: 'Item 2.1',
            items: [
              {
                id: 211,
                title: 'Item 2.1.1',
                items: []
              }, {
                id: 212,
                title: 'Item 2.1.2',
                items: []
              }
            ]
          }, {
            id: 22,
            title: 'Item 2.2',
            items: [
              {
                id: 221,
                title: 'Item 2.2.1',
                items: []
              }, {
                id: 222,
                title: 'Item 2.2.2',
                items: []
              }
            ]
          }
        ]
      }, {
        id: 3,
        title: 'Item 3',
        items: []
      }, {
        id: 4,
        title: 'Item 4',
        items: [
          {
            id: 41,
            title: 'Item 4.1',
            items: []
          }
        ]
      }, {
        id: 5,
        title: 'Item 5',
        items: []
      }, {
        id: 6,
        title: 'Item 6',
        items: []
      }, {
        id: 7,
        title: 'Item 7',
        items: []
      }
    ];

    $scope.list2 = [
      {
        id: 1,
        title: 'Item 1',
        items: []
      }, {
        id: 2,
        title: 'Item 2',
        items: [
          {
            id: 21,
            title: 'Item 2.1',
            items: [
              {
                id: 211,
                title: 'Item 2.1.1',
                items: []
              }, {
                id: 212,
                title: 'Item 2.1.2',
                items: []
              }
            ]
          }, {
            id: 22,
            title: 'Item 2.2',
            items: [
              {
                id: 221,
                title: 'Item 2.2.1',
                items: []
              }, {
                id: 222,
                title: 'Item 2.2.2',
                items: []
              }
            ]
          }
        ]
      }, {
        id: 3,
        title: 'Item 3',
        items: []
      }, {
        id: 4,
        title: 'Item 4',
        items: [
          {
            id: 41,
            title: 'Item 4.1',
            items: []
          }
        ]
      }, {
        id: 5,
        title: 'Item 5',
        items: []
      }, {
        id: 6,
        title: 'Item 6',
        items: []
      }, {
        id: 7,
        title: 'Item 7',
        items: []
      }
    ];

    /*$scope.editItem = function(scope) {
      scope.editing = true;
    };*/

    $scope.cancelEditingItem = function(scope) {
      scope.editing = false;
    };

    /*$scope.saveGroup = function(group) {
      group.save();
    };*/

    $scope.selectedItem = {};

    $scope.options = {};

    $scope.remove = function(scope) {
      scope.remove();
    };

    $scope.toggle = function(scope) {
      scope.toggle();
    };

    $scope.newSubItem = function(scope) {
      var nodeData;
      nodeData = scope.$modelValue;
      nodeData.items.push({
        id: nodeData.id * 10 + nodeData.items.length,
        title: nodeData.title + '.' + (nodeData.items.length + 1),
        items: []
      });
    };

    var getRootNodesScope = function($event) {
      var target = angular.element($event.target).parents('.tile').find('.angular-ui-tree');

      return target.scope();
    };

    $scope.collapseAll = function($event) {
      var scope = getRootNodesScope($event);
      scope.collapseAll();
    };

    $scope.expandAll = function($event) {
      var scope = getRootNodesScope($event);
      scope.expandAll();
    };

  });

/**
 * lazy-model directive
 *
 * AngularJS directive that works like `ng-model` but saves changes
 * only when form is submitted (otherwise changes are canceled)
 */

angular.module('lazyModel', [])

// lazy-model
.directive('lazyModel', ['$compile', '$timeout',
  function($compile, $timeout) {
    'use strict';
    return {
      restrict: 'A',
      priority: 500,
      terminal: true,
      require: ['lazyModel', '^form', '?^lazySubmit'],
      scope: true,
      controller: ['$scope', '$element', '$attrs', '$parse',
        function($scope, $element, $attrs, $parse) {
          if ($attrs.lazyModel === '') {
            throw '`lazy-model` should have a value.';
          }

          // getter and setter for original model
          var ngModelGet = $parse($attrs.lazyModel);
          var ngModelSet = ngModelGet.assign;

          // accept changes
          this.accept = function() {
            ngModelSet($scope.$parent, $scope.buffer);
          };

          // reset changes
          this.reset = function() {
            $scope.buffer = ngModelGet($scope.$parent);
          };

          // watch for original model change (and initialization also)
          $scope.$watch($attrs.lazyModel, angular.bind(this, function () {
            this.reset();
          }));
        }],
      compile: function compile(elem) {
        // set ng-model to buffer in directive scope (nested)
        elem.attr('ng-model', 'buffer');
        // remove lazy-model attribute to exclude recursion
        elem.removeAttr('lazy-model');
        // store compiled fn
        var compiled = $compile(elem);
        return {
          pre: function(scope) {
            // compile element with ng-model directive poining to `scope.buffer`   
            compiled(scope);
          },
          post: function postLink(scope, elem, attr, ctrls) {
            var lazyModelCtrl = ctrls[0];
            var formCtrl = ctrls[1];
            var lazySubmitCtrl = ctrls[2];
            // parentCtrl may be formCtrl or lazySubmitCtrl
            var parentCtrl = lazySubmitCtrl || formCtrl;

            // for the first time attach hooks
            if (parentCtrl.$lazyControls === undefined) {
              parentCtrl.$lazyControls = [];

              // find form element
              var form = elem.parent();
              while (form[0].tagName !== 'FORM') {
                form = form.parent();
              }

              // bind submit
              form.bind('submit', function() {
                // this submit handler must be called LAST after all other `submit` handlers
                // to get final value of formCtrl.$valid. The only way - is to call it in
                // the next tick via $timeout
                $timeout(function() {
                  if (formCtrl.$valid) {
                    // form valid - accept new values
                    for (var i = 0; i < parentCtrl.$lazyControls.length; i++) {
                      parentCtrl.$lazyControls[i].accept();
                    }

                    // call final hook `lazy-submit`
                    if (lazySubmitCtrl) {
                      lazySubmitCtrl.finalSubmit();
                    }
                  }
                });
              });

              // bind reset
              form.bind('reset', function(e) {
                e.preventDefault();
                $timeout(function() {
                  // reset changes
                  for (var i = 0; i < parentCtrl.$lazyControls.length; i++) {
                    parentCtrl.$lazyControls[i].reset();
                  }
                });
              });

            }

            // add to collection
            parentCtrl.$lazyControls.push(lazyModelCtrl);

            // remove from collection on destroy
            scope.$on('$destroy', function() {
              for (var i = parentCtrl.$lazyControls.length; i--;) {
                if (parentCtrl.$lazyControls[i] === lazyModelCtrl) {
                  parentCtrl.$lazyControls.splice(i, 1);
                }
              }
            });

          }
        };
      }
    };
  }
])

// lazy-submit
.directive('lazySubmit', function() {
    'use strict';
    return {
      restrict: 'A',
      require: ['lazySubmit', 'form'],
      controller: ['$element', '$attrs', '$scope', '$parse',
        function($element, $attrs, $scope, $parse) {
          var finalHook = $attrs.lazySubmit ? $parse($attrs.lazySubmit) : angular.noop;
          this.finalSubmit = function() {
            finalHook($scope);
          };
        }
      ]
    };
});
'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ButtonsiconsCtrl
 * @description
 * # ButtonsiconsCtrl
 * Controller of the minovateApp
 */
app
  .controller('ButtonsIconsCtrl', function ($scope) {
     $scope.page = {
      title: 'Buttons & Icons',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:activateButton
 * @description
 * # activateButton
 */
app
  .directive('activateButton', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element, attrs) {
        var activatedClass = 'btn-activated';
        var status = attrs.activateButton;
        var activate = function() {
          element.addClass(activatedClass);
          setTimeout(function() {
            element.removeClass(activatedClass);
          }, 1000 );
        };

        element.on('click', function() {
          if (!element.hasClass(activatedClass) && status === 'success') {
            element.addClass('btn-activated-success');
            setTimeout(function() {
              element.removeClass('btn-activated-success');
            }, 1000 );
          } else if (!element.hasClass(activatedClass) && status === 'error') {
            element.addClass('btn-activated-error');
            setTimeout(function() {
              element.removeClass('btn-activated-error');
            }, 1000 );
          } else if (!element.hasClass(activatedClass)) {
            activate();
          }
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UiNavsCtrl
 * @description
 * # UiNavsCtrl
 * Controller of the minovateApp
 */
app
  .controller('NavsCtrl', function ($scope) {
    $scope.page = {
      title: 'Navigation & Accordions',
      subtitle: 'Place subtitle here...'
    };
  })
  .controller('AccordionDemoCtrl', function ($scope) {
    $scope.oneAtATime = true;

    $scope.groups = [
      {
        title: 'Dynamic Group Header - 1',
        content: 'Dynamic Group Body - 1'
      },
      {
        title: 'Dynamic Group Header - 2',
        content: 'Dynamic Group Body - 2'
      }
    ];

    $scope.items = ['Item 1', 'Item 2', 'Item 3'];

    $scope.addItem = function() {
      var newItemNo = $scope.items.length + 1;
      $scope.items.push('Item ' + newItemNo);
    };

    $scope.status = {
      isFirstOpen: true,
      isFirstDisabled: false
    };
  })

  .controller('TabsDemoCtrl', function ($scope) {
    $scope.tabs = [
      { title:'Dynamic Title 1', content:'Dynamic content 1' },
      { title:'Dynamic Title 2', content:'Dynamic content 2', disabled: true }
    ];

    $scope.alertMe = function() {
      setTimeout(function() {
         //alert('You\'ve selected the alert tab!');
      });
    };
  })

  .controller('PillsDemoCtrl', function ($scope) {
    $scope.pills = [
      { title:'Dynamic Title 1', content:'Dynamic content 1' },
      { title:'Dynamic Title 2', content:'Dynamic content 2', disabled: true }
    ];

    $scope.alertMe = function() {
      setTimeout(function() {
        //alert('You\'ve selected the alert pill!');
      });
    };
  });


'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UiModalsCtrl
 * @description
 * # UiModalsCtrl
 * Controller of the minovateApp
 */
app
  .controller('ModalsCtrl', function ($scope) {
    $scope.page = {
      title: 'Modals',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('ModalDemoCtrl', function ($scope, $uibModal, $log) {

    $scope.items = ['item1', 'item2', 'item3'];

    $scope.open = function(size) {

      var modalInstance = $uibModal.open({
        templateUrl: 'myModalContent.html',
        controller: 'ModalInstanceCtrl',
        size: size,
        resolve: {
          items: function () {
            return $scope.items;
          }
        }
      });

      modalInstance.result.then(function (selectedItem) {
        $scope.selected = selectedItem;
      }, function () {
        $log.info('Modal dismissed at: ' + new Date());
      });
    };
  })

  // Please note that $modalInstance represents a modal window (instance) dependency.
  // It is not the same as the $modal service used above.

  .controller('ModalInstanceCtrl', function ($scope, $uibModalInstance, items) {

    $scope.items = items;
    $scope.selected = {
      item: $scope.items[0]
    };

    $scope.ok = function () {
      $uibModalInstance.close($scope.selected.item);
    };

    $scope.cancel = function () {
      $uibModalInstance.dismiss('cancel');
    };
  })

  .controller('SplashDemoCtrl', function ($scope, $uibModal, $log) {
    $scope.items = ['item1', 'item2', 'item3'];

    $scope.openSplash = function(event, size) {

      var options = angular.element(event.target).data('options');

      var modalInstance = $uibModal.open({
        templateUrl: 'mySplashContent.html',
        controller: 'ModalInstanceCtrl',
        size: size,
        backdropClass: 'splash' + ' ' + options,
        windowClass: 'splash' + ' ' + options,
        resolve: {
          items: function () {
            return $scope.items;
          }
        }
      });

      modalInstance.result.then(function (selectedItem) {
        $scope.selected = selectedItem;
      }, function () {
        $log.info('Modal dismissed at: ' + new Date());
      });
    };
  });


'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UitilesCtrl
 * @description
 * # UitilesCtrl
 * Controller of the minovateApp
 */
app
  .controller('TilesCtrl', function ($scope) {
    $scope.page = {
      title: 'Tiles',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UiPortletsCtrl
 * @description
 * # UiPortletsCtrl
 * Controller of the minovateApp
 */
app
  .controller('PortletsCtrl', function ($scope) {
    $scope.page = {
      title: 'Portlets',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UiGridCtrl
 * @description
 * # UiGridCtrl
 * Controller of the minovateApp
 */
app
  .controller('GridCtrl', function ($scope) {
    $scope.page = {
      title: 'Grid',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UiAlertsCtrl
 * @description
 * # UiAlertsCtrl
 * Controller of the minovateApp
 */
app
  .controller('AlertsCtrl', function ($scope) {
     $scope.page = {
      title: 'Alerts & Notifications',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('AlertDemoCtrl', ['$scope', '$location', '$anchorScroll', '$timeout', function ($scope, $location, $anchorScroll, $timeout) {

    $scope.alerts = [];

    $scope.alertOptions = {
      colors: [
        {name:'primary'},
        {name:'success'},
        {name:'warning'},
        {name:'danger'},
        {name:'info'},
        {name:'default'},
        {name:'cyan'},
        {name:'amethyst'},
        {name:'green'},
        {name:'orange'},
        {name:'red'},
        {name:'greensea'},
        {name:'dutch'},
        {name:'hotpink'},
        {name:'drank'},
        {name:'blue'},
        {name:'lightred'},
        {name:'slategray'},
        {name:'darkgray'}
      ],
      durations: [
        {name:'never close', value: 9999*9999},
        {name:'1 second', value: 1000},
        {name:'5 seconds', value: 5000},
        {name:'10 seconds', value: 10000}
      ],
      icons: [
        {name: 'none', value: ''},
        {name: 'warning', value: 'fa-warning'},
        {name: 'check', value: 'fa-check'},
        {name: 'user', value: 'fa-user'}
      ],
      msg: 'Place alert text here...'
    };

    $scope.alertType = $scope.alertOptions.colors[2]; // warning

    $scope.alertDuration = $scope.alertOptions.durations[0]; // never close

    $scope.alertIcon = $scope.alertOptions.icons[0]; // none

    $scope.alertCloseable = true;

    $scope.alertCloseAll = true;

    $scope.alertFocus = true;

    $scope.showAlert = function() {

      var alert = {
        msg: $scope.alertOptions.msg,
        type: $scope.alertType.name,
        timeout: $scope.alertDuration.value,
        icon: $scope.alertIcon.value,
        closeable: $scope.alertCloseable,
        closeall: $scope.alertCloseAll,
        focus: $scope.alertFocus
      };

      if (alert.closeall) {
        $scope.alerts = [];
      }

      $scope.alerts.push(alert);

      if (alert.focus) {
        $location.hash('alertsPlaceholder');

        // call $anchorScroll()
        $anchorScroll();
      }

      $timeout(function() {

        $scope.alerts.splice($scope.alerts.indexOf(alert), 1);

      }, $scope.alerts[$scope.alerts.indexOf(alert)].timeout);

    };

    $scope.closeAlert = function(index) {
      $scope.alerts.splice(index, 1);
    };

  }])

  .controller('ToasterDemoCtrl',['$scope', 'toastr', 'toastrConfig', function ($scope, toastr, toastrConfig) {

    var openedToasts = [];

    $scope.toast = {
      colors: [
        {name:'primary'},
        {name:'success'},
        {name:'warning'},
        {name:'error'},
        {name:'info'},
        {name:'default'},
        {name:'cyan'},
        {name:'amethyst'},
        {name:'green'},
        {name:'orange'},
        {name:'red'},
        {name:'greensea'},
        {name:'dutch'},
        {name:'hotpink'},
        {name:'drank'},
        {name:'blue'},
        {name:'lightred'},
        {name:'slategray'},
        {name:'darkgray'}
      ],
      msg: 'Gnome & Growl type non-blocking notifications',
      title: 'This is toaster notification'
    };

    $scope.options = {
      position: 'toast-top-right',
      type: 'success',
      iconClass: $scope.toast.colors[1],
      timeout: '5000',
      extendedTimeout: '1000',
      html: false,
      closeButton: false,
      tapToDismiss: true,
      closeHtml: '<i class="fa fa-times"></i>'
    };

    $scope.$watchCollection('options', function(newValue) {
      toastrConfig.allowHtml = newValue.html;
      toastrConfig.extendedTimeOut = parseInt(newValue.extendedTimeout, 10);
      toastrConfig.positionClass = newValue.position;
      toastrConfig.timeOut = parseInt(newValue.timeout, 10);
      toastrConfig.closeButton = newValue.closeButton;
      toastrConfig.tapToDismiss = newValue.tapToDismiss;
      toastrConfig.closeHtml = newValue.closeHtml;
    });

    $scope.clearLastToast = function() {
      var toast = openedToasts.pop();
      toastr.clear(toast);
    };

    $scope.clearToasts = function() {
      toastr.clear();
    };

    $scope.openToast = function() {

      var toast = toastr[$scope.options.type]($scope.toast.msg, $scope.toast.title, {
                    iconClass: 'toast-'+$scope.options.iconClass.name + ' ' + 'bg-'+$scope.options.iconClass.name
                  });

      openedToasts.push(toast);

    };

  }]);


'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UiGeneralCtrl
 * @description
 * # UiGeneralCtrl
 * Controller of the minovateApp
 */
app
  .controller('GeneralCtrl', function ($scope) {
    $scope.page = {
      title: 'General Elements',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('ProgressDemoCtrl', function ($scope) {
    $scope.max = 200;

    $scope.random = function() {
      var value = Math.floor((Math.random() * 100) + 1);
      var type;

      if (value < 25) {
        type = 'success';
      } else if (value < 50) {
        type = 'info';
      } else if (value < 75) {
        type = 'warning';
      } else {
        type = 'danger';
      }

      $scope.showWarning = (type === 'danger' || type === 'warning');

      $scope.dynamic = value;
      $scope.type = type;
    };

    $scope.random();

    $scope.randomStacked = function() {
      $scope.stacked = [];
      var types = ['success', 'info', 'warning', 'danger'];

      for (var i = 0, n = Math.floor((Math.random() * 4) + 1); i < n; i++) {
          var index = Math.floor((Math.random() * 4));
          $scope.stacked.push({
            value: Math.floor((Math.random() * 30) + 1),
            type: types[index]
          });
      }
    };

    $scope.randomStacked();
  })

  .controller('RatingDemoCtrl', function ($scope) {
    $scope.rate = 7;
    $scope.max = 10;
    $scope.isReadonly = false;

    $scope.hoveringOver = function(value) {
      $scope.overStar = value;
      $scope.percent = 100 * (value / $scope.max);
    };
  })

  .controller('ButtonsCtrl', function ($scope) {
    $scope.singleModel = 1;

    $scope.radioModel = 'Middle';

    $scope.checkModel = {
      left: false,
      middle: true,
      right: false
    };
  })

  .controller('CarouselDemoCtrl', function ($scope) {
    $scope.myInterval = 5000;
    $scope.noWrapSlides = false;
    var slides = $scope.slides = [];
    $scope.addSlide = function() {
      var newWidth = 801 + slides.length + 1;
      slides.push({
        image: '//placekitten.com/' + newWidth + '/300',
        text: ['More','Extra','Lots of','Surplus'][slides.length % 4] + ' ' +
        ['Cats', 'Kittys', 'Felines', 'Cutes'][slides.length % 4]
      });
    };
    for (var i=0; i<4; i++) {
      $scope.addSlide();
    }
  })

  .controller('TooltipDemoCtrl', function ($scope, $sce) {
    $scope.dynamicTooltip = 'Hello, World!';
    $scope.dynamicTooltipText = 'dynamic';
    $scope.htmlTooltip = $sce.trustAsHtml('I\'ve been made <b>bold</b>!');
  })

  /* jshint ignore:start */
  .controller('TypeaheadCtrl', function($scope, $http) {

    $scope.selected = undefined;
    $scope.states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Dakota', 'North Carolina', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'];

    // Any function returning a promise object can be used to load values asynchronously
    $scope.getLocation = function(val) {
      return $http.get('http://maps.googleapis.com/maps/api/geocode/json', {
        params: {
          address: val,
          sensor: false
        }
      }).then(function(response){
        return response.data.results.map(function(item){
          return item.formatted_address;
        });
      });

    };

    $scope.statesWithFlags = [{'name':'Alabama','flag':'5/5c/Flag_of_Alabama.svg/45px-Flag_of_Alabama.svg.png'},{'name':'Alaska','flag':'e/e6/Flag_of_Alaska.svg/43px-Flag_of_Alaska.svg.png'},{'name':'Arizona','flag':'9/9d/Flag_of_Arizona.svg/45px-Flag_of_Arizona.svg.png'},{'name':'Arkansas','flag':'9/9d/Flag_of_Arkansas.svg/45px-Flag_of_Arkansas.svg.png'},{'name':'California','flag':'0/01/Flag_of_California.svg/45px-Flag_of_California.svg.png'},{'name':'Colorado','flag':'4/46/Flag_of_Colorado.svg/45px-Flag_of_Colorado.svg.png'},{'name':'Connecticut','flag':'9/96/Flag_of_Connecticut.svg/39px-Flag_of_Connecticut.svg.png'},{'name':'Delaware','flag':'c/c6/Flag_of_Delaware.svg/45px-Flag_of_Delaware.svg.png'},{'name':'Florida','flag':'f/f7/Flag_of_Florida.svg/45px-Flag_of_Florida.svg.png'},{'name':'Georgia','flag':'5/54/Flag_of_Georgia_%28U.S._state%29.svg/46px-Flag_of_Georgia_%28U.S._state%29.svg.png'},{'name':'Hawaii','flag':'e/ef/Flag_of_Hawaii.svg/46px-Flag_of_Hawaii.svg.png'},{'name':'Idaho','flag':'a/a4/Flag_of_Idaho.svg/38px-Flag_of_Idaho.svg.png'},{'name':'Illinois','flag':'0/01/Flag_of_Illinois.svg/46px-Flag_of_Illinois.svg.png'},{'name':'Indiana','flag':'a/ac/Flag_of_Indiana.svg/45px-Flag_of_Indiana.svg.png'},{'name':'Iowa','flag':'a/aa/Flag_of_Iowa.svg/44px-Flag_of_Iowa.svg.png'},{'name':'Kansas','flag':'d/da/Flag_of_Kansas.svg/46px-Flag_of_Kansas.svg.png'},{'name':'Kentucky','flag':'8/8d/Flag_of_Kentucky.svg/46px-Flag_of_Kentucky.svg.png'},{'name':'Louisiana','flag':'e/e0/Flag_of_Louisiana.svg/46px-Flag_of_Louisiana.svg.png'},{'name':'Maine','flag':'3/35/Flag_of_Maine.svg/45px-Flag_of_Maine.svg.png'},{'name':'Maryland','flag':'a/a0/Flag_of_Maryland.svg/45px-Flag_of_Maryland.svg.png'},{'name':'Massachusetts','flag':'f/f2/Flag_of_Massachusetts.svg/46px-Flag_of_Massachusetts.svg.png'},{'name':'Michigan','flag':'b/b5/Flag_of_Michigan.svg/45px-Flag_of_Michigan.svg.png'},{'name':'Minnesota','flag':'b/b9/Flag_of_Minnesota.svg/46px-Flag_of_Minnesota.svg.png'},{'name':'Mississippi','flag':'4/42/Flag_of_Mississippi.svg/45px-Flag_of_Mississippi.svg.png'},{'name':'Missouri','flag':'5/5a/Flag_of_Missouri.svg/46px-Flag_of_Missouri.svg.png'},{'name':'Montana','flag':'c/cb/Flag_of_Montana.svg/45px-Flag_of_Montana.svg.png'},{'name':'Nebraska','flag':'4/4d/Flag_of_Nebraska.svg/46px-Flag_of_Nebraska.svg.png'},{'name':'Nevada','flag':'f/f1/Flag_of_Nevada.svg/45px-Flag_of_Nevada.svg.png'},{'name':'New Hampshire','flag':'2/28/Flag_of_New_Hampshire.svg/45px-Flag_of_New_Hampshire.svg.png'},{'name':'New Jersey','flag':'9/92/Flag_of_New_Jersey.svg/45px-Flag_of_New_Jersey.svg.png'},{'name':'New Mexico','flag':'c/c3/Flag_of_New_Mexico.svg/45px-Flag_of_New_Mexico.svg.png'},{'name':'New York','flag':'1/1a/Flag_of_New_York.svg/46px-Flag_of_New_York.svg.png'},{'name':'North Carolina','flag':'b/bb/Flag_of_North_Carolina.svg/45px-Flag_of_North_Carolina.svg.png'},{'name':'North Dakota','flag':'e/ee/Flag_of_North_Dakota.svg/38px-Flag_of_North_Dakota.svg.png'},{'name':'Ohio','flag':'4/4c/Flag_of_Ohio.svg/46px-Flag_of_Ohio.svg.png'},{'name':'Oklahoma','flag':'6/6e/Flag_of_Oklahoma.svg/45px-Flag_of_Oklahoma.svg.png'},{'name':'Oregon','flag':'b/b9/Flag_of_Oregon.svg/46px-Flag_of_Oregon.svg.png'},{'name':'Pennsylvania','flag':'f/f7/Flag_of_Pennsylvania.svg/45px-Flag_of_Pennsylvania.svg.png'},{'name':'Rhode Island','flag':'f/f3/Flag_of_Rhode_Island.svg/32px-Flag_of_Rhode_Island.svg.png'},{'name':'South Carolina','flag':'6/69/Flag_of_South_Carolina.svg/45px-Flag_of_South_Carolina.svg.png'},{'name':'South Dakota','flag':'1/1a/Flag_of_South_Dakota.svg/46px-Flag_of_South_Dakota.svg.png'},{'name':'Tennessee','flag':'9/9e/Flag_of_Tennessee.svg/46px-Flag_of_Tennessee.svg.png'},{'name':'Texas','flag':'f/f7/Flag_of_Texas.svg/45px-Flag_of_Texas.svg.png'},{'name':'Utah','flag':'f/f6/Flag_of_Utah.svg/45px-Flag_of_Utah.svg.png'},{'name':'Vermont','flag':'4/49/Flag_of_Vermont.svg/46px-Flag_of_Vermont.svg.png'},{'name':'Virginia','flag':'4/47/Flag_of_Virginia.svg/44px-Flag_of_Virginia.svg.png'},{'name':'Washington','flag':'5/54/Flag_of_Washington.svg/46px-Flag_of_Washington.svg.png'},{'name':'West Virginia','flag':'2/22/Flag_of_West_Virginia.svg/46px-Flag_of_West_Virginia.svg.png'},{'name':'Wisconsin','flag':'2/22/Flag_of_Wisconsin.svg/45px-Flag_of_Wisconsin.svg.png'},{'name':'Wyoming','flag':'b/bc/Flag_of_Wyoming.svg/43px-Flag_of_Wyoming.svg.png'}];
  })
  /* jshint ignore:end */

  .controller('DropdownCtrl', function ($scope) {

     $scope.items = [
      'The first choice!',
      'And another choice for you.',
      'but wait! A third!'
    ];

    $scope.status = {
      isopen: false
    };

    $scope.toggled = function(open) {
      console.log('Dropdown is now: ', open);
    };

    $scope.toggleDropdown = function($event) {
      $event.preventDefault();
      $event.stopPropagation();
      $scope.status.isopen = !$scope.status.isopen;
    };
  })

  .controller('DatepickerDemoCtrl', function ($scope) {

    $scope.today = function() {
      $scope.dt = new Date();
    };

    $scope.today();

    $scope.clear = function () {
      $scope.dt = null;
    };

    // Disable weekend selection
    $scope.disabled = function(date, mode) {
      return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
    };

    $scope.toggleMin = function() {
      $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();

    $scope.open = function($event) {
      $event.preventDefault();
      $event.stopPropagation();

      $scope.opened = true;
    };

    $scope.dateOptions = {
      formatYear: 'yy',
      startingDay: 1,
      'class': 'datepicker'
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];
  })

  .controller('TimepickerDemoCtrl', function ($scope) {
    $scope.mytime = new Date();

    $scope.hstep = 1;
    $scope.mstep = 15;

    $scope.options = {
      hstep: [1, 2, 3],
      mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function() {
      $scope.ismeridian = ! $scope.ismeridian;
    };

    $scope.update = function() {
      var d = new Date();
      d.setHours( 14 );
      d.setMinutes( 0 );
      $scope.mytime = d;
    };

    $scope.changed = function () {
      console.log('Time changed to: ' + $scope.mytime);
    };

    $scope.clear = function() {
      $scope.mytime = null;
    };
  })

  .controller('PaginationDemoCtrl', function ($scope) {
    $scope.totalItems = 64;
    $scope.currentPage = 4;

    $scope.setPage = function (pageNo) {
      $scope.currentPage = pageNo;
    };

    $scope.pageChanged = function() {
      console.log('Page changed to: ' + $scope.currentPage);
    };

    $scope.maxSize = 5;
    $scope.bigTotalItems = 175;
    $scope.bigCurrentPage = 1;
  })

  .controller('PopoverDemoCtrl', function ($scope) {
    $scope.dynamicPopover = 'Hello, World!';
    $scope.dynamicPopoverTitle = 'Title';
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:setNgAnimate
 * @description
 * # setNgAnimate
 */
app
  .directive('setNgAnimate', ['$animate', function ($animate) {
    return {
      link: function ($scope, $element, $attrs) {
        $scope.$watch( function() {
          return $scope.$eval($attrs.setNgAnimate, $scope);
        }, function(valnew){
          console.log('Directive animation Enabled: ' + valnew);
          $animate.enabled(!!valnew, $element);
        });
      }
    };
  }]);

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UiTreeCtrl
 * @description
 * # UiTreeCtrl
 * Controller of the minovateApp
 */
app
  /* jshint ignore:start */
  .controller('AbnTestController', function($scope, $timeout) {
    var apple_selected, tree, treedata_avm, treedata_geography;
    $scope.my_tree_handler = function(branch) {
      var _ref;
      $scope.output = "You selected: " + branch.label;
      if ((_ref = branch.data) != null ? _ref.description : void 0) {
        return $scope.output += '(' + branch.data.description + ')';
      }
    };
    apple_selected = function(branch) {
      return $scope.output = "APPLE! : " + branch.label;
    };
    treedata_avm = [
      {
        label: 'Animal',
        children: [
          {
            label: 'Dog',
            data: {
              description: "man's best friend"
            }
          }, {
            label: 'Cat',
            data: {
              description: "Felis catus"
            }
          }, {
            label: 'Hippopotamus',
            data: {
              description: "hungry, hungry"
            }
          }, {
            label: 'Chicken',
            children: ['White Leghorn', 'Rhode Island Red', 'Jersey Giant']
          }
        ]
      }, {
        label: 'Vegetable',
        data: {
          definition: "A plant or part of a plant used as food, typically as accompaniment to meat or fish, such as a cabbage, potato, carrot, or bean.",
          data_can_contain_anything: true
        },
        onSelect: function(branch) {
          return $scope.output = "Vegetable: " + branch.data.definition;
        },
        children: [
          {
            label: 'Oranges'
          }, {
            label: 'Apples',
            children: [
              {
                label: 'Granny Smith',
                onSelect: apple_selected
              }, {
                label: 'Red Delicous',
                onSelect: apple_selected
              }, {
                label: 'Fuji',
                onSelect: apple_selected
              }
            ]
          }
        ]
      }, {
        label: 'Mineral',
        children: [
          {
            label: 'Rock',
            children: ['Igneous', 'Sedimentary', 'Metamorphic']
          }, {
            label: 'Metal',
            children: ['Aluminum', 'Steel', 'Copper']
          }, {
            label: 'Plastic',
            children: [
              {
                label: 'Thermoplastic',
                children: ['polyethylene', 'polypropylene', 'polystyrene', ' polyvinyl chloride']
              }, {
                label: 'Thermosetting Polymer',
                children: ['polyester', 'polyurethane', 'vulcanized rubber', 'bakelite', 'urea-formaldehyde']
              }
            ]
          }
        ]
      }
    ];
    treedata_geography = [
      {
        label: 'North America',
        children: [
          {
            label: 'Canada',
            children: ['Toronto', 'Vancouver']
          }, {
            label: 'USA',
            children: ['New York', 'Los Angeles']
          }, {
            label: 'Mexico',
            children: ['Mexico City', 'Guadalajara']
          }
        ]
      }, {
        label: 'South America',
        children: [
          {
            label: 'Venezuela',
            children: ['Caracas', 'Maracaibo']
          }, {
            label: 'Brazil',
            children: ['Sao Paulo', 'Rio de Janeiro']
          }, {
            label: 'Argentina',
            children: ['Buenos Aires', 'Cordoba']
          }
        ]
      }
    ];
    $scope.my_data = treedata_avm;
    $scope.try_changing_the_tree_data = function() {
      if ($scope.my_data === treedata_avm) {
        return $scope.my_data = treedata_geography;
      } else {
        return $scope.my_data = treedata_avm;
      }
    };
    $scope.my_tree = tree = {};
    $scope.try_async_load = function() {
      $scope.my_data = [];
      $scope.doing_async = true;
      return $timeout(function() {
        if (Math.random() < 0.5) {
          $scope.my_data = treedata_avm;
        } else {
          $scope.my_data = treedata_geography;
        }
        $scope.doing_async = false;
        return tree.expand_all();
      }, 1000);
    };
    return $scope.try_adding_a_branch = function() {
      var b;
      b = tree.get_selected_branch();
      return tree.add_branch(b, {
        label: 'New Branch',
        data: {
          something: 42,
          "else": 43
        }
      });
    };
  })
  /* jshint ignore:end */
  .controller('TreeCtrl', function ($scope) {
    $scope.page = {
      title: 'Tree',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:FormsCommonCtrl
 * @description
 * # FormsCommonCtrl
 * Controller of the minovateApp
 */
app
  .controller('FormsCommonCtrl', function ($scope) {
    $scope.page = {
      title: 'Common Elements',
      subtitle: 'Place subtitle here...'
    };

    $scope.val = 36;

    var updateModel = function(val){
      $scope.$apply(function(){
        $scope.val = val;
      });
    };

    angular.element('#slider').on('slideStop', function(data){
      updateModel(data.value);
    });

    $scope.clear = function($event) {
      $scope.number.selected = undefined;
      $event.preventDefault();
      $event.stopPropagation();
    };

    $scope.number= {};
    $scope.numbers = ['One', 'Two', 'Three', 'Four'];

    $scope.country = {};
    $scope.countries = [ // Taken from https://gist.github.com/unceus/6501985
      {name: 'Afghanistan', code: 'AF'},
      {name: 'Åland Islands', code: 'AX'},
      {name: 'Albania', code: 'AL'},
      {name: 'Algeria', code: 'DZ'},
      {name: 'American Samoa', code: 'AS'},
      {name: 'Andorra', code: 'AD'},
      {name: 'Angola', code: 'AO'},
      {name: 'Anguilla', code: 'AI'},
      {name: 'Antarctica', code: 'AQ'},
      {name: 'Antigua and Barbuda', code: 'AG'},
      {name: 'Argentina', code: 'AR'},
      {name: 'Armenia', code: 'AM'},
      {name: 'Aruba', code: 'AW'},
      {name: 'Australia', code: 'AU'},
      {name: 'Austria', code: 'AT'},
      {name: 'Azerbaijan', code: 'AZ'},
      {name: 'Bahamas', code: 'BS'},
      {name: 'Bahrain', code: 'BH'},
      {name: 'Bangladesh', code: 'BD'},
      {name: 'Barbados', code: 'BB'},
      {name: 'Belarus', code: 'BY'},
      {name: 'Belgium', code: 'BE'},
      {name: 'Belize', code: 'BZ'},
      {name: 'Benin', code: 'BJ'},
      {name: 'Bermuda', code: 'BM'},
      {name: 'Bhutan', code: 'BT'},
      {name: 'Bolivia', code: 'BO'},
      {name: 'Bosnia and Herzegovina', code: 'BA'},
      {name: 'Botswana', code: 'BW'},
      {name: 'Bouvet Island', code: 'BV'},
      {name: 'Brazil', code: 'BR'},
      {name: 'British Indian Ocean Territory', code: 'IO'},
      {name: 'Brunei Darussalam', code: 'BN'},
      {name: 'Bulgaria', code: 'BG'},
      {name: 'Burkina Faso', code: 'BF'},
      {name: 'Burundi', code: 'BI'},
      {name: 'Cambodia', code: 'KH'},
      {name: 'Cameroon', code: 'CM'},
      {name: 'Canada', code: 'CA'},
      {name: 'Cape Verde', code: 'CV'},
      {name: 'Cayman Islands', code: 'KY'},
      {name: 'Central African Republic', code: 'CF'},
      {name: 'Chad', code: 'TD'},
      {name: 'Chile', code: 'CL'},
      {name: 'China', code: 'CN'},
      {name: 'Christmas Island', code: 'CX'},
      {name: 'Cocos (Keeling) Islands', code: 'CC'},
      {name: 'Colombia', code: 'CO'},
      {name: 'Comoros', code: 'KM'},
      {name: 'Congo', code: 'CG'},
      {name: 'Congo, The Democratic Republic of the', code: 'CD'},
      {name: 'Cook Islands', code: 'CK'},
      {name: 'Costa Rica', code: 'CR'},
      {name: 'Cote D\'Ivoire', code: 'CI'},
      {name: 'Croatia', code: 'HR'},
      {name: 'Cuba', code: 'CU'},
      {name: 'Cyprus', code: 'CY'},
      {name: 'Czech Republic', code: 'CZ'},
      {name: 'Denmark', code: 'DK'},
      {name: 'Djibouti', code: 'DJ'},
      {name: 'Dominica', code: 'DM'},
      {name: 'Dominican Republic', code: 'DO'},
      {name: 'Ecuador', code: 'EC'},
      {name: 'Egypt', code: 'EG'},
      {name: 'El Salvador', code: 'SV'},
      {name: 'Equatorial Guinea', code: 'GQ'},
      {name: 'Eritrea', code: 'ER'},
      {name: 'Estonia', code: 'EE'},
      {name: 'Ethiopia', code: 'ET'},
      {name: 'Falkland Islands (Malvinas)', code: 'FK'},
      {name: 'Faroe Islands', code: 'FO'},
      {name: 'Fiji', code: 'FJ'},
      {name: 'Finland', code: 'FI'},
      {name: 'France', code: 'FR'},
      {name: 'French Guiana', code: 'GF'},
      {name: 'French Polynesia', code: 'PF'},
      {name: 'French Southern Territories', code: 'TF'},
      {name: 'Gabon', code: 'GA'},
      {name: 'Gambia', code: 'GM'},
      {name: 'Georgia', code: 'GE'},
      {name: 'Germany', code: 'DE'},
      {name: 'Ghana', code: 'GH'},
      {name: 'Gibraltar', code: 'GI'},
      {name: 'Greece', code: 'GR'},
      {name: 'Greenland', code: 'GL'},
      {name: 'Grenada', code: 'GD'},
      {name: 'Guadeloupe', code: 'GP'},
      {name: 'Guam', code: 'GU'},
      {name: 'Guatemala', code: 'GT'},
      {name: 'Guernsey', code: 'GG'},
      {name: 'Guinea', code: 'GN'},
      {name: 'Guinea-Bissau', code: 'GW'},
      {name: 'Guyana', code: 'GY'},
      {name: 'Haiti', code: 'HT'},
      {name: 'Heard Island and Mcdonald Islands', code: 'HM'},
      {name: 'Holy See (Vatican City State)', code: 'VA'},
      {name: 'Honduras', code: 'HN'},
      {name: 'Hong Kong', code: 'HK'},
      {name: 'Hungary', code: 'HU'},
      {name: 'Iceland', code: 'IS'},
      {name: 'India', code: 'IN'},
      {name: 'Indonesia', code: 'ID'},
      {name: 'Iran, Islamic Republic Of', code: 'IR'},
      {name: 'Iraq', code: 'IQ'},
      {name: 'Ireland', code: 'IE'},
      {name: 'Isle of Man', code: 'IM'},
      {name: 'Israel', code: 'IL'},
      {name: 'Italy', code: 'IT'},
      {name: 'Jamaica', code: 'JM'},
      {name: 'Japan', code: 'JP'},
      {name: 'Jersey', code: 'JE'},
      {name: 'Jordan', code: 'JO'},
      {name: 'Kazakhstan', code: 'KZ'},
      {name: 'Kenya', code: 'KE'},
      {name: 'Kiribati', code: 'KI'},
      {name: 'Korea, Democratic People\'s Republic of', code: 'KP'},
      {name: 'Korea, Republic of', code: 'KR'},
      {name: 'Kuwait', code: 'KW'},
      {name: 'Kyrgyzstan', code: 'KG'},
      {name: 'Lao People\'s Democratic Republic', code: 'LA'},
      {name: 'Latvia', code: 'LV'},
      {name: 'Lebanon', code: 'LB'},
      {name: 'Lesotho', code: 'LS'},
      {name: 'Liberia', code: 'LR'},
      {name: 'Libyan Arab Jamahiriya', code: 'LY'},
      {name: 'Liechtenstein', code: 'LI'},
      {name: 'Lithuania', code: 'LT'},
      {name: 'Luxembourg', code: 'LU'},
      {name: 'Macao', code: 'MO'},
      {name: 'Macedonia, The Former Yugoslav Republic of', code: 'MK'},
      {name: 'Madagascar', code: 'MG'},
      {name: 'Malawi', code: 'MW'},
      {name: 'Malaysia', code: 'MY'},
      {name: 'Maldives', code: 'MV'},
      {name: 'Mali', code: 'ML'},
      {name: 'Malta', code: 'MT'},
      {name: 'Marshall Islands', code: 'MH'},
      {name: 'Martinique', code: 'MQ'},
      {name: 'Mauritania', code: 'MR'},
      {name: 'Mauritius', code: 'MU'},
      {name: 'Mayotte', code: 'YT'},
      {name: 'Mexico', code: 'MX'},
      {name: 'Micronesia, Federated States of', code: 'FM'},
      {name: 'Moldova, Republic of', code: 'MD'},
      {name: 'Monaco', code: 'MC'},
      {name: 'Mongolia', code: 'MN'},
      {name: 'Montserrat', code: 'MS'},
      {name: 'Morocco', code: 'MA'},
      {name: 'Mozambique', code: 'MZ'},
      {name: 'Myanmar', code: 'MM'},
      {name: 'Namibia', code: 'NA'},
      {name: 'Nauru', code: 'NR'},
      {name: 'Nepal', code: 'NP'},
      {name: 'Netherlands', code: 'NL'},
      {name: 'Netherlands Antilles', code: 'AN'},
      {name: 'New Caledonia', code: 'NC'},
      {name: 'New Zealand', code: 'NZ'},
      {name: 'Nicaragua', code: 'NI'},
      {name: 'Niger', code: 'NE'},
      {name: 'Nigeria', code: 'NG'},
      {name: 'Niue', code: 'NU'},
      {name: 'Norfolk Island', code: 'NF'},
      {name: 'Northern Mariana Islands', code: 'MP'},
      {name: 'Norway', code: 'NO'},
      {name: 'Oman', code: 'OM'},
      {name: 'Pakistan', code: 'PK'},
      {name: 'Palau', code: 'PW'},
      {name: 'Palestinian Territory, Occupied', code: 'PS'},
      {name: 'Panama', code: 'PA'},
      {name: 'Papua New Guinea', code: 'PG'},
      {name: 'Paraguay', code: 'PY'},
      {name: 'Peru', code: 'PE'},
      {name: 'Philippines', code: 'PH'},
      {name: 'Pitcairn', code: 'PN'},
      {name: 'Poland', code: 'PL'},
      {name: 'Portugal', code: 'PT'},
      {name: 'Puerto Rico', code: 'PR'},
      {name: 'Qatar', code: 'QA'},
      {name: 'Reunion', code: 'RE'},
      {name: 'Romania', code: 'RO'},
      {name: 'Russian Federation', code: 'RU'},
      {name: 'Rwanda', code: 'RW'},
      {name: 'Saint Helena', code: 'SH'},
      {name: 'Saint Kitts and Nevis', code: 'KN'},
      {name: 'Saint Lucia', code: 'LC'},
      {name: 'Saint Pierre and Miquelon', code: 'PM'},
      {name: 'Saint Vincent and the Grenadines', code: 'VC'},
      {name: 'Samoa', code: 'WS'},
      {name: 'San Marino', code: 'SM'},
      {name: 'Sao Tome and Principe', code: 'ST'},
      {name: 'Saudi Arabia', code: 'SA'},
      {name: 'Senegal', code: 'SN'},
      {name: 'Serbia and Montenegro', code: 'CS'},
      {name: 'Seychelles', code: 'SC'},
      {name: 'Sierra Leone', code: 'SL'},
      {name: 'Singapore', code: 'SG'},
      {name: 'Slovakia', code: 'SK'},
      {name: 'Slovenia', code: 'SI'},
      {name: 'Solomon Islands', code: 'SB'},
      {name: 'Somalia', code: 'SO'},
      {name: 'South Africa', code: 'ZA'},
      {name: 'South Georgia and the South Sandwich Islands', code: 'GS'},
      {name: 'Spain', code: 'ES'},
      {name: 'Sri Lanka', code: 'LK'},
      {name: 'Sudan', code: 'SD'},
      {name: 'Suriname', code: 'SR'},
      {name: 'Svalbard and Jan Mayen', code: 'SJ'},
      {name: 'Swaziland', code: 'SZ'},
      {name: 'Sweden', code: 'SE'},
      {name: 'Switzerland', code: 'CH'},
      {name: 'Syrian Arab Republic', code: 'SY'},
      {name: 'Taiwan, Province of China', code: 'TW'},
      {name: 'Tajikistan', code: 'TJ'},
      {name: 'Tanzania, United Republic of', code: 'TZ'},
      {name: 'Thailand', code: 'TH'},
      {name: 'Timor-Leste', code: 'TL'},
      {name: 'Togo', code: 'TG'},
      {name: 'Tokelau', code: 'TK'},
      {name: 'Tonga', code: 'TO'},
      {name: 'Trinidad and Tobago', code: 'TT'},
      {name: 'Tunisia', code: 'TN'},
      {name: 'Turkey', code: 'TR'},
      {name: 'Turkmenistan', code: 'TM'},
      {name: 'Turks and Caicos Islands', code: 'TC'},
      {name: 'Tuvalu', code: 'TV'},
      {name: 'Uganda', code: 'UG'},
      {name: 'Ukraine', code: 'UA'},
      {name: 'United Arab Emirates', code: 'AE'},
      {name: 'United Kingdom', code: 'GB'},
      {name: 'United States', code: 'US'},
      {name: 'United States Minor Outlying Islands', code: 'UM'},
      {name: 'Uruguay', code: 'UY'},
      {name: 'Uzbekistan', code: 'UZ'},
      {name: 'Vanuatu', code: 'VU'},
      {name: 'Venezuela', code: 'VE'},
      {name: 'Vietnam', code: 'VN'},
      {name: 'Virgin Islands, British', code: 'VG'},
      {name: 'Virgin Islands, U.S.', code: 'VI'},
      {name: 'Wallis and Futuna', code: 'WF'},
      {name: 'Western Sahara', code: 'EH'},
      {name: 'Yemen', code: 'YE'},
      {name: 'Zambia', code: 'ZM'},
      {name: 'Zimbabwe', code: 'ZW'}
    ];

    $scope.availableColors = ['Red','Green','Blue','Yellow','Magenta','Maroon','Umbra','Turquoise'];
    $scope.multipleDemo = {};
    $scope.multipleDemo.colors = ['Blue','Red'];

    $scope.htmlVariable = '<h2>Try me!</h2><p>textAngular is a super cool WYSIWYG Text Editor directive for AngularJS</p><p><b>Features:</b></p><ol><li>Automatic Seamless Two-Way-Binding</li><li class="text-red">Super Easy <b>Theming</b> Options</li><li>Simple Editor Instance Creation</li><li>Safely Parses Html for Custom Toolbar Icons</li><li>Doesn&apos;t Use an iFrame</li><li>Works with Firefox, Chrome, and IE8+</li></ol><p><b>Code at GitHub:</b> <a href="https://github.com/fraywing/textAngular">Here</a> </p>';

  })

  .controller('tagsInputCtrl', function($scope, $http) {
    $scope.movies = [
      'The Dark Knight',
      'Heat',
      'Inception',
      'The Dark Knight Rises',
      'Kill Bill: Vol. 1',
      'Terminator 2: Judgment Day',
      'The Matrix',
      'Minority Report',
      'The Bourne Ultimatum'
    ];

    $scope.loadMovies = function(query) {
      return $http.get('scripts/jsons/movies.json');
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:FormsValidateCtrl
 * @description
 * # FormsValidateCtrl
 * Controller of the minovateApp
 */
app
  .controller('FormsValidateCtrl', function ($scope) {
    $scope.page = {
      title: 'Validation Elements',
      subtitle: 'Place subtitle here...'
    };

    // function to submit the form after all validation has occurred
		$scope.submitForm = function(isValid) {
      console.log('validate form');

			// check to make sure the form is completely valid
			if (isValid) {
				console.log('our form is amazing');
			} else {
        console.log('form is invalid');
      }

		};

    $scope.notBlackListed = function(value) {
      var blacklist = ['bad@domain.com','verybad@domain.com'];
      return blacklist.indexOf(value) === -1;
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:onBlurValidation
 * @description
 * # onBlurValidation
 */
app
  .directive('onBlurValidation', function () {
    return {
      restrict: 'A',
      require: '?ngModel',
      link: function (scope, elm, attr, ctrl) {
        if (!ctrl) {
          return;
        }

        elm.on('focus', function () {
          elm.addClass('has-focus');

          scope.$apply(function () {
            ctrl.hasFocus = true;
          });
        });

        elm.on('blur', function () {
          elm.removeClass('has-focus');
          elm.addClass('has-visited');

          scope.$apply(function () {
            ctrl.hasFocus = false;
            ctrl.hasVisited = true;
          });
        });

        elm.closest('form').on('submit', function () {
          elm.addClass('has-visited');

          scope.$apply(function () {
            ctrl.hasFocus = false;
            ctrl.hasVisited = true;
          });
        });

      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:formSubmit
 * @description
 * # formSubmit
 */
app
  .directive('submit', function() {
    return {
      restrict: 'A',
      link: function(scope, formElement, attrs) {
        var form;
        form = scope[attrs.name];
        return formElement.bind('submit', function() {
          angular.forEach(form, function(field, name) {
            if (typeof name === 'string' && !name.match('^[$]')) {
              if (field.$pristine) {
                return field.$setViewValue(field.$value);
              }
            }
          });
          if (form.$valid) {
            return scope.$apply(attrs.submit);
          }
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:FormsWizardCtrl
 * @description
 * # FormsWizardCtrl
 * Controller of the minovateApp
 */
app
  .controller('FormWizardCtrl', function ($scope) {
    $scope.page = {
      title: 'Form Wizard',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:FormUploadCtrl
 * @description
 * # FormUploadCtrl
 * Controller of the minovateApp
 */
app
  .controller('FormUploadCtrl', ['$scope', 'FileUploader', function($scope, FileUploader) {
    var uploader = $scope.uploader = new FileUploader({
      //url: 'scripts/modules/fileupload/upload.php' //enable this option to get f
    });

    // FILTERS

    uploader.filters.push({
      name: 'customFilter',
      fn: function() {
        return this.queue.length < 10;
      }
    });

    uploader.filters.push({
      name: 'imageFilter',
      fn: function(item /*{File|FileLikeObject}*/, options) {
          var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
          return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
      }
    });

    // CALLBACKS

    uploader.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
      console.info('onWhenAddingFileFailed', item, filter, options);
    };
    uploader.onAfterAddingFile = function(fileItem) {
      console.info('onAfterAddingFile', fileItem);
    };
    uploader.onAfterAddingAll = function(addedFileItems) {
      console.info('onAfterAddingAll', addedFileItems);
    };
    uploader.onBeforeUploadItem = function(item) {
      console.info('onBeforeUploadItem', item);
    };
    uploader.onProgressItem = function(fileItem, progress) {
      console.info('onProgressItem', fileItem, progress);
    };
    uploader.onProgressAll = function(progress) {
      console.info('onProgressAll', progress);
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
      console.info('onSuccessItem', fileItem, response, status, headers);
    };
    uploader.onErrorItem = function(fileItem, response, status, headers) {
      console.info('onErrorItem', fileItem, response, status, headers);
    };
    uploader.onCancelItem = function(fileItem, response, status, headers) {
      console.info('onCancelItem', fileItem, response, status, headers);
    };
    uploader.onCompleteItem = function(fileItem, response, status, headers) {
      console.info('onCompleteItem', fileItem, response, status, headers);
    };
    uploader.onCompleteAll = function() {
      console.info('onCompleteAll');
    };

    console.info('uploader', uploader);
  }]);

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:FormImgCropCtrl
 * @description
 * # FormImgCropCtrl
 * Controller of the minovateApp
 */
app
  .controller('FormImgCropCtrl', function ($scope) {
    $scope.myImage='';
    $scope.myCroppedImage='';
    $scope.cropType='circle';

    var handleFileSelect=function(evt) {
      var file=evt.currentTarget.files[0];
      var reader = new FileReader();
      reader.onload = function (evt) {
        $scope.$apply(function($scope){
          $scope.myImage=evt.target.result;
        });
      };
      reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#fileInput')).on('change',handleFileSelect);
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:TablesBootstrapCtrl
 * @description
 * # TablesBootstrapCtrl
 * Controller of the minovateApp
 */
app
  .controller('TablesBootstrapCtrl', function ($scope) {
     $scope.page = {
      title: 'Bootstrap Tables',
      subtitle: 'Place subtitle here...'
    };

    $scope.users = [
      { name: 'Mark', lastname: 'Otto', username: '@mdo', checked: true, selected: false },
      { name: 'Jacob', lastname: 'Thornton', username: '@fat', checked: false, selected: false },
      { name: 'Mary', lastname: 'the Bird', username: '@twitter', checked: true, selected: false },
      { name: 'Marv', lastname: 'Bond', username: '@marvo', checked: false, selected: false },
      { name: 'Larry', lastname: 'Cardl', username: '@lurie', checked: false, selected: false },
      { name: 'Jennifer', lastname: 'Minelly', username: '@jen', checked: true, selected: false },
      { name: 'Sly', lastname: 'Stall', username: '@sly', checked: true, selected: false },
      { name: 'Arnold', lastname: 'Percy', username: '@arnie', checked: true, selected: false },
      { name: 'Jack', lastname: 'Black', username: '@blacko', checked: false, selected: false }
    ];

    $scope.selectedAll = false;

    $scope.selectAll = function () {
      angular.forEach($scope.users, function(user) {
        user.selected = $scope.selectedAll;
      });
    };

  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:checkToggler
 * @description
 * # checkToggler
 */
app
  .directive('checkToggler', function () {
    return {
      restrict: 'E',
      link: function postLink(scope, element) {
        element.on('click', function(){

          if (element.hasClass('checked')) {
            element.removeClass('checked');
          } else {
            element.addClass('checked');
          }

        });
      }
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:TablesDatatablesCtrl
 * @description
 * # TablesDatatablesCtrl
 * Controller of the minovateApp
 */
app

  .controller('TablesDatatablesCtrl', function ($scope) {

    $scope.page = {
      title: 'DataTables',
      subtitle: 'Place subtitle here...'
    };

  })

  .controller('BasicDatatableCtrl', function ($scope, DTOptionsBuilder, DTColumnBuilder, $resource) {

    var vm = this;
    vm.message = '';

    function rowCallback(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      // Unbind first in order to avoid any duplicate handler (see https://github.com/l-lin/angular-datatables/issues/87)
      angular.element('td', nRow).unbind('click');
      angular.element('td', nRow).bind('click', function() {
        $scope.$apply(function() {
          vm.someClickHandler(aData);
        });
        angular.element('.row_selected').removeClass('row_selected');
        angular.element(nRow).addClass('row_selected');
      });
      return nRow;
    }

    vm.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
        return $resource('http://www.filltext.com/?rows=300&id={index}&firstName={firstName}&lastName={lastName}&pretty=true').query().$promise;
    })

      .withPaginationType('full_numbers')
      .withBootstrap()
      // Activate col reorder plugin
      .withColReorder()
      .withColReorderCallback(function() {
        console.log('Columns order has been changed with: ' + this.fnOrder());
      })
      .withOption('rowCallback', rowCallback);

    vm.dtColumns = [
      DTColumnBuilder.newColumn('id').withTitle('ID'),
      DTColumnBuilder.newColumn('firstName').withTitle('First name'),
      DTColumnBuilder.newColumn('lastName').withTitle('Last name')
    ];

    function someClickHandler(info) {
      vm.message = info.id + ' - ' + info.firstName;
    }

    vm.someClickHandler = someClickHandler;

  })

  .controller('ChangeDatatableCtrl', function ($scope, $resource, DTOptionsBuilder, DTColumnDefBuilder) {

    var vm = this;

    function _buildPerson2Add(id) {
      return {
        id: id,
        firstName: 'Foo' + id,
        lastName: 'Bar' + id
      };
    }
    function addPerson() {
      vm.persons.push(angular.copy(vm.person2Add));
      vm.person2Add = _buildPerson2Add(vm.person2Add.id + 1);
    }
    function modifyPerson(index) {
      vm.persons.splice(index, 1, angular.copy(vm.person2Add));
      vm.person2Add = _buildPerson2Add(vm.person2Add.id + 1);
    }
    function removePerson(index) {
      vm.persons.splice(index, 1);
    }

    vm.persons = $resource('http://www.filltext.com/?rows=16&id={index}&firstName={firstName}&lastName={lastName}&pretty=true').query();
    vm.dtOptions = DTOptionsBuilder.newOptions().withPaginationType('full_numbers').withBootstrap();
    vm.dtColumnDefs = [
      DTColumnDefBuilder.newColumnDef(0),
      DTColumnDefBuilder.newColumnDef(1),
      DTColumnDefBuilder.newColumnDef(2),
      DTColumnDefBuilder.newColumnDef(3).notSortable()
    ];
    vm.person2Add = _buildPerson2Add(1);
    vm.addPerson = addPerson;
    vm.modifyPerson = modifyPerson;
    vm.removePerson = removePerson;

  })

  .controller('ResponsiveDatatableCtrl', function ($scope, DTOptionsBuilder, DTColumnBuilder) {

    var vm = this;
    vm.dtOptions = DTOptionsBuilder.fromSource('http://www.filltext.com/?rows=300&id={index}&firstName={firstName}&lastName={lastName}&tel={phone|format}&address={streetAddress}&city={city}&state={usState|abbr}&zip={zip}&pretty=true')
      .withPaginationType('full_numbers')
      .withBootstrap()
      // Active Responsive plugin
      .withOption('responsive', true);
    vm.dtColumns = [
      DTColumnBuilder.newColumn('id').withTitle('ID'),
      DTColumnBuilder.newColumn('firstName').withTitle('First name'),
      DTColumnBuilder.newColumn('lastName').withTitle('Last name'),
      // .notVisible() does not work in this case. Use .withClass('none') instead
      DTColumnBuilder.newColumn('tel').withTitle('Phone').withClass('none'),
      DTColumnBuilder.newColumn('address').withTitle('Street Address').withClass('none'),
      DTColumnBuilder.newColumn('city').withTitle('City').withClass('none'),
      DTColumnBuilder.newColumn('state').withTitle('State').withClass('none'),
      DTColumnBuilder.newColumn('zip').withTitle('Zip').withClass('none')
    ];
  })

  .controller('AdvancedDatatableCtrl', function ($scope, DTOptionsBuilder, DTColumnBuilder) {

    var vm = this;

    function stateChange(iColumn, bVisible) {
      console.log('The column', iColumn, ' has changed its status to', bVisible);
    }

    vm.dtOptions = DTOptionsBuilder.fromSource('http://www.filltext.com/?rows=300&id={index}&firstName={firstName}&lastName={lastName}&pretty=true')
      // Add Bootstrap compatibility
      .withBootstrap()
      // Active ColVis plugin
      .withColVis()
      // Add a state change function
      .withColVisStateChange(stateChange)
      // Exclude the last column from the list
      .withColVisOption('aiExclude', [2])
      // Add Table tools compatibility
      .withTableTools('scripts/vendor/datatables/TableTools/swf/copy_csv_xls_pdf.swf')
      .withTableToolsButtons([
        'copy',
        'print', {
          'sExtends': 'collection',
          'sButtonText': 'Save',
          'aButtons': ['csv', 'xls', 'pdf']
        }
      ]);
    vm.dtColumns = [
      DTColumnBuilder.newColumn('id').withTitle('ID'),
      DTColumnBuilder.newColumn('firstName').withTitle('First name'),
      DTColumnBuilder.newColumn('lastName').withTitle('Last name')
    ];


  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:TablesUiGridCtrl
 * @description
 * # TablesUiGridCtrl
 * Controller of the minovateApp
 */
app
  .controller('TablesUiGridCtrl', function ($scope) {
     $scope.page = {
      title: 'UI Grid',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('BasicUiGridCtrl', function ($scope, $resource) {

    $scope.myData = $resource('http://www.filltext.com/?rows=300&firstName={firstName}&lastName={lastName}&company={business}&employed={bool}&pretty=true').query();

    $scope.gridOptions = {
      data: 'myData'
    };

  })

  .controller('FilterUiGridCtrl', function ($scope, $resource, uiGridConstants) {

    $scope.myData = $resource('http://www.filltext.com/?rows=300&name={firstName}~{lastName}&gender=["male","female"]&company={business}&email={email}&phone={phone}&age={numberRange|18,80}}&pretty=true').query();

    $scope.gridOptions = {
      data: 'myData',
      enableFiltering: true,
      columnDefs: [
        // default
        { field: 'name' },
        // pre-populated search field
        { field: 'gender', filter: { term: 'male' } },
        // no filter input
        { field: 'company', enableFiltering: false  },
        // specifies one of the built-in conditions
        // and a placeholder for the input
        {
          field: 'email',
          filter: {
            condition: uiGridConstants.filter.ENDS_WITH,
            placeholder: 'ends with'
          }
        },
        // custom condition function
        {
          field: 'phone',
          filter: {
            condition: function(searchTerm, cellValue) {
              var strippedValue = (cellValue + '').replace(/[^\d]/g, '');
              return strippedValue.indexOf(searchTerm) >= 0;
            }
          }
        },
        // multiple filters
        { field: 'age', filters: [
          {
            condition: uiGridConstants.filter.GREATER_THAN,
            placeholder: 'greater than'
          },
          {
            condition: uiGridConstants.filter.LESS_THAN,
            placeholder: 'less than'
          }
        ]}
      ]
    };

  })

  .controller('FooterUiGridCtrl', function ($scope, $resource, uiGridConstants) {

    $scope.myData = $resource('http://www.filltext.com/?rows=300&name={firstName}~{lastName}&street={numberLength}&age={numberRange|18,80}&ageMin={numberRange|18,80}&ageMax={numberRange|18,80}&customCellTemplate={numberRange|18,80}&pretty=true').query();

    $scope.gridOptions = {
      showGridFooter: true,
      showColumnFooter: true,
      enableFiltering: true,
      columnDefs: [
        { field: 'name', width: 150, aggregationType: uiGridConstants.aggregationTypes.count },
        { field: 'street',aggregationType: uiGridConstants.aggregationTypes.sum, width: 150 },
        { field: 'age', aggregationType: uiGridConstants.aggregationTypes.avg, aggregationHideLabel: true, width: 100 },
        { name: 'ageMin', field: 'age', aggregationType: uiGridConstants.aggregationTypes.min, width: 130, displayName: 'Age for min' },
        { name: 'ageMax', field: 'age', aggregationType: uiGridConstants.aggregationTypes.max, width: 130, displayName: 'Age for max' },
        { name: 'customCellTemplate', field: 'age', width: 150, footerCellTemplate: '<div class="ui-grid-cell-contents" style="background-color: Red;color: White">custom template</div>' }
      ],
      data: 'myData',
      onRegisterApi: function(gridApi) {
        $scope.gridApi = gridApi;
      }
    };

    $scope.toggleFooter = function() {
      $scope.gridOptions.showGridFooter = !$scope.gridOptions.showGridFooter;
      $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.OPTIONS);
    };

    $scope.toggleColumnFooter = function() {
      $scope.gridOptions.showColumnFooter = !$scope.gridOptions.showColumnFooter;
      $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.OPTIONS);
    };
  })

  .controller('ResizeUiGridCtrl', function ($scope, $resource) {

    $scope.myData = $resource('http://www.filltext.com/?rows=300&name={firstName}~{lastName}&gender=["male","female"]&company={business}&pretty=true').query();

    $scope.gridOptions = {
      enableSorting: true,
      columnDefs: [
        { field: 'name', minWidth: 200, width: '50%' },
        { field: 'gender', width: '30%', enableColumnResizing: false },
        { field: 'company', width: '20%' }
      ],
      data: 'myData'
    };

  })

  .controller('EditUiGridCtrl', function ($scope, $resource) {

    $scope.myData = $resource('http://www.filltext.com/?rows=300&id={index}&name={firstName}~{lastName}&age={numberRange|18,80}&gender=[1,2]&registered={date}&isActive={bool}&pretty=true').query();

    $scope.gridOptions = {
      columnDefs: [
        { name: 'id', enableCellEdit: false, width: '10%' },
        { name: 'name', displayName: 'Name (editable)', width: '20%' },
        { name: 'age', displayName: 'Age' , type: 'number', width: '10%' },
        { name: 'gender', displayName: 'Gender', cellFilter: 'mapGender', width: '20%',editableCellTemplate: 'ui-grid/dropdownEditor', editDropdownValueLabel: 'gender', editDropdownOptionsArray: [
          { id: 1, gender: 'male' },
          { id: 2, gender: 'female' }
        ] },
        { name: 'registered', displayName: 'Registered' , type: 'date', cellFilter: 'date:"yyyy-MM-dd"', width: '20%' },
        { name: 'isActive', displayName: 'Active', type: 'boolean', width: '10%' }
      ],
      data: 'myData'
    };

    $scope.msg = {};

    $scope.gridOptions.onRegisterApi = function(gridApi){
      //set gridApi on scope
      $scope.gridApi = gridApi;
      gridApi.edit.on.afterCellEdit($scope,function(rowEntity, colDef, newValue, oldValue){
        $scope.msg.lastCellEdited = 'edited row id:' + rowEntity.id + ' Column:' + colDef.name + ' newValue:' + newValue + ' oldValue:' + oldValue ;
        $scope.$apply();
      });
    };

  })

  .filter('mapGender', function() {
    var genderHash = {
      1: 'male',
      2: 'female'
    };

    return function(input) {
      if (!input){
        return '';
      } else {
        return genderHash[input];
      }
    };
  })

  .controller('ReorderUiGridCtrl', function ($scope, $resource) {

    $scope.myData = $resource('http://www.filltext.com/?rows=300&name={firstName}~{lastName}&gender=["male","female"]&company={business}&pretty=true').query();

    $scope.gridOptions = {
      data: 'myData'
    };

  });



'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:TablesNgTableCtrl
 * @description
 * # TablesNgTableCtrl
 * Controller of the minovateApp
 */
app
  .controller('TablesNgTableCtrl', function ($scope) {
    $scope.page = {
      title: 'ngTable',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('TableSortCtrl', function($scope, $filter, ngTableParams) {
    var data = [{name: 'Moroni', age: 50},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34}];

    $scope.tableParams = new ngTableParams({
      page: 1,            // show first page
      count: 10,          // count per page
      sorting: {
        name: 'asc'     // initial sorting
      }
    }, {
      total: data.length, // length of data
      getData: function($defer, params) {
        // use build-in angular filter
        var orderedData = params.sorting() ?
            $filter('orderBy')(data, params.orderBy()) :
            data;

        $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
      }
    });
  })

  .controller('TableFilterCtrl', function($scope, $filter, ngTableParams) {
    var data = [{name: 'Moroni', age: 50},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34}];

    $scope.tableParams = new ngTableParams({
      page: 1,            // show first page
      count: 10,          // count per page
      filter: {
        name: 'M'       // initial filter
      },
      sorting: {
        name: 'asc'     // initial sorting
      }
    }, {
      total: data.length, // length of data
      getData: function($defer, params) {
        // use build-in angular filter
        var filteredData = params.filter() ?
            $filter('filter')(data, params.filter()) :
            data;
        var orderedData = params.sorting() ?
            $filter('orderBy')(filteredData, params.orderBy()) :
            data;

        params.total(orderedData.length); // set total for recalc pagination
        $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
      }
    });
  })

  .controller('TableEditCtrl', function($scope, $filter, $q, ngTableParams) {
    var data = [{id: 1, name: 'Moroni', age: 50, money: -10},
      {id: 2, name: 'Tiancum', age: 43,money: 120},
      {id: 3, name: 'Jacob', age: 27, money: 5.5},
      {id: 4, name: 'Nephi', age: 29,money: -54},
      {id: 5, name: 'Enos', age: 34,money: 110},
      {id: 6, name: 'Tiancum', age: 43, money: 1000},
      {id: 7, name: 'Jacob', age: 27,money: -201},
      {id: 8, name: 'Nephi', age: 29, money: 100},
      {id: 9, name: 'Enos', age: 34, money: -52.5},
      {id: 10, name: 'Tiancum', age: 43, money: 52.1},
      {id: 11, name: 'Jacob', age: 27, money: 110},
      {id: 12, name: 'Nephi', age: 29, money: -55},
      {id: 13, name: 'Enos', age: 34, money: 551},
      {id: 14, name: 'Tiancum', age: 43, money: -1410},
      {id: 15, name: 'Jacob', age: 27, money: 410},
      {id: 16, name: 'Nephi', age: 29, money: 100},
      {id: 17, name: 'Enos', age: 34, money: -100}
    ];

    $scope.tableParams = new ngTableParams({
      page: 1,            // show first page
      count: 10,           // count per page
      sorting: {
        name: 'asc'     // initial sorting
      }
    }, {
      total: data.length, // length of data
      getData: function($defer, params) {
        // use build-in angular filter
        var orderedData = params.sorting() ?
            $filter('orderBy')(data, params.orderBy()) :
            data;

        $defer.resolve($scope.users = orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
      }
    });

    var inArray = Array.prototype.indexOf ?
      function (val, arr) {
        return arr.indexOf(val);
      } :
      function (val, arr) {
        var i = arr.length;
        while (i--) {
          if (arr[i] === val) {return i;}
        }
        return -1;
      };

    $scope.names = function() {
      var def = $q.defer(),
          arr = [],
          names = [];
      angular.forEach(data, function(item){
        if (inArray(item.name, arr) === -1) {
          arr.push(item.name);
          names.push({
            'id': item.name,
            'title': item.name
          });
        }
      });
      def.resolve(names);
      return def;
    };

    $scope.checkboxes = { 'checked': false, items: {} };

    // watch for check all checkbox
    $scope.$watch('checkboxes.checked', function(value) {
      angular.forEach($scope.users, function(item) {
        if (angular.isDefined(item.id)) {
          $scope.checkboxes.items[item.id] = value;
        }
      });
    });

    // watch for data checkboxes
    $scope.$watch('checkboxes.items', function() {
      if (!$scope.users) {
        return;
      }
      var checked = 0, unchecked = 0,
          total = $scope.users.length;
      angular.forEach($scope.users, function(item) {
        checked   +=  ($scope.checkboxes.items[item.id]) || 0;
        unchecked += (!$scope.checkboxes.items[item.id]) || 0;
      });
      if ((unchecked === 0) || (checked === 0)) {
        $scope.checkboxes.checked = (checked === total);
      }
      // grayed checkbox
      angular.element(document.getElementById('select_all')).prop('indeterminate', (checked !== 0 && unchecked !== 0));
    }, true);
  })

  .controller('TableColumnsCtrl', function($scope, $filter, ngTableParams) {
    var data = [{name: 'Moroni', age: 50},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34}];
    $scope.columns = [
      { title: 'Name', field: 'name', visible: true, filter: { 'name': 'text' } },
      { title: 'Age', field: 'age', visible: true }
    ];
    $scope.tableParams = new ngTableParams({
      page: 1,            // show first page
      count: 10,          // count per page
      filter: {
        name: 'M'       // initial filter
      }
    }, {
      total: data.length, // length of data
      getData: function($defer, params) {
        // use build-in angular filter
        var orderedData = params.sorting() ?
            $filter('orderBy')(data, params.orderBy()) :
            data;

        $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
      }
    });
  })

  .controller('TableGroupCtrl', function($scope, $filter, ngTableParams, $timeout) {
    $scope.rows = [{name: 'Moroni', age: 50, role: 'Administrator', date: '00/00/01'},
      {name: 'Tiancum', age: 43, role: 'Administrator', date: '00/00/01'},
      {name: 'Jacob', age: 27, role: 'Administrator', date: '00/00/01'},
      {name: 'Nephi', age: 29, role: 'Moderator', date: '00/00/01'},
      {name: 'Enos', age: 34, role: 'User', date: '00/00/01'},
      {name: 'Tiancum', age: 43, role: 'User', date: '00/00/01'},
      {name: 'Jacob', age: 27, role: 'User', date: '00/00/01'},
      {name: 'Nephi', age: 29, role: 'Moderator', date: '00/00/01'},
      {name: 'Enos', age: 34, role: 'User', date: '00/00/01'},
      {name: 'Tiancum', age: 43, role: 'Moderator', date: '00/00/01'},
      {name: 'Jacob', age: 27, role: 'User', date: '00/00/01'},
      {name: 'Nephi', age: 29, role: 'User', date: '00/00/01'},
      {name: 'Enos', age: 34, role: 'Moderator', date: '00/00/01'},
      {name: 'Tiancum', age: 43, role: 'User', date: '00/00/01'},
      {name: 'Jacob', age: 27, role: 'User', date: '00/00/01'},
      {name: 'Nephi', age: 29, role: 'User', date: '00/00/01'},
      {name: 'Enos', age: 34, role: 'User', date: '00/00/01'},
      {name: 'Micah', age: 29, role: 'Moderator', date: '00/00/01'},
      {name: 'Viviane', age: 34, role: 'Moderator', date: '00/00/01'},
      {name: 'Marconi', age: 43, role: 'User', date: '00/00/01'},
      {name: 'Leonan', age: 27, role: 'Administrator', date: '00/00/02'},
      {name: 'Arnaldo', age: 29, role: 'User', date: '00/00/02'},
      {name: 'Zuleide', age: 34, role: 'Moderator', date: '00/00/02'}];

    $scope.groupby = 'role'; //Default order IF null get table without groups(not possible ?)

    //dynamic grouping
    $scope.tableParams = new ngTableParams({
      page: 1,            // show first page
      count: 100          // count per page
    }, {
      groupBy: $scope.groupby,
      total: function () { return $scope.rows.length; }, // length of data
      getData: function($defer, params) {
        var orderedData = params.sorting() ?
            $filter('orderBy')($scope.rows, $scope.tableParams.orderBy()) :   $scope.rows;

        $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
      }
    });

    $timeout(function(){
      $scope.$watch('groupby', function(value){
        $scope.tableParams.settings().groupBy = value;
        console.log('Scope Value', $scope.groupby);
        console.log('Watch value', this.last);
        console.log('new table',$scope.tableParams);
        $scope.tableParams.reload();
      });
    }, 0);


  })

  .controller('TableRowCtrl', function ($scope, $filter, ngTableParams) {
    var data = [
      {name: 'Moroni', age: 50},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34},
      {name: 'Tiancum', age: 43},
      {name: 'Jacob', age: 27},
      {name: 'Nephi', age: 29},
      {name: 'Enos', age: 34}
    ];
    $scope.data = data;

    $scope.tableParams = new ngTableParams({
      page: 1,            // show first page
      count: 10,          // count per page
      filter: {
        //name: 'M'       // initial filter
      },
      sorting: {
        //name: 'asc'     // initial sorting
      }
    }, {
      total: data.length, // length of data
      getData: function ($defer, params) {
        // use build-in angular filter
        var filteredData = params.filter() ?
            $filter('filter')(data, params.filter()) :
            data;
        var orderedData = params.sorting() ?
            $filter('orderBy')(filteredData, params.orderBy()) :
            data;

        params.total(orderedData.length); // set total for recalc pagination
        $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
      }
    });

    $scope.changeSelection = function() {
      // console.info(user);
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:TablesSmartTableCtrl
 * @description
 * # TablesSmartTableCtrl
 * Controller of the minovateApp
 */
app
  .controller('TablesSmartTableCtrl', function ($scope) {
    $scope.page = {
      title: 'Smart Table',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('BasicTableCtrl', ['$scope', '$filter', function ($scope) {
    var firstnames = ['Laurent', 'Blandine', 'Olivier', 'Max'];
    var lastnames = ['Renard', 'Faivre', 'Frere', 'Eponge'];
    var dates = ['1987-05-21', '1987-04-25', '1955-08-27', '1966-06-06'];
    var id = 1;

    function generateRandomItem(id) {

      var firstname = firstnames[Math.floor(Math.random() * 3)];
      var lastname = lastnames[Math.floor(Math.random() * 3)];
      var birthdate = dates[Math.floor(Math.random() * 3)];
      var balance = Math.floor(Math.random() * 2000);

      return {
        id: id,
        firstName: firstname,
        lastName: lastname,
        birthDate: new Date(birthdate),
        balance: balance
      };
    }

    $scope.rowCollection = [];

    for (id; id < 5; id++) {
      $scope.rowCollection.push(generateRandomItem(id));
    }

    //copy the references (you could clone ie angular.copy but then have to go through a dirty checking for the matches)
    $scope.displayedCollection = [].concat($scope.rowCollection);

    //add to the real data holder
    $scope.addRandomItem = function addRandomItem() {
      $scope.rowCollection.push(generateRandomItem(id));
      id++;
    };

    //remove to the real data holder
    $scope.removeItem = function removeItem(row) {
      var index = $scope.rowCollection.indexOf(row);
      if (index !== -1) {
        $scope.rowCollection.splice(index, 1);
      }
    };

    $scope.predicates = ['firstName', 'lastName', 'birthDate', 'balance', 'email'];
    $scope.selectedPredicate = $scope.predicates[0];
  }])

  .controller('RowTableCtrl', ['$scope', '$filter', function (scope) {
    var
        nameList = ['Pierre', 'Pol', 'Jacques', 'Robert', 'Elisa'],
        familyName = ['Dupont', 'Germain', 'Delcourt', 'bjip', 'Menez'];

    function createRandomItem() {
      var
          firstName = nameList[Math.floor(Math.random() * 4)],
          lastName = familyName[Math.floor(Math.random() * 4)],
          age = Math.floor(Math.random() * 100),
          email = firstName + lastName + '@whatever.com',
          balance = Math.random() * 3000;

      return{
        firstName: firstName,
        lastName: lastName,
        age: age,
        email: email,
        balance: balance
      };
    }

    scope.itemsByPage=10;

    scope.rowCollection = [];
    for (var j = 0; j < 200; j++) {
      scope.rowCollection.push(createRandomItem());
    }
  }])

  .controller('PipeTableCtrl', ['$scope', '$timeout', function ($scope, $timeout) {
    var nameList = ['Pierre', 'Pol', 'Jacques', 'Robert', 'Elisa'];
    var familyName = ['Dupont', 'Germain', 'Delcourt', 'bjip', 'Menez'];
    var promise = null;


    $scope.isLoading = false;
    $scope.rowCollection = [];


    function createRandomItem() {
      var
          firstName = nameList[Math.floor(Math.random() * 4)],
          lastName = familyName[Math.floor(Math.random() * 4)],
          age = Math.floor(Math.random() * 100),
          email = firstName + lastName + '@whatever.com',
          balance = Math.random() * 3000;

      return{
        firstName: firstName,
        lastName: lastName,
        age: age,
        email: email,
        balance: balance
      };
    }

    function getAPage() {
      $scope.rowCollection=[];
      for (var j = 0; j < 20; j++) {
        $scope.rowCollection.push(createRandomItem());
      }
    }

    $scope.callServer = function getData(tableState, tableController) {

      //here you could create a query string from tableState
      //fake ajax call
      $scope.isLoading = true;

      $timeout(function () {
        getAPage();
        $scope.isLoading = false;
      }, 2000);

    };

    getAPage();

  }]);



'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:TablesFootableCtrl
 * @description
 * # TablesFootableCtrl
 * Controller of the minovateApp
 */
app
  .controller('TablesFootableCtrl', function ($scope) {
    $scope.page = {
      title: 'FooTable',
      subtitle: 'Place subtitle here...'
    };

    $scope.users = [
      { name: 'Mark', lastname: 'Otto', username: '@mdo', phone: '59411994', email: 'otto@mail.com' },
      { name: 'Jacob', lastname: 'Thornton', username: '@fat', phone: '55499126', email: 'jacob@mail.com' },
      { name: 'Mary', lastname: 'the Bird', username: '@twitter', phone: '3159694', email: 'mary@mail.com' },
      { name: 'Marv', lastname: 'Bond', username: '@marvo', phone: '98456152', email: 'marv@mail.com' },
      { name: 'Larry', lastname: 'Cardl', username: '@lurie', phone: '69851195', email: 'larry@mail.com' },
      { name: 'Jennifer', lastname: 'Minelly', username: '@jen', phone: '396784', email: 'jenny@mail.com' },
      { name: 'Sly', lastname: 'Stall', username: '@sly', phone: '9564842', email: 'sly@mail.com' },
      { name: 'Arnold', lastname: 'Percy', username: '@arnie', phone: '1236978', email: 'arnie@mail.com' },
      { name: 'Jack', lastname: 'Black', username: '@blacko', phone: '63248895', email: 'blacko@mail.com' }
    ];
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ChartsCtrl
 * @description
 * # ChartsCtrl
 * Controller of the minovateApp
 */
app
  .controller('ChartsCtrl', function ($scope) {

    $scope.page = {
      title: 'Charts & Graphs',
      subtitle: 'Place subtitle here...'
    };

    $scope.basicData = [
      { year: '2009', a: 15,  b: 5 },
      { year: '2010', a: 20,  b: 10 },
      { year: '2011', a: 35,  b: 25 },
      { year: '2012', a: 40, b: 30 }
    ];

    $scope.donutData = [
      {label: 'Download Sales', value: 12},
      {label: 'In-Store Sales', value: 30},
      {label: 'Mail-Order Sales', value: 20}
    ];

    $scope.areaData = [
      { year: '2009', a: 10,  b: 3 },
      { year: '2010', a: 14,  b: 5 },
      { year: '2011', a: 8,  b: 2 },
      { year: '2012', a: 20, b: 15 }
    ];

  })

  .controller('LineChartCtrl', function ($scope) {

    $scope.dataset = [{
      data: [[1,5.3],[2,5.9],[3,7.2],[4,8],[5,7],[6,6.5],[7,6.2],[8,6.7],[9,7.2],[10,7],[11,6.8],[12,7]],
      label: 'Sales',
      points: {
        show: true,
        radius: 6
      },
      splines: {
        show: true,
        tension: 0.45,
        lineWidth: 5,
        fill: 0
      }
    }, {
      data: [[1,6.6],[2,7.4],[3,8.6],[4,9.4],[5,8.3],[6,7.9],[7,7.2],[8,7.7],[9,8.9],[10,8.4],[11,8],[12,8.3]],
      label: 'Orders',
      points: {
        show: true,
        radius: 6
      },
      splines: {
        show: true,
        tension: 0.45,
        lineWidth: 5,
        fill: 0
      }
    }];

    $scope.options = {
      colors: ['#a2d200', '#cd97eb'],
      series: {
        shadowSize: 0
      },
      xaxis:{
        font: {
          color: '#ccc'
        },
        position: 'bottom',
        ticks: [
          [ 1, 'Jan' ], [ 2, 'Feb' ], [ 3, 'Mar' ], [ 4, 'Apr' ], [ 5, 'May' ], [ 6, 'Jun' ], [ 7, 'Jul' ], [ 8, 'Aug' ], [ 9, 'Sep' ], [ 10, 'Oct' ], [ 11, 'Nov' ], [ 12, 'Dec' ]
        ]
      },
      yaxis: {
        font: {
          color: '#ccc'
        }
      },
      grid: {
        hoverable: true,
        clickable: true,
        borderWidth: 0,
        color: '#ccc'
      },
      tooltip: true,
      tooltipOpts: {
        content: '%s: %y.4',
        defaultTheme: false,
        shifts: {
          x: 0,
          y: 20
        }
      }
    };
  })

  .controller('BarChartCtrl', function ($scope) {

    $scope.data2 = [];

    for (var i = 0; i < 20; ++i) {
      $scope.data2.push([i, Math.sin(i+0.1)]);
    }

    $scope.dataset = [{
      data: $scope.data2,
      label: 'Satisfaction',
      color: '#e05d6f'
    }];

    $scope.options = {
      series: {
        shadowSize: 0
      },
      bars: {
        show: true,
        barWidth: 0.6,
        lineWidth: 0,
        fillColor: {
          colors: [{ opacity:0.8 }, { opacity:0.8}]
        }
      },
      xaxis: {
        font: {
          color: '#ccc'
        }
      },
      yaxis: {
        font: {
          color: '#ccc'
        },
        min: -2,
        max: 2
      },
      grid: {
        hoverable: true,
        clickable: true,
        borderWidth: 0,
        color: '#ccc'
      },
      tooltip: true
    };
  })

  .controller('OrderedChartCtrl', function ($scope) {

    $scope.dataset = [{
      data: [[10, 50], [20, 80], [30, 60], [40, 40]],
      label: 'A'
    }, {
      data: [[10, 30], [20, 50], [30, 70], [40, 50]],
      label: 'B'
    }, {
      data: [[10, 40], [20, 60], [30, 90], [40, 60]],
      label: 'C'
    }];

    $scope.options = {
      series: {
        shadowSize: 0
      },
      bars: {
        show: true,
        fill: true,
        lineWidth: 0,
        fillColor: {
          colors: [{ opacity:0.6 }, { opacity:0.8}]
        },
        order: 1, // order bars
        colors: ['#428bca','#d9534f','#A40778']
      },
      xaxis: {
        font: {
          color: '#ccc'
        }
      },
      yaxis: {
        font: {
          color: '#ccc'
        }
      },
      grid: {
        hoverable: true,
        clickable: true,
        borderWidth: 0,
        color: '#ccc'
      },
      tooltip: true
    };
  })

  .controller('StackedChartCtrl', function ($scope) {

    $scope.dataset = [{
      data: [[10, 50], [20, 80], [30, 60], [40, 40]],
      label: 'A'
    }, {
      data: [[10, 30], [20, 50], [30, 70], [40, 50]],
      label: 'B'
    }, {
      data: [[10, 40], [20, 60], [30, 90], [40, 60]],
      label: 'C'
    }];

    $scope.options = {
      series: {
        shadowSize: 0,
        stack: true // stack bars
      },
      bars: {
        show: true,
        fill: true,
        lineWidth: 0,
        fillColor: {
          colors: [{ opacity:0.6 }, { opacity:0.8}]
        },
        colors: ['#428bca','#d9534f','#A40778']
      },
      xaxis: {
        font: {
          color: '#ccc'
        }
      },
      yaxis: {
        font: {
          color: '#ccc'
        }
      },
      grid: {
        hoverable: true,
        clickable: true,
        borderWidth: 0,
        color: '#ccc'
      },
      tooltip: true
    };
  })

  .controller('CombinedChartCtrl', function ($scope) {

    $scope.dataset = [{
      data: [[0, 8], [1, 15], [2, 16], [3, 14], [4,16], [5,18], [6,17], [7,15], [8,12], [9,13]],
      label: 'Unique Visits',
      points: {
        show: true,
        radius: 3
      },
      splines: {
        show: true,
        tension: 0.45,
        lineWidth: 4,
        fill: 0
      }
    }, {
      data: [[0, 5], [1, 9], [2, 10], [3, 8], [4,9], [5, 12], [6, 14], [7, 13], [8, 10], [9, 12]],
      label: 'Page Views',
      bars: {
        show: true,
        barWidth: 0.4,
        lineWidth: 0,
        fillColor: { colors: [{ opacity: 0.6 }, { opacity: 0.8}] }
      }
    }];

    $scope.options = {
      colors: ['#16a085','#FF0066'],
      series: {
        shadowSize: 0
      },
      xaxis: {
        font: {
          color: '#ccc'
        }
      },
      yaxis: {
        font: {
          color: '#ccc'
        }
      },
      grid: {
        hoverable: true,
        clickable: true,
        borderWidth: 0,
        color: '#ccc'
      },
      tooltip: true,
      tooltipOpts: { content: '%s of %x.1 is %y.4',  defaultTheme: false, shifts: { x: 0, y: 20 } }
    };
  })

  .controller('PieChartCtrl', function ($scope) {

    $scope.dataset = [
      { label: 'Chrome', data: 30 },
      { label: 'Firefox', data: 15 },
      { label: 'Safari', data: 15 },
      { label: 'IE', data: 10 },
      { label: 'Opera', data: 5 },
      { label: 'Other', data: 10}
    ];

    $scope.options = {
      series: {
        pie: {
          show: true,
          innerRadius: 0,
          stroke: {
            width: 0
          },
          label: {
            show: true,
            threshold: 0.05
          }
        }
      },
      colors: ['#428bca','#5cb85c','#f0ad4e','#d9534f','#5bc0de','#616f77'],
      grid: {
        hoverable: true,
        clickable: true,
        borderWidth: 0,
        color: '#ccc'
      },
      tooltip: true,
      tooltipOpts: { content: '%s: %p.0%' }
    };
  })

  .controller('DonutChartCtrl', function ($scope) {

    $scope.dataset = [
      { label: 'Chrome', data: 30 },
      { label: 'Firefox', data: 15 },
      { label: 'Safari', data: 15 },
      { label: 'IE', data: 10 },
      { label: 'Opera', data: 5 },
      { label: 'Other', data: 10}
    ];

    $scope.options = {
      series: {
        pie: {
          show: true,
          innerRadius: 0.5,
          stroke: {
            width: 0
          },
          label: {
            show: true,
            threshold: 0.05
          }
        }
      },
      colors: ['#428bca','#5cb85c','#f0ad4e','#d9534f','#5bc0de','#616f77'],
      grid: {
        hoverable: true,
        clickable: true,
        borderWidth: 0,
        color: '#ccc'
      },
      tooltip: true,
      tooltipOpts: { content: '%s: %p.0%' }
    };
  })

  .controller('RealtimeChartCtrl', function ($scope, $interval) {

    var data = [],
        totalPoints = 300;

    function getRandomData() {

      if (data.length > 0) {
        data = data.slice(1);
      }

      // Do a random walk

      while (data.length < totalPoints) {

        var prev = data.length > 0 ? data[data.length - 1] : 50,
            y = prev + Math.random() * 10 - 5;

        if (y < 0) {
          y = 0;
        } else if (y > 100) {
          y = 100;
        }

        data.push(y);
      }

      // Zip the generated y values with the x values

      var res = [];
      for (var i = 0; i < data.length; ++i) {
        res.push([i, data[i]]);
      }

      return res;
    }

    var updateInterval = 300;

    $interval(function() {
      $scope.dataset = [{
        data: getRandomData()
      }];
      getRandomData();
    }, updateInterval);

    $scope.dataset = [{
      data: getRandomData()
    }];

    $scope.options = {
      colors: ['#a2d200'],
      series: {
        shadowSize: 0,
        lines: {
          show: true,
          fill: 0.1
        }
      },
      xaxis:{
        font: {
          color: '#ccc'
        },
        tickFormatter: function() {
          return '';
        }
      },
      yaxis: {
        font: {
          color: '#ccc'
        },
        min: 0,
        max: 110
      },
      grid: {
        hoverable: true,
        clickable: true,
        borderWidth: 0,
        color: '#ccc'
      },
      tooltip: true,
      tooltipOpts: {
        content: '%y%',
        defaultTheme: false,
        shifts: {
          x: 0,
          y: 20
        }
      }
    };

  })

  .controller('RickshawChartCtrl', function($scope){

    $scope.renderers = [{
      id: 'area',
      name: 'Area'
    }, {
      id: 'line',
      name: 'Line'
    }, {
      id: 'bar',
      name: 'Bar'
    }, {
      id: 'scatterplot',
      name: 'Scatterplot'
    }];

    $scope.palettes = [
      'spectrum14',
      'spectrum2000',
      'spectrum2001',
      'colorwheel',
      'cool',
      'classic9',
      'munin'
    ];

    $scope.rendererChanged = function(id) {
      $scope['options' + id] = {
        renderer: $scope['renderer' + id].id
      };
    };

    $scope.paletteChanged = function(id) {
      $scope['features' + id] = {
        palette: $scope['palette' + id]
      };
    };

    $scope.changeSeriesData = function(id) {
      var seriesList = [];
      for (var i = 0; i < 3; i++) {
        var series = {
          name: 'Series ' + (i + 1),
          data: []
        };
        for (var j = 0; j < 10; j++) {
          series.data.push({x: j, y: Math.random() * 20});
        }
        seriesList.push(series);
        $scope['series' + id][i] = series;
      }
      //$scope['series' + id] = seriesList;
    };

    $scope.options1 = {
      renderer: 'area'
    };
    $scope.series1 = [{
      name: 'Series 1',
      color: 'steelblue',
      data: [{x: 0, y: 23}, {x: 1, y: 15}, {x: 2, y: 79}, {x: 3, y: 31}, {x: 4, y: 60}]
    }, {
      name: 'Series 2',
      color: 'lightblue',
      data: [{x: 0, y: 30}, {x: 1, y: 20}, {x: 2, y: 64}, {x: 3, y: 50}, {x: 4, y: 15}]
    }];

    $scope.options2 = {
      renderer: 'line'
    };
    $scope.features2 = {
      hover: {
        xFormatter: function(x) {
          return 't=' + x;
        },
        yFormatter: function(y) {
          return '$' + y;
        }
      }
    };
    $scope.series2 = [{
      name: 'Series 1',
      color: 'steelblue',
      data: [{x: 0, y: 23}, {x: 1, y: 15}, {x: 2, y: 79}, {x: 3, y: 31}, {x: 4, y: 60}]
    }, {
      name: 'Series 2',
      color: 'lightblue',
      data: [{x: 0, y: 30}, {x: 1, y: 20}, {x: 2, y: 64}, {x: 3, y: 50}, {x: 4, y: 15}]
    }];

    $scope.options3 = {
      renderer: 'bar'
    };
    $scope.features3 = {
      palette: 'colorwheel'
    };
    $scope.series3 = [{
      name: 'Series 1',
      data: [{x: 0, y: 23}, {x: 1, y: 15}, {x: 2, y: 79}, {x: 3, y: 31}, {x: 4, y: 60}]
    }, {
      name: 'Series 2',
      data: [{x: 0, y: 30}, {x: 1, y: 20}, {x: 2, y: 64}, {x: 3, y: 50}, {x: 4, y: 15}]
    }];

    $scope.options4 = {
      renderer: 'bar'
    };
    $scope.features4 = {
      palette: 'colorwheel',
      xAxis: {
      }
    };
    $scope.series4 = [{
      name: 'Series 1',
      data: [{x: 0, y: 230}, {x: 1, y: 1500}, {x: 2, y: 790}, {x: 3, y: 310}, {x: 4, y: 600}]
    }, {
      name: 'Series 2',
      data: [{x: 0, y: 300}, {x: 1, y: 2000}, {x: 2, y: 640}, {x: 3, y: 500}, {x: 4, y: 150}]
    }];

    $scope.options5 = {
      renderer: 'bar'
    };
    $scope.features5 = {
      palette: 'colorwheel',
      yAxis: {
        tickFormat: 'formatKMBT'
      }
    };
    $scope.series5 = [{
      name: 'Series 1',
      data: [{x: 0, y: 230}, {x: 1, y: 1500}, {x: 2, y: 790}, {x: 3, y: 310}, {x: 4, y: 600}]
    }, {
      name: 'Series 2',
      data: [{x: 0, y: 300}, {x: 1, y: 2000}, {x: 2, y: 640}, {x: 3, y: 500}, {x: 4, y: 150}]
    }];

    $scope.options6 = {
      renderer: 'line'
    };
    $scope.features6 = {
      palette: 'colorwheel',
      legend: {
        toggle: true,
        highlight: true
      }
    };
    $scope.series6 = [{
      name: 'Series 1',
      data: [{x: 0, y: 230}, {x: 1, y: 1500}, {x: 2, y: 790}, {x: 3, y: 310}, {x: 4, y: 600}]
    }, {
      name: 'Series 2',
      data: [{x: 0, y: 300}, {x: 1, y: 2000}, {x: 2, y: 640}, {x: 3, y: 500}, {x: 4, y: 150}]
    }];

    $scope.series0 = [];

    $scope.renderer0 = $scope.renderers[0];
    $scope.palette0 = $scope.palettes[0];

    $scope.rendererChanged(0);
    $scope.paletteChanged(0);
    $scope.changeSeriesData(0);
  })

  .controller('RickshawRealtimeChartCtrl', function($scope, $interval){

    $scope.options1 = {
      renderer: 'area'
    };

    var seriesData = [ [], []];
    var random = new Rickshaw.Fixtures.RandomData(50);

    for (var i = 0; i < 50; i++) {
      random.addData(seriesData);
    }

    var updateInterval = 800;

    $interval(function() {
      random.removeData(seriesData);
      random.addData(seriesData);
    }, updateInterval);

    $scope.series1 = [{
      name: 'Series 1',
      color: 'steelblue',
      data: seriesData[0]
    }, {
      name: 'Series 2',
      color: 'lightblue',
      data: seriesData[1]
    }];

    $scope.features1 = {
      hover: {
        xFormatter: function(x) {
          return new Date(x * 1000).toUTCString();
        },
        yFormatter: function(y) {
          return Math.floor(y) + '%';
        }
      }
    };
  })

  .controller('SparklineChartCtrl', function($scope){
    $scope.lineChart = {
      data: [15,16,18,17,16,18,25,26,23],
      options: {
        type: 'line',
        width: '100%',
        height:'250px',
        fillColor: 'rgba(34, 190, 239, .3)',
        lineColor: 'rgba(34, 190, 239, .5)',
        lineWidth: 2,
        spotRadius: 5,
        valueSpots:[5,6,8,7,6,8,5,4,7],
        minSpotColor: '#eaf9fe',
        maxSpotColor: '#00a3d8',
        highlightSpotColor: '#00a3d8',
        highlightLineColor: '#bec6ca',
        normalRangeMin: 0
      }
    };
    $scope.barChart = {
      data: [5,6,7,2,1,-4,-2,4,6,8],
      options: {
        width:'100%',
        type: 'bar',
        height: '250px',
        barWidth: '30px',
        barSpacing: 10,
        barColor: '#16a085',
        negBarColor: '#FF0066'
      }
    };
    $scope.pieChart = {
      data: [5,10,20,15],
      options: {
        type: 'pie',
        width: 'auto',
        height: '250px',
        sliceColors: ['#22beef','#a2d200','#ffc100','#ff4a43']
      }
    };
  })

  .controller('EasypiechartCtrl',function($scope){
    $scope.easypiechart = {
      percent: 65,
      options: {
        animate: {
          duration: 3000,
          enabled: true
        },
        barColor: '#1693A5',
        lineCap: 'round',
        size: 180,
        lineWidth: 5
      }
    };
    $scope.easypiechart2 = {
      percent: 30,
      options: {
        animate: {
          duration: 3000,
          enabled: true
        },
        barColor: '#A40778',
        scaleColor: false,
        lineCap: 'round',
        size: 180,
        lineWidth: 5
      }
    };
    $scope.easypiechart3 = {
      percent: 78,
      options: {
        animate: {
          duration: 3000,
          enabled: true
        },
        barColor: '#e05d6f',
        lineCap: 'butt',
        size: 220,
        lineWidth: 10
      }
    };
    $scope.easypiechart4 = {
      percent: 60,
      options: {
        animate: {
          duration: 3000,
          enabled: true
        },
        barColor: '#5cb85c',
        lineCap: 'round',
        scaleColor: false,
        size: 220,
        lineWidth: 10
      }
    };
  })

  .controller('GaugeChartCtrl', function($scope){

    $scope.gaugeChart1 = {
      data: {
        maxValue: 3000,
        animationSpeed: 40,
        val: 658
      },
      options: {
        lines: 12,
        // The number of lines to draw
        angle: 0.15,
        // The length of each line
        lineWidth: 0.44,
        // The line thickness
        pointer: {
          length: 1,
          // The radius of the inner circle
          strokeWidth: 0.035,
          // The rotation offset
          color: '#000000' // Fill color
        },
        limitMax: 'false',
        // If true, the pointer will not go past the end of the gauge
        colorStart: '#6FADCF',
        // Colors
        colorStop: '#8FC0DA',
        // just experiment with them
        strokeColor: '#f2f2f2',
        // to see which ones work best for you
        generateGradient: true,
        percentColors: [
          [0.0, '#1693A5'],
          [1.0, '#1693A5']
        ]
      }
    };

    $scope.gaugeChart2 = {
      data: {
        maxValue: 3000,
        animationSpeed: 40,
        val: 1258
      },
      options: {
        lines: 12,
        // The number of lines to draw
        angle: 0.10,
        // The length of each line
        lineWidth: 0.40,
        // The line thickness
        pointer: {
          length: 0.9,
          // The radius of the inner circle
          strokeWidth: 0.035,
          // The rotation offset
          color: '#000000' // Fill color
        },
        limitMax: 'false',
        // If true, the pointer will not go past the end of the gauge
        colorStart: '#6FADCF',
        // Colors
        colorStop: '#8FC0DA',
        // just experiment with them
        strokeColor: '#f2f2f2',
        // to see which ones work best for you
        generateGradient: true,
        percentColors: [
          [0.0, '#FF0066'],
          [1.0, '#FF0066']
        ]
      }
    };

    $scope.gaugeChart3 = {
      data: {
        maxValue: 3000,
        animationSpeed: 40,
        val: 1485
      },
      options: {
        lines: 12,
        // The number of lines to draw
        angle: 0.05,
        // The length of each line
        lineWidth: 0.34,
        // The line thickness
        pointer: {
          length: 0.8,
          // The radius of the inner circle
          strokeWidth: 0.035,
          // The rotation offset
          color: '#000000' // Fill color
        },
        limitMax: 'false',
        // If true, the pointer will not go past the end of the gauge
        colorStart: '#6FADCF',
        // Colors
        colorStop: '#8FC0DA',
        // just experiment with them
        strokeColor: '#f2f2f2',
        // to see which ones work best for you
        generateGradient: true,
        percentColors: [
          [0.0, '#428bca'],
          [1.0, '#428bca']
        ]
      }
    };

    $scope.gaugeChart4 = {
      data: {
        maxValue: 3000,
        animationSpeed: 40,
        val: 2514
      },
      options: {
        lines: 12,
        // The number of lines to draw
        angle: 0,
        // The length of each line
        lineWidth: 0.3,
        // The line thickness
        pointer: {
          length: 0.7,
          // The radius of the inner circle
          strokeWidth: 0.035,
          // The rotation offset
          color: '#000000' // Fill color
        },
        limitMax: 'false',
        // If true, the pointer will not go past the end of the gauge
        colorStart: '#6FADCF',
        // Colors
        colorStop: '#8FC0DA',
        // just experiment with them
        strokeColor: '#f2f2f2',
        // to see which ones work best for you
        generateGradient: true,
        percentColors: [
          [0.0, '#f0ad4e'],
          [1.0, '#f0ad4e']
        ]
      }
    };

  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:chartMorris
 * @description
 * # chartMorris
 * https://github.com/jasonshark/ng-morris/blob/master/src/ngMorris.js
 */
app
  .directive('morrisLineChart', function(){

    return {
      restrict: 'A',
      scope: {
        lineData: '=',
        lineXkey: '@',
        lineYkeys: '@',
        lineLabels: '@',
        lineColors: '@'
      },
      link: function (scope, elem, attrs){
        var colors,
            morris;
        if (scope.lineColors === void 0 || scope.lineColors === '') {
          colors = null;
        } else {
          colors = JSON.parse(scope.lineColors);
        }
        scope.$watch('lineData', function(){
          if(scope.lineData){
            if(!morris) {
              morris = new Morris.Line({
                element: elem,
                data: scope.lineData,
                xkey: scope.lineXkey,
                ykeys: JSON.parse(scope.lineYkeys),
                labels: JSON.parse(scope.lineLabels),
                lineColors: colors || ['#0b62a4', '#7a92a3', '#4da74d', '#afd8f8', '#edc240', '#cb4b4b', '#9440ed'],
                resize: true
              });
            } else {
              morris.setData(scope.lineData);
            }
          }
        });
      }
    };
  })

  .directive('morrisAreaChart', function(){

    return {
      restrict: 'A',
      scope: {
        lineData: '=',
        lineXkey: '@',
        lineYkeys: '@',
        lineLabels: '@',
        lineColors: '@',
        lineWidth: '@',
        fillOpacity: '@',
        showGrid: '@'
      },
      link: function (scope, elem, attrs){
        var colors,
            morris;
        if (scope.lineColors === void 0 || scope.lineColors === '') {
          colors = null;
        } else {
          colors = JSON.parse(scope.lineColors);
        }
        scope.$watch('lineData', function(){
          if(scope.lineData){
            if(!morris) {
              morris = new Morris.Area({
                element: elem,
                data: scope.lineData,
                xkey: scope.lineXkey,
                ykeys: JSON.parse(scope.lineYkeys),
                labels: JSON.parse(scope.lineLabels),
                lineColors: colors || ['#0b62a4', '#7a92a3', '#4da74d', '#afd8f8', '#edc240', '#cb4b4b', '#9440ed'],
                lineWidth: scope.lineWidth || '0',
                fillOpacity: scope.fillOpacity || '0.8',
                grid: scope.showGrid || false,
                resize: true
              });
            } else {
              morris.setData(scope.lineData);
            }
          }
        });
      }
    };
  })

  .directive('morrisBarChart', function(){
    return {
      restrict: 'A',
      scope: {
        barData: '=',
        barXkey: '@',
        barYkeys: '@',
        barLabels: '@',
        barColors: '@'
      },
      link: function(scope, elem, attrs){

        var colors,
            morris;
        if (scope.barColors === void 0 || scope.barColors === '') {
          colors = null;
        } else {
          colors = JSON.parse(scope.barColors);
        }

        scope.$watch('barData', function(){
          if(scope.barData){
            if(!morris) {
              morris = new Morris.Bar({
                element: elem,
                data: scope.barData,
                xkey: scope.barXkey,
                ykeys: JSON.parse(scope.barYkeys),
                labels: JSON.parse(scope.barLabels),
                barColors: colors || ['#0b62a4', '#7a92a3', '#4da74d', '#afd8f8', '#edc240', '#cb4b4b', '#9440ed'],
                xLabelMargin: 2,
                resize: true
              });
            } else {
              morris.setData(scope.barData);
            }
          }
        });
      }
    };
  })

  .directive('morrisDonutChart', function(){
    return {
      restrict: 'A',
      scope: {
        donutData: '=',
        donutColors: '@'
      },
      link: function(scope, elem, attrs){
        var colors,
            morris;
        if (scope.donutColors === void 0 || scope.donutColors === '') {
          colors = null;
        } else {
          colors = JSON.parse(scope.donutColors);
        }

        scope.$watch('donutData', function(){
          if(scope.donutData){
            if(!morris) {
              morris = new Morris.Donut({
                element: elem,
                data: scope.donutData,
                colors: colors || ['#0B62A4', '#3980B5', '#679DC6', '#95BBD7', '#B0CCE1', '#095791', '#095085', '#083E67', '#052C48', '#042135'],
                resize: true
              });
            } else {
              morris.setData(scope.donutData);
            }
          }
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:gaugeChart
 * @description
 * # gaugeChart
*/

app
  .directive('gaugeChart', [
  function() {
    return {
      restrict: 'A',
      scope: {
        data: '=',
        options: '='
      },
      link: function($scope, $el) {
        var data = $scope.data,
            options = $scope.options,
            gauge = new Gauge($el[0]).setOptions(options);

        gauge.maxValue = data.maxValue;
        gauge.animationSpeed = data.animationSpeed;
        gauge.set(data.val);
      }
    };
  }
]);

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:wrapOwlcarousel
 * @description
 * # wrapOwlcarousel
 */
app
  .directive('wrapOwlcarousel', function () {
    return {
      restrict: 'E',
      link: function postLink(scope, element) {
        var options = scope.$eval(angular.element(element).attr('data-options'));

        angular.element(element).owlCarousel(options);
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:todoFocus
 * @description
 * # todoFocus
 */
app
  .directive('todoFocus', function ($timeout) {
    return {
      restrict: 'A',
      link: function postLink(scope, element, attrs) {
        scope.$watch(attrs.todoFocus, function (newVal) {
          if (newVal) {
            $timeout(function () {
              element[0].focus();
            }, 0, false);
          }
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:todoEscape
 * @description
 * # todoEscape
 */
app
  .directive('todoEscape', function() {
    var ESCAPE_KEY = 27;

    return {
      restrict: 'A',
      link: function postLink(scope, element, attrs) {
        element.bind('keydown', function (event) {
          if (event.keyCode === ESCAPE_KEY) {
            scope.$apply(attrs.todoEscape);
          }
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:clock
 * @description
 * # clock
 */
app
  .directive('clock', function () {
    return {
      restrict: 'EA',
      replace: true,
      scope: {
        radius: '@',
        zone: '@?',
        lightFill: '@?',
        darkFill: '@?'
      },
      template: '<div class=\'bloc-clock\' ng-style=\'divStyle()\'>\n  <svg xmlns="http://www.w3.org/2000/svg"\n   style="padding: 10px 0"\n    width="100%"\n       height="100%"\n       viewBox="0 0 200 200">\n    <g class=\'face\' style=\'stroke: black; stroke-width: 0px;\'>\n      <g>\n        <circle style="stroke: rgba(255,255,255,.5); fill: rgba(255,255,255,0); stroke-width: 6px;"\n                cx="100"\n                cy="100"\n                r="100"/>\n        <line x1="100"\n              x2="100"\n              y1="10"\n              y2="0"\n              />\n        <line x1="150"\n              x2="145"\n              y1="13"\n              y2="22"\n              />\n        <line x1="150"\n              x2="145"\n              y1="13"\n              y2="22"\n              />\n        <line x1="187"\n              x2="178"\n              y1="50"\n              y2="55"\n              />\n        <line x1="190"\n              x2="200"\n              y1="100"\n              y2="100"\n              />\n        <line x1="187"\n              x2="178"\n              y1="150"\n              y2="145"\n              />\n        <line x1="150"\n              x2="145"\n              y1="187"\n              y2="178"\n              />\n        <line x1="100"\n              x2="100"\n              y1="190"\n              y2="200"\n              />\n        <line x1="50"\n              x2="55"\n              y1="187"\n              y2="178"\n              />\n        <line x1="13"\n              x2="22"\n              y1="150"\n              y2="145"\n              />\n        <line x1="0"\n              x2="10"\n              y1="100"\n              y2="100"\n              />\n        <line x1="13"\n              x2="22"\n              y1="50"\n              y2="55"\n              />\n        <line x1="50"\n              x2="55"\n              y1="13"\n              y2="22"\n              />\n      </g>\n      <g>\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="45"\n                style="stroke-width: 6px"\n                class="hourhand"/>\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="15"\n                style="stroke-width: 6px"\n                class="minutehand" />\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="5"\n                style="stroke-width: 3px; stroke: rgba(255,255,255,.5)"\n                class="secondhand"/>\n      <circle cx="100"\n cy="100"\n r="8"\n fill="white"\n />\n   </g>\n    </g>\n  </svg>\n</div>',
      link: function(scope, el, attrs) {
        var drawClock, drawHands, getRad, getX, getY;

        scope.divStyle = function() {
          return {
            width: scope.w(),
            height: scope.w(),
            margin: '0 auto'
          };
        };

        scope.w = function() {
          return scope.radius * 2;
        };

        getX = function(degrees, r, adjust, x) {
          var adj;
          x = x || r;
          adj = adjust || 1;
          return x + r * adj * Math.cos(getRad(degrees));
        };

        getY = function(degrees, r, adjust, y) {
          var adj;
          y = y || r;
          adj = adjust || 1;
          return y + r * adj * Math.sin(getRad(degrees));
        };

        getRad = function(degrees) {
          var adjust;
          adjust = Math.PI / 2;
          return (degrees * Math.PI / 180) - adjust;
        };

        drawHands = function() {
          var $circle, H_HAND_SIZE, M_HAND_SIZE, S_HAND_SIZE, dark, drawHand, fillColor, hour, hour24, r, strokeColor, t;
          S_HAND_SIZE = 0.95;
          M_HAND_SIZE = 0.85;
          H_HAND_SIZE = 0.55;
          t = scope.zone ? moment.tz(new Date(), scope.zone) : moment();
          r = 100;
          $circle = el.find('circle');
          hour24 = Number(t.format('H'));
          dark = hour24 >= 18 || hour24 < 6;
          strokeColor = 'rgba(255,255,255,1)';
          el.find('line').not('.secondhand').css('stroke', strokeColor);
          drawHand = function(hand, value, size, degrees) {
            var deg, x2, y2;
            deg = degrees * value;
            x2 = getX(deg, r, size, r);
            y2 = getY(deg, r, size, r);
            hand.attr('x1', r);
            hand.attr('y1', r);
            hand.attr('x2', x2);
            hand.attr('y2', y2);
          };
          hour = t.hour() + t.minute() / 60;
          drawHand(angular.element(el).find('.secondhand'), t.second(), S_HAND_SIZE, 6);
          drawHand(angular.element(el).find('.minutehand'), t.minute(), M_HAND_SIZE, 6);
          drawHand(angular.element(el).find('.hourhand'), hour, H_HAND_SIZE, 30);
        };
        drawHands();
        setInterval(drawHands, 1000);
      }
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UiWidgetsCtrl
 * @description
 * # UiWidgetsCtrl
 * Controller of the minovateApp
 */
app
  .controller('WidgetsCtrl', function ($scope) {
    $scope.page = {
      title: 'Widgets',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('TodoWidgetCtrl', function($scope) {
    $scope.todos = [{
      text: 'Release update',
      completed: false
    },{
      text: 'Make a backup',
      completed: false
    },{
      text: 'Send e-mail to Ici',
      completed: true
    },{
      text: 'Buy tickets',
      completed: false
    },{
      text: 'Resolve issues',
      completed: false
    },{
      text: 'Compile new version',
      completed: false
    }];

    var todos = $scope.todos;

    $scope.addTodo = function() {
      $scope.todos.push({
        text: $scope.todo,
        completed: false
      });
      $scope.todo = '';
    };

    $scope.removeTodo = function(todo) {
      todos.splice(todos.indexOf(todo), 1);
    };

    $scope.editTodo = function(todo) {
      $scope.editedTodo = todo;
      // Clone the original todo to restore it on demand.
      $scope.originalTodo = angular.extend({}, todo);
    };

    $scope.doneEditing = function(todo) {
      $scope.editedTodo = null;

      todo.text = todo.text.trim();

      if (!todo.text) {
        $scope.removeTodo(todo);
      }
    };

    $scope.revertEditing = function(todo) {
      todos[todos.indexOf(todo)] = $scope.originalTodo;
      $scope.doneEditing($scope.originalTodo);
    };

  })

  .controller('CalendarWidgetCtrl', function ($scope) {

    $scope.today = function() {
      $scope.dt = new Date();
    };

    $scope.today();

    $scope.clear = function () {
      $scope.dt = null;
    };

    // Disable weekend selection
    $scope.disabled = function(date, mode) {
      return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
    };

    $scope.toggleMin = function() {
      $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();

    $scope.open = function($event) {
      $event.preventDefault();
      $event.stopPropagation();

      $scope.opened = true;
    };

    $scope.dateOptions = {
      formatYear: 'yy',
      startingDay: 1,
      'class': 'datepicker'
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];
  })

  .controller('MessageWidgetCtrl', function($scope){
    $scope.availableRecipients = ['RLake@nec.gov','RBastian@lacus.io','VMonroe@orci.ly','YMckenzie@mattis.gov','VMcmyne@molestie.org','BKliban@aliquam.gov','HHellems@tincidunt.org','KAngell@sollicitudin.ly'];
    $scope.recipients = {};
    $scope.recipients.emails = ['RLake@nec.gov','VMonroe@orci.ly'];

    $scope.messageContent = '<h2>Try me!</h2><p>textAngular is a super cool WYSIWYG Text Editor directive for AngularJS</p><p><b>Code at GitHub:</b> <a href="https://github.com/fraywing/textAngular">Here</a> </p>';
  })

  .controller('AppointmentsWidgetCtrl', function($scope){
    $scope.date = new Date();
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:activeToggle
 * @description
 * # activeToggle
 */
app
  .directive('activeToggle', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element, attr) {

        element.on('click', function(){

          var target = angular.element(attr.target) || Array(element);

          if (element.hasClass('active')) {
            element.removeClass('active');
            target.removeClass('show');
          } else {
            element.addClass('active');
            target.addClass('show');
          }

        });

      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:vectorMap
 * @description
 * # vectorMap
 */
app
  .directive('vectorMap', function () {
    return {
      restrict: 'AE',
      scope: {
        options: '='
      },
      link: function postLink(scope, element) {
        var options = scope.options;
        element.vectorMap(options);
      }
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:MapsVectorMapCtrl
 * @description
 * # MapsVectorMapCtrl
 * Controller of the minovateApp
 */
app
  .controller('VectorMapCtrl', function ($scope) {

    $scope.page = {
      title: 'Vector Maps',
      subtitle: 'Place subtitle here...'
    };

    var sampleData = {'af':'16.63','al':'11.58','dz':'158.97','ao':'85.81','ag':'1.1','ar':'351.02','am':'8.83','au':'1219.72','at':'366.26','az':'52.17','bs':'7.54','bh':'21.73','bd':'105.4','bb':'3.96','by':'52.89','be':'461.33','bz':'1.43','bj':'6.49','bt':'1.4','bo':'19.18','ba':'16.2','bw':'12.5','br':'2023.53','bn':'11.96','bg':'44.84','bf':'8.67','bi':'1.47','kh':'11.36','cm':'21.88','ca':'1563.66','cv':'1.57','cf':'2.11','td':'7.59','cl':'199.18','cn':'5745.13','co':'283.11','km':'0.56','cd':'12.6','cg':'11.88','cr':'35.02','ci':'22.38','hr':'59.92','cy':'22.75','cz':'195.23','dk':'304.56','dj':'1.14','dm':'0.38','do':'50.87','ec':'61.49','eg':'216.83','sv':'21.8','gq':'14.55','er':'2.25','ee':'19.22','et':'30.94','fj':'3.15','fi':'231.98','fr':'2555.44','ga':'12.56','gm':'1.04','ge':'11.23','de':'3305.9','gh':'18.06','gr':'305.01','gd':'0.65','gt':'40.77','gn':'4.34','gw':'0.83','gy':'2.2','ht':'6.5','hn':'15.34','hk':'226.49','hu':'132.28','is':'12.77','in':'1430.02','id':'695.06','ir':'337.9','iq':'84.14','ie':'204.14','il':'201.25','it':'2036.69','jm':'13.74','jp':'5390.9','jo':'27.13','kz':'129.76','ke':'32.42','ki':'0.15','kr':'986.26','undefined':'5.73','kw':'117.32','kg':'4.44','la':'6.34','lv':'23.39','lb':'39.15','ls':'1.8','lr':'0.98','ly':'77.91','lt':'35.73','lu':'52.43','mk':'9.58','mg':'8.33','mw':'5.04','my':'218.95','mv':'1.43','ml':'9.08','mt':'7.8','mr':'3.49','mu':'9.43','mx':'1004.04','md':'5.36','mn':'5.81','me':'3.88','ma':'91.7','mz':'10.21','mm':'35.65','na':'11.45','np':'15.11','nl':'770.31','nz':'138','ni':'6.38','ne':'5.6','ng':'206.66','no':'413.51','om':'53.78','pk':'174.79','pa':'27.2','pg':'8.81','py':'17.17','pe':'153.55','ph':'189.06','pl':'438.88','pt':'223.7','qa':'126.52','ro':'158.39','ru':'1476.91','rw':'5.69','ws':'0.55','st':'0.19','sa':'434.44','sn':'12.66','rs':'38.92','sc':'0.92','sl':'1.9','sg':'217.38','sk':'86.26','si':'46.44','sb':'0.67','za':'354.41','es':'1374.78','lk':'48.24','kn':'0.56','lc':'1','vc':'0.58','sd':'65.93','sr':'3.3','sz':'3.17','se':'444.59','ch':'522.44','sy':'59.63','tw':'426.98','tj':'5.58','tz':'22.43','th':'312.61','tl':'0.62','tg':'3.07','to':'0.3','tt':'21.2','tn':'43.86','tr':'729.05','tm':0,'ug':'17.12','ua':'136.56','ae':'239.65','gb':'2258.57','us':'14624.18','uy':'40.71','uz':'37.72','vu':'0.72','ve':'285.21','vn':'101.99','ye':'30.02','zm':'15.69','zw':'5.57'};

    $scope.worldMap = {
      map: 'world_en',
      backgroundColor: null,
      borderColor: '#fff',
      borderOpacity: 0.25,
      borderWidth: 0.5,
      color: '#e7eaeb',
      enableZoom: true,
      hoverColor: '#16a085',
      normalizeFunction: 'polynomial',
      scaleColors: ['#C8EEFF', '#006491'],
      selectedColor: '#666',
      values: sampleData
    };

    $scope.UsaMap = {
      map: 'usa_en',
      backgroundColor: null,
      borderColor: '#222',
      borderOpacity: 0.25,
      borderWidth: 1,
      color: '#e7eaeb',
      hoverColor: '#16a085',
      selectedColor: '#666666',
      enableZoom: true,
      showTooltip: true,
      selectedRegion: 'MO'
    };

    $scope.EuropeMap = {
      map: 'europe_en',
      backgroundColor: null,
      borderColor: '#222',
      borderOpacity: 0.25,
      borderWidth: 1,
      color: '#e7eaeb',
      hoverColor: '#16a085',
      selectedColor: '#666666',
      enableZoom: true,
      showTooltip: true
    };

    $scope.GermanyMap = {
      map: 'germany_en',
      backgroundColor: null,
      borderColor: '#222',
      borderOpacity: 0.25,
      borderWidth: 1,
      color: '#e7eaeb',
      hoverColor: '#16a085',
      selectedColor: '#666666',
      enableZoom: true,
      showTooltip: true
    };

  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:MapsGoogleMapCtrl
 * @description
 * # MapsGoogleMapCtrl
 * Controller of the minovateApp
 */
app
  .controller('GoogleMapCtrl', function ($scope, $ocLazyLoad) {
    $scope.page = {
      title: 'Google Maps',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('Map1Ctrl', function ($scope) {
    $scope.map = {center: {latitude: 51.219053, longitude: 4.404418 }, zoom: 14 };
    $scope.options = {scrollwheel: false};
  })

  .controller('Map2Ctrl', function($scope) {
    $scope.map = {center: {latitude: 40.1451, longitude: -99.6680 }, zoom: 4, bounds: {}};
    $scope.options = {scrollwheel: false};
    $scope.drawingManagerOptions = {
      drawingMode: google.maps.drawing.OverlayType.MARKER,
      drawingControl: true,
      drawingControlOptions: {
        position: google.maps.ControlPosition.TOP_CENTER,
        drawingModes: [
          google.maps.drawing.OverlayType.MARKER,
          google.maps.drawing.OverlayType.CIRCLE,
          google.maps.drawing.OverlayType.POLYGON,
          google.maps.drawing.OverlayType.POLYLINE,
          google.maps.drawing.OverlayType.RECTANGLE
        ]
      },
      circleOptions: {
        fillColor: '#ffff00',
        fillOpacity: 1,
        strokeWeight: 5,
        clickable: false,
        editable: true,
        zIndex: 1
      }
    };
    $scope.markersAndCircleFlag = true;
    $scope.drawingManagerControl = {};
    $scope.$watch('markersAndCircleFlag', function() {
      if (!$scope.drawingManagerControl.getDrawingManager) {
        return;
      }
      var controlOptions = angular.copy($scope.drawingManagerOptions);
      if (!$scope.markersAndCircleFlag) {
        controlOptions.drawingControlOptions.drawingModes.shift();
        controlOptions.drawingControlOptions.drawingModes.shift();
      }
      $scope.drawingManagerControl.getDrawingManager().setOptions(controlOptions);
    });
  })

  .controller('Map3Ctrl', function($scope) {
    $scope.map = {center: {latitude: 51.219053, longitude: 4.404418 }, zoom: 4 };
    $scope.options = {scrollwheel: false};
    $scope.showWeather = true;
  })

  .controller('Map4Ctrl', function($scope, $log) {
    $scope.map = {center: {latitude: 40.1451, longitude: -99.6680}, zoom: 4};
    $scope.options = {scrollwheel: false};
    $scope.marker = {
      coords: {
        latitude: 40.1451,
        longitude: -99.6680
      },
      show: false,
      id: 0
    };

    $scope.windowOptions = {
      visible: false
    };

    $scope.onClick = function () {
      $scope.windowOptions.visible = !$scope.windowOptions.visible;
    };

    $scope.closeClick = function () {
      $scope.windowOptions.visible = false;
    };

    $scope.title = 'Window Title!';
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:CalendarCtrl
 * @description
 * # CalendarCtrl
 * Controller of the minovateApp
 */
app
  .controller('CalendarCtrl', function ($scope,$compile,uiCalendarConfig) {

    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    /* event source that pulls from google.com */
    $scope.eventSource = {
      url: 'http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic',
      className: 'gcal-event',           // an option!
      currentTimezone: 'America/Chicago' // an option!
    };

    /* event source that contains custom events on the scope */
    $scope.events = [
      {title: 'All Day Event',start: new Date(y, m, 1), className: ['b-l b-2x b-greensea']},
      {title: 'Long Event',start: new Date(y, m, d - 5),end: new Date(y, m, d - 2), className: ['bg-dutch']},
      {id: 999,title: 'Repeating Event',start: new Date(y, m, d - 3, 16, 0),allDay: false, className: ['b-l b-2x b-primary']},
      {id: 999,title: 'Repeating Event',start: new Date(y, m, d + 4, 16, 0),allDay: false, className: ['b-l b-2x b-primary']},
      {title: 'Birthday Party',start: new Date(y, m, d + 1, 19, 0),end: new Date(y, m, d + 1, 22, 30),allDay: false, className: ['b-l b-2x b-default']},
      {title: 'Click for Google',start: new Date(y, m, 28),end: new Date(y, m, 29),url: 'http://google.com/', className: ['b-l b-2x b-hotpink']},
      {title: 'Make cupcakes', start: new Date(y, m, 2), className: ['b-l b-2x b-info'], location:'Bratislava', info:'The best in whole world.'},
      {title: 'Call wife', start: new Date(y, m, 6),end: new Date(y, m, 7), className: ['b-l b-2x b-red'], location:'Piestany', info:'And say her hello.'}
    ];

    /* alert on dayClick */
    $scope.precision = 400;
    $scope.lastClickTime = 0;
    $scope.doubleClick = function( date, jsEvent, view ){
      var time = new Date().getTime();
      if(time - $scope.lastClickTime <= $scope.precision){
        $scope.events.push({
          title: 'New Event',
          start: date,
          className: ['b-l b-2x b-info']
        });
      }
      $scope.lastClickTime = time;
    };

    /* alert on Drop */
    $scope.alertOnDrop = function(event, delta, revertFunc, jsEvent, ui, view){
      $scope.alertMessage = ('Event Droped to make dayDelta ' + delta);
    };

    /* alert on Resize */
    $scope.alertOnResize = function(event, delta, revertFunc, jsEvent, ui, view){
      $scope.alertMessage = ('Event Resized to make dayDelta ' + delta);
    };

    $scope.overlay = angular.element('.fc-overlay');

    $scope.alertOnMouseOver = function( event, jsEvent, view ){
      $scope.event = event;
      $scope.overlay.removeClass('left right');
      var wrap = angular.element(jsEvent.target).closest('.fc-event');
      var cal = wrap.closest('.calendar');
      var left = wrap.offset().left - cal.offset().left;
      var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
      if( right > $scope.overlay.width() ) {
        $scope.overlay.addClass('left');
      } else if ( left > $scope.overlay.width() ) {
        $scope.overlay.addClass('right');
      }
      if (wrap.find('.fc-overlay').length === 0) {
        wrap.append( $scope.overlay );
      }
    };

    /* config object */
    $scope.uiConfig = {
      calendar:{
        height: 450,
        editable: true,
        header:{
          left: 'prev',
          center: 'title',
          right: 'next'
        },
        dayClick: $scope.doubleClick,
        eventDrop: $scope.alertOnDrop,
        eventResize: $scope.alertOnResize,
        eventMouseover: $scope.alertOnMouseOver
      }
    };

    /* add custom event*/
    $scope.addEvent = function() {
      $scope.events.push({
        title: 'New Event',
        start: new Date(y, m, d),
        className: ['b-l b-2x b-info']
      });
    };

    /* remove event */
    $scope.remove = function(index) {
      $scope.events.splice(index,1);
    };

    /* Change View */
    $scope.changeView = function(view, calendar) {
      angular.element('.calendar').fullCalendar('changeView', view);
    };

    $scope.today = function(calendar) {
      angular.element('.calendar').fullCalendar('today');
    };

    /* event sources array*/
    $scope.eventSources = [$scope.events];


  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:MailInboxCtrl
 * @description
 * # MailInboxCtrl
 * Controller of the minovateApp
 */
app
  .controller('MailInboxCtrl', function ($scope, $resource) {
    $scope.mails = $resource('scripts/jsons/mails.json').query();

    $scope.selectedAll = false;

    $scope.selectAll = function () {

      if ($scope.selectedAll) {
        $scope.selectedAll = false;
      } else {
        $scope.selectedAll = true;
      }

      angular.forEach($scope.mails, function(mail) {
        mail.selected = $scope.selectedAll;
      });
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:MailComposeCtrl
 * @description
 * # MailComposeCtrl
 * Controller of the minovateApp
 */
app
  .controller('MailComposeCtrl', function ($scope) {
    $scope.availableRecipients = ['RLake@nec.gov','RBastian@lacus.io','VMonroe@orci.ly','YMckenzie@mattis.gov','VMcmyne@molestie.org','BKliban@aliquam.gov','HHellems@tincidunt.org','KAngell@sollicitudin.ly'];
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:MailSingleCtrl
 * @description
 * # MailSingleCtrl
 * Controller of the minovateApp
 */
app
  .controller('MailSingleCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:MailCtrl
 * @description
 * # MailCtrl
 * Controller of the minovateApp
 */
app
  .controller('MailCtrl', function ($scope) {

  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:PagesLoginCtrl
 * @description
 * # PagesLoginCtrl
 * Controller of the minovateApp
 */
app
  .controller('LoginCtrl', function ($scope, $state) {
    $scope.login = function() {
      $state.go('app.dashboard');
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:PagesSignupCtrl
 * @description
 * # PagesSignupCtrl
 * Controller of the minovateApp
 */
app
  .controller('SignupCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:PagesForgotPasswordCtrl
 * @description
 * # PagesForgotPasswordCtrl
 * Controller of the minovateApp
 */
app
  .controller('ForgotPasswordCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:PagesGalleryCtrl
 * @description
 * # PagesGalleryCtrl
 * Controller of the minovateApp
 */
app
  .controller('GalleryCtrl', function ($scope) {

    $scope.page = {
      title: 'Gallery',
      subtitle: 'Place subtitle here...'
    };

    $scope.images = [
      {
        src: 'http://lorempixel.com/800/600/cats/1',
        title: 'Sed ut perspiciatis unde',
        category: 'cats',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/cats/2',
        title: 'Quis autem vel eum iure',
        category: 'cats',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/cats/3',
        title: 'Temporibus autem quibusdam',
        category: 'cats',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/cats/4',
        title: 'Neque porro quisquam est',
        category: 'cats',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/cats/5',
        title: 'Et harum quidem rerum',
        category: 'cats',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/animals/6',
        title: 'Nemo enim ipsam voluptatem',
        category: 'animals',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/animals/7',
        title: 'At vero eos et accusamus',
        category: 'animals',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/animals/8',
        title: 'Itaque earum rerum hic tenetur',
        category: 'animals',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/animals/9',
        title: 'Ut enim ad minima veniam',
        category: 'animals',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/animals/10',
        title: 'Temporibus autem quibusdam',
        category: 'animals',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/city/1',
        title: 'Neque porro quisquam est',
        category: 'cities',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/city/2',
        title: 'Nam libero tempore',
        category: 'cities',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/city/3',
        title: 'Neque porro quisquam est',
        category: 'cities',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/city/4',
        title: 'Nam libero tempore',
        category: 'cities',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/city/5',
        title: 'Neque porro quisquam est',
        category: 'cities',
        selected: false
      },{
        src: 'http://lorempixel.com/800/600/city/6',
        title: 'Nam libero tempore',
        category: 'cities',
        selected: false
     }
    ];

    $scope.selectedAll = false;
    $scope.isSelected = false;

    $scope.selectAll = function () {

      if ($scope.selectedAll) {
        $scope.selectedAll = false;
        $scope.isSelected = false;
      } else {
        $scope.selectedAll = true;
        $scope.isSelected = true;
      }

      angular.forEach($scope.images, function(image) {
        image.selected = $scope.selectedAll;
      });
    };

    $scope.selectImage = function(index) {

      var i = 0;

      if ($scope.images[index].selected) {
        $scope.images[index].selected = false;
      } else {
        $scope.images[index].selected = true;
        $scope.isSelected = true;
      }

      angular.forEach($scope.images, function(image) {
        if (image.selected) {
          i++;
        }
      });

      if (i === 0) {
        $scope.isSelected = false;
      }
    };

  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:PagesTimelineCtrl
 * @description
 * # PagesTimelineCtrl
 * Controller of the minovateApp
 */
app
  .controller('TimelineCtrl', function ($scope) {
    $scope.page = {
      title: 'Timeline',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:PagesChatCtrl
 * @description
 * # PagesChatCtrl
 * Controller of the minovateApp
 */
app
  .controller('ChatCtrl', function ($scope, $resource) {
    $scope.inbox = $resource('scripts/jsons/chats.json').query();

    $scope.archive = function(index) {
      $scope.inbox.splice(index, 1);
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:PagesSearchResultsCtrl
 * @description
 * # PagesSearchResultsCtrl
 * Controller of the minovateApp
 */
app
  .controller('SearchResultsCtrl', function ($scope) {
    $scope.page = {
      title: 'Search Results',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:PagesProfileCtrl
 * @description
 * # PagesProfileCtrl
 * Controller of the minovateApp
 */
app
  .controller('ProfileCtrl', function ($scope) {
    $scope.page = {
      title: 'Profile Page',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:HelpCtrl
 * @description
 * # HelpCtrl
 * Controller of the minovateApp
 */
app
  .controller('HelpCtrl', function ($scope) {
     $scope.page = {
      title: 'Documentation',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:anchorScroll
 * @description
 * # anchorScroll
 */
app
  .directive('anchorScroll', ['$location', '$anchorScroll', function($location, $anchorScroll) {
    return {
      restrict: 'AC',
      link: function(scope, el, attr) {
        el.on('click', function(e) {
          $location.hash(attr.anchorScroll);
          $anchorScroll();
        });
      }
    };
  }]);

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ShopOrdersCtrl
 * @description
 * # ShopOrdersCtrl
 * Controller of the minovateApp
 */
app
  .controller('OrdersCtrl', function ($scope) {
    $scope.page = {
      title: 'Orders',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('OrdersTableCtrl', function ($scope, DTOptionsBuilder, DTColumnDefBuilder, DTColumnBuilder, $resource) {

    var vm = this;
    vm.orders = [];
    vm.dtOptions = DTOptionsBuilder.newOptions()
      .withBootstrap()
      .withOption('order', [[1, 'asc']])
      .withDOM('<"row"<"col-md-8 col-sm-12"<"inline-controls"l>><"col-md-4 col-sm-12"<"pull-right"f>>>t<"row"<"col-md-4 col-sm-12"<"inline-controls"l>><"col-md-4 col-sm-12"<"inline-controls text-center"i>><"col-md-4 col-sm-12"p>>')
      .withLanguage({
        "sLengthMenu": 'View _MENU_ records',
        "sInfo":  'Found _TOTAL_ records',
        "oPaginate": {
          "sPage":    "Page",
          "sPageOf":  "of"
        }
      })
      .withPaginationType('input')
      //.withScroller()
      //.withOption("sScrollY", false)
      //.withOption("sScrollX")
      .withColumnFilter();


    vm.dtColumnDefs = [
      DTColumnDefBuilder.newColumnDef(0).notSortable(),
      DTColumnDefBuilder.newColumnDef(8).notSortable()
    ];

    vm.selectedAll = false;

    vm.selectAll = function () {

      if ($scope.selectedAll) {
        $scope.selectedAll = false;
      } else {
        $scope.selectedAll = true;
      }

      angular.forEach(vm.orders, function(order) {
        order.selected = $scope.selectedAll;
      });
    };

    $resource('http://www.filltext.com/?rows=300&id={index}&date={date|01-01-2012,01-01-2015}&placedby={firstName}~{lastName}&status=["pending","closed","sent","cancelled"]&quantity={number|20}&total={numberLength|3}}&shipto={streetAddress}~{city}&selected=false&pretty=true').query().$promise.then(function(orders) {
      vm.orders = orders;
    });

  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ShopOrdersCtrl
 * @description
 * # ShopOrdersCtrl
 * Controller of the minovateApp
 */
app
  .controller('ProductsCtrl', function ($scope) {
    $scope.page = {
      title: 'Products',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('ProductsTableCtrl', function ($scope, DTOptionsBuilder, DTColumnDefBuilder, DTColumnBuilder, $resource) {

    var vm = this;
    vm.products = [];
    vm.dtOptions = DTOptionsBuilder.newOptions()
      .withBootstrap()
      .withOption('order', [[1, 'asc']])
      .withDOM('<"row"<"col-md-8 col-sm-12"<"inline-controls"l>><"col-md-4 col-sm-12"<"pull-right"f>>>t<"row"<"col-md-4 col-sm-12"<"inline-controls"l>><"col-md-4 col-sm-12"<"inline-controls text-center"i>><"col-md-4 col-sm-12"p>>')
      .withLanguage({
        "sLengthMenu": 'View _MENU_ records',
        "sInfo":  'Found _TOTAL_ records',
        "oPaginate": {
          "sPage":    "Page",
          "sPageOf":  "of"
        }
      })
      .withPaginationType('input')
      //.withScroller()
      //.withOption("sScrollY", false)
      //.withOption("sScrollX")
      .withColumnFilter();


    vm.dtColumnDefs = [
      DTColumnDefBuilder.newColumnDef(0).notSortable(),
      DTColumnDefBuilder.newColumnDef(7).notSortable()
    ];

    vm.selectedAll = false;

    vm.selectAll = function () {

      if ($scope.selectedAll) {
        $scope.selectedAll = false;
      } else {
        $scope.selectedAll = true;
      }

      angular.forEach(vm.products, function(product) {
        product.selected = $scope.selectedAll;
      });
    };

    $resource('http://www.filltext.com/?rows=300&id={index}&name={lorem|2}&category=["Food","Drinks","Accesories","Electro","Kitchen","Bathroom"]&price={numberLength|3}}&date={date|01-01-2012,01-01-2015}&status=["published","not published","deleted"]&pretty=true').query().$promise.then(function(products) {
      vm.products = products;
    });

  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ShopOrdersCtrl
 * @description
 * # ShopOrdersCtrl
 * Controller of the minovateApp
 */
app
  .controller('InvoicesCtrl', function ($scope) {
    $scope.page = {
      title: 'Invoices',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('InvoicesTableCtrl', function ($scope, DTOptionsBuilder, DTColumnDefBuilder, DTColumnBuilder, $resource) {

    var vm = this;
    vm.invoices = [];
    vm.dtOptions = DTOptionsBuilder.newOptions()
      .withBootstrap()
      .withOption('order', [[1, 'asc']])
      .withDOM('<"row"<"col-md-8 col-sm-12"<"inline-controls"l>><"col-md-4 col-sm-12"<"pull-right"f>>>t<"row"<"col-md-4 col-sm-12"<"inline-controls"l>><"col-md-4 col-sm-12"<"inline-controls text-center"i>><"col-md-4 col-sm-12"p>>')
      .withLanguage({
        "sLengthMenu": 'View _MENU_ records',
        "sInfo":  'Found _TOTAL_ records',
        "oPaginate": {
          "sPage":    "Page",
          "sPageOf":  "of"
        }
      })
      .withPaginationType('input')
      //.withScroller()
      //.withOption("sScrollY", false)
      //.withOption("sScrollX")
      .withColumnFilter();


    vm.dtColumnDefs = [
      DTColumnDefBuilder.newColumnDef(0).notSortable(),
      DTColumnDefBuilder.newColumnDef(6).notSortable()
    ];

    vm.selectedAll = false;

    vm.selectAll = function () {

      if ($scope.selectedAll) {
        $scope.selectedAll = false;
      } else {
        $scope.selectedAll = true;
      }

      angular.forEach(vm.invoices, function(invoice) {
        invoice.selected = $scope.selectedAll;
      });
    };

    $resource('http://www.filltext.com/?rows=300&id={index}&date={date|01-01-2012,01-01-2015}&invoicefor={firstName}~{lastName}&status=["paid","unpaid","sent","cancelled"]&total={numberLength|3}}&selected=false&pretty=true').query().$promise.then(function(invoices) {
      vm.invoices = invoices;
    });

  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ShopSingleOrderCtrl
 * @description
 * # ShopSingleOrderCtrl
 * Controller of the minovateApp
 */
app
  .controller('SingleOrderCtrl', function ($scope) {
    $scope.page = {
      title: 'Single Order',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ShopSingleProductCtrl
 * @description
 * # ShopSingleProductCtrl
 * Controller of the minovateApp
 */
app
  .controller('SingleProductCtrl', function ($scope) {
    $scope.page = {
      title: 'Single Product',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:ShopSingleInvoiceCtrl
 * @description
 * # ShopSingleInvoiceCtrl
 * Controller of the minovateApp
 */
app
  .controller('SingleInvoiceCtrl', function ($scope) {
    $scope.page = {
      title: 'Single Invoice',
      subtitle: 'Place subtitle here...'
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:OffcanvaslayoutCtrl
 * @description
 * # OffcanvaslayoutCtrl
 * Controller of the minovateApp
 */
app
  .controller('OffcanvaslayoutCtrl', function ($scope) {
    $scope.page = {
      title: 'Off-canvas sidebar',
      subtitle: 'On small devices'
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:offcanvasSidebar
 * @description
 * # offcanvasSidebar
 */
app
  .directive('offcanvasSidebar', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element) {

        var app = angular.element('.appWrapper'),
            $window = angular.element(window),
            width = $window.width();

        element.on('click', function(e) {
          if (app.hasClass('offcanvas-opened')) {
            app.removeClass('offcanvas-opened');
          } else {
            app.addClass('offcanvas-opened');
          }
          e.preventDefault();
        });

      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:submitValidate
 * @description
 * # submitValidate
 */
app
  .directive('submitValidate', function () {
    return {
      require: 'form',
      restrict: 'A',
      link: function( scope , element , attributes ){
        var $element = angular.element(element);
        $element.on('submit', function(e) {
          $element.find('.ng-pristine').removeClass('ng-pristine').addClass('ng-dirty');
          var form = scope[ attributes.name ];
          angular.forEach( form , function ( formElement , fieldName ) {
            if ( fieldName[0] === '$' ) {return;}
            formElement.$pristine = false;
            formElement.$dirty = true;
          },this);
          form.$setDirty();
          scope.$apply();
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:nativeTab
 * @description
 * # nativeTab
 */
app
  .directive('nativeTab', function () {
    return {
      restrict: 'A',
      link: function( scope , element , attributes ){
        var $element = angular.element(element);
        $element.on('click', function(e) {
          e.preventDefault();
          $element.tab('show');
        });
      }
    };
  });

'use strict';

app
  .controller('LeafletMapCtrl', function ($scope) {

    $scope.page = {
      title: 'Leaflet Maps',
      subtitle: 'Place subtitle here...'
    };

  })

  .controller("leafletMap1", [ "$scope", function($scope) {

  }])

  .controller("leafletMap2", [ "$scope", function($scope) {
    angular.extend($scope, {
      autodiscover: {
        autoDiscover: true
      }
    });
  }])

  .controller("leafletMap3", [ "$scope", "leafletData", function($scope, leafletData) {

    L.Icon.Default.imagePath = 'styles/images';

    angular.extend($scope, {
      london: {
        lat: 51.505,
        lng: -0.09,
        zoom: 4
      },
      controls: {
        draw: {}
      },
      layers: {
        baselayers: {
          mapbox_light: {
            name: 'Mapbox Light',
            url: 'http://api.tiles.mapbox.com/v4/{mapid}/{z}/{x}/{y}.png?access_token={apikey}',
            type: 'xyz',
            layerOptions: {
              apikey: 'pk.eyJ1IjoiYnVmYW51dm9scyIsImEiOiJLSURpX0pnIn0.2_9NrLz1U9bpwMQBhVk97Q',
              mapid: 'bufanuvols.lia22g09'
            },
            layerParams: {
              showOnSelector: false
            }
          }
        },
        overlays: {
          draw: {
            name: 'draw',
            type: 'group',
            visible: true,
            layerParams: {
              showOnSelector: false
            }
          }
        }
      }
    });


    leafletData.getMap("map3").then(function(map) {
      leafletData.getLayers("map3").then(function(baselayers) {
        var drawnItems = baselayers.overlays.draw;
        map.on('draw:created', function (e) {
          var layer = e.layer;
          drawnItems.addLayer(layer);
          console.log(JSON.stringify(layer.toGeoJSON()));
        });
      });
    });
  }])

  .controller("leafletMap4", [ "$scope", function($scope) {

    angular.extend($scope, {
      berlin: {
        lat: 52.52,
        lng: 13.40,
        zoom: 14
      },
      markers: {
        m1: {
          lat: 52.52,
          lng: 13.40
        }
      },
      layers: {
        baselayers: {
          googleTerrain: {
            name: 'Google Terrain',
            layerType: 'TERRAIN',
            type: 'google'
          },
          googleHybrid: {
            name: 'Google Hybrid',
            layerType: 'HYBRID',
            type: 'google'
          },
          googleRoadmap: {
            name: 'Google Streets',
            layerType: 'ROADMAP',
            type: 'google'
          }
        }
      }
    });

  }])

  .controller("leafletMap5", ["$scope", "$http", function($scope, $http) {

    var points = [];
    var heatmap = {
      name: 'Heat Map',
      type: 'heat',
      data: points,
      visible: true
    };

    $http.get("scripts/jsons/heat-points.json").success(function(data) {
      $scope.layers.overlays = {
        heat: {
          name: 'Heat Map',
          type: 'heat',
          data: data,
          layerOptions: {
            radius: 20,
            blur: 10
          },
          visible: true
        }
      };
    });

    angular.extend($scope, {
      center: {
        lat: 37.774546,
        lng: -122.433523,
        zoom: 12
      },
      layers: {
        baselayers: {
          mapbox_light: {
            name: 'Mapbox Light',
            url: 'http://api.tiles.mapbox.com/v4/{mapid}/{z}/{x}/{y}.png?access_token={apikey}',
            type: 'xyz',
            layerOptions: {
              apikey: 'pk.eyJ1IjoiYnVmYW51dm9scyIsImEiOiJLSURpX0pnIn0.2_9NrLz1U9bpwMQBhVk97Q',
              mapid: 'bufanuvols.lia22g09'
            }
          }
        }
      }
    });
  }]);

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:UimasonryCtrl
 * @description
 * # UiMasonryCtrl
 * Controller of the minovateApp
 */
app
  .controller('UiMasonryCtrl', function ($scope, ipsumService) {

    /*jshint bitwise:false */

    $scope.page = {
      title: 'Masonry',
      subtitle: 'Place subtitle here...'
    };

    function genBrick() {
      var height = ~~(Math.random() * 500) + 100;
      var id = ~~(Math.random() * 10000);
      return {
        src: 'http://lorempixel.com/g/720/' + height + '/?' + id,
        title: ipsumService.randomMi()+ipsumService.words(1)+' '+ipsumService.randomMi()+ipsumService.words(1),
        content: ipsumService.sentences(2)
      };
    }

    $scope.bricks = [
      genBrick(),
      genBrick(),
      genBrick(),
      genBrick(),
      genBrick(),
      genBrick(),
      genBrick(),
      genBrick(),
      genBrick(),
      genBrick()
    ];
  });

'use strict';

app
  .controller('IntroPageCtrl', function ($scope) {
    $scope.page = {
      title: 'Intro page',
      subtitle: 'Place subtitle here...'
    };

    $scope.IntroOptions = {
      overlayOpacity: 0.8,
      showBullets: false,
      showStepNumbers: false,
      nextLabel: 'Next <i class="fa fa-chevron-right"></i>',
      prevLabel: '<i class="fa fa-chevron-left"></i> Previous',
      skipLabel: '<i class="zmdi zmdi-close"></i>',
      doneLabel: '<i class="zmdi zmdi-close"></i>',
      steps: [
        {
          element: 'header .branding',
          intro: '<h2 class="header">Branding Section</h2> <p>You can place your logo here.</p>',
          position: 'right'
        },
        {
          element: 'header .sidebar-collapse',
          intro: '<h2 class="header">Sidebar Offset</h2> <p>You can change sidebar width by clicking on this element.</p>'
        },
        {
          element: 'header .settings',
          intro: '<h2 class="header">Template Settings</h2> <p>You can change various things from this dropdown like colors and element visibility options.</p>'
        },
        {
          element: '#main-search',
          intro: '<h2 class="header">Main Search Field</h2> <p>You can assign you search engine function to this element.</p>'
        },
        {
          element: 'header .dropdown.users',
          intro: '<h2 class="header">User Requests</h2> <p>Just some dummy data here.</p>'
        },
        {
          element: 'header .dropdown.messages',
          intro: '<h2 class="header">Received Messages</h2> <p>Just some dummy data here again.</p>'
        },
        {
          element: 'header .dropdown.notifications',
          intro: '<h2 class="header">Notifications</h2> <p>Just some dummy data here again.</p>'
        },
        {
          element: 'header .dropdown.nav-profile',
          intro: '<h2 class="header">Your Profile</h2> <p>Dropdown for user actions and subpages.</p>'
        },
        {
          element: 'header .dropdown.language',
          intro: '<h2 class="header">Language Change</h2> <p>You can change current template language. Only for example purposes, just sidebar menu elements translated.</p>',
          position: 'left'
        },
        {
          element: '.toggle-right-sidebar',
          intro: '<h2 class="header">Right Sidebar Toggle</h2> <p>This one toggle on/off right sidebar.</p>',
          position: 'left'
        },
        {
          element: '#sidebar',
          intro: '<h2 class="header">Sidebar</h2> <p>You can find menu element at this section.</p>',
          position: 'right'
        }
      ]
    };
  });

'use strict';

app
  .controller('UiDragulaCtrl', function ($scope, dragularService, ipsumService) {


    $scope.page = {
      title: 'Dragula',
      subtitle: 'Place subtitle here...'
    };

    $scope.tasks = {
      new: [{
        name: ipsumService.words(2),
        created: '26.09.2015',
        due_date: '29.10.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-high',
          value: 'High priority'
        }
      },
      {
        name: ipsumService.words(2),
        created: '18.05.2015',
        due_date: '30.11.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-high',
          value: 'High priority'
        }
      },
      {
        name: ipsumService.words(2),
        created: '13.08.2015',
        due_date: '15.12.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-low',
          value: 'Low priority'
        }
      },
      {
        name: ipsumService.words(2),
        created: '12.10.2015',
        due_date: '24.12.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-normal',
          value: 'Normal priority'
        }
      }],
      progress: [{
        name: ipsumService.words(2),
        created: '13.08.2015',
        due_date: '16.10.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-medium',
          value: 'Medium priority'
        }
      },
      {
        name: ipsumService.words(2),
        created: '05.06.2015',
        due_date: '26.11.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-low',
          value: 'Low priority'
        }
      }],
      finished: [{
        name: ipsumService.words(2),
        created: '06.09.2015',
        due_date: '13.10.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-normal',
          value: 'Normal priority'
        }
      },
      {
        name: ipsumService.words(2),
        created: '09.05.2015',
        due_date: '08.09.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-high',
          value: 'High priority'
        }
      },
      {
        name: ipsumService.words(2),
        created: '12.08.2015',
        due_date: '16.08.2015',
        dsc: ipsumService.sentences(3),
        priority: {
          key: 'priority-medium',
          value: 'Medium priority'
        }
      }]
    };

    $scope.dragularOptions = {
      classes: {
        mirror: 'drag-task'
      },
      nameSpace: 'common'
    };

  });

'use strict';

app

  .controller('mtAutocompleteCtrl', function ($scope) {
    $scope.page = {
      title: 'Autocomplete',
      subtitle: 'Place subtitle here...'
    };
  })

  .controller('mtBasicAutocompleteCtrl', function ($timeout, $q, $log) {
    var self = this;
    self.simulateQuery = false;
    self.isDisabled    = false;
    // list of `state` value/display objects
    self.states        = loadAll();
    self.querySearch   = querySearch;
    self.selectedItemChange = selectedItemChange;
    self.searchTextChange   = searchTextChange;
    // ******************************
    // Internal methods
    // ******************************
    /**
     * Search for states... use $timeout to simulate
     * remote dataservice call.
     */
    function querySearch (query) {
      var results = query ? self.states.filter( createFilterFor(query) ) : self.states,
        deferred;
      if (self.simulateQuery) {
        deferred = $q.defer();
        $timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
        return deferred.promise;
      } else {
        return results;
      }
    }
    function searchTextChange(text) {
      $log.info('Text changed to ' + text);
    }
    function selectedItemChange(item) {
      $log.info('Item changed to ' + JSON.stringify(item));
    }
    /**
     * Build `states` list of key/value pairs
     */
    function loadAll() {
      var allStates = 'Alabama, Alaska, Arizona, Arkansas, California, Colorado, Connecticut, Delaware,\
                Florida, Georgia, Hawaii, Idaho, Illinois, Indiana, Iowa, Kansas, Kentucky, Louisiana,\
                Maine, Maryland, Massachusetts, Michigan, Minnesota, Mississippi, Missouri, Montana,\
                Nebraska, Nevada, New Hampshire, New Jersey, New Mexico, New York, North Carolina,\
                North Dakota, Ohio, Oklahoma, Oregon, Pennsylvania, Rhode Island, South Carolina,\
                South Dakota, Tennessee, Texas, Utah, Vermont, Virginia, Washington, West Virginia,\
                Wisconsin, Wyoming';
      return allStates.split(/, +/g).map( function (state) {
        return {
          value: state.toLowerCase(),
          display: state
        };
      });
    }
    /**
     * Create filter function for a query string
     */
    function createFilterFor(query) {
      var lowercaseQuery = angular.lowercase(query);
      return function filterFn(state) {
        return (state.value.indexOf(lowercaseQuery) === 0);
      };
    }
  })

  .controller('mtCustomAutocompleteCtrl', function ($timeout, $q, $log) {
    var self = this;
    self.simulateQuery = false;
    self.isDisabled    = false;
    self.repos         = loadAll();
    self.querySearch   = querySearch;
    self.selectedItemChange = selectedItemChange;
    self.searchTextChange   = searchTextChange;
    // ******************************
    // Internal methods
    // ******************************
    /**
     * Search for repos... use $timeout to simulate
     * remote dataservice call.
     */
    function querySearch (query) {
      var results = query ? self.repos.filter( createFilterFor(query) ) : self.repos,
          deferred;
      if (self.simulateQuery) {
        deferred = $q.defer();
        $timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
        return deferred.promise;
      } else {
        return results;
      }
    }
    function searchTextChange(text) {
      $log.info('Text changed to ' + text);
    }
    function selectedItemChange(item) {
      $log.info('Item changed to ' + JSON.stringify(item));
    }
    /**
     * Build `components` list of key/value pairs
     */
    function loadAll() {
      var repos = [
        {
          'name'      : 'Angular 1',
          'url'       : 'https://github.com/angular/angular.js',
          'watchers'  : '3,623',
          'forks'     : '16,175'
        },
        {
          'name'      : 'Angular 2',
          'url'       : 'https://github.com/angular/angular',
          'watchers'  : '469',
          'forks'     : '760'
        },
        {
          'name'      : 'Angular Material',
          'url'       : 'https://github.com/angular/material',
          'watchers'  : '727',
          'forks'     : '1,241'
        },
        {
          'name'      : 'Bower Material',
          'url'       : 'https://github.com/angular/bower-material',
          'watchers'  : '42',
          'forks'     : '84'
        },
        {
          'name'      : 'Material Start',
          'url'       : 'https://github.com/angular/material-start',
          'watchers'  : '81',
          'forks'     : '303'
        }
      ];
      return repos.map( function (repo) {
        repo.value = repo.name.toLowerCase();
        return repo;
      });
    }
    /**
     * Create filter function for a query string
     */
    function createFilterFor(query) {
      var lowercaseQuery = angular.lowercase(query);
      return function filterFn(item) {
        return (item.value.indexOf(lowercaseQuery) === 0);
      };
    }
  })

  .controller('mtFloatingAutocompleteCtrl', function ($timeout, $q) {
    var self = this;
    // list of `state` value/display objects
    self.states        = loadAll();
    self.selectedItem  = null;
    self.searchText    = null;
    self.querySearch   = querySearch;
    // ******************************
    // Internal methods
    // ******************************
    /**
     * Search for states... use $timeout to simulate
     * remote dataservice call.
     */
    function querySearch (query) {
      var results = query ? self.states.filter( createFilterFor(query) ) : [];
      return results;
    }
    /**
     * Build `states` list of key/value pairs
     */
    function loadAll() {
      var allStates = 'Alabama, Alaska, Arizona, Arkansas, California, Colorado, Connecticut, Delaware,\
                Florida, Georgia, Hawaii, Idaho, Illinois, Indiana, Iowa, Kansas, Kentucky, Louisiana,\
                Maine, Maryland, Massachusetts, Michigan, Minnesota, Mississippi, Missouri, Montana,\
                Nebraska, Nevada, New Hampshire, New Jersey, New Mexico, New York, North Carolina,\
                North Dakota, Ohio, Oklahoma, Oregon, Pennsylvania, Rhode Island, South Carolina,\
                South Dakota, Tennessee, Texas, Utah, Vermont, Virginia, Washington, West Virginia,\
                Wisconsin, Wyoming';
      return allStates.split(/, +/g).map( function (state) {
        return {
          value: state.toLowerCase(),
          display: state
        };
      });
    }
    /**
     * Create filter function for a query string
     */
    function createFilterFor(query) {
      var lowercaseQuery = angular.lowercase(query);
      return function filterFn(state) {
        return (state.value.indexOf(lowercaseQuery) === 0);
      };
    }
  });





'use strict';

app

  .controller('mtBottomSheetCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Bottom Sheet',
      subtitle: 'Place subtitle here...'
    };

    $scope.alert = '';
    $scope.showListBottomSheet = function($event) {
      $scope.alert = '';
      $mdBottomSheet.show({
        parent: angular.element(document.getElementById('bottomSheetContainer')),
        templateUrl: 'bottom-sheet-list-template.html',
        controller: 'mtListBottomSheetCtrl',
        targetEvent: $event
      }).then(function(clickedItem) {
        $scope.alert = clickedItem.name + ' clicked!';
      });
    };
    $scope.showGridBottomSheet = function($event) {
      $scope.alert = '';
      $mdBottomSheet.show({
        parent: angular.element(document.getElementById('bottomSheetContainer')),
        templateUrl: 'bottom-sheet-grid-template.html',
        controller: 'mtGridBottomSheetCtrl',
        targetEvent: $event
      }).then(function(clickedItem) {
        $scope.alert = clickedItem.name + ' clicked!';
      });
    };
  })

  .controller('mtListBottomSheetCtrl', function($scope, $mdBottomSheet) {
    $scope.items = [
      { name: 'Share', icon: 'zmdi zmdi-share' },
      { name: 'Upload', icon: 'zmdi zmdi-upload' },
      { name: 'Copy', icon: 'zmdi zmdi-copy' },
      { name: 'Print this page', icon: 'zmdi zmdi-print' }
    ];
    $scope.listItemClick = function($index) {
      var clickedItem = $scope.items[$index];
      $mdBottomSheet.hide(clickedItem);
    };
  })

  .controller('mtGridBottomSheetCtrl', function($scope, $mdBottomSheet) {
    $scope.items = [
      { name: 'Google+', icon: 'zmdi zmdi-google-plus' },
      { name: 'Mail', icon: 'zmdi zmdi-email' },
      { name: 'Message', icon: 'zmdi zmdi-comment-text' },
      { name: 'Copy', icon: 'zmdi zmdi-copy' },
      { name: 'Facebook', icon: 'zmdi zmdi-facebook-box' },
      { name: 'Twitter', icon: 'zmdi zmdi-twitter-box' }
    ];
    $scope.listItemClick = function($index) {
      var clickedItem = $scope.items[$index];
      $mdBottomSheet.hide(clickedItem);
    };
  });





'use strict';

app

  .controller('mtButtonsCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Buttons',
      subtitle: 'Place subtitle here...'
    };

    $scope.title1 = 'Button';
    $scope.title4 = 'Warn';
    $scope.isDisabled = true;
    $scope.googleUrl = 'http://google.com';

  });





'use strict';

app

  .controller('mtCardsCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Cards',
      subtitle: 'Place subtitle here...'
    };

    $scope.imagePath = 'http://placekitten.com/g/800/300';

  });





'use strict';

app

  .controller('mtCheckboxCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Checkbox',
      subtitle: 'Place subtitle here...'
    };

    $scope.data = {};
    $scope.data.cb1 = true;
    $scope.data.cb2 = false;
    $scope.data.cb3 = false;
    $scope.data.cb4 = false;
    $scope.data.cb5 = false;

    $scope.items = [1,2,3,4,5];
    $scope.selected = [];
    $scope.toggle = function (item, list) {
      var idx = list.indexOf(item);
      if (idx > -1) list.splice(idx, 1);
      else list.push(item);
    };
    $scope.exists = function (item, list) {
      return list.indexOf(item) > -1;
    };

  });





'use strict';

app

  .controller('mtChipsCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Chips',
      subtitle: 'Place subtitle here...'
    };

  })

  .controller('mtChipsBasicDemoCtrl', mtChipsBasicDemoCtrl)
  .controller('mtChipsContactChipDemoCtrl', mtChipsContactChipDemoCtrl)
  .controller('mtChipsCustomInputDemoCtrl', mtChipsCustomInputDemoCtrl)
  .controller('mtChipsStaticDemoCtrl', mtChipsStaticDemoCtrl);

function mtChipsBasicDemoCtrl ($timeout, $q) {
  var self = this;
  self.readonly = false;
  // Lists of fruit names and Vegetable objects
  self.fruitNames = ['Apple', 'Banana', 'Orange'];
  self.roFruitNames = angular.copy(self.fruitNames);
  self.tags = [];
  self.vegObjs = [
    {
      'name' : 'Broccoli',
      'type' : 'Brassica'
    },
    {
      'name' : 'Cabbage',
      'type' : 'Brassica'
    },
    {
      'name' : 'Carrot',
      'type' : 'Umbelliferous'
    }
  ];
  self.newVeg = function(chip) {
    return {
      name: chip,
      type: 'unknown'
    };
  };
}

function mtChipsContactChipDemoCtrl ($timeout, $q) {
  var self = this;
  self.querySearch = querySearch;
  self.allContacts = loadContacts();
  self.contacts = [self.allContacts[0]];
  self.filterSelected = true;
  /**
   * Search for contacts.
   */
  function querySearch (query) {
    var results = query ?
      self.allContacts.filter(createFilterFor(query)) : [];
    return results;
  }
  /**
   * Create filter function for a query string
   */
  function createFilterFor(query) {
    var lowercaseQuery = angular.lowercase(query);
    return function filterFn(contact) {
      return (contact._lowername.indexOf(lowercaseQuery) != -1);;
    };
  }
  function loadContacts() {
    var contacts = [
      'Marina Augustine',
      'Oddr Sarno',
      'Nick Giannopoulos',
      'Narayana Garner',
      'Anita Gros',
      'Megan Smith',
      'Tsvetko Metzger',
      'Hector Simek',
      'Some-guy withalongalastaname'
    ];
    return contacts.map(function (c, index) {
      var cParts = c.split(' ');
      var contact = {
        name: c,
        email: cParts[0][0].toLowerCase() + '.' + cParts[1].toLowerCase() + '@example.com',
        image: 'http://lorempixel.com/50/50/people?' + index
      };
      contact._lowername = contact.name.toLowerCase();
      return contact;
    });
  }
}

function mtChipsCustomInputDemoCtrl ($timeout, $q) {
  var self = this;
  self.readonly = false;
  self.selectedItem = null;
  self.searchText = null;
  self.querySearch = querySearch;
  self.vegetables = loadVegetables();
  self.selectedVegetables = [];
  self.numberChips = [];
  self.numberChips2 = [];
  self.numberBuffer = '';
  /**
   * Search for vegetables.
   */
  function querySearch (query) {
    var results = query ? self.vegetables.filter(createFilterFor(query)) : [];
    return results;
  }
  /**
   * Create filter function for a query string
   */
  function createFilterFor(query) {
    var lowercaseQuery = angular.lowercase(query);
    return function filterFn(vegetable) {
      return (vegetable._lowername.indexOf(lowercaseQuery) === 0) ||
        (vegetable._lowertype.indexOf(lowercaseQuery) === 0);
    };
  }
  function loadVegetables() {
    var veggies = [
      {
        'name': 'Broccoli',
        'type': 'Brassica'
      },
      {
        'name': 'Cabbage',
        'type': 'Brassica'
      },
      {
        'name': 'Carrot',
        'type': 'Umbelliferous'
      },
      {
        'name': 'Lettuce',
        'type': 'Composite'
      },
      {
        'name': 'Spinach',
        'type': 'Goosefoot'
      }
    ];
    return veggies.map(function (veg) {
      veg._lowername = veg.name.toLowerCase();
      veg._lowertype = veg.type.toLowerCase();
      return veg;
    });
  }
}

function mtChipsStaticDemoCtrl ($timeout, $q) {
  this.chipText = 'Football';
}





'use strict';

app

  .controller('mtContentCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Content',
      subtitle: 'Place subtitle here...'
    };

  });





'use strict';

app

  .controller('mtDialogCtrl', function($scope, $timeout, $mdDialog) {

    $scope.page = {
      title: 'Dialog',
      subtitle: 'Place subtitle here...'
    };

    $scope.status = '  ';
    $scope.showAlert = function(ev) {
      // Appending dialog to document.body to cover sidenav in docs app
      // Modal dialogs should fully cover application
      // to prevent interaction outside of dialog
      $mdDialog.show(
        $mdDialog.alert()
          .parent(angular.element(document.querySelector('#popupContainer')))
          .clickOutsideToClose(true)
          .title('This is an alert title')
          .content('You can specify some description text in here.')
          .ariaLabel('Alert Dialog Demo')
          .ok('Got it!')
          .targetEvent(ev)
      );
    };
    $scope.showConfirm = function(ev) {
      // Appending dialog to document.body to cover sidenav in docs app
      var confirm = $mdDialog.confirm()
        .parent(angular.element(document.querySelector('#popupContainer')))
        .title('Would you like to delete your debt?')
        .content('All of the banks have agreed to forgive you your debts.')
        .ariaLabel('Lucky day')
        .ok('Please do it!')
        .cancel('Sounds like a scam')
        .targetEvent(ev);
      $mdDialog.show(confirm).then(function() {
        $scope.status = 'You decided to get rid of your debt.';
      }, function() {
        $scope.status = 'You decided to keep your debt.';
      });
    };
    $scope.showAdvanced = function(ev) {
      $mdDialog.show({
        controller: DialogController,
        templateUrl: 'dialog1.tmpl.html',
        parent: angular.element(document.body),
        targetEvent: ev,
        clickOutsideToClose:true
      })
        .then(function(answer) {
          $scope.status = 'You said the information was "' + answer + '".';
        }, function() {
          $scope.status = 'You cancelled the dialog.';
        });
    };

  });

function DialogController($scope, $mdDialog) {
  $scope.hide = function() {
    $mdDialog.hide();
  };
  $scope.cancel = function() {
    $mdDialog.cancel();
  };
  $scope.answer = function(answer) {
    $mdDialog.hide(answer);
  };
}





'use strict';

app

  .controller('mtDividerCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Divider',
      subtitle: 'Place subtitle here...'
    };

    var imagePath = 'http://placekitten.com/g/80/80';
    $scope.messages = [{
      face : imagePath,
      what: 'Brunch this weekend?',
      who: 'Min Li Chan',
      when: '3:08PM',
      notes: " I'll be in your neighborhood doing errands"
    }, {
      face : imagePath,
      what: 'Brunch this weekend?',
      who: 'Min Li Chan',
      when: '3:08PM',
      notes: " I'll be in your neighborhood doing errands"
    }, {
      face : imagePath,
      what: 'Brunch this weekend?',
      who: 'Min Li Chan',
      when: '3:08PM',
      notes: " I'll be in your neighborhood doing errands"
    }, {
      face : imagePath,
      what: 'Brunch this weekend?',
      who: 'Min Li Chan',
      when: '3:08PM',
      notes: " I'll be in your neighborhood doing errands"
    }, {
      face : imagePath,
      what: 'Brunch this weekend?',
      who: 'Min Li Chan',
      when: '3:08PM',
      notes: " I'll be in your neighborhood doing errands"
    }];

  });





'use strict';

app

  .controller('mtFabSpeedDialCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Fab Speed Dial',
      subtitle: 'Place subtitle here...'
    };

  })

  .controller('mtFabSpeedDialBasicCtrl', function() {
    this.topDirections = ['left', 'up'];
    this.bottomDirections = ['down', 'right'];
    this.isOpen = false;
    this.availableModes = ['md-fling', 'md-scale'];
    this.selectedMode = 'md-fling';
    this.availableDirections = ['up', 'down', 'left', 'right'];
    this.selectedDirection = 'up';
  })

  .controller('mtFabSpeedDialMoreCtrl', function($mdDialog) {
    var self = this;
    self.hidden = false;
    self.items = [
      {name: "Twitter", icon: "zmdi zmdi-twitter-box", direction: "left" },
      {name: "Facebook", icon: "zmdi zmdi-facebook-box", direction: "right" },
      {name: "Google Hangout", icon: "zmdi zmdi-google-plus-box", direction: "left" }
    ];
    self.openDialog = function($event, item) {
      // Show the dialog
      $mdDialog.show({
        clickOutsideToClose: true,
        controller: function($mdDialog) {
          // Save the clicked item
          this.item = item;
          // Setup some handlers
          this.close = function() {
            $mdDialog.cancel();
          };
          this.submit = function() {
            $mdDialog.hide();
          };
        },
        controllerAs: 'dialog',
        templateUrl: 'dialog.html',
        targetEvent: $event
      });
    }
  });





'use strict';

app

  .controller('mtFabToolbarCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Fab Toolbar',
      subtitle: 'Place subtitle here...'
    };

    $scope.isOpen = false;
    $scope.demo = {
      isOpen: false,
      count: 0,
      selectedDirection: 'left'
    };

  });





'use strict';

app

  .controller('mtGridListCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Grid List',
      subtitle: 'Place subtitle here...'
    };

  })

  .controller('gridListDemoCtrl', function($scope) {
    this.tiles = buildGridModel({
      icon : "avatar:svg-",
      title: "Svg-",
      background: ""
    });
    function buildGridModel(tileTmpl){
      var it, results = [ ];
      for (var j=0; j<11; j++) {
        it = angular.extend({},tileTmpl);
        it.icon  = it.icon + (j+1);
        it.title = it.title + (j+1);
        it.span  = { row : 1, col : 1 };
        switch(j+1) {
          case 1:
            it.background = "red";
            it.span.row = it.span.col = 2;
            break;
          case 2: it.background = "green";         break;
          case 3: it.background = "darkBlue";      break;
          case 4:
            it.background = "blue";
            it.span.col = 2;
            break;
          case 5:
            it.background = "yellow";
            it.span.row = it.span.col = 2;
            break;
          case 6: it.background = "pink";          break;
          case 7: it.background = "darkBlue";      break;
          case 8: it.background = "purple";        break;
          case 9: it.background = "deepBlue";      break;
          case 10: it.background = "lightPurple";  break;
          case 11: it.background = "yellow";       break;
        }
        results.push(it);
      }
      return results;
    }
  })

  .controller('gridListResponsiveDemoCtrl', function($scope) {
    var COLORS = ['#ffebee', '#ffcdd2', '#ef9a9a', '#e57373', '#ef5350', '#f44336', '#e53935', '#d32f2f', '#c62828', '#b71c1c', '#ff8a80', '#ff5252', '#ff1744', '#d50000', '#f8bbd0', '#f48fb1', '#f06292', '#ec407a', '#e91e63', '#d81b60', '#c2185b', '#ad1457', '#880e4f', '#ff80ab', '#ff4081', '#f50057', '#c51162', '#e1bee7', '#ce93d8', '#ba68c8', '#ab47bc', '#9c27b0', '#8e24aa', '#7b1fa2', '#4a148c', '#ea80fc', '#e040fb', '#d500f9', '#aa00ff', '#ede7f6', '#d1c4e9', '#b39ddb', '#9575cd', '#7e57c2', '#673ab7', '#5e35b1', '#4527a0', '#311b92', '#b388ff', '#7c4dff', '#651fff', '#6200ea', '#c5cae9', '#9fa8da', '#7986cb', '#5c6bc0', '#3f51b5', '#3949ab', '#303f9f', '#283593', '#1a237e', '#8c9eff', '#536dfe', '#3d5afe', '#304ffe', '#e3f2fd', '#bbdefb', '#90caf9', '#64b5f6', '#42a5f5', '#2196f3', '#1e88e5', '#1976d2', '#1565c0', '#0d47a1', '#82b1ff', '#448aff', '#2979ff', '#2962ff', '#b3e5fc', '#81d4fa', '#4fc3f7', '#29b6f6', '#03a9f4', '#039be5', '#0288d1', '#0277bd', '#01579b', '#80d8ff', '#40c4ff', '#00b0ff', '#0091ea', '#e0f7fa', '#b2ebf2', '#80deea', '#4dd0e1', '#26c6da', '#00bcd4', '#00acc1', '#0097a7', '#00838f', '#006064', '#84ffff', '#18ffff', '#00e5ff', '#00b8d4', '#e0f2f1', '#b2dfdb', '#80cbc4', '#4db6ac', '#26a69a', '#009688', '#00897b', '#00796b', '#00695c', '#a7ffeb', '#64ffda', '#1de9b6', '#00bfa5', '#e8f5e9', '#c8e6c9', '#a5d6a7', '#81c784', '#66bb6a', '#4caf50', '#43a047', '#388e3c', '#2e7d32', '#1b5e20', '#b9f6ca', '#69f0ae', '#00e676', '#00c853', '#f1f8e9', '#dcedc8', '#c5e1a5', '#aed581', '#9ccc65', '#8bc34a', '#7cb342', '#689f38', '#558b2f', '#33691e', '#ccff90', '#b2ff59', '#76ff03', '#64dd17', '#f9fbe7', '#f0f4c3', '#e6ee9c', '#dce775', '#d4e157', '#cddc39', '#c0ca33', '#afb42b', '#9e9d24', '#827717', '#f4ff81', '#eeff41', '#c6ff00', '#aeea00', '#fffde7', '#fff9c4', '#fff59d', '#fff176', '#ffee58', '#ffeb3b', '#fdd835', '#fbc02d', '#f9a825', '#f57f17', '#ffff8d', '#ffff00', '#ffea00', '#ffd600', '#fff8e1', '#ffecb3', '#ffe082', '#ffd54f', '#ffca28', '#ffc107', '#ffb300', '#ffa000', '#ff8f00', '#ff6f00', '#ffe57f', '#ffd740', '#ffc400', '#ffab00', '#fff3e0', '#ffe0b2', '#ffcc80', '#ffb74d', '#ffa726', '#ff9800', '#fb8c00', '#f57c00', '#ef6c00', '#e65100', '#ffd180', '#ffab40', '#ff9100', '#ff6d00', '#fbe9e7', '#ffccbc', '#ffab91', '#ff8a65', '#ff7043', '#ff5722', '#f4511e', '#e64a19', '#d84315', '#bf360c', '#ff9e80', '#ff6e40', '#ff3d00', '#dd2c00', '#d7ccc8', '#bcaaa4', '#795548', '#d7ccc8', '#bcaaa4', '#8d6e63', '#eceff1', '#cfd8dc', '#b0bec5', '#90a4ae', '#78909c', '#607d8b', '#546e7a', '#cfd8dc', '#b0bec5', '#78909c'];
    this.colorTiles = (function() {
      var tiles = [];
      for (var i = 0; i < 46; i++) {
        tiles.push({
          color: randomColor(),
          colspan: randomSpan(),
          rowspan: randomSpan()
        });
      }
      return tiles;
    })();
    function randomColor() {
      return COLORS[Math.floor(Math.random() * COLORS.length)];
    }
    function randomSpan() {
      var r = Math.random();
      if (r < 0.8) {
        return 1;
      } else if (r < 0.9) {
        return 2;
      } else {
        return 3;
      }
    }
  })

  .config( function( $mdIconProvider ){
    $mdIconProvider.iconSet("avatar", 'images/avatar-icons.svg', 128);
  });





'use strict';

app

  .controller('mtInputsCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Inputs',
      subtitle: 'Place subtitle here...'
    };

    $scope.user = {
      title: 'Developer',
      email: 'ipsum@lorem.com',
      firstName: '',
      lastName: '' ,
      company: 'Google' ,
      address: '1600 Amphitheatre Pkwy' ,
      city: 'Mountain View' ,
      state: 'CA' ,
      biography: 'Loves kittens, snowboarding, and can type at 130 WPM.\n\nAnd rumor has it she bouldered up Castle Craig!',
      postalCode : '94043'
    };

    $scope.project = {
      description: 'Nuclear Missile Defense System',
      rate: 500
    };

    $scope.user2 = {
      name: 'John Doe',
      email: '',
      phone: '',
      address: 'Mountain View, CA'
    };

  })

  .config( function($mdThemingProvider){
    // Configure a dark theme with primary foreground yellow
    $mdThemingProvider.theme('docs-dark', 'default')
      .primaryPalette('yellow')
      .dark();
  });





'use strict';

app

  .controller('mtListCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'List',
      subtitle: 'Place subtitle here...'
    };

  })

  .controller('mtListBasicCtrl', function($scope, $timeout, $mdBottomSheet) {

    var imagePath = 'http://placekitten.com/g/600/600';
    $scope.phones = [
      { type: 'Home', number: '(555) 251-1234' },
      { type: 'Cell', number: '(555) 786-9841' }
    ];
    $scope.todos = [
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      }
    ];

  })

  .controller('mtListControlsCtrl', function($scope, $timeout, $mdDialog) {

    $scope.toppings = [
      { name: 'Pepperoni', wanted: true },
      { name: 'Sausage', wanted: false },
      { name: 'Black Olives', wanted: true },
      { name: 'Green Peppers', wanted: false }
    ];
    $scope.settings = [
      { name: 'Wi-Fi', extraScreen: 'Wi-fi menu', icon: 'zmdi zmdi-wifi-alt-2', enabled: true },
      { name: 'Bluetooth', extraScreen: 'Bluetooth menu', icon: 'zmdi zmdi-bluetooth', enabled: false }
    ];
    $scope.messages = [
      {id: 1, title: "Message A", selected: false},
      {id: 2, title: "Message B", selected: true},
      {id: 3, title: "Message C", selected: true}
    ];
    $scope.people = [
      { name: 'Janet Perkins', img: 'http://placekitten.com/g/600/600', newMessage: true },
      { name: 'Mary Johnson', img: 'http://placekitten.com/g/600/601', newMessage: false },
      { name: 'Peter Carlsson', img: 'http://placekitten.com/g/600/602', newMessage: false }
    ];
    $scope.goToPerson = function(person, event) {
      $mdDialog.show(
        $mdDialog.alert()
          .title('Navigating')
          .content('Inspect ' + person)
          .ariaLabel('Person inspect demo')
          .ok('Neat!')
          .targetEvent(event)
      );
    };
    $scope.navigateTo = function(to, event) {
      $mdDialog.show(
        $mdDialog.alert()
          .title('Navigating')
          .content('Imagine being taken to ' + to)
          .ariaLabel('Navigation demo')
          .ok('Neat!')
          .targetEvent(event)
      );
    };
    $scope.doSecondaryAction = function(event) {
      $mdDialog.show(
        $mdDialog.alert()
          .title('Secondary Action')
          .content('Secondary actions can be used for one click actions')
          .ariaLabel('Secondary click demo')
          .ok('Neat!')
          .targetEvent(event)
      );
    };

  });





'use strict';

app

  .controller('mtMenuCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Menu',
      subtitle: 'Place subtitle here...'
    };

  })

  .controller('mtMenuBasicCtrl', function mtMenuBasicCtrl($mdDialog) {
    var originatorEv;
    this.openMenu = function($mdOpenMenu, ev) {
      originatorEv = ev;
      $mdOpenMenu(ev);
    };
    this.notificationsEnabled = true;
    this.toggleNotifications = function() {
      this.notificationsEnabled = !this.notificationsEnabled;
    };
    this.redial = function() {
      $mdDialog.show(
        $mdDialog.alert()
          .targetEvent(originatorEv)
          .clickOutsideToClose(true)
          .parent('body')
          .title('Suddenly, a redial')
          .content('You just called a friend; who told you the most amazing story. Have a cookie!')
          .ok('That was easy')
      );
      originatorEv = null;
    };
    this.checkVoicemail = function() {
      // This never happens.
    };
  })

  .controller('mtMenuPositionsCtrl', function mtMenuBasicCtrl($mdDialog) {
    var originatorEv;
    this.openMenu = function($mdOpenMenu, ev) {
      originatorEv = ev;
      $mdOpenMenu(ev);
    };
    this.announceClick = function(index) {
      $mdDialog.show(
        $mdDialog.alert()
          .title('You clicked!')
          .content('You clicked the menu item at index ' + index)
          .ok('Nice')
          .targetEvent(originatorEv)
      );
      originatorEv = null;
    };
  })

  .controller('mtMenuWidthCtrl', function mtMenuBasicCtrl($mdDialog) {
    var vm = this;
    this.announceClick = function(index) {
      $mdDialog.show(
        $mdDialog.alert()
          .title('You clicked!')
          .content('You clicked the menu item at index ' + index)
          .ok('Nice')
      );
    };
  });





'use strict';

app

  .controller('mtProgressCircularCtrl', function($scope, $interval) {

    $scope.page = {
      title: 'Progress Circular',
      subtitle: 'Place subtitle here...'
    };

    $scope.mode = 'query';
    $scope.determinateValue = 30;
    $interval(function() {
      $scope.determinateValue += 1;
      if ($scope.determinateValue > 100) {
        $scope.determinateValue = 30;
      }
    }, 100, 0, true);

  });





'use strict';

app

  .controller('mtProgressLinearCtrl', function($scope, $interval) {

    $scope.page = {
      title: 'Progress Linear',
      subtitle: 'Place subtitle here...'
    };

    $scope.mode = 'query';
    $scope.determinateValue = 30;
    $scope.determinateValue2 = 30;
    $interval(function() {
      $scope.determinateValue += 1;
      $scope.determinateValue2 += 1.5;
      if ($scope.determinateValue > 100) {
        $scope.determinateValue = 30;
        $scope.determinateValue2 = 30;
      }
    }, 100, 0, true);
    $interval(function() {
      $scope.mode = ($scope.mode == 'query' ? 'determinate' : 'query');
    }, 7200, 0, true);

  });





'use strict';

app

  .controller('mtRadioButtonCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Radio Button',
      subtitle: 'Place subtitle here...'
    };

    $scope.data = {
      group1 : 'Banana',
      group2 : '2',
      group3 : 'avatar-1'
    };
    $scope.avatarData = [{
        id: "avatars:svg-1",
        title: 'avatar 1',
        value: 'avatar-1'
      },{
        id: "avatars:svg-2",
        title: 'avatar 2',
        value: 'avatar-2'
      },{
        id: "avatars:svg-3",
        title: 'avatar 3',
        value: 'avatar-3'
    }];
    $scope.radioData = [
      { label: '1', value: 1 },
      { label: '2', value: 2 },
      { label: '3', value: '3', isDisabled: true },
      { label: '4', value: '4' }
    ];
    $scope.submit = function() {
      alert('submit');
    };
    $scope.addItem = function() {
      var r = Math.ceil(Math.random() * 1000);
      $scope.radioData.push({ label: r, value: r });
    };
    $scope.removeItem = function() {
      $scope.radioData.pop();
    };

  })

  .config(function($mdIconProvider) {
    $mdIconProvider.iconSet("avatars", 'images/avatar-icons.svg',128);
  });





'use strict';

app

  .controller('mtSelectCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Select',
      subtitle: 'Place subtitle here...'
    };

    $scope.userState = '';
    $scope.states = ('AL AK AZ AR CA CO CT DE FL GA HI ID IL IN IA KS KY LA ME MD MA MI MN MS ' +
    'MO MT NE NV NH NJ NM NY NC ND OH OK OR PA RI SC SD TN TX UT VT VA WA WV WI ' +
    'WY').split(' ').map(function (state) { return { abbrev: state }; });

    $scope.sizes = [
      "small (12-inch)",
      "medium (14-inch)",
      "large (16-inch)",
      "insane (42-inch)"
    ];
    $scope.toppings = [
      { category: 'meat', name: 'Pepperoni' },
      { category: 'meat', name: 'Sausage' },
      { category: 'meat', name: 'Ground Beef' },
      { category: 'meat', name: 'Bacon' },
      { category: 'veg', name: 'Mushrooms' },
      { category: 'veg', name: 'Onion' },
      { category: 'veg', name: 'Green Pepper' },
      { category: 'veg', name: 'Green Olives' }
    ];

    $scope.loadUsers = function() {
      // Use timeout to simulate a 650ms request.
      $scope.users = [];
      return $timeout(function() {
        $scope.users = [
          { id: 1, name: 'Scooby Doo' },
          { id: 2, name: 'Shaggy Rodgers' },
          { id: 3, name: 'Fred Jones' },
          { id: 4, name: 'Daphne Blake' },
          { id: 5, name: 'Velma Dinkley' },
        ];
      }, 650);
    };

    $scope.clearValue = function() {
      $scope.myModel = undefined;
    };
    $scope.save = function() {
      alert('Form was valid!');
    };

  });





'use strict';

app

  .controller('mtSidenavCtrl', function($scope, $timeout, $mdSidenav, $mdUtil, $log) {

    $scope.page = {
      title: 'Sidenav',
      subtitle: 'Place subtitle here...'
    };

    $scope.toggleLeft = buildToggler('left');
    $scope.toggleRight = buildToggler('right');
    /**
     * Build handler to open/close a SideNav; when animation finishes
     * report completion in console
     */
    function buildToggler(navID) {
      var debounceFn =  $mdUtil.debounce(function(){
        $mdSidenav(navID)
          .toggle()
          .then(function () {
            $log.debug("toggle " + navID + " is done");
          });
      },200);
      return debounceFn;
    }

  })

  .controller('mtSidenavLeftCtrl', function ($scope, $timeout, $mdSidenav, $log) {
    $scope.close = function () {
      $mdSidenav('left').close()
        .then(function () {
          $log.debug("close LEFT is done");
        });
    };
  })
  .controller('mtSidenavRightCtrl', function ($scope, $timeout, $mdSidenav, $log) {
    $scope.close = function () {
      $mdSidenav('right').close()
        .then(function () {
          $log.debug("close RIGHT is done");
        });
    };
  });





'use strict';

app

  .controller('mtSliderCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Slider',
      subtitle: 'Place subtitle here...'
    };

    $scope.color = {
      red: Math.floor(Math.random() * 255),
      green: Math.floor(Math.random() * 255),
      blue: Math.floor(Math.random() * 255)
    };
    $scope.rating1 = 3;
    $scope.rating2 = 2;
    $scope.rating3 = 4;
    $scope.disabled1 = 0;
    $scope.disabled2 = 70;

  });





'use strict';

app

  .controller('mtSubheaderCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Subheader',
      subtitle: 'Place subtitle here...'
    };

    var imagePath = 'http://placekitten.com/g/600/600';
    $scope.messages = [
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      },
      {
        face : imagePath,
        what: 'Brunch this weekend?',
        who: 'Min Li Chan',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      }
    ];

  })

  .config(function($mdThemingProvider) {
    $mdThemingProvider.theme('altTheme')
      .primaryPalette('purple');
  });





'use strict';

app

  .controller('mtSwipeCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Swipe',
      subtitle: 'Place subtitle here...'
    };

    $scope.onSwipeLeft = function(ev) {
      alert('You swiped left!!');
    };
    $scope.onSwipeRight = function(ev) {
      alert('You swiped right!!');
    };

  });





'use strict';

app

  .controller('mtSwitchCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Switch',
      subtitle: 'Place subtitle here...'
    };

    $scope.data = {
      cb1: true,
      cb4: true,
      cb5: false
    };
    $scope.onChange = function(cbState) {
      $scope.message = "The switch is now: " + cbState;
    };

  });





'use strict';

app

  .controller('mtTabsCtrl', function($scope, $timeout, $log) {

    $scope.page = {
      title: 'Tabs',
      subtitle: 'Place subtitle here...'
    };

    var tabs = [
        { title: 'One', content: "Tabs will become paginated if there isn't enough room for them."},
        { title: 'Two', content: "You can swipe left and right on a mobile device to change tabs."},
        { title: 'Three', content: "You can bind the selected tab via the selected attribute on the md-tabs element."},
        { title: 'Four', content: "If you set the selected tab binding to -1, it will leave no tab selected."},
        { title: 'Five', content: "If you remove a tab, it will try to select a new one."},
        { title: 'Six', content: "There's an ink bar that follows the selected tab, you can turn it off if you want."},
        { title: 'Seven', content: "If you set ng-disabled on a tab, it becomes unselectable. If the currently selected tab becomes disabled, it will try to select the next tab."},
        { title: 'Eight', content: "If you look at the source, you're using tabs to look at a demo for tabs. Recursion!"},
        { title: 'Nine', content: "If you set md-theme=\"green\" on the md-tabs element, you'll get green tabs."},
        { title: 'Ten', content: "If you're still reading this, you should just go check out the API docs for tabs!"}
      ],
      selected = null,
      previous = null;

    $scope.tabs = tabs;
    $scope.selectedIndex = 2;
    $scope.$watch('selectedIndex', function(current, old){
      previous = selected;
      selected = tabs[current];
      if ( old + 1 && (old != current)) $log.debug('Goodbye ' + previous.title + '!');
      if ( current + 1 )                $log.debug('Hello ' + selected.title + '!');
    });
    $scope.addTab = function (title, view) {
      view = view || title + " Content View";
      tabs.push({ title: title, content: view, disabled: false});
    };
    $scope.removeTab = function (tab) {
      var index = tabs.indexOf(tab);
      tabs.splice(index, 1);
    };

    $scope.data = {
      selectedIndex: 0,
      secondLocked:  true,
      secondLabel:   "Item Two",
      bottom:        false
    };
    $scope.next = function() {
      $scope.data.selectedIndex = Math.min($scope.data.selectedIndex + 1, 2) ;
    };
    $scope.previous = function() {
      $scope.data.selectedIndex = Math.max($scope.data.selectedIndex - 1, 0);
    };

  });





'use strict';

app

  .controller('mtToastCtrl', function($scope, $mdToast, $animate) {

    $scope.page = {
      title: 'Toast',
      subtitle: 'Place subtitle here...'
    };

    $scope.toastPosition = {
      bottom: false,
      top: true,
      left: false,
      right: true
    };
    $scope.getToastPosition = function() {
      return Object.keys($scope.toastPosition)
        .filter(function(pos) { return $scope.toastPosition[pos]; })
        .join(' ');
    };
    $scope.showCustomToast = function() {
      $mdToast.show({
        parent: angular.element(document.querySelector('#toastContainer')),
        controller: 'ToastShowCtrl',
        templateUrl: 'toast-template.html',
        hideDelay: 6000,
        position: $scope.getToastPosition()
      });
    };
    $scope.showSimpleToast = function() {
      $mdToast.show(
        $mdToast.simple()
          .parent(angular.element(document.querySelector('#toastContainer')))
          .content('Simple Toast!')
          .position($scope.getToastPosition())
          .hideDelay(3000)
      );
    };
    $scope.showActionToast = function() {
      var toast = $mdToast.simple()
        .parent(angular.element(document.querySelector('#toastContainer')))
        .content('Action Toast!')
        .action('OK')
        .highlightAction(false)
        .position($scope.getToastPosition());
      $mdToast.show(toast).then(function(response) {
        if ( response == 'ok' ) {
          alert('You clicked \'OK\'.');
        }
      });
    };

  })

  .controller('ToastShowCtrl', function($scope, $mdToast) {
    $scope.closeToast = function() {
      $mdToast.hide();
    };
  });





'use strict';

app

  .controller('mtToolbarCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Toolbar',
      subtitle: 'Place subtitle here...'
    };

    var imagePath = 'http://placekitten.com/g/80/80';
    $scope.todos = [];
    for (var i = 0; i < 15; i++) {
      $scope.todos.push({
        face: imagePath,
        what: "Brunch this weekend?",
        who: "Min Li Chan",
        notes: "I'll be in your neighborhood doing errands."
      });
    }

  });





'use strict';

app

  .controller('mtTooltipCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Tooltip',
      subtitle: 'Place subtitle here...'
    };

    $scope.demo = {};

  });





'use strict';

app

  .controller('mtWhiteframeCtrl', function($scope, $timeout, $mdBottomSheet) {

    $scope.page = {
      title: 'Whiteframe',
      subtitle: 'Place subtitle here...'
    };

  });




