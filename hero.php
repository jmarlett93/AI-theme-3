<?php 

$hero_image = null;

if(have_posts() ) : while ( have_posts() ) : the_post();

$hero_image = get_field('hero_image');
$author_line = '';
if(is_singular('events')) {
	$the_date = new DateTime(get_field('event_date'));
	$the_time = (get_field('event_time') ? ' at <b>' . get_field('event_time') . '</b>' : null);
	$author_line = (get_field('presenter') || get_field('event_date') ? "<div class=\"postAuthor\">" . get_field('presenter_prepended_text') . " <b>" . get_field('presenter') . "</b><br />Date: <b>" . $the_date->format('F j, Y') . "</b>" . $the_time . "</div>" : "");
}
else if(is_single())
	$author_line = "<div class=\"postAuthor\">By: <b>" . get_the_author() . "</b> on <b>" . get_the_time('F j, Y') . "</b></div>";
else if(is_home()) {
	
}

endwhile;endif;

$hero_image = get_field('hero_image');
$cmslug = get_category_by_slug( 'media' );

if(is_home()): 

		$argsh = array(
			'numberposts'      => 4,
			'offset'           => 0,
			'orderby'          => 'date',
			'tag'              => 'hero',
			'post_type'        => array('events', 'post'),
			'post_status'      => 'publish',
			'suppress_filters' => true );
			
		$hero_posts = get_posts( $argsh);
		
		$herohtml = "";
		$heroi = 0;
		for($heroi = 0; $heroi < count($hero_posts); $heroi++):
			$num = "";
			if($heroi == 0): $num = "zero"; elseif($heroi == 1): $num = "one"; elseif($heroi == 2): $num = "two"; elseif($heroi == 3): $num = "three"; elseif($heroi == 4): $num = "four"; endif;
			if($heroi == 0): $herohtml .= '<div class="col zero">'; elseif($heroi == 1): $herohtml .= '<div class="col one">'; elseif($heroi == 2): $herohtml .= '<div class="col two">'; elseif($heroi == 4): $herohtml .= '<div class="col three">'; endif;
			$field = get_field('hero_image', $hero_posts[$heroi]->ID);
			$herohtml .= '<div class="hero-' . $num . ' hero-item"><a class="inner" href="' . get_permalink($hero_posts[$heroi]->ID) . '"><figure style="background-image:url(' . $field['url'] . ')"></figure><div class="footer">' . get_the_title($hero_posts[$heroi]) . '</div></a></div>';
			if($heroi == 0 || $heroi == 1 || $heroi == 3|| (count($hero_posts) == 1 && $heroi == 0) || (count($hero_posts) == 3 && $heroi == 2)): $herohtml .= '</div>'; endif;
		endfor; ?>
        
    	<div id="m-global-main__filler"></div>
        
        <div id="m-index-main__branding" class="clearfix"> 
            	<div class="infobox"> 
                	
                    <div class="box one">
        	            <div class="main"> From publishing new studies to highlighting thoughtful research, our vision is to create a more informed, intelligent conversation on popular and sensitive cultural topics.</div>
                	</div>
                </div> 
        </div>
        
        <div id="m-index-main__hero" class="clearfix">
			
			<?php echo($herohtml); ?>
			
        </div>

<?php return; elseif(is_archive() || in_category($cmslug->term_id) || is_page('topics')): 
		$queried_object = get_queried_object();
		$taxonomy = $queried_object->taxonomy;
		$term_id = $queried_object->term_id;
		$hero_image = null;
		$title = null;
		if(is_page()):
			$hero_image = get_field('background_image');
			$title = get_the_title();
		
		elseif(is_post_type_archive('events')):
			/*Queries events to display hero image for most recent event on event page*/
			$args = array(
    		'numberposts'   	   => -1,
			'offset'           => 0,
			'offset'           => 0,
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => 'event_date',
			'orderby'          => 'meta_value_num',
			'order'            => 'DEsSC',
			'post_type'        => 'events',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true );
			$recent_event = get_posts( $args);
			$hero_image = get_field('hero_image', $recent_event[0]);
			$title = explode(": ", get_the_archive_title(), 2); $title = $title[1];
			
		else:
			$hero_image = get_field('hero_image', $taxonomy . '_' . $term_id);
			$title = explode(": ", get_the_archive_title(), 2); $title = $title[1];
		endif;
?>
        <div id="m-archive-main__hero" class="parallax clearfix" data-origin-height="237" data-minHeight="#m-global-header">
        	<figure style="background-image:url(<?php echo $hero_image['url']; ?>)"></figure>
            <figure class="blur hide" style="background-image:url(<?php echo $hero_image['url']; ?>)"></figure>
            <div class="gridContainer clearfix"></div>
        </div>

<?php return; elseif(is_page()): ?>
        <div id="m-page-main__hero" class="clearfix" data-origin-height="237" data-minHeight="#m-global-header">
            <div class="gridContainer clearfix"></div>
        </div>
<?php return; else: ?>
        <div id="m-single-main__hero" class="parallax clearfix" data-origin-height="584" data-minHeight="#m-global-header">
        	<figure style="background-image:url(<?php echo $hero_image['url']; ?>)"></figure>
            <figure class="blur hide" style="background-image:url(<?php echo $hero_image['url']; ?>)"></figure>
            <div class="footer"><div class="gridContainer clearfix"><div class="postTitle"><?php echo get_the_title(); ?></div><?php echo $author_line; ?></div></div>
        </div>
<?php return; endif; ?>