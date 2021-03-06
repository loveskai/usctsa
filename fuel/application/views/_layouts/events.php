<?php 
require_once(FACEBOOK_PATH . 'libraries/HTMLhelper.php');
$this->load->view('_blocks/header') ?>

<div id="wrapper">
    <?php
    $APP_ID = '445984512214350';
    $GROUP_ID = '12171823426';
    ?>
    <script>
        var DAYS = [
            "<?=lang('tsa_calendar_sun')?>",
            "<?=lang('tsa_calendar_mon')?>", 
            "<?=lang('tsa_calendar_tue')?>", 
            "<?=lang('tsa_calendar_wed')?>",
            "<?=lang('tsa_calendar_thu')?>", 
            "<?=lang('tsa_calendar_fri')?>",
            "<?=lang('tsa_calendar_sat')?>"
        ];
        var MONTHES = [
            "<?=lang('tsa_calendar_jan')?>",
            "<?=lang('tsa_calendar_feb')?>", 
            "<?=lang('tsa_calendar_mar')?>", 
            "<?=lang('tsa_calendar_apr')?>",
            "<?=lang('tsa_calendar_may')?>", 
            "<?=lang('tsa_calendar_jun')?>",
            "<?=lang('tsa_calendar_jul')?>",
            "<?=lang('tsa_calendar_aug')?>", 
            "<?=lang('tsa_calendar_sep')?>",
            "<?=lang('tsa_calendar_oct')?>", 
            "<?=lang('tsa_calendar_nov')?>",
            "<?=lang('tsa_calendar_dec')?>"
        ];
        var GROUP_ID = '<?= $GROUP_ID ?>';
        var APP_ID = '<?= $APP_ID ?>';
        var ACCESS_TOKEN = '';
        var TODAY = new Date();

        function day(day) {
            return '<span class="day">' + DAYS[day - 1] + '</span>';
        }

        var currentDate = new Date();
        var cached_events = null;
        var initialized = false;

        function initializeEvents() {
            var today = new Date();
            resetDate(today.getFullYear(), today.getMonth(), today.getDate());

            $("#prev_button").click(function() {
                if (currentDate.getMonth() === 0) {
                    resetDate(currentDate.getFullYear() - 1, 11, 1);
                }
                else {
                    resetDate(currentDate.getFullYear(), currentDate.getMonth() - 1, currentDate.getDate());
                }
            });
            $("#next_button").click(function() {
                if (currentDate.getMonth() === 11) {
                    resetDate(currentDate.getFullYear() + 1, 0, 1);
                }
                else {
                    resetDate(currentDate.getFullYear(), currentDate.getMonth() + 1, currentDate.getDate());
                }
            });
            $("#jump_to_today").click(function() {
                resetDate(TODAY.getFullYear(), TODAY.getMonth(), TODAY.getDate());
            });
        }


        function getFirstDay() {
            var firstDay = currentDate.getDate() % 7;
            firstDay = ((currentDate.getDay() + 8) - firstDay) % 7;
            return firstDay;
        }

        function getLastDate() {
            var lastD = new Date(currentDate.getFullYear(), currentDate.getMonth(), 28);
            var lastDate = lastD.getDate();
            while (lastD.getMonth() === currentDate.getMonth()) {
                lastDate = lastD.getDate();
                lastD.setDate(lastD.getDate() + 1);
            }
            return lastDate;
        }

        function displayCalendar() {
            var year = currentDate.getFullYear();
            var month = currentDate.getMonth();
            $("#month").text(MONTHES[month]);
            $("#year").text(year);

            var firstDay = getFirstDay();
            var lastDate = getLastDate();

            for (var i = firstDay; i > 0; i--) {
                $("#cell" + i).find(".date").html(day(i));
            }

            for (var i = firstDay + 1; i <= lastDate + firstDay; i++) {
                if (i <= 7) {
                    $("#cell" + i).find(".date").html(day(i) + " " + (i - firstDay));
                }
                else {
                    $("#cell" + i).find(".date").html(i - firstDay);
                }
            }

            if (year === TODAY.getFullYear() && month === TODAY.getMonth()) {
                $("#cell" + (TODAY.getDate() + firstDay)).addClass("today");
                $("#jump_to_today").css('display', 'none');
            }
            else {
                $(".today").removeClass("today");
                $("#jump_to_today").css('display', 'block');
            }
        }

        function hideMessage() {
            $("#calendar").css("opacity", "1.0");
            $("#centerMessage").css("display", "none");
            $("#centerMessage .text").text("");
        }

        function displayMessage(msg) {
            $("#calendar").css("opacity", "0.4");
            $("#centerMessage").css("display", "table");
            $("#centerMessage .text").text(msg);
        }

        function resetDate(year, month, date) {
            currentDate = new Date(year, month, date);
            displayCalendar();

            if (currentDate.getFullYear() >= TODAY.getFullYear()
                    && currentDate.getMonth() >= TODAY.getMonth()) {
                cached_events = null;   //clear cached event.
            }

            loadGroupEvents(getFirstDay());
        }

        function fbError(response) {
            displayMessage("<?=lang('tsa_calendar_fb_connection_fail')?>");
            console.log("unable to retrieve data from Facebook: " + JSON.stringify(response.error));
        }

        function loadGroupEvents(firstDay) {
            if (null === cached_events) {
                displayMessage("");
                var spinner = $('<img>').attr('src', '<?= img_path('spinner.gif') ?>');
                $("#centerMessage .text").append(spinner);

                FB.api('/' + GROUP_ID + '/events?access_token=' + ACCESS_TOKEN, function(response) {
                    if (!response || response.error) {
                        fbError(response);
                        return;
                    }
                    var data = response['data'];
                    cached_events = data;
                    showEvents(cached_events, firstDay);
                });
            }
            else {
                showEvents(cached_events, firstDay);
            }
        }

        function formatTime(date) {
            var str = date.getHours() % 13 + ":" + (date.getMinutes() === 0 ? "00" : date.getMinutes());
            str += date.getHours() > 11 ? " pm" : " am";
            return str;
        }
        function formatDate(date) {
            var str = MONTHES[date.getMonth()] + " " + date.getDate() + ", " + date.getFullYear();
            return str;
        }


        function makeDate(fbDate) {
            try {
                var a = fbDate.split(/[^0-9]/);
                return new Date(a[0], a[1] - 1, a[2], a[3], a[4], a[5]);
            }
            catch (e) {
                alert(typeof fbDate);
            }
        }

        function showEvents(data, firstDay) {
            $('.events').html('');
            var itemCount = 0;
            for (var index in data) {
                var time = makeDate(data[index]['start_time']);
                if (time.getMonth() === currentDate.getMonth() && time.getFullYear() === currentDate.getFullYear()) {
                    var text = data[index]['name'].trim();
                    if (data[index]['name'].length > 31)
                        text = text.substring(0, 28) + '...';
                    var newDiv = $('<div>').attr('class', 'event').html(text);
                    $("#cell" + (time.getDate() + firstDay)).find('.events').append(newDiv);
                    newDiv.click(function(id) {
                        return function() {
                            showEvent(id);
                        };
                    }(data[index]['id']));
                    itemCount++;
                }
            }

            if (itemCount === 0) {
                displayMessage("<?=lang('tsa_calendar_no_event')?>");
            }
            else {
                hideMessage();
            }
        }
        var dialogs = [];

        function showEvent(id) {
            if (dialogs.indexOf(id) >= 0)
                return;

            FB.api('/' + id + '?fields=description,start_time,location,venue,owner,id,name,picture,cover&access_token=' + ACCESS_TOKEN, function(response) {
                if (!response || response.error) {
                    fbError(response);
                    return;
                }
                var dialog = $("#dialog").clone(false);
                dialog.addClass("copy");
                dialog.removeAttr('id');
                dialog.find('.center-message').remove();
                
                
                var postLink = 'http://www.facebook.com/events/' + response.id;
                
                if(typeof(response.owner) !== 'undefined'){
                    var userLink = 'http://www.facebook.com/' + response.owner.id;
                    $('<a>').attr('class', 'text').attr('href', userLink).attr('target', '_blank').text(response.owner.name).appendTo(dialog.find('span.host'));
                }
                else{
                    $('<span>').attr('class', 'text').text('Unknown').appendTo(dialog.find('span.host'));
                }
                
                var date = makeDate(response['start_time']);
                dialog.find('span.time').html(formatTime(date));
                dialog.find('span.date').html(formatDate(date));
                dialog.find('span.location').html(response.location);
                var desc = makeAllLinksA(response.description).replace(/\n/g, '<br/>');
                var descSpan = $('<span>').addClass('description-text').html(desc);
                var descBody = dialog.find('.description .panel-body');
                if(typeof(response.picture) !== 'undefined' && typeof(response.picture.data) !== 'undefined'){
                    var container = $('<span>').addClass('cover-container');
                    var eventPic = $('<img>').attr('src',response.picture.data.url).css({float:'left', padding:'5px'}).appendTo(container);
                    if(typeof(response.cover) !== 'undefined'){
                        var zoom = $('<span>').addClass('glyphicon glyphicon-zoom-in').appendTo(container);
                        eventPic.addClass('event-picture');
                        $('<img>').addClass('event-cover').attr('src',response.cover.source).css({float:'left',height:100}).appendTo(container);
                    }
                    container.appendTo(descBody);
                }
                
                descBody.append(descSpan);
                dialog.find('.description .link a').attr('href', postLink).attr('target', '_blank');
                if(typeof(response.venue) !== 'undefined'){
                    if (typeof (response.venue.street) !== 'undefined') {
                        var address = response.venue.street + ', ' + response.venue.city;
                        +', ' + response.venue.state + ' ' + response.venue.zip;
                        dialog.find('span.address').html(address);
                        var button = dialog.find('.map-button');
                        button.find('.glyphicon').removeClass('glyphicon-collapse-up').addClass('glyphicon-collapse-down');
                        button.find('.map-label').html("<?=lang('tsa_calendar_show_map')?>");

                        button.click((function(dialog) {
                            var mapToggle = false;
                            var canva = null;
                            return function() {
                                if (mapToggle === null) {
                                    return;
                                }
                                var mapDiv = dialog.find(".map");
                                var button = dialog.find('.map-button');

                                if (!mapToggle) {
                                    mapDiv.css("display", "block");
                                    mapToggle = null;
                                    mapDiv.animate({height: 200}, 800,
                                            function() {
                                                button.find('.glyphicon').removeClass('glyphicon-collapse-down').addClass('glyphicon-collapse-up');
                                                button.find('.map-label').html("<?=lang('tsa_calendar_hide_map')?>");
                                                mapToggle = true;
                                            });
                                    if (canva === null) {
                                        canva = $('<div>').attr('class', 'map-canva').css({height: 200, 'z-index': '0'});
                                        mapDiv.append(canva);
                                        var latlng = new google.maps.LatLng(response.venue.latitude, response.venue.longitude);
                                        var mapOptions = {
                                            zoom: 12,
                                            center: latlng
                                        };
                                        var map = new google.maps.Map(canva.get(0), mapOptions);
                                        var marker = new google.maps.Marker({
                                            position: latlng,
                                            map: map,
                                            title: response.location
                                        });
                                    }
                                }
                                else {
                                    mapToggle = null;
                                    mapDiv.animate({height: 0}, 500, function() {
                                        mapDiv.css("display", "none");
                                        mapDiv.remove(".map-canva");
                                        button.find('.glyphicon').removeClass('glyphicon-collapse-up').addClass('glyphicon-collapse-down');
                                        button.find('.map-label').html("<?=lang('tsa_calendar_show_map')?>");
                                        mapToggle = false;
                                    });
                                }
                            };
                        })(dialog));
                    }
                }
                
                function closeHandle(id, dialog) {
                    return function() {
                        var index = dialogs.indexOf(id);
                        dialogs.splice(index, 1);
                        dialog.dialog("close");
                    };
                }

                var closeHandler = closeHandle(id, dialog);

                dialog.find("#close_btn").click(closeHandler);

                dialog.dialog({
                    dialogClass: "no-close",
                    height: 500,
                    width: 640,
                    title: response.name,
                    close: closeHandler
                });

                dialogs[dialogs.length] = id;
            });
        }

        $(document).ready(
                function() {
                    var today = new Date();
                    $("#month").text(MONTHES[today.getMonth()]);
                    $("#year").text(today.getFullYear());
                    displayCalendar(today.getFullYear(), today.getMonth(), today.getDate());

                    FB.init({
                        appId: APP_ID, // App ID
                        status: true, // check login status
                        cookie: false, // enable cookies to allow the server to access the session
                        xfbml: true  // parse XFBML
                    });

                    $("#centerMessage").css("display", "table");
                    FB.getLoginStatus(function(response) {
                        if (response.status !== 'connected') {
                            // the user isn't logged in to Facebook.
                            $("#centerMessage").css("display", "table");
                        }
                    });

                    FB.Event.subscribe('auth.authResponseChange', function(response) {
                        if (response.status === 'connected') {
                            ACCESS_TOKEN = response['authResponse']['accessToken'];
                            if (!initialized) {
                                initializeEvents();
                                initialized = true;
                            }
                        } else {
                            FB.login(function(){}, {'scope': 'user_events'});
                        }
                    });
                });
    </script>

    <style>
        html{
            font-family:Tahoma Geogria San-serif;
        }
        .button{
            display: inline;
            cursor:pointer;
        }
        .day{
            font-weight: bold;
        }
        .today{
            background-color:lightpink;
        }
        .errorMessage{
            color:red;
            text-align:right;
            font-size:12px;
            display:inline;
        }
        
        table{
            width: 800.5px;
            margin:0 auto;
            border-width: 0px;
            border-spacing: 0px;
            padding-left:2px;
            padding-right: 2px;


        }
        
        table, td{

        }
        
        table#calendar td:first-child,  table#calendar td:last-child{
            background-color: rgba(254,250,240,0.8);
        }
        table td{
            width:56px;
            height:97px;
            border-style:solid;
            border-color: #CCC;
            border-width:1px;
            
        }

        table#calendar td:hover{
            background-color:navajowhite;
        }

        #calendar{
            height: 500px;
            margin-bottom: 70px;
            z-index: 1;
            //border-width: 1px;
            //border-style: solid;
            opacity: 0.4;
        }

        #calendar_banner, #calendar_banner td{
            font-size: 18px;
            border-width: 0px;
            text-align: right;
            vertical-align:middle;
            height:30px;
            font-family: monospace, Tahoma,Georgia,Serif;
        }

        #jump_to_today_wrapper{
            width:580px;
            display: table-cell;
            text-align:left;
            vertical-align:middle;
        }
        #jump_to_today{
            font-size: 16px;
            color: white;
            width:110px;
            display:none;
        }

        #calendar .date{
            text-align: right;
            font-size:12px;
            margin-right:4px;
            color: grey;
        }
        .events{
            height:80%;
            overflow-y:scroll;
            width:107px;
            margin-left:3px;
        }
        .event{
            font-size: 12px;
            display:block;
            overflow-x:hidden;
            border-width:1px;
            border-style:solid;
            border-color:darkorange;
            text-align: center;
            background-color: peachpuff;
            border-radius: 4px;
            cursor: pointer;
            font-family:monospace;
            word-wrap:break-word;
        }
        #centerMessage{
            font-size: 40px;
            color: grey;
            position: absolute;
            display:none;
            width: 960px;
            height: 500px;
            z-index: 10;
            text-align: center;
        }
        #centerMessage .text{
            margin-top: auto;
            margin-bottom: auto;
            vertical-align: middle;
            display:table-cell;
            text-align:center;
        }

        .dialog .center-message{
            font-size: 40px;
            position: absolute;
            width: 98%;
            height: 50%;
            z-index: 10;
            text-align: center;
        }
        
        .dialog .center-message .message{
            position:relative;
            height:50%;
            top:80%;
        }
        
        #fbButton{
            margin-bottom: 10px;
        }

        #calendar_banner thread .button span{
            height:40px;
        }

        .date_panel .glyphicon{
            font-size: 20px;
        }

        .date_panel_wrapper{
            display:table-cell;
            width: auto;
        }

        .date_panel{
            display:table;
            vertical-align: middle;
            text-align:right;
            -webkit-user-select: none; /* Chrome/Safari */        
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* IE10+ */
        }

        .date_panel div{
            display:table-cell;
        }

        .date_display{
            vertical-align: middle;
        }

        #dialog{
            display:none;
        }

        .dialog{
            width: 90%;
            height:90%;
            overflow:hidden;
            margin:0 auto;
        }

        .content{
            height:88%;
            overflow:scroll;
        }

        .description{
            //overflow:scroll;
            margin-top:4px;
            background-color: white;
            margin-bottom: 4px;
            height:60%;

        }

        .description .panel-body{
            overflow:scroll;
            height:80%;
        }

        .dialog ul{
            margin-bottom: 0px;
        }

        .dialog .info-list span, .dialog .panel-heading span{
            font-size:14px;
            display:table-cell;
            vertical-align: middle;
            margin-right:5px;
        }

        .info-list span.glyphicon, .panel-heading span.glyphicon{
            font-size:18px;
            color: darkred;
            width:30px;
        }

        span.time{
            width: 80px;
        }

        div.place-wrapper{
            display:table-cell;
            width:auto;
        }

        div.place{
            display:table;
            width:auto
        }

        .place>span.locaton,.place>span.address{
            display:table-row;
        }

        .place>span.address{
            font-size:12px;
            color:grey;
        }

        .link span.glyphicon{
            font-size: 14px;
            width: 80px;
            color:#0E3E7E ;
        }
        
        .panel-heading>.link{
            width: 50px; 
        }

        .panel-heading .description-header{
            width: auto;
        }

        .panel-heading{
            display:table;
            width:100%;
        }

        button span{
            color:darkred;
        }

        .buttons{
            float:right;
            margin: 4px;
        }

        .location-info>.map-button{
            width: 120px;
            display:table-cell;
            cursor: pointer;
        }

        .info-list li{
            display:table;
            width:100%;
        }

        li.location>.map-outer{
            display:table-row;
        }

        .location-info{
            display: table-row;
        }

        .map-outer>.map-wrapper{
            display:block;
        }

        .map-item{
            display:none;
        }
        
        .cover-container:hover .event-cover{
            display:inline-block;
            padding:5px;
        }
        
        .cover-container{
            float:right;
        }
        
        .cover-container:hover .event-picture{
            display:none;
        }
        .event-cover{
            display:none;
        }
        
        .cover-container:hover .glyphicon-zoom-in{
            display:none;
        } 
        
        .cover-container .glyphicon-zoom-in{
            float:left;
            font-size: 20px;
            margin-left:-28px;
            margin-top:32px;
            color:white;
        }

    </style>

    <section id="main_inner">
        <?php echo fuel_var('body', ''); ?>
    </section>
    <!-- Cover Photo -->
    <div class="pagewidth">            
        <table id="calendar_banner">
            <thead>
                <tr>
                    <td>
                        <div id="jump_to_today_wrapper">
                            <div id="jump_to_today" class="button btn btn-danger">&rarr; Today</div>
                        </div>
                        <div class="date_panel_wrapper">
                            <div class="date_panel">
                                <div class="button btn-lg" id="prev_button"><span class="glyphicon glyphicon-circle-arrow-left"></span> </div> 
                                <div class="date_display"> <span id="month"> </span> <span id="year"></span> </div>
                                <div class="button btn-lg" id="next_button"><span class="glyphicon glyphicon-circle-arrow-right"></span></div>
                            </div>
                        </div>
                    </td>
                </tr>
            </thead>
        </table>
        <div id="calendar_div">
            <div id="centerMessage">
                <div class="text">
                    <div id="fbButton"><fb:login-button size="xlarge" scope="user_events" onlogin="initializeEvents();"></fb:login-button></div>
                    <div id="loginMessage"><?=lang('tsa_calendar_login')?></div>
                </div>
            </div>
            <table id="calendar">
                <tr>
                    <td id="cell1">
                        <div class="date">

                        </div>
                        <div class="events">

                        </div>
                    </td>
                    <td id="cell2">
                        <div class="date">

                        </div>
                        <div class="events">

                        </div>
                    </td>
                    <td id="cell3" >
                        <div class="date">

                        </div>
                        <div class="events">

                        </div>
                    </td>
                    <td id="cell4" >                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell5">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell6">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell7">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                </tr>
                <tr>
                    <td id="cell8">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell9">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell10">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell11">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell12">                           
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell13">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell14">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                </tr>
                <tr>
                    <td id="cell15">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell16">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell17">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell18">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell19">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell20">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell21">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                </tr>
                <tr>
                    <td id="cell22">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell23">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell24">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell25">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell26">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell27">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell28">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                </tr>
                <tr>
                    <td id="cell29">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell30">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell31">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell32">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell33">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell34">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                    <td id="cell35">                            
                        <div class="date">

                        </div>
                        <div class="events">

                        </div></td>
                </tr>
            </table>
        </div>
    </div>	
</div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<div id="dialog" class="dialog">
    <?php $this->load->view('_blocks/dialog_content') ?>
</div>

<?php $this->load->view('_blocks/footer') ?>