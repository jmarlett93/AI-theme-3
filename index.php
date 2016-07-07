<?php

$isPipe = $_GET['ajaxpipe'];
if($isPipe):
header('Content-Type: application/json');
endif;

include 'tweets.php';

$researchandnewscol1 = '';

$mediacol = '';

$directorposts = '';

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
	//'category_name' => 'research',
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
	//'category_name' => 'media',
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true );
	
$media_posts = query_posts( $argsm, ARRAY_A );
if (have_posts()): $i = 0; while(have_posts()) : the_post(); $fieldi = get_field('image_media'); $fieldv = get_field('video_media_cover'); $t = '<li' . ($i > 9 ? ' class="no-mobile"' : '') . '><a href="' . get_the_permalink() . '"><figure class="mediapreview" style="background-image: url(' . (get_field('media_type') == 'image' ? $fieldi['url'] : $fieldv['url']) . ');"></figure></a></li>'; $mediacol .= $t; $i++; endwhile; endif;
wp_reset_query();

$argsd = array(
	'numberposts' => 3,
	 'offset' => 0,
	 'posts_per_page' => 3,
	 'tag' => 'director-pick',
	 'order' => 'DESC',
	 'post_type'=> 'post',
	 'post_status' => 'publish',
	 'suppress_filters' => true );
	 
$d_posts = get_posts($argsd, ARRAY_A);

$id = 0;

for($id = 0; $id < count($d_posts); $id++ ): setup_postdata($post);
	$directorposts .= '<div class="dir_post"><h1>' . get_the_title() . '</h1><div class="excerpt">' . get_the_excerpt() . '</div><a href="'. get_permalink($d_posts[$id]->ID) . '">read more...</a></div>' ;
endfor;

wp_reset_postdata();

$content = <<<AI_HEREDOC_HOME_MAIN

		$post_hero
       
        <div id="main" class="gridContainer clearfix">
        
        	<section class="director-picks">
				<h1>Director Picks and News</h1>
					<div class="flex-container">
        				$directorposts 
					</div>
        	</section>
        
          <div id="Media">
          	
				<div class="heading">
            		<div class="title">Media</div>
                	<a href="/research/media/" class="see-all-link">See All</a>
            	</div>
            	<div class="col" class="fluid">
                	<div class="mediaBox">
                    	<div class="mediacontainer">
                		<div class="media">
                    
                    	<iframe src="https://www.youtube.com/embed/cO1ifNaNABY?rel=0" frameborder="0" allowfullscreen></iframe>
                        
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