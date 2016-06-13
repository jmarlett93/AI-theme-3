<?php 

$isPipe = $_GET['ajaxpipe'];
if($isPipe):
header('Content-Type: application/json');
endif;

$tag = get_term_by('slug', get_query_var('tag'), 'post_tag');

if(get_field('throw404', $taxonomy . '_' . $tag->term_id)): header('HTTP/1.0 404 not found'); return; endif;

$post_hero;
ob_start();
get_template_part('hero');
$post_hero = ob_get_contents();
ob_end_clean();

$title = explode(": ", get_the_archive_title(), 2); $title = $title[1];

$cslug = get_category_by_slug( 'research' );
$research_args = array(
	'posts_per_page'   => -1,
	'offset'           => 0,
	'orderby'          => 'post_date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'category__in' => array($cslug->term_id),
	'tag'			   => get_query_var('tag'),
	'post_type'        => 'post',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'post_status'      => 'publish',
	'suppress_filters' => true 
);
$event_args = array(
	'posts_per_page'   => -1,
	'offset'           => 0,
	'include'          => '',
	'exclude'          => '',
	'tag'			   => get_query_var('tag'),
	'meta_key'         => 'event_date',
	'orderby'          => 'meta_value_num',
	'order'            => 'ASC',
	'post_type'        => 'events',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'post_status'      => 'publish',
	'suppress_filters' => true 
);
$cmslug = get_category_by_slug( 'media' );
$media_args = array(
    'numberposts' => 12,
    'offset' => 0,
	'posts_per_page' => 12,
    'category__in' => array($cmslug->term_id),
	'tag'			   => get_query_var('tag'),
	//'category_name' => 'media',
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true );

$research_posts_array = get_posts( $research_args );
$event_posts_array = get_posts( $event_args );
$media_posts_array = query_posts( $media_args, ARRAY_A );

$research_posts_html = '';
$events_posts_html_old = '';
$events_posts_html_new = '';
$media_posts_html = '';

foreach($research_posts_array as $post): setup_postdata($post);
$field = get_field('hero_image');
$t = '<div class="category-result result visible" data-postid="' . get_the_ID() . '" data-category="research" data-title="' . get_the_title() . '" data-author="' . get_the_author() . '" data-heroimageurl="' . $field['url'] . '" data-permalink="' . get_permalink() . '"><a href="' . get_permalink() . '"><div class="title">' . get_the_title() . '</div></a><div class="attr"><div class="author">By: <a href="">' . get_the_author() . '</a></div><div class="date">' . get_the_time('F j, Y') . '</div></div><div class="excerpt">' . get_the_excerpt() . '</div></div>';
$research_posts_html .= $t;
endforeach; wp_reset_postdata();

foreach($event_posts_array as $post): setup_postdata($post);
$field = get_field('hero_image');
$d = DateTime::createFromFormat('Ymd', get_field('event_date'));
$td = new DateTime();
$t = '<div class="category-result result visible" data-postid="' . get_the_ID() . '" data-category="research" data-title="' . get_the_title() . '" data-author="' . get_the_author() . '" data-heroimageurl="' . $field['url'] . '" data-permalink="' . get_permalink() . '"><a href="' . get_permalink() . '"><div class="title">' . get_the_title() . '</div></a><div class="attr"><em>' . $d->format('F j, Y') . '</em></div><div class="excerpt">' . get_the_excerpt() . '</div></div>';
if($d > $td): $events_posts_html_new .= $t; else: $events_posts_html_old = $t . $events_posts_html_old; endif;
endforeach; wp_reset_postdata();

foreach($media_posts_array as $post): setup_postdata($post); 
$fieldi = get_field('image_media'); $fieldv = get_field('video_media_cover'); $t = '<li' . ($i > 3 ? ' class="no-mobile"' : '') . '><a href="' . get_the_permalink() . '"><figure class="mediapreview" style="background-image: url(' . (get_field('media_type') == 'image' ? $fieldi['url'] : $fieldv['url']) . ');"></figure></a></li>'; $media_posts_html .= $t; $i++;
endforeach; wp_reset_postdata();

if($events_posts_html_old != ''): $events_posts_html_old = "<div class=\"sectionHeading\"><div class=\"title\">Past Events</div></div>" . $events_posts_html_old; endif;
if($events_posts_html_new != ''): $events_posts_html_new = "<div class=\"sectionHeading\"><div class=\"title\">Upcoming Events</div></div>" . $events_posts_html_new; endif;
if($media_posts_html != ''): $media_posts_html = "<div class=\"sectionHeading\"><div class=\"title\">Media</div></div><ul id=\"mediaList\">" . $media_posts_html . "</ul>"; endif;

$content = <<<AI_HEREDOC_SINGLE_MAIN

		$post_hero
        <div id="main" class="gridContainer clearfix readermain">
        	<div class="col-two">
				$events_posts_html_new
				$events_posts_html_old
				$media_posts_html
        	</div>
        	<div class="col-one">
				<div class="sectionHeading"><div class="title">Research & News</div></div>
				$research_posts_html
        	</div>
        </div>

AI_HEREDOC_SINGLE_MAIN;

$response = array(
	"title" => $title . " | Austin Institute",
	"url" => get_tag_link($tag->term_id),
	"main" => $content,
	"pageReady" => "\$(\"#m-global-header\").addClass(\"backgroundTransparent\");",
	"pageLeave" => "\$(\"#m-global-header\").removeClass(\"backgroundTransparent\");"
	);

if ( !$isPipe ):

get_header(); echo $response["main"]; echo "<script>ai.page.ready=" . json_encode($response["pageReady"]) . ";ai.page.leave=" . json_encode($response["pageLeave"]) . ";</script>"; get_footer();

else:

echo json_encode($response);

endif;

?>