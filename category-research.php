<?php

$isPipe = $_GET['ajaxpipe'];
if($isPipe):
header('Content-Type: application/json');
endif;

$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id; 
$cat = get_category(get_query_var('cat'));
$args = array(
	'posts_per_page'   => 15,
	'offset'           => 0,
	'category__in'     => array($cat->term_id),
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
$posts_array = get_posts( $args );

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

$field = get_field('hero_image', $taxonomy . '_' . $term_id);
$hero_image = $field['url'];
$category_name = $cat->name;
$category_id = $cat->cat_ID;
$article_list_html = '';

foreach($posts_array as $post): setup_postdata($post);
$field = get_field('hero_image');
$t = '<div class="category-result result visible" data-postid="' . get_the_ID() . '" data-category="research" data-title="' . get_the_title() . '" data-author="' . get_the_author() . '" data-heroimageurl="' . $field['url'] . '" data-permalink="' . get_permalink() . '"> <div class="date">' .get_the_time('F j, Y') . '</div><a href="' . get_permalink() . '"><div class="title">' . get_the_title() . '</div></a><div class="attr"><div class="author">By: <a href="">' . get_the_author() . '</a></div></div><div class="excerpt">' . get_the_excerpt() . '</div></div>';
$article_list_html .= $t;
endforeach; wp_reset_postdata();

$main = <<<AI_CATEGORYRESEARCH_MAIN

		$post_hero
        <div id="main" class="gridContainer clearfix categorymain noscroll">
        	<div class="col-one side-nav">
            	$side_nav
            </div>
            <div class="col-two resultscontainer">
            $article_list_html
            </div>
        </div>

AI_CATEGORYRESEARCH_MAIN;


$response = array(
	"title" => $cat->name . " | Austin Institute",
	"url" => get_category_link($cat->term_id),
	"main" => $main,
	"pageReady" => "\$(window).resize();ai.searchPage.setSearch();\$(\"#m-global-header\").addClass(\"backgroundTransparent\");",
	"pageLeave" => "\$(\"#s\").off(\"keyup\");\$(\"#m-global-header\").removeClass(\"backgroundTransparent\");"
	);

if ( !$isPipe ):

get_header(); echo $response["main"]; echo "<script>ai.page.ready=" . json_encode($response["pageReady"]) . ";ai.page.leave=" . json_encode($response["pageLeave"]) . ";</script>"; get_footer();

else:

echo json_encode($response);

endif;

?>