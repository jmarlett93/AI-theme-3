<?php

$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id; 
$tags = get_tags( array('orderby' => 'count', 'order' => 'DESC', 'hide_empty' => 'false') );

foreach($tags as $tag):

if(!get_field('throw404', $tag->taxonomy . '_' . $tag->term_id)):

$tag_link = get_tag_link($tag->term_id);

$topic_list_html .= '<li><a href="' . $tag_link . '">';

$topic_list_html .= $tag->name . '</a></li>';

endif; endforeach;
?>

                <div class="sectionHeading"><div class="title">Topics</div></div>
				<div class="wrapper">
					<ul id="exploreList">
                    
						<?php echo( $topic_list_html); ?>
					
                    </ul>
				</div>