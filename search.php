<?php
$search_query = array(
	'category__in'   => array(intval($_GET['category_id'])),
	's'          	  => $_GET['s']
);

$search = new WP_Query($search_query);

?>

<?php if ( $_GET['format'] == "media" ): ?>
<div class="masonry-grid-sizer"></div><div class="masonry-gutter-sizer"></div>
<?php endif; ?>
<?php if ( $search->have_posts() ):
	while ( $search->have_posts() ): $search->the_post(); ?>
    	<?php if( $_GET['format'] == "media" ):
			$mtype = get_field('media_type');
			$ratio = 1;
			if($mtype == "image") {
				$img = get_field('image_media');
				$imgw = wp_get_attachment_image_src($img['id'])[1];
				$imgh = wp_get_attachment_image_src($img['id'])[2];
				$ratio = ($imgw / $imgh);
			}
			else {
				$ratio = (16 / 9);
			}
		?>
		<div class="masonry-item<?php if($mtype != "image") { echo " w2"; } ?>" data-masonry-ratio="<?php echo $ratio; ?>"><a href="<?php echo the_permalink(); ?>"><img class="preview" src="<?php if($mtype == "image") { echo get_field('image_media')['url']; } else { echo get_field('video_media_cover')['url']; } ?>" /></a></div>
        <?php else: ?>
    			<div class="category-result result visible" data-postid="<?php the_ID(); ?>" data-category="research" data-title="<?php the_title(); ?>" data-author="<?php the_author(); ?>" data-heroimageurl="<?php echo get_field('hero_image')['url']; ?>" data-permalink="<?php the_permalink(); ?>">
                	<div class="title"><?php the_title(); ?></div>
                	<div class="attr">
                    	<div class="author">By: <a href=""><?php the_author(); ?></a></div><div class="date"><?php the_time('F j, Y'); ?></div>
                    </div>
                    <div class="excerpt"><?php the_excerpt(); ?></div>
                </div>
    	<?php endif; ?>
<?php endwhile; else: ?>
	
    			<div class="category-result nonresult searchstatus">No results found for: "<?php echo $_GET['s']; ?>."</div>
    
<?php endif; wp_reset_postdata(); ?>

