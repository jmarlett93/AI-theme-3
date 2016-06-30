<?php 

function global_vars() {

    global $theme_vars;
    $theme_vars = array(
		array(
			'res' => 'ai.js',
			'var' => 'dimensions_js',
        	'val' => array( // dimension,value,min/max
				array("x",481,"min"),
				array("x",769,"min"),
			)
		),
		array(
			'res' => '../style.css',
			'var' => 'css_media_queries_0',
			'val' => 'media only screen and (min-width: 481px)'
		),
		array(
			'res' => '../style.css',
			'var' => 'css_media_queries_1',
			'val' => 'media only screen and (min-width: 769px)'
		)
    );

}

function setCookies() {
	if( !session_id() )
	{
		session_set_cookie_params(0);
		session_start();
	if(!isset($_COOKIE['p_fv'])) {
		setcookie('p_fv', date('Y-m-d H:i:s'), 2147483647, "/");		//first visit: perpetual: initialize to date time now
		setcookie('p_ml', 0, 2147483647, "/");					//is on mail list: perpetual: initialize to false
		setcookie('s_fv', 1, 0, "/");								//is first visit: session: set as true since p_fv was just set
	}
	elseif(!isset($_COOKIE['s_fv'])) {
		setcookie('s_fv', 0, 0, "/");	
	}
	if(!isset($_COOKIE['s_ml'])) {							//is first visit: session: set as true since p_fv was just set
		if($_COOKIE['p_ml'] == 0){
			setcookie('s_ml', 0, 0, "/");
		}
	}
	}
}
function register_session()
{
}
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 Views";
    }
	else if($count==1){
		return "1 View";
	}
    return $count.' Views';
}
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
function SearchFilter($query) {
    // If 's' request variable is set but empty
    if (isset($_GET['s']) && empty($_GET['s']) && $query->is_main_query()){
        $query->is_search = true;
        $query->is_home = false;
    }
    return $query;
}
function new_excerpt_more( $more ) {
	return ' ...';
}
function get_custom_cat_template($single_template) {
   global $post;
   if ( in_category( 'media' )) {
      $single_template = dirname( __FILE__ ) . '/single-media.php';
   }
   return $single_template;
} 
function is_ajax() {
	if( ! empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) == 'xmlhttprequest' )
    	return true;
	else
		return false;
}

function create_post_type() {
  register_post_type( 'events',
    array(
      'labels' => array(
        'name' => __( 'Events' ),
        'singular_name' => __( 'Event' )
      ),
      'public' => true,
      'has_archive' => true,
	  'can_export' => true,
	  'taxonomies' => array('post_tag')
    )
  );
}

function page_hook(){
	do_action('page_hook'); }//custom hook for modifying static page behaviour
	
function modify_page_display(){
	if(is_page('support')) {
	 echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input name="cmd" type="hidden" value="_s-xclick" /><input name="hosted_button_id" type="hidden" value="WLGQQHFAWY226" /><input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" type="image" /><img src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /></form>'; } 	
	}

add_action('page_hook','modify_page_display');
add_action( 'parse_query', 'global_vars' );
add_filter('init', 'setCookies');
add_action('init', 'register_session');
add_filter('pre_get_posts', 'SearchFilter');
add_filter('excerpt_more', 'new_excerpt_more');
add_filter('single_template', 'get_custom_cat_template');
add_action( 'init', 'create_post_type' );
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

?>