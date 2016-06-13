<?php 

$isPipe = $_GET['ajaxpipe'];
if($isPipe):
header('Content-Type: application/json');
endif;


$post_hero;
ob_start();
get_template_part('hero');
$post_hero = ob_get_contents();
ob_end_clean();

$side_nav;
ob_start();
get_template_part('sideNav');
$side_nav = ob_get_contents();
ob_end_clean();

//create media query

$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id; 
$cat = get_category_by_slug( 'media' );
$args = array(
	'posts_per_page'   => -1,
	'offset'           => 0,
	'category'         => '',
	'category_name'    => $cat->name,
	'orderby'          => 'post_date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'post',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'post_status'      => 'publish',
	'suppress_filters' => true 
);

//make array of media posts

$posts_array = get_posts( $args );

$previewposts = query_posts(array(
	'showposts' => 14,
	'orderby' => 'rand',
	'category_name' => $cat->name
));

$field = get_field('hero_image', $taxonomy . '_' . $term_id);
$hero_image = $field['url'];
$category_name = $cat->name;
$category_id = $cat->cat_ID;
$hero_html = '';
$grid_html = '';

$ppx = 0;
$ppy = 0;

//takes selected $previewposts and creates masonry objects

foreach($previewposts as $p):
 $t = '<div class="mpitem" data-pos-x=' . $ppx . ' data-pos-y=' . $ppy .'>' . get_the_title($p->ID) . '</div>';
 $hero_html .= $t;
 $ppx++; $ppx = $ppx%7; if($ppx == 0) {$ppy++;}
endforeach;

foreach($posts_array as $post): setup_postdata($post); 
	$mtype = get_field('media_type');
	$ratio = 1;
	if($mtype == "image") {
		$img = get_field('image_media');
		$imgattachment = wp_get_attachment_image_src($img['id']);
		$imgw = $imgattachment[1];
		$imgh = $imgattachment[2];
		$ratio = ($imgw / $imgh);
	}
	else {
		$ratio = (16 / 9);
	}
	$fieldi = get_field('image_media');
	$fieldv = get_field('video_media_cover');
	$t = '<div class="masonry-item'; /*if($mtype != "image") { $t .= ' w2"'; }*/ $t .= ' data-masonry-ratio="' . $ratio . '"><a href="' . get_permalink() . '"><img class="preview" src="'; if($mtype == "image") { $t .= $fieldi['url']; } else { $t .= $fieldv['url']; } $t .='" /></a></div>';
	$grid_html .= $t;
endforeach; wp_reset_postdata();

$main = <<<AI_CATEGORYANIMATES_MAIN


<style type="text/css">
#mpcontainer {
	position: absolute;
	left: 50%;
	width: 1442px;
	height: 416px;
	margin: 0px -721px;
}
.mpitem {
	position: absolute;
	min-width: 202px;
	height: 202px;
	margin: 2px;
}
</style>
		$post_hero
		
        <div id="main" class="gridContainer clearfix categorymain">
        	<div class="col-one side-nav">
            	$side_nav
            </div>
			
            	<div class="col-two resultscontainer">
            		<style type="text/css">
						.masonry-grid-sizer {
							width: 30.33%; }

						.masonry-gutter-sizer {
							width: 3%;	}
							
						.masonry-item {
							min-height:20px;
							width: 30.33%;
							margin-bottom: 26px;	}
						
						.masonry-item.w2 {
							height: 32px;
							width: 63.66%; }
							
						.masonry-item.w3 {
							height: 54px;
							width: 100%; }
							
						.masonry-item img.preview {
							border: 0px; }
							
						.masonry-item img.preview:hover {
							transition: all 0.2s ease;
							border: 4px solid #455478;
							border-radius: 2px;  }
                	</style>
					
					<div class="mediaBox"> 
						
					</div>
					
            		<div id="animates-grid-container">
                		<div class="masonry-grid-sizer"></div><div class="masonry-gutter-sizer"></div>
                		$grid_html
            			</div>
        			</div>
        	
		</div>

AI_CATEGORYANIMATES_MAIN;

/*global $response;*/

$singleResponse = null;

if(!$response):
$response = array(
	"title" => $cat->name . " | Austin Institute",
	"url" => get_category_link($cat->term_id),
	"main" => $main,
	"pageReady" => "ai.categoryMedia.init();ai.searchPage.setSearch();\$(window).resize();\$(\"#m-global-header\").addClass(\"backgroundTransparent\");",
    "pageLeave" => "\$(\"#s\").off(\"keyup\");\$(\"footer\").removeClass(\"hidden\");\$(\"#m-global-header\").removeClass(\"backgroundTransparent\");"
	);
	
else:
$singleResponse = $response;
$response["title"] = $cat->name . " | Austin Institute";
$response["url"] = get_category_link($cat->term_id);
$response["main"] = $main;
$response["pageReady"] .= "ai.categoryMedia.init();ai.searchPage.setSearch();\$(window).resize();\$(\"#m-global-header\").addClass(\"backgroundTransparent\");ai.page.title=" . json_encode($response["title"]) . ";ai.page.url=" . json_encode($response["url"]) . ";History.replaceState(History.getState(),'" . json_encode($response["title"]) . "','" . json_encode($response["url"]) . "');ai.deploy_Media.setContent(" . json_encode($singleResponse) . ");";
$response["pageLeave"] .= "\$(\"#s\").off(\"keyup\");\$(\"#m-global-header\").removeClass(\"backgroundTransparent\");";
endif;

if ( !$isPipe ):

get_header(); echo $response["main"]; echo "<script>ai.page.ready=" . json_encode($response["pageReady"]) . ";ai.page.leave=" . json_encode($response["pageLeave"]) . ";</script>"; get_footer();

else:

echo json_encode($response);

endif; ?>