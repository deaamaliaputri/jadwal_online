<!doctype html>
<html ng-app="minovateApp" ng-controller="MainCtrl" class="no-js containerClass">
  <head>
    <meta charset="utf-8">
    <title>Minovate - Admin Dashboard</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <!— Place favicon.ico and apple-touch-icon.png in the root directory —>
    <link rel="icon" type="../image/ico" href="favicon.ico" />
    <link rel="stylesheet" href="../styles/vendor.0cc3d200.css">
    <link rel="stylesheet" href="../styles/main.1be6a35c.css">
  </head>
  <body id="minovate" class="main.settings.navbarHeaderColor main.settings.activeColor containerClass header-fixed aside-fixed rightbar-hidden appWrapper" ng-class="{'header-fixed': main.settings.headerFixed, 'header-static': !main.settings.headerFixed, 'aside-fixed': main.settings.asideFixed, 'aside-static': !main.settings.asideFixed, 'rightbar-show': main.settings.rightbarShow, 'rightbar-hidden': !main.settings.rightbarShow}">

    <!--[if lt IE 7]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!— Application content —>
    <div id="wrap" ui-view autoscroll="false"></div>

    <!— Page Loader —>
    <div id="pageloader" page-loader></div>


    <!— Google Analytics: change UA-XXXXX-X to be your site's ID —>
     <script>
       (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
       (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
       m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
       })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

       ga('create', 'UA-XXXXX-X');
       ga('send', 'pageview');
    </script>
    <script src='//maps.googleapis.com/maps/api/js?libraries=weather,geometry,visualization,places,drawing&sensor=false&language=en&v=3.17'></script>

    <!--[if lt IE 9]>
    <script src="scripts/oldieshim.f2dbeece.js"></script>
    <![endif]-->

    <script src="../scripts/vendor.4c173da6.js"></script>

    <script src="../scripts/app.d7ca82ad.js"></script>
</body>
</html>