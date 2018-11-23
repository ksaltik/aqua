require(
    [
    "jquery",
    "owlcarousel",
    "FME_Productvideos/js/jwplayer78",
    
    ],
    function ($,owlcarousel, jwplayer78) {

    ////alert("Inside Function");
        if ($(".product_video_module")) {
            /*var player = $(".play2");//.find('[data-toggle="jwplayer"]');
                player.click(
                    function () {
                        //alert("//alert 1");
                    });
                 */

                var player = $(".thumb");
                player.click(
                    function () {
                    
                        document.getElementById("my_video").style.paddingBottom = "inertial";
                        var vid = $(this.children).attr('data-video');
                        var dif = $(this.children).attr('data-diff');
                        var thumbb = $(this.children).attr('data-thumbb');
                        var toggle = $(this.children).attr('data-toggle');
                        //data-toggle
                        if(toggle=="jwplayer")
                        {
                            playJwVideo(vid,thumbb);
                            showContent(this.children, dif);
                        }
                        else
                        {
                            playIframVideo(vid);
                            showContent(this.children, dif);
                        }
                       
                    }
                );

            /*window.addEventListener("load", this.videomedia, false);
            function videomedia()
            {
                //alert("sdfsdfdsfsdfs");

           
                var owl = $("#owl-demo");
                $('#owl-demo').owlCarousel();
                $('#owl-demo').owlCarousel(
                    {
                    // Most important owl features
                        items: 5,
                        itemsCustom: false,
                        itemsDesktop: [1199, 5],
                        itemsDesktopSmall: [980, 4],
                        itemsTablet: [768, 3],
                        itemsTabletSmall: false,
                        itemsMobile: [479, 2],
                        itemsMobile : [400, 1],
                        singleItem: false,
                        itemsScaleUp: false,
                    //Basic Speeds
                        slideSpeed: 200,
                        paginationSpeed: 800,
                        rewindSpeed: 1000,
                        //Autoplay
                        autoPlay: false,
                        stopOnHover: false,
                    // Navigation
                        navigation: true,
                        //navigationText : ["prev","next"],
                        navigationText: false,
                        rewindNav: true,
                        scrollPerPage: false,
                    // Responsive
                        responsive: true,
                        responsiveRefreshRate: 200,
                        responsiveBaseWidth: window,
                    //Mouse Events
                        dragBeforeAnimFinish: true,
                        mouseDrag: true,
                        touchDrag: true

                    }
                );
                var player = $(".body").find('[data-toggle="jwplayer"]');
                player.click(
                    function () {
                        //alert("//alert 1");
                        document.getElementById("my_video").style.paddingBottom = "inertial";
                        var vid = $(this).attr('data-video');
                        var dif = $(this).attr('data-diff');
                        var thumbb = $(this).attr('data-thumbb');
                        playJwVideo(vid,thumbb);
                        showContent(this, dif);
                    }
                );

                var iframe = $(".body").find('[data-toggle="iframe"]');
                iframe.click(
                    function () {
                        //alert("//alert iframe");
                        var vid = $(this).attr('data-video');
                        var dif = $(this).attr('data-diff');
                        playIframVideo(vid);
                        showContent(this, dif);

                    }
                );
        
            }*/
       
            function playJwVideo(video,thumbb)
            {
            

                var fileExt = video.split('.').pop();
                if (fileExt == 'flv' || fileExt == "FLV") {
                    document.getElementById("my_video").style.paddingBottom = "54%";
                    var playerVersion = swfobject.getFlashPlayerVersion();
                    var output = "You have Flash player " +
                    playerVersion.major + "." + playerVersion.minor + "." +
                    playerVersion.release + " installed";
                    if (playerVersion.major<12) {
                        document.getElementById("error").innerHTML = "Install Latest Version of flash player";
                    }

                    jwplayer("my_video").setup(
                        {
                            file: video,
                            height: '100%',
                            image: thumbb,
                            autostart: false,
                            width: '100%',
                            key: 'OHFCJtp7SHNY+TZDEWvuYnNf1uc1BaEfp6hkIg==',
                            stretching:'fill',

               
                
                        }
                    );
                } else {
                    document.getElementById("error").innerHTML = "";
                    playIframVideo(video);
              //      var html5;
             //   html5 = "<video controls preload=metadata width='100%' height='100%' poster="+thumbb+"><source src="+video+" type='video/"+fileExt+"'><p>Please use a modern browser to view this video.</p></video>"
              //  $("#my_video").html(html5);
                }
            
            }

            function playIframVideo(video)
            {
                document.getElementById("my_video").style.paddingBottom = "54%";
                var i_frame = document.createElement('iframe');
                i_frame.frameBorder = 0;
                i_frame.height = "100%";
                i_frame.width = "100%";
                i_frame.id = "fitvid0";
                i_frame.setAttribute("src", video);
                $("#my_video").html(i_frame);
                //$("#my_video").fitVids();

            }

            function showContent(content, dif)
            {
            
                var title = $(content).attr('data-title');
                var content = $(content).attr('data-content');
                $("#info").css("display", "block");
                $("#title").html(title);
                if (dif != '') {
                    $("#time_frame").html('Uploaded ' + dif + ' ago');
                } else {
                    $("#time_frame").html('Uploaded today');
                }

                $("#content").html(content);
                $('html,body').animate(
                    {
                        scrollTop: $("#tab-label-productvideos").offset().top},
                    'slow'
                );
            }

            $(window).bind(
                "load",
                function () {
                    //alert("//alert 2");
                    var to_play = $("#play1");
                    var dif = to_play.attr('data-diff');
                    var player = to_play.attr('data-toggle');
                    var vid = to_play.attr('data-video');
                    var thumbb = $(this).attr('data-thumbb');
                    if (player = 'iframe') {
                        playIframVideo(vid);
                    } else if (player = 'jwplayer') {
                        playJwVideo(vid,thumbb);
                    }

                    showContent(to_play, dif);
                }
            );
        }


    }
);