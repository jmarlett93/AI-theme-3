<?php 

$isPipe = $_GET['ajaxpipe'];
if($isPipe):
header('Content-Type: application/json');
endif;

setPostViews(get_the_ID());

$field = get_field('hero_image');
$hero_image = $field['url'];
$the_title = get_the_title();
$the_author = get_field('presenter');
$the_author_prepend = get_field('presenter_prepended_text');
$the_date = new DateTime(get_field('event_date'));
$the_time = (get_field('event_time') ? ' at <b>' . get_field('event_time') . '</b>' : '');

$post_hero;
ob_start();
get_template_part('hero');
$post_hero = ob_get_contents();
ob_end_clean();

$article = '';
$permalink = get_permalink();
$permalink_encoded = urlencode($permalink);
$pop_posts_html = '';
$tags = null;
$tags_html = '';

while ( have_posts() ) : the_post(); $article = do_shortcode(wpautop(get_the_content())); $the_author = get_the_author(); $tags= get_the_tags(); endwhile;
$categorybyslug = get_category_by_slug( 'research' );
$argspop = array(
    'numberposts' => 5,
	'posts_per_page' => 5,
    'category__in' => array($categorybyslug->term_id),
	'meta_key' => 'post_views_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true );
$pop_posts = query_posts( $argspop, ARRAY_A );
if (have_posts()): while(have_posts()) : the_post(); $t = '<a href="' . get_the_permalink() . '"><li><div class="linktitle">' . get_the_title() . '</div</li></a>'; $pop_posts_html .= $t; endwhile; endif;
wp_reset_query();

if($tags && count($tags) > 0) {
	foreach( $tags as $tag ) {
		if(get_field('throw404', $tag->taxonomy . '_' . $tag->term_id)) { break; }
		$t = '<a href="' . get_tag_link($tag->term_id) . '" title="' . $tag->name . '">' . $tag->name . '</a>';
		if($tags_html != '') { $tags_html .= ', '; }
		$tags_html .= $t;
	}
	if($tags_html != '') { $tags_html = '<i>FILED UNDER</i>: ' . $tags_html; }
}

$content = <<<AI_HEREDOC_SINGLE_MAIN

		$post_hero
        <div id="main" class="gridContainer clearfix readermain">
          <div class="col-two" style="margin-top: 10px;">
<div class="info-section">
            
            </div>
          	<div class="info-section">
            	<div class="title">Most Popular</div>
            	<ol class="article-list">$pop_posts_html</ol>
           </div>
          </div>
          <div class="col-one">
		  <section id="social-mobile" class="hide_tablet">
            	<div class="item"><a href="http://twitter.com/share?url=$permalink_encoded&amp;text=$the_title" target="_blank" onclick="window.open(this.href,'','menubar=no,toolbar=no,width=800,height=300')"><div class="icon" style="background-color:#4b9fe0;background-image:url('/images\/twitter_icn.svg');"></div><div class="count socialcount" data-source="twitter" data-url="$permalink_encoded">Tweet</div></a></div><div class="item"><a href="https://www.facebook.com/sharer/sharer.php?u=$permalink" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;"><div class="icon" style="background-color:#3b5998;background-image:url('/images/facebook_icn.svg');"></div><div class="count socialcount" data-source="facebook" data-url="$permalink">Share</div></a></div><div class="item"><a href="mailto:%20?subject=Austin Institute%20%7C%20$the_title&body=$permalink_encoded"><div class="icon" style="background-color:#339a63;background-image:url('/images\/email_icn.svg');"></div><div class="count">Email</div></a></div>
            </section>
          	<article>
            $article
            </article>
            <section id="social" class="no-mobile">
            	<a href="http://twitter.com/share?url=$permalink_encoded&amp;text=$the_title" target="_blank" onclick="window.open(this.href,'','menubar=no,toolbar=no,width=800,height=300')"><div class="item"><div class="icon" style="background-color:#4b9fe0;background-image:url('/images\/twitter_icn.svg');"></div><div class="count socialcount" data-source="twitter" data-url="$permalink_encoded">Tweet</div></div></a><a href="https://www.facebook.com/sharer/sharer.php?u=$permalink" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;"><div class="item"><div class="icon" style="background-color:#3b5998;background-image:url('/images/facebook_icn.svg');"></div><div class="count socialcount" data-source="facebook" data-url="$permalink"></div>Share</div></a><a href="mailto:%20?subject=Austin Institute%20%7C%20$the_title&body=$permalink_encoded"><div class="item"><div class="icon" style="background-color:#339a63;background-image:url('/images\/email_icn.svg');"></div><div class="count">Email</div></div></a>
            </section>
           </div>
        </div>

AI_HEREDOC_SINGLE_MAIN;

$response = array(
	"title" => get_the_title() . " | Austin Institute",
	"url" => get_the_permalink(),
	"main" => $content,
	"pageReady" => "\$(window).resize();ai.social.getCount();\$(\"#m-global-header\").addClass(\"backgroundTransparent\");",
	"pageLeave" => "\$(\"#m-global-header\").removeClass(\"backgroundTransparent\");"
	);

if ( !$isPipe ):

get_header(); echo $response["main"]; echo "<script>ai.page.ready=" . json_encode($response["pageReady"]) . ";ai.page.leave=" . json_encode($response["pageLeave"]) . ";</script>"; get_footer();

else:

echo json_encode($response);

endif;

?>