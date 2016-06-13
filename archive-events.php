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

$title = explode(": ", get_the_archive_title(), 2); $title = $title[1];

$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id; 
$cat = get_category(get_query_var('cat'));
$args = array(
	'posts_per_page'   => -1,
	'offset'           => 0,
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => 'event_date',
	'orderby'          => 'meta_value_num',
	'order'            => 'ASC',
	'post_type'        => 'events',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'post_status'      => 'publish',
	'suppress_filters' => true 
);
$posts_array = get_posts( $args );

$isPipe = $_GET['ajaxpipe'];

$field = get_field('hero_image', $taxonomy . '_' . $term_id);
$hero_image = $field['url'];
$category_name = $cat->name;
$category_id = $cat->cat_ID;
$article_list_html_old = '';
$article_list_html_new = '';

foreach($posts_array as $post): setup_postdata($post);
$field = get_field('hero_image');
$d = DateTime::createFromFormat('Ymd', get_field('event_date'));
$td = new DateTime();
$t = '<div class="category-result result visible" data-postid="' . get_the_ID() . '" data-category="research" data-title="' . get_the_title() . '" data-author="' . get_the_author() . '" data-heroimageurl="' . $field['url'] . '" data-permalink="' . get_permalink() . '"><div class="date">' . $d->format('F j, Y') . '</div><a href="' . get_permalink() . '"><div class="title">' . get_the_title() . '</div></a><div class="attr"><div class="author">By: <a href="">' . get_the_author() . '</a></div>' . '</div><div class="excerpt">' . get_the_excerpt() . '</div></div>';
if($d > $td): $article_list_html_new .= $t; else: $article_list_html_old = $t . $article_list_html_old; endif;
endforeach; wp_reset_postdata();
if($article_list_html_old != ''): $article_list_html_old = "<div class=\"sectionHeading\"><div class=\"title\">Past Events</div></div>" . $article_list_html_old; endif;
if($article_list_html_new != ''): $article_list_html_new = "<div class=\"sectionHeading\"><div class=\"title\">Upcoming Events</div></div>" . $article_list_html_new; endif;

$content = <<<AI_CATEGORYRESEARCH_MAIN

		$post_hero
        <div id="main" class="gridContainer clearfix categorymain noscroll">
        	<div class="col-one side-nav">
            	$side_nav
            </div>
            <div class="col-two resultscontainer">
            $article_list_html_new
			$article_list_html_old
            </div>
        </div>

AI_CATEGORYRESEARCH_MAIN;

$response = array(
	"title" => $title . " | Austin Institute",
	"url" => get_post_type_archive_link('events'),
	"main" => $content,
	"pageReady" => "\$(window).resize();ai.searchPage.setSearch();\$(\"footer\").addClass(\"hidden\");\$(\"#m-global-header\").addClass(\"backgroundTransparent\");",
	"pageLeave" => "\$(\"#s\").off(\"keyup\");\$(\"footer\").removeClass(\"hidden\");\$(\"#m-global-header\").removeClass(\"backgroundTransparent\");"
	);
	
if ( !$isPipe ):

get_header(); echo $response["main"]; echo "<script>ai.page.ready=" . json_encode($response["pageReady"]) . ";ai.page.leave=" . json_encode($response["pageLeave"]) . ";</script>"; get_footer();

else:

echo json_encode($response);

endif;

?>