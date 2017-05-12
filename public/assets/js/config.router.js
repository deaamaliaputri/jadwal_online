'use strict';

/**
 * Config for the router
 */
app.config(['$stateProvider', '$urlRouterProvider', '$controllerProvider', '$compileProvider', '$filterProvider', '$provide', '$ocLazyLoadProvider', 'JS_REQUIRES',
function ($stateProvider, $urlRouterProvider, $controllerProvider, $compileProvider, $filterProvider, $provide, $ocLazyLoadProvider, jsRequires) {

    app.controller = $controllerProvider.register;
    app.directive = $compileProvider.directive;
    app.filter = $filterProvider.register;
    app.factory = $provide.factory;
    app.service = $provide.service;
    app.constant = $provide.constant;
    app.value = $provide.value;

    // LAZY MODULES

    $ocLazyLoadProvider.config({
        debug: false,
        events: true,
        modules: jsRequires.modules
    });

    // APPLICATION ROUTES
    // -----------------------------------
    // For any unmatched url, redirect to /app/dashboard
    $urlRouterProvider.otherwise("/app/dashboard");
    //
    // Set up the states
    $stateProvider.state('app', {
        url: "/app",
        templateUrl: "assets/views/app.html",
        resolve: loadSequence('modernizr', 'moment', 'angularMoment', 'uiSwitch', 'perfect-scrollbar-plugin', 'toaster', 'ngAside', 'vAccordion', 'sweet-alert', 'chartjs', 'tc.chartjs', 'oitozero.ngSweetAlert', 'chatCtrl', 'truncate', 'htmlToPlaintext', 'angular-notification-icons'),
        abstract: true
    }).state('app.dashboard', {
        url: "/dashboard",
        templateUrl: "assets/views/dashboard.html",
        resolve: loadSequence('jquery-sparkline', 'dashboardCtrl'),
        title: 'Dashboard',
        ncyBreadcrumb: {
            label: 'Dashboard'
        }
    })
    //daftar
        .state('app.daftar', {
            url: '/daftar',
            templateUrl: 'assets/src/daftar/daftar-list.html',
            title: 'Data Daftar',
            resolve: loadSequence('daftarCtrl', 'daftar_service'),
        })

        .state('app.daftar-create', {
            url: '/daftar-create',
            templateUrl: 'assets/src/daftar/daftar-create.html',
            title: 'Data Daftar create',
            resolve: loadSequence('daftarcreateCtrl', 'daftar_service'),
        })

        .state('app.daftar-password', {  
            url: '/daftar-password',  
            templateUrl: 'assets/src/daftar/daftar-password.html',  
            title: 'Tambah Data Daftar',  
            resolve: loadSequence('DaftarPasswordCtrl', 'daftar_service'),  
        })  

        //daftar Edit
        .state('app.daftar-edit', {
            url: '/daftar-edit/:id',
            templateUrl: 'assets/src/daftar/daftar-edit.html',
            title: 'Data Daftar',
            resolve: loadSequence('daftar-edit', 'daftar_service'),
        })

//teachers
 .state('app.teachers', {
            url: '/teachers',
            templateUrl: 'assets/src/teachers/teachers-list.html',
            title: 'Data Teachers',
            resolve: loadSequence('teachersCtrl', 'teachers_service'),
        })

        .state('app.teachers-create', {
            url: '/teachers-create',
            templateUrl: 'assets/src/teachers/teachers-create.html',
            title: 'Data Teachers create',
            resolve: loadSequence('teacherscreateCtrl', 'teachers_service'),
        })

        //teachers Edit
        .state('app.teachers-edit', {
            url: '/teachers-edit/:id',
            templateUrl: 'assets/src/teachers/teachers-edit.html',
            title: 'Data Teachers',
            resolve: loadSequence('teachers-edit', 'teachers_service'),
        })

//departments
 .state('app.departments', {
            url: '/departments',
            templateUrl: 'assets/src/departments/departments-list.html',
            title: 'Data Departments',
            resolve: loadSequence('departmentsCtrl', 'departments_service'),
        })

        .state('app.departments-create', {
            url: '/departments-create',
            templateUrl: 'assets/src/departments/departments-create.html',
            title: 'Data Departments create',
            resolve: loadSequence('departmentscreateCtrl', 'departments_service'),
        })

        //departments Edit
        .state('app.departments-edit', {
            url: '/departments-edit/:id',
            templateUrl: 'assets/src/departments/departments-edit.html',
            title: 'Data Departments',
            resolve: loadSequence('departments-edit', 'departments_service'),
        })

//kelas
 .state('app.kelas', {
            url: '/kelas',
            templateUrl: 'assets/src/kelas/kelas-list.html',
            title: 'Data Kelas',
            resolve: loadSequence('kelasCtrl', 'kelas_service'),
        })

        .state('app.kelas-create', {
            url: '/kelas-create',
            templateUrl: 'assets/src/kelas/kelas-create.html',
            title: 'Data Kelas create',
            resolve: loadSequence('kelascreateCtrl', 'kelas_service'),
        })

        //kelas Edit
        .state('app.kelas-edit', {
            url: '/kelas-edit/:id',
            templateUrl: 'assets/src/kelas/kelas-edit.html',
            title: 'Data Kelas',
            resolve: loadSequence('kelas-edit', 'kelas_service'),
        })

//schedules
 .state('app.schedules', {
            url: '/schedules',
            templateUrl: 'assets/src/schedules/schedules-list.html',
            title: 'Data Schedules',
            resolve: loadSequence('schedulesCtrl', 'schedules_service'),
        })

        .state('app.schedules-create', {
            url: '/schedules-create',
            templateUrl: 'assets/src/schedules/schedules-create.html',
            title: 'Data Schedules create',
            resolve: loadSequence('schedulescreateCtrl', 'schedules_service'),
        })

        //schedules Edit
        .state('app.schedules-edit', {
            url: '/schedules-edit/:id',
            templateUrl: 'assets/src/schedules/schedules-edit.html',
            title: 'Data Schedules',
            resolve: loadSequence('schedules-edit', 'schedules_service'),
        })

//subjects
 .state('app.subjects', {
            url: '/subjects',
            templateUrl: 'assets/src/subjects/subjects-list.html',
            title: 'Data Subjects',
            resolve: loadSequence('subjectsCtrl', 'subjects_service'),
        })

        .state('app.subjects-create', {
            url: '/subjects-create',
            templateUrl: 'assets/src/subjects/subjects-create.html',
            title: 'Data Subjects create',
            resolve: loadSequence('subjectscreateCtrl', 'subjects_service'),
        })

        //subjects Edit
        .state('app.subjects-edit', {
            url: '/subjects-edit/:id',
            templateUrl: 'assets/src/subjects/subjects-edit.html',
            title: 'Data Subjects',
            resolve: loadSequence('subjects-edit', 'subjects_service'),
        })

        //students
 .state('app.students', {
            url: '/students',
            templateUrl: 'assets/src/students/students-list.html',
            title: 'Data Students',
            resolve: loadSequence('studentsCtrl', 'students_service'),
        })

        .state('app.students-create', {
            url: '/students-create',
            templateUrl: 'assets/src/students/students-create.html',
            title: 'Data Students create',
            resolve: loadSequence('studentscreateCtrl', 'students_service'),
        })

        //students Edit
        .state('app.students-edit', {
            url: '/students-edit/:id',
            templateUrl: 'assets/src/students/students-edit.html',
            title: 'Data Students',
            resolve: loadSequence('students-edit', 'students_service'),
        })

        // //urusan Create
        // .state('app.urusan-create', {
        //     url: '/urusan-create',
        //     templateUrl: 'assets//src/urusan/urusan/urusan-create.html',
        //     data: {title: 'Tambah Data Urusan Pemerintah Daerah'},
        //     controller: 'UrusanCreateCtrl',
        //     resolve: load(['assets//src/urusan/urusan/urusan-service.js', 'assets//src/urusan/urusan/UrusanCreateCtrl.js'])
        // })
        //
        // //urusan Edit
        // .state('app.urusan-edit', {
        //     url: '/urusan-edit/:id',
        //     templateUrl: 'assets//src/urusan/urusan/urusan-edit.html',
        //     data: {title: 'Edit Data Urusan Pemerintah Daerah'},
        //     controller: 'UrusanEditCtrl',
        //     resolve: load(['assets//src/urusan/urusan/urusan-service.js', 'assets//src/urusan/urusan/UrusanEditCtrl.js'])
        // })



        .state('app.ui', {
        url: '/ui',
        template: '<div ui-view class="fade-in-up"></div>',
        title: 'UI Elements',
        ncyBreadcrumb: {
            label: 'UI Elements'
        }
    }).state('app.ui.elements', {
        url: '/elements',
        templateUrl: "assets/views/ui_elements.html",
        title: 'Elements',
        icon: 'ti-layout-media-left-alt',
        ncyBreadcrumb: {
            label: 'Elements'
        }
    }).state('app.ui.buttons', {
        url: '/buttons',
        templateUrl: "assets/views/ui_buttons.html",
        title: 'Buttons',
        resolve: loadSequence('spin', 'ladda', 'angular-ladda', 'laddaCtrl'),
        ncyBreadcrumb: {
            label: 'Buttons'
        }
    }).state('app.ui.links', {
        url: '/links',
        templateUrl: "assets/views/ui_links.html",
        title: 'Link Effects',
        ncyBreadcrumb: {
            label: 'Link Effects'
        }
    }).state('app.ui.icons', {
        url: '/icons',
        templateUrl: "assets/views/ui_icons.html",
        title: 'Font Awesome Icons',
        ncyBreadcrumb: {
            label: 'Font Awesome Icons'
        },
        resolve: loadSequence('iconsCtrl')
    }).state('app.ui.lineicons', {
        url: '/line-icons',
        templateUrl: "assets/views/ui_line_icons.html",
        title: 'Linear Icons',
        ncyBreadcrumb: {
            label: 'Linear Icons'
        },
        resolve: loadSequence('iconsCtrl')
    }).state('app.ui.modals', {
        url: '/modals',
        templateUrl: "assets/views/ui_modals.html",
        title: 'Modals',
        ncyBreadcrumb: {
            label: 'Modals'
        },
        resolve: loadSequence('asideCtrl')
    }).state('app.ui.toggle', {
        url: '/toggle',
        templateUrl: "assets/views/ui_toggle.html",
        title: 'Toggle',
        ncyBreadcrumb: {
            label: 'Toggle'
        }
    }).state('app.ui.tabs_accordions', {
        url: '/accordions',
        templateUrl: "assets/views/ui_tabs_accordions.html",
        title: "Tabs & Accordions",
        ncyBreadcrumb: {
            label: 'Tabs & Accordions'
        },
        resolve: loadSequence('vAccordionCtrl')
    }).state('app.ui.panels', {
        url: '/panels',
        templateUrl: "assets/views/ui_panels.html",
        title: 'Panels',
        ncyBreadcrumb: {
            label: 'Panels'
        }
    }).state('app.ui.notifications', {
        url: '/notifications',
        templateUrl: "assets/views/ui_notifications.html",
        title: 'Notifications',
        ncyBreadcrumb: {
            label: 'Notifications'
        },
        resolve: loadSequence('toasterCtrl', 'sweetAlertCtrl', 'NotificationIconsCtrl')
    }).state('app.ui.treeview', {
        url: '/treeview',
        templateUrl: "assets/views/ui_tree.html",
        title: 'TreeView',
        ncyBreadcrumb: {
            label: 'Treeview'
        },
        resolve: loadSequence('angularBootstrapNavTree', 'treeCtrl')
    }).state('app.ui.media', {
        url: '/media',
        templateUrl: "assets/views/ui_media.html",
        title: 'Media',
        ncyBreadcrumb: {
            label: 'Media'
        }
    }).state('app.ui.nestable', {
        url: '/nestable2',
        templateUrl: "assets/views/ui_nestable.html",
        title: 'Nestable List',
        ncyBreadcrumb: {
            label: 'Nestable List'
        },
        resolve: loadSequence('jquery-nestable-plugin', 'ng-nestable', 'nestableCtrl')
    }).state('app.ui.typography', {
        url: '/typography',
        templateUrl: "assets/views/ui_typography.html",
        title: 'Typography',
        ncyBreadcrumb: {
            label: 'Typography'
        }
    }).state('app.table', {
        url: '/table',
        template: '<div ui-view class="fade-in-up"></div>',
        title: 'Tables',
        ncyBreadcrumb: {
            label: 'Tables'
        }
    }).state('app.table.basic', {
        url: '/basic',
        templateUrl: "assets/views/table_basic.html",
        title: 'Basic Tables',
        ncyBreadcrumb: {
            label: 'Basic'
        }
    }).state('app.table.responsive', {
        url: '/responsive',
        templateUrl: "assets/views/table_responsive.html",
        title: 'Responsive Tables',
        ncyBreadcrumb: {
            label: 'Responsive'
        }
    }).state('app.table.dynamic', {
        url: '/dynamic',
        templateUrl: "assets/views/table_dynamic.html",
        title: 'Dynamic Tables',
        ncyBreadcrumb: {
            label: 'Dynamic'
        },
        resolve: loadSequence('dynamicTableCtrl')
    }).state('app.table.data', {
        url: '/data',
        templateUrl: "assets/views/table_data.html",
        title: 'ngTable',
        ncyBreadcrumb: {
            label: 'ngTable'
        },
        resolve: loadSequence('ngTable', 'ngTableCtrl')
    }).state('app.table.export', {
        url: '/export',
        templateUrl: "assets/views/table_export.html",
        title: 'Table'
    }).state('app.form', {
        url: '/form',
        template: '<div ui-view class="fade-in-up"></div>',
        title: 'Forms',
        ncyBreadcrumb: {
            label: 'Forms'
        }
    }).state('app.form.elements', {
        url: '/elements',
        templateUrl: "assets/views/form_elements.html",
        title: 'Forms Elements',
        ncyBreadcrumb: {
            label: 'Elements'
        },
        resolve: loadSequence('ui.select', 'monospaced.elastic', 'ui.mask', 'touchspin-plugin', 'selectCtrl', 'spectrum-plugin', 'angularSpectrumColorpicker')
    }).state('app.form.xeditable', {
        url: '/xeditable',
        templateUrl: "assets/views/form_xeditable.html",
        title: 'Angular X-Editable',
        ncyBreadcrumb: {
            label: 'X-Editable'
        },
        resolve: loadSequence('xeditable', 'checklist-model', 'xeditableCtrl')
    }).state('app.form.texteditor', {
        url: '/editor',
        templateUrl: "assets/views/form_text_editor.html",
        title: 'Text Editor',
        ncyBreadcrumb: {
            label: 'Text Editor'
        },
        resolve: loadSequence('ckeditor-plugin', 'ckeditor', 'ckeditorCtrl')
    }).state('app.form.wizard', {
        url: '/wizard',
        templateUrl: "assets/views/form_wizard.html",
        title: 'Form Wizard',
        ncyBreadcrumb: {
            label: 'Wizard'
        },
        resolve: loadSequence('wizardCtrl')
    }).state('app.form.validation', {
        url: '/validation',
        templateUrl: "assets/views/form_validation.html",
        title: 'Form Validation',
        ncyBreadcrumb: {
            label: 'Validation'
        },
        resolve: loadSequence('validationCtrl')
    }).state('app.form.cropping', {
        url: '/image-cropping',
        templateUrl: "assets/views/form_image_cropping.html",
        title: 'Image Cropping',
        ncyBreadcrumb: {
            label: 'Image Cropping'
        },
        resolve: loadSequence('ngImgCrop', 'cropCtrl')
    }).state('app.form.upload', {
        url: '/file-upload',
        templateUrl: "assets/views/form_file_upload.html",
        title: 'Multiple File Upload',
        ncyBreadcrumb: {
            label: 'File Upload'
        },
        resolve: loadSequence('angularFileUpload', 'uploadCtrl')
    }).state('app.pages', {
        url: '/pages',
        template: '<div ui-view class="fade-in-up"></div>',
        title: 'Pages',
        ncyBreadcrumb: {
            label: 'Pages'
        }
    }).state('app.pages.user', {
        url: '/user',
        templateUrl: "assets/views/pages_user_profile.html",
        title: 'User Profile',
        ncyBreadcrumb: {
            label: 'User Profile'
        },
        resolve: loadSequence('flow', 'userCtrl')
    }).state('app.pages.invoice', {
        url: '/invoice',
        templateUrl: "assets/views/pages_invoice.html",
        title: 'Invoice',
        ncyBreadcrumb: {
            label: 'Invoice'
        }
    }).state('app.pages.timeline', {
        url: '/timeline',
        templateUrl: "assets/views/pages_timeline.html",
        title: 'Timeline',
        ncyBreadcrumb: {
            label: 'Timeline'
        },
        resolve: loadSequence('ngMap')
    }).state('app.pages.calendar', {
        url: '/calendar',
        templateUrl: "assets/views/pages_calendar.html",
        title: 'Calendar',
        ncyBreadcrumb: {
            label: 'Calendar'
        },
        resolve: loadSequence('moment', 'mwl.calendar', 'calendarCtrl')
    }).state('app.pages.messages', {
        url: '/messages',
        templateUrl: "assets/views/pages_messages.html",
        resolve: loadSequence('truncate', 'htmlToPlaintext', 'inboxCtrl')
    }).state('app.pages.messages.inbox', {
        url: '/inbox/:inboxID',
        templateUrl: "assets/views/pages_inbox.html",
        controller: 'ViewMessageCrtl'
    }).state('app.pages.blank', {
        url: '/blank',
        templateUrl: "assets/views/pages_blank_page.html",
        ncyBreadcrumb: {
            label: 'Starter Page'
        }
    }).state('app.utilities', {
        url: '/utilities',
        template: '<div ui-view class="fade-in-up"></div>',
        title: 'Utilities',
        ncyBreadcrumb: {
            label: 'Utilities'
        }
    }).state('app.utilities.search', {
        url: '/search',
        templateUrl: "assets/views/utility_search_result.html",
        title: 'Search Results',
        ncyBreadcrumb: {
            label: 'Search Results'
        }
    }).state('app.utilities.pricing', {
        url: '/pricing',
        templateUrl: "assets/views/utility_pricing_table.html",
        title: 'Pricing Table',
        ncyBreadcrumb: {
            label: 'Pricing Table'
        }
    }).state('app.maps', {
        url: "/maps",
        templateUrl: "assets/views/maps.html",
        resolve: loadSequence('ngMap', 'mapsCtrl'),
        title: "Maps",
        ncyBreadcrumb: {
            label: 'Maps'
        }
    }).state('app.charts', {
        url: "/charts",
        templateUrl: "assets/views/charts.html",
        resolve: loadSequence('chartjs', 'tc.chartjs', 'chartsCtrl'),
        title: "Charts",
        ncyBreadcrumb: {
            label: 'Charts'
        }
    }).state('app.documentation', {
        url: "/documentation",
        templateUrl: "assets/views/documentation.html",
        title: "Documentation",
        ncyBreadcrumb: {
            label: 'Documentation'
        }
    }).state('error', {
        url: '/error',
        template: '<div ui-view class="fade-in-up"></div>'
    }).state('error.404', {
        url: '/404',
        templateUrl: "assets/views/utility_404.html",
    }).state('error.500', {
        url: '/500',
        templateUrl: "assets/views/utility_500.html",
    })

	// Login routes

	.state('login', {
	    url: '/login',
	    template: '<div ui-view class="fade-in-right-big smooth"></div>',
	    abstract: true
	}).state('login.signin', {
	    url: '/signin',
	    templateUrl: "assets/views/login_login.html"
	}).state('login.forgot', {
	    url: '/forgot',
	    templateUrl: "assets/views/login_forgot.html"
	}).state('login.registration', {
	    url: '/registration',
	    templateUrl: "assets/views/login_registration.html"
	}).state('login.lockscreen', {
	    url: '/lock',
	    templateUrl: "assets/views/login_lock_screen.html"
	});

    // Generates a resolve object previously configured in constant.JS_REQUIRES (config.constant.js)
    function loadSequence() {
        var _args = arguments;
        return {
            deps: ['$ocLazyLoad', '$q',
			function ($ocLL, $q) {
			    var promise = $q.when(1);
			    for (var i = 0, len = _args.length; i < len; i++) {
			        promise = promiseThen(_args[i]);
			    }
			    return promise;

			    function promiseThen(_arg) {
			        if (typeof _arg == 'function')
			            return promise.then(_arg);
			        else
			            return promise.then(function () {
			                var nowLoad = requiredData(_arg);
			                if (!nowLoad)
			                    return $.error('Route resolve: Bad resource name [' + _arg + ']');
			                return $ocLL.load(nowLoad);
			            });
			    }

			    function requiredData(name) {
			        if (jsRequires.modules)
			            for (var m in jsRequires.modules)
			                if (jsRequires.modules[m].name && jsRequires.modules[m].name === name)
			                    return jsRequires.modules[m];
			        return jsRequires.scripts && jsRequires.scripts[name];
			    }
			}]
        };
    }
}])
    .config(function ($httpProvider) {
        $httpProvider.defaults.headers.post = {
            "X-Requested-With": "XMLHttpRequest",
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        };
        $httpProvider.defaults.headers.put = {
            "X-Requested-With": "XMLHttpRequest",
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        };
        $httpProvider.defaults.headers.get = {
            "X-Requested-With": "XMLHttpRequest",
            'Content-Type': 'application/x-www-form-urlencoded'
        };
        $httpProvider.defaults.headers.delete = {
            "X-Requested-With": "XMLHttpRequest",
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        };
    })
    .config(['$translateProvider', function ($translateProvider) {
        // Register a loader for the static files
        // So, the module will search missing translation tables under the specified urls.
        // Those urls are [prefix][langKey][suffix].
        // $translateProvider.useStaticFilesLoader({
        //     prefix: '../angular/i18n/',
        //     suffix: '.json'
        // });
        // Tell the module what language to use by default
        $translateProvider.preferredLanguage('en');
        // Tell the module to store the language in the local storage
        $translateProvider.useLocalStorage();
    }]);
