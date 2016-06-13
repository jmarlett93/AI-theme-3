<?php

$isPipe = $_GET['ajaxpipe'];
if($isPipe):
header('Content-Type: application/json');
endif;

/*if ( !$isPipe ): 
header('Location: /research/media?mediapermalink=' . urlencode(get_permalink()));
endif;*/

setPostViews(get_the_ID());

$cat = null;
$the_content = null;
$the_title = null;
$html_media = null;
$the_author = null;
$the_author_link = null;
$permalink = null;
$permalink_encoded = null;
$the_time = null;

$nextpost = null;
$prevpost = null;

while ( have_posts() ) : the_post(); $cat = get_the_category($post->ID); $the_content = get_the_content(); $the_title=get_the_title(); $the_author = get_the_author(); $the_author_link = get_the_author_link(); $permalink = get_permalink(); $permalink_encoded = urlencode($permalink); $the_time = get_the_time('F j, Y');

if(get_field('media_type') == 'image') {
	$field = get_field('image_media');
	$html_media = '<img src="' . $field['url'] . '" />';
}
else if(get_field('media_type') == 'video_uploaded') {
	$field = get_field('video_media_cover');
	$html_media = '<video controls poster="' . $field['url'] . '">';
	$vmp4 = get_field('video_media_mp4');
	$vogg = get_field('video_media_ogg');
	if(!empty($vmp4)){
		$html_media .= '<source src="' . $vmp4['url'] . '" type="video/mp4" />';
	}
	else if(!empty($vogg)){
		$html_media .= '<source src="' . $vogg['url'] . '" type="video/ogg" />';
	}
	$html_media .= '</video>';
}
else if (get_field('media_type') == 'video_embedded') {
	$html_media = get_field('video_embedded_html');
}

$nextpost = get_next_post(true);
$prevpost = get_previous_post(true);

endwhile;

$referrer = urlencode(($_GET['ref'] ? "?ref=" . $_GET['ref'] : $_SERVER['HTTP_REFERER']));

//$social_share = get_template_part('socialBar');
	
$content = <<<AI_HEREDOC_SINGLEANIMATE_MAIN
		    
            <div class="media"> 
			$html_media
			</div>
			<div class="media_attr">
			<div class="info">Posted by: <strong>$the_author</strong> on <span class="">$the_time</span></div>
            <div class="description">$the_content</div>
            </div>	
				<div id= "social" class="media">
				<a href="http://twitter.com/share?url=$permalink_encoded&amp;text=$the_title" target="_blank" onclick="window.open(this.href,'','menubar=no,toolbar=no,width=800,height=300')">
                <div class="item">
                	<div class="icon" style="background-color:#4b9fe0;background-image:url('/images\/twitter_icn.svg');"></div>
                    <div class="count socialcount" data-source="twitter" data-url="$permalink_encoded">Tweet</div></div></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=$permalink" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;">
                <div class="item">
                	<div class="icon" style="background-color:#3b5998;background-image:url('/images/facebook_icn.svg');"></div>
                    	<div class="count socialcount" data-source="facebook" data-url="$permalink"></div>Share</div></a>
				<div>
        	
AI_HEREDOC_SINGLEANIMATE_MAIN;

global $response;

$response = array(
	"media_title" => $the_title . " | Austin Institute",
	"media_url" => $permalink,
	"main" => null,
	"mediaResult" => $content,
    "pageReady" => "\$(\"#m-global-header\").addClass(\"backgroundTransparent\");",
	);

if ( !$isPipe ):

$main;
ob_start();
get_template_part('category-media');
$main = ob_get_contents();
ob_end_clean();

echo $main;

else:

echo json_encode($response);

endif; ?>