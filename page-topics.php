<?php

$isPipe = $_GET['ajaxpipe'];
if($isPipe):
header('Content-Type: application/json');
endif;

$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id; 
$tags = get_tags( array('orderby' => 'count', 'order' => 'DESC', 'hide_empty' => 'false') );

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
$article_list_html = '';

foreach($tags as $tag):
if(!get_field('throw404', $tag->taxonomy . '_' . $tag->term_id)):
$field = get_field('hero_image', $tag->taxonomy . '_' . $tag->term_id);

$t = '<div class="category-result result visible" data-postid="' . $tag->term_id . '" data-category="research" data-title="' . $tag->name . '" data-heroimageurl="' . $field['url'] . '" data-permalink="' . get_tag_link($tag->term_id) . '"><a href="' . get_tag_link($tag->term_id) . '"><div class="title">' . $tag->name . '</div></a><div class="excerpt">' . $tag->description . '</div></div>';

$topic_list_html .= $t;
endif; endforeach; wp_reset_postdata();

$main = <<<AI_HEREDOC_TOPICS_MAIN

		$post_hero
        <div id="main" class="gridContainer clearfix categorymain noscroll">
        	<div class="col-one side-nav">
            	$side_nav
            </div>
            <div class="col-two resultscontainer">
            $topic_list_html
            </div>
        </div>

AI_HEREDOC_TOPICS_MAIN;

$response = array(
	"title" => "Topics | Austin Institute",
	"url" => get_permalink(),
	"main" => $main,
	"pageReady" => "\$(window).resize();ai.searchPage.setSearch();\$(\"footer\").addClass(\"hidden\");\$(\"#m-global-header\").addClass(\"backgroundTransparent\");",
	"pageLeave" => "\$(\"#s\").off(\"keyup\");\$(\"footer\").removeClass(\"hidden\");\$(\"#m-global-header\").removeClass(\"backgroundTransparent\");"
	);

?><?php 

if ( !$isPipe ):

get_header(); echo $response["main"]; echo "<script>ai.page.ready=" . json_encode($response["pageReady"]) . ";ai.page.leave=" . json_encode($response["pageLeave"]) . ";</script>"; get_footer();

else:

echo json_encode($response);

endif;

?>