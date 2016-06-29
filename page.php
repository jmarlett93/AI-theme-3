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

$field = get_field('background_image');
$backgroundimage = $field['url'];
$title = "";
$article = "";
if (have_posts()): while (have_posts()) : the_post();
	$article = do_shortcode(wpautop(get_the_content()));
	$title = get_the_title();
endwhile; endif;
$content = <<<AI_HEREDOC_PAGE_MAIN

<style type="text/css">
#contentContainer.parallax-cover { 
	background-image:url($backgroundimage);
	background-attachment: fixed;
	background-position: top center;
	background-size: cover;
}
</style>

		<div id="contentContainer" class="clearfix parallax-cover">
            $post_hero
			<div class="gridContainer clearfix">
				<div id="main">
					<h1>$title</h1>
					<article>
						$article
					</article>
				</div>
			</div>
        </div>

AI_HEREDOC_PAGE_MAIN;

$response = array(
	"title" => $title . " | Austin Institute",
	"url" => get_the_permalink(),
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