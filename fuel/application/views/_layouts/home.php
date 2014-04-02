<!DOCTYPE html> <!-- HTML5 declaration -->
<html>

    <head>
        <meta charset="UTF-8">
        <title><?=fuel_var('page_title')?></title>
        
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="assets/css/index.css">
        <link rel="stylesheet" type="text/css" href="assets/js/engine1/style.css" media="screen" /> <!-- for WOWSlider -->
        
        <script type="text/javascript" src="assets/js/jquery.js"></script>
        <script type="text/javascript" src="assets/js/main.js"></script>
    </head>


    <body>
    
		<!-- top nav template -->
		<div id="nav"></div> <!-- using javascript to load nav.html --> 
        
        <!-- Wrapper -->
        <div id="wrapper">


            <!-- Cover Photo -->
            <div class="pagewidth">

                <!-- Start WOWSlider.com BODY section id=wowslider-container1 -->
                <div id="wowslider-container1">
                    <div class="ws_images"><ul>
                            <li><img src='<?='assets/images/'.fuel_var('cover_photo_1', 'cover_photo_1.jpg')?>' alt="Washington Square, San Francisco" title="Washington Square, San Francisco" id="wows1_0"/></li>
                            <li><img src='<?='assets/images/'.fuel_var('cover_photo_2', 'cover_photo_2.jpg')?>' alt="Coit Tower, San Francisco" title="Coit Tower, San Francisco" id="wows1_1"/></li>
                            <li><img src='<?='assets/images/'.fuel_var('cover_photo_3', 'cover_photo_3.jpg')?>' alt="Ventura Beach, Ventura" title="Ventura Beach, Ventura" id="wows1_2"/></li>
                            <li><img src='<?='assets/images/'.fuel_var('cover_photo_4', 'cover_photo_4.jpg')?>' alt="Twin Peaks, San Francisco" title="Twin Peaks, San Francisco" id="wows1_3"/></li>
                        </ul></div>
                    <div class="ws_bullets"><div>
                            <a href="#wows1_0" title="Washington Square, San Francisco"><img src="assets/data1/tooltips/cover_photo_1.jpg" alt="Washington Square, San Francisco"/>1</a>
                            <a href="#wows1_1" title="Coit Tower, San Francisco"><img src="assets/data1/tooltips/cover_photo_2.jpg" alt="Coit Tower, San Francisco"/>2</a>
                            <a href="#wows1_2" title="Ventura Beach, Ventura"><img src="assets/data1/tooltips/cover_photo_3.jpg" alt="Ventura Beach, Ventura"/>3</a>
                            <a href="#wows1_3" title="Twin Peaks, San Francisco"><img src="assets/data1/tooltips/cover_photo_4.jpg" alt="Twin Peaks, San Francisco"/>4</a>
                        </div></div>
                    <!-- <span class="wsl"><a href="http://wowslider.com">Slideshow CSS3</a> by WOWSlider.com v5.0m</span> -->
                    <a href="#" class="ws_frame"></a>
                    <div class="ws_shadow"></div>
                </div>
                <script type="text/javascript" src="assets/js/engine1/wowslider.js"></script>
                <script type="text/javascript" src="assets/js/engine1/script.js"></script>
                <!-- End WOWSlider.com BODY section -->

            </div>


            <!-- Announcements -->
            <div class="container">
				<!-- Headline -->
                <div class="headline"><h2><?=fuel_var('heading', 'Announcements')?></h2></div>

                <!-- Accordion -->
                <?php foreach($sections as $section){ ?>
                <div class="acc-container">
                	<span class="acc-trigger"><a href="#" onClick="return false;"><?=$section['title']?></a></span>
                    <div class="content">
	                    <p>
							<?= $section['content'] ?>
	                    </p>
                    </div>
         		</div>
				<?php } ?>
            </div>
            

            <!-- footer -->
            <footer>
                <div class="footer">
                    <p>
                    	<?=fuel_var('page_footer')?>
                    </p>
                </div>
            </footer>

        </div>        
    </body>
</html>