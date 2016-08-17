<?php

$cat = get_category(get_query_var('cat'));
$terms = get_queried_object();
$archiveTitle = '';
if(is_archive()): $t = explode(": ", get_the_archive_title(), 2); $archiveTitle = $t[1]; endif;
$post = $wp_query->post;

?>
<!doctype html>
<!--[if lt IE 7]> <html class="ie6 oldie"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldie"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="">
<!--<![endif]-->
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php if(is_home()) { echo "Austin Institute"; } else if(is_archive()) { echo $archiveTitle . " | Austin Institute"; } else { echo get_the_title() . " | Austin Institute"; } ?></title>
<link type="text/css" rel="stylesheet" href="/wp-content/plugins/simple-pull-quote/css/simple-pull-quote.css">
<link href="<?php bloginfo('template_directory'); ?>/resources/boilerplate.css" rel="stylesheet" type="text/css">
<link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" type="text/css">
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<meta name="description" content="<?php echo get_bloginfo('description'); ?>" />
<link rel="publisher" href="https://plus.google.com/112909995040199545184"/>
<meta property="og:title" content="<?php if(is_home()) { echo "Austin Institute"; } else if(is_category()) { echo $cat->name . " | Austin Institute"; } else { echo get_the_title() . " | Austin Institute"; } ?>"/>
<meta property="og:site_name" content="Austin Institute for the Study of Family and Culture"/>
<meta property="og:url" content="<?php if(is_home()) { echo home_url(); } else if(is_category()) { echo get_category_link($cat->term_id); } else if(is_page()) { echo get_permalink( $post->ID ); } else { echo get_permalink(); } ?>"/>
<meta property="og:description" content="<?php if(is_page()) { the_excerpt(); } else { echo get_bloginfo('description'); } ?>"/>
<meta property="og:image" content="<?php if(in_category('media', $post->ID)) { if(get_field("media_type", $post->ID) == "image") { $field = get_field("image_media", $post->ID); echo $field['url']; } else { $field = get_field("video_media_cover", $post->ID); echo $field['url']; } } 

else if(in_category('research', $post->ID) && get_field("hero_image", $cat->term_id)) { $field = get_field("hero_image", $cat->term_id); echo $field['url']; } 

else if(is_archive('events', $post->ID)&& get_field("hero_image", $cat->term-id)){$field = get_field("hero_image", $cat->term_id); echo $field['url'];} 

else { echo rtrim(home_url(),"/") . "/images/AI_logo.png"; } ?>" />
<meta name="twitter:card" content="<?php if(in_category('media', $post->ID)) { if(get_field("media_type", $post->ID) == "image") { echo "photo"; } else { echo "player"; } } else { echo "summary"; } ?>" />
<meta name="twitter:url" content="<?php if(is_home()) { echo home_url(); } else if(is_category()) { echo get_category_link($cat->term_id); } else if(is_page()) { echo get_permalink( $post->ID ); } else { echo get_permalink(); } ?>" />
<meta name="twitter:title" content="<?php if(is_home()) { echo "Austin Institute"; } else if(is_category()) { echo $cat->name . " | Austin Institute"; } else { echo get_the_title() . " | Austin Institute"; } ?>" />
<meta name="twitter:description" content="<?php if(is_page()) { the_excerpt(); } else { echo get_bloginfo('description'); } ?>" />
<meta name="twitter:image" content="<?php if(in_category('media', $post->ID)) { if(get_field("media_type", $post->ID) == "image") { $field = get_field("image_media", $post->ID); echo $field['url']; } else { $field = get_field("video_media_cover", $post->ID); echo $field['url']; } } else if(in_category('research', $post->ID) && get_field("hero_image", $cat->term_id)) { $field = get_field("hero_image", $cat->term_id); echo $field['url']; } else { echo rtrim(home_url(),"/") . "/images/AI_logo.png"; } ?>" />

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/resources/jquery-2.1.3.min.js"></script>
<script src="//use.typekit.net/imk1gxm.js"></script>
<script>try{Typekit.load();}catch(e){}</script>
<script src="<?php bloginfo('template_directory'); ?>/resources/jquery.history.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/resources/respond.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/resources/masonry.pkgd.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/resources/imagesloaded.pkgd.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/resources/jquery-noisy-json.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/resources/ai.js"></script>

<style type="text/css">
	#m-global-header {
		-webkit-transition: all 0.3s linear;
	    -moz-transition: all 0.3s linear;
    	-o-transition: all 0.3s linear;
    	transition: all 0.3s linear;
	}
	#m-global-header.backgroundTransparent {
		background-color: rgba(51,51,51,0);
		border-color: rgba(51,51,51,0);
	}
</style>
<?php wp_head(); ?>
</head>
<body>
	<header style="padding-bottom:0px;">
        <div id="m-global-header">
        	<div class="wrapper">
                <div id="m-global-header__logo"><a href="/"><div class="logo"></div></a></div>
                <div id="m-global-header__tagline">
                	 <a href="/"><div class="brand">Austin Institute</div> <div>For The Study Of Family & Culture</div> </a></div>
                
                <div id="m-global-header__nav">
                    <ul class="menu">
                    <li><a href="/about/">About</a></li>
                    <li><a href="/research/">Research</a></li>
                    <li><a href="/research/media/">Media</a></li>
                    <li><a href="/events/">Events</a></li>
                    <li><a href="/contact/">Contact</a></li>
                    <li><a href="/support/" class="donate">Donate</a></li>
                    </ul>
                    
                </div>
            </div>
        </div>
        <div id="m-global-headerOpacity"></div>
        
    </header>
    <main>