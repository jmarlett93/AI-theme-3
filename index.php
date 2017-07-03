<?php

$isPipe = $_GET['ajaxpipe'];
if($isPipe):
header('Content-Type: application/json');
endif;

include 'tweets.php';

$researchandnewscol1 = '';

$mediacol = '';

$post_hero;
ob_start();
get_template_part('hero');
$post_hero = ob_get_contents();
ob_end_clean();

$cslug = get_category_by_slug( 'research' );
$argsc1 = array(
    'numberposts' => 9,
    'offset' => 0,
	'posts_per_page' => 9,
    'category__in' => array($cslug->term_id),
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true );
	
$recent_posts1 = query_posts( $argsc1, ARRAY_A );
if (have_posts()): while(have_posts()) : the_post(); $t = '<a href="' . get_the_permalink() . '"><li>' . get_the_title() . '</li></a>'; $researchandnewscol1 .= $t; endwhile; endif;
wp_reset_query();

$cmslug = get_category_by_slug( 'media' );
$argsm = array(
    'numberposts' => 12,
    'offset' => 0,
	'posts_per_page' => 12,
    'category__in' => array($cmslug->term_id),
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true );
	
$media_posts = query_posts( $argsm, ARRAY_A );
if (have_posts()): $i = 0; while(have_posts()) : the_post(); 
$fieldi = get_field('image_media'); 
$fieldv = get_field('video_media_cover'); 
$t = '<li' . ($i > 9 ? ' class="no-mobile"' : '') . '><a href="'
 . get_the_permalink() . '"><figure class="mediapreview" style="background-image: url('
  . (get_field('media_type') == 'image' ? $fieldi['url'] : $fieldv['url']) . ');"></figure></a></li>'; 

$mediacol .= $t; $i++; endwhile; endif;
wp_reset_query();

$cfmslug = get_category_by_slug('media');

//finish query
$argsfm = array(
    'numberposts' => 1,
   'category__in' => array($cfmslug->term_id),
   'tag' => 'featured-homepage-media-post'
);

$featured_media_iframe = '';
  
$featured_media_post = query_posts( $argsfm, ARRAY_A );

if(have_posts()): $i = 0; while(have_posts()) : the_post();
    $featured_media_iframe = get_field('video_embedded_html');
    $i++;
    endwhile; endif;
    wp_reset_query();

$content = <<<AI_HEREDOC_HOME_MAIN

	   $post_hero

        <div id="main" class="gridContainer clearfix">
        
          <div id="Media">
          	
				<div class="heading">
            		<div class="title">Media</div>
                	<a href="/research/media/" class="see-all-link">See All</a>
            	</div>
            	<div class="col" class="fluid">
                	<div class="mediaBox">
                    	<div class="mediacontainer">
                		<div class="media">
                        $featured_media_iframe
                    	</div>
                       </div>
                    </div>
                    
            		<ul id="mediaList">
                		$mediacol
                	</ul>
            	</div>
            </div>    
          
          <div id="News">
          	
			<div class="heading">
            	<div class="title">Research</div>
                <a href="/research/" class="see-all-link">See All</a>
            </div>
            <div class="col one" class="fluid">
            	<ul>
                	$researchandnewscol1
                </ul>
            </div>
          </div>
          
        </div>

AI_HEREDOC_HOME_MAIN;
?><?php 

if ( !$isPipe ): get_header(); echo $content; get_footer();

else: 

$response = array(
	"title" => "Austin Institute",
	"url" => "/",
	"main" => $content
	);
	
echo json_encode($response);

endif;

?>