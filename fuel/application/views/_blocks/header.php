<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title><?= fuel_var('page_title', ''); ?></title>

        <meta name="keywords" content="<?php echo fuel_var('meta_keywords') ?>">
        <meta name="description" content="<?php echo fuel_var('meta_description') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets_path('css/style.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets_path('css/newstudents.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets_path('js/jquery-ui/css/custom-theme/jquery-ui-1.10.4.custom.min.css') ?>">
        <script type="text/javascript" src="<?= assets_path('js/jquery.js') ?>"></script>
        <script type="text/javascript" src="<?= assets_path('js/main.js') ?>"></script>
        <script type="text/javascript" src="<?= assets_path('js/json2.js') ?>"></script>
        <script type="text/javascript" src="<?= assets_path('js/jquery-ui/js/jquery-ui-1.10.4.custom.min.js') ?>"></script>
    </head>
    <body>
        <header>
            <div id="header">

                <!-- Title -->
                <div id="title">
                    <!-- Logo -->
                    <div id="logo">
                        <a href="index.html"><img src="<?=  assets_path('images/usc_logo.png') ?>" alt="USC Logo" width="130" height="130" /></a>
                        <div id="tagline">USC Taiwanese Student Association<br>南加大台灣同學會</div>
                    </div>

                    <!-- Social Icons -->
                    <div id="contact">
                        <div class="social-icons">
                            <ul>
                                <li class="facebook"><a href="https://www.facebook.com/usctsa" target="_blank">Facebook</a></li>
                            </ul>
                        </div>

                        <!-- Contact Details -->
                        <!--
                        <div id="contact-details">
                                <ul>
                                <li><a href="mailto:tsa.usc@gmail.com">Contact us</a></li>
                            </ul>
                        </div>
                        -->
                    </div>
                </div>
                <div id="nav_bar">
                    <nav>
                        <?= fuel_nav(array('render_type' => 'basic')); ?>
                    </nav>
                </div>
            </div>
        </header>
        <div id="wrapper">	
