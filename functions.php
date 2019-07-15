<?php

require_once 'modules/cpt.php';

add_action('wp_enqueue_scripts', 'rsm_enqueue_script');
function rsm_enqueue_script() {
	wp_localize_script( 'jquery', 'ajax_url', admin_url('admin-ajax.php') );
	wp_enqueue_script('jquery');
	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/css/style.css');
	wp_enqueue_style('child-slider', get_stylesheet_directory_uri() . '/css/flickity.css');

	wp_enqueue_script( 'child-sliderjs',  get_stylesheet_directory_uri().'/js/flickity.pkgd.min.js', array('jquery'));
}

add_action('wp_ajax_rsm_save_image', 'rsm_upload_image');
// add_action('wp_ajax_nopriv_rsm_save_image', 'rsm_upload_image');

function rsm_upload_image() {
	if ( empty($_POST['imgBase64']) ) {
		echo 'wrong data';
		return;
	}

	$img = $_POST['imgBase64'];
	$img = str_replace('data:image/jpeg;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);

	$file_name = uniqid() . '.jpg';

	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['basedir']. '/merged/';
	$upload_url = $upload_dir['baseurl'];

	if (!is_dir($upload_path)) {
	  // dir doesn't exist, make it
	  mkdir($upload_path);
	}

	$success = file_put_contents($upload_path . $file_name, $data);
	$img_url = $upload_url . '/merged/' . $file_name;
	if ($success) {
		wp_send_json(array('status' => 'success', 'data' => $img_url));
	} else {
		wp_send_json(array('status' => 'fail', 'data' => $success));
	}
}

add_filter('redirect_canonical','pif_disable_redirect_canonical');

function pif_disable_redirect_canonical($redirect_url) {
    if (is_singular()) $redirect_url = false;
return $redirect_url;
}

function rsm_get_gallery_items($artist_id, $search_params) {	
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	 $no_of_item = 9;
	if(is_singular('product')){
		$no_of_item = 8;
	}		
	if(is_page_template('new-custom-template.php')){
		$args = array(
		'posts_per_page' =>$no_of_item,
        'paged' => $paged,
		'post_type' => 'product',		
	   );
	}else{
		$args = array(
		'posts_per_page' =>$no_of_item,
        'paged' => $paged,
		'post_type' => 'product',		
		'meta_query' => array(			
			array(
				'key' => 'artist',
				'value' => $artist_id,
				'compare' => '='
			),
		)
	   );		
	}
	
	
	//WAYS TO BUY
	/*if( $search_params['auction_sell'] == 'yes' ){
		array_push( $args['meta_query'], array(	'key' => 'allow_auction_sell', 'value' => 'yes', 'compare' => '=' ) );	
	}
	if( $search_params['direct_sell'] == 'yes' ){
		array_push( $args['meta_query'], array(	'key' => 'allow_direct_sell', 'value' => 'yes', 'compare' => '=' ) );	
	}
	if( $search_params['contact_gallery'] == 'yes' ){
		array_push( $args['meta_query'], array(	'key' => 'selled_via_admin', 'value' => 'yes', 'compare' => '=' ) );	
	}*/
	//WAYS TO BUY
	if( $search_params['auction_sell'] == 'yes' OR $search_params['direct_sell'] == 'yes' OR $search_params['contact_gallery'] == 'yes' ){
		$array_count_ways = array_push( $args['meta_query'], array( 'relation' => 'OR' ) );	
	}
		
	if( $search_params['auction_sell'] == 'yes' ){
		array_push( $args['meta_query'][$array_count_ways-1], array( 'key' => 'allow_auction_sell', 'value' => 'yes', 'compare' => '=' ) );	
	}
	if( $search_params['direct_sell'] == 'yes' ){
		array_push( $args['meta_query'][$array_count_ways-1], array( 'key' => 'allow_direct_sell', 'value' => 'yes', 'compare' => '=' ) );	
	}
	if( $search_params['contact_gallery'] == 'yes' ){
		array_push( $args['meta_query'][$array_count_ways-1], array( 'key' => 'selled_via_admin', 'value' => 'yes', 'compare' => '=' ) );	
	}
	
	
	
	//CATEGORIES aka art work types
	if( $search_params['search_by_category'] == 'yes' ){
		$args['tax_query'] = array(
		  array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => array() )
		);
		
		//cats
		if( $search_params['drawing'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'drawing' );			
		}
		if( $search_params['other'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'other' );
		}
		if( $search_params['painting'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'painting' );
		}
		if( $search_params['photography'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'photography' ); 
		}
		if( $search_params['prints'] == 'yes' ){
			array_push( $args['tax_query']['terms'], 'prints' );
		}
		if( $search_params['sculpture'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'sculpture' );
		}
		if( $search_params['work-on-paper'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'work-on-paper' );
		}
	}
	
	//PRICE aka art work types
	if( !empty($search_params['price_from']) ){
		array_push( $args['meta_query'], array(	'key' => '_price', 'value' => $search_params['price_from'], 'type' => 'numeric', 'compare' => '>='  ) );
	}
	if( !empty($search_params['price_to']) ){
		array_push( $args['meta_query'], array(	'key' => '_price', 'value' => $search_params['price_to'], 'type' => 'numeric', 'compare' => '<=' ) );
	}
	
	//TIME	
	if( $search_params['add_array_for_time'] == "yes"){
		$array_count = array_push( $args['meta_query'], array( 'relation' => 'OR' ) );	
	}
	if($array_count){
		if( !empty($search_params['time_p_newer']) ){			
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => 'NEWER', 'compare' => '=') );
		}
		if( !empty($search_params['time_p_earlier']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => 'EARLIER', 'compare' => '=' ) );
		}
		if( !empty($search_params['time_p_1940']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1940', 'compare' => '=' ) );
		}
		if( !empty($search_params['time_p_1950']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1950', 'compare' => '=' ) ); 
		}
		if( !empty($search_params['time_p_1960']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1960', 'compare' => '=' ) );
		}	
		if( !empty($search_params['time_p_1970']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1970', 'compare' => '=' ) );
		}
		if( !empty($search_params['time_p_1980']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1980', 'compare' => '=' ) );
		}
		if( !empty($search_params['time_p_1990']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1990', 'compare' => '=' ) );
		}
	}
	
	//ORDER BY
	//alfbet
	if( $search_params['order_by'] == 'alfbet' ){
		$args['orderby'] = 'title';
		$args['order'] = 'ASC';
	}
	//published_at
	if( $search_params['order_by'] == 'published_at' ){		
		$args['orderby'] = 'date';
		$args['order'] = 'DESC';
	}
	//year_desc
	if( $search_params['order_by'] == 'year_desc' ){
		$args['meta_key']   = 'years_from';
		$args['orderby'] = 'meta_value_num';
		$args['order'] = 'DESC';	
	}
	//year_asc
	if( $search_params['order_by'] == 'year_asc' ){
		$args['meta_key']   = 'years_from';
		$args['orderby'] = 'meta_value_num';
		$args['order'] = 'ASC';
	}	
	//var_dump( $args );
	
	return new WP_Query($args);
	
}


function rsm_get_auctions_items( $search_params ) {	
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		
	$args = array(
		'posts_per_page' => 9,
		'post_type' => 'product',
		'paged' => $paged,	
		'meta_query' => array(
			array(
				'key' => 'artist',
				'value' => '987654321987654321',
				'compare' => '!='
			),
		)
	);
	
	//WAYS TO BUY
	if( $search_params['auction_sell'] == 'yes' OR $search_params['direct_sell'] == 'yes' OR $search_params['contact_gallery'] == 'yes' ){
		$array_count_ways = array_push( $args['meta_query'], array( 'relation' => 'OR' ) );	
	}
		
	if( $search_params['auction_sell'] == 'yes' ){
		array_push( $args['meta_query'][$array_count_ways-1], array(	'key' => 'allow_auction_sell', 'value' => 'yes', 'compare' => '=' ) );	
	}
	if( $search_params['direct_sell'] == 'yes' ){
		array_push( $args['meta_query'][$array_count_ways-1], array(	'key' => 'allow_direct_sell', 'value' => 'yes', 'compare' => '=' ) );	
	}
	if( $search_params['contact_gallery'] == 'yes' ){
		array_push( $args['meta_query'][$array_count_ways-1], array(	'key' => 'selled_via_admin', 'value' => 'yes', 'compare' => '=' ) );	
	}
	
	//CATEGORIES aka art work types
	if( $search_params['search_by_category'] == 'yes' ){
		$args['tax_query'] = array(
		  array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => array() )
		);
		
		//cats
		if( $search_params['drawing'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'drawing' );			
		}
		if( $search_params['other'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'other' );
		}
		if( $search_params['painting'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'painting' );
		}
		if( $search_params['photography'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'photography' ); 
		}
		if( $search_params['prints'] == 'yes' ){
			array_push( $args['tax_query']['terms'], 'prints' );
		}
		if( $search_params['sculpture'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'sculpture' );
		}
		if( $search_params['work-on-paper'] == 'yes' ){
			array_push( $args['tax_query'][0]['terms'], 'work-on-paper' );
		}
	}	
	
	//PRICE aka art work types
	if( !empty($search_params['price_from']) ){
		array_push( $args['meta_query'], array(	'key' => '_price', 'value' => $search_params['price_from'], 'type' => 'numeric', 'compare' => '>='  ) );
	}
	if( !empty($search_params['price_to']) ){
		array_push( $args['meta_query'], array(	'key' => '_price', 'value' => $search_params['price_to'], 'type' => 'numeric', 'compare' => '<=' ) );
	}
	
	//TIME	
	if( $search_params['add_array_for_time'] == "yes"){
		$array_count = array_push( $args['meta_query'], array( 'relation' => 'OR' ) );	
	}
	if($array_count){
		if( !empty($search_params['time_p_newer']) ){			
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => 'NEWER', 'compare' => '=') );
		}
		if( !empty($search_params['time_p_earlier']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => 'EARLIER', 'compare' => '=' ) );
		}
		if( !empty($search_params['time_p_1940']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1940', 'compare' => '=' ) );
		}
		if( !empty($search_params['time_p_1950']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1950', 'compare' => '=' ) ); 
		}
		if( !empty($search_params['time_p_1960']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1960', 'compare' => '=' ) );
		}	
		if( !empty($search_params['time_p_1970']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1970', 'compare' => '=' ) );
		}
		if( !empty($search_params['time_p_1980']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1980', 'compare' => '=' ) );
		}
		if( !empty($search_params['time_p_1990']) ){
			array_push( $args['meta_query'][$array_count-1], array( 'key' => 'time_period', 'value' => '1990', 'compare' => '=' ) );
		}
	}
	
	//ORDER BY
	//alfbet
	if( $search_params['order_by'] == 'alfbet' ){
		$args['orderby'] = 'title';
		$args['order'] = 'ASC';
	}
	//published_at
	if( $search_params['order_by'] == 'published_at' ){		
		$args['orderby'] = 'date';
		$args['order'] = 'DESC';
	}
	//year_desc
	if( $search_params['order_by'] == 'year_desc' ){
		$args['meta_key']   = 'years_from';
		$args['orderby'] = 'meta_value_num';
		$args['order'] = 'DESC';	
	}
	//year_asc
	if( $search_params['order_by'] == 'year_asc' ){
		$args['meta_key']   = 'years_from';
		$args['orderby'] = 'meta_value_num';
		$args['order'] = 'ASC';
	}	
	//var_dump( $args );
	
	return new WP_Query($args);	
}


function rsm_get_product_image($artist_id) {
	$args = array(
		'post_type' => 'product',
		'posts_per_page'         => '10',
		'meta_query' => array(
			array(
			'key' => 'artist',
			'value' => $artist_id,
			'compare' => '='
			)
		)
	);

	return new WP_Query($args);
}

//change add to cart to buy now
add_filter( 'woocommerce_product_single_add_to_cart_text', 'bbloomer_custom_add_cart_button_single_product' ); 
function bbloomer_custom_add_cart_button_single_product( $label ) {
    
   $label = __('Buy now', 'woocommerce');
    
   return $label;
 
}


function remove_image_zoom_support() {
    remove_theme_support( 'wc-product-gallery-zoom' );
	//remove_theme_support( 'wc-product-gallery-lightbox' );
	//remove_theme_support( 'wc-product-gallery-slider' ); 
}
add_action( 'wp', 'remove_image_zoom_support', 100 );



add_action( 'wp_enqueue_scripts', 'update_woo_js', 99 );
function update_woo_js(){                
	wp_deregister_script('photoswipe-ui-default');	
	 
	wp_register_script( 'photoswipe1', get_stylesheet_directory_uri() . '/woocommerce/assets/js/photoswipe.min.js' );
	wp_register_script( 'photoswipe2', get_stylesheet_directory_uri() . '/woocommerce/assets/js/photoswipe-ui-default.min.js' );

	wp_enqueue_script('photoswipe1');
	wp_enqueue_script('photoswipe2');	
}

/*----REGISTER POST TYPE FOR EXHIBITIONS-----*/
//we can consider it just collection of artworks
//and some metadata such as start/end date, place, description and etc
add_action('init', 'add_exhibitions_post_type');
function add_exhibitions_post_type(){
	register_post_type('Exhibition', array(
		'labels'             => array(
			'name'               => 'Exhibitions', 
			'singular_name'      => 'Exhibition', 
			'add_new'            => 'Add new',
			'add_new_item'       => 'Add new exhibition',
			'edit_item'          => 'Edit exhibition',
			'new_item'           => 'New exhibition',
			'view_item'          => 'View exhibition',
			'search_items'       => 'Search for exhibition',
			'not_found'          => 'No exhibitions found',
			'not_found_in_trash' => 'No exhibitions in the trash',
			'parent_item_colon'  => '',
			'menu_name'          => 'Exhibitions'

		  ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array('title','editor','author','thumbnail','excerpt','comments')
	) );
}
/*----REGISTER POST TYPE FOR EXHIBITIONS-----*/


add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
function woo_rename_tabs( $tabs ) {

	$tabs['description']['title'] = __( 'About the Work' );		// Rename the description tab
	$tabs['additional_information']['title'] = __( 'Additional Information' );	// Rename the additional information tab

	return $tabs;

}

add_action('woocommerce_after_single_product_summary', 'artist_other_product', 10);
function artist_other_product(){
	?>
				<div class="entry-content clearfix" itemprop="text">
		<div class="row artwork-gallery">
			  
				<form method="GET">				
					<h5><?php _e('Oder by:', 'woo-au'); ?></h5>
					<div>
						<select name="order_by">
							<option <?php if( $_GET['order_by'] == 'alfbet' ){ echo 'selected=""'; } ?>  selected="" value="alfbet"><?php _e('Default', 'woo-au'); ?></option>
							<option <?php if( $_GET['order_by'] == 'published_at' ){ echo 'selected=""'; } ?> value="published_at"><?php _e('Recently added', 'woo-au'); ?></option>
							<option <?php if( $_GET['order_by'] == 'year_desc' ){ echo 'selected=""'; } ?> value="year_desc"><?php _e('Artwork year (desc.)', 'woo-au'); ?> </option>
							<option <?php if( $_GET['order_by'] == 'year_asc' ){ echo 'selected=""'; } ?> value="year_asc"><?php _e('Artwork year (asc.)', 'woo-au'); ?></option>
						</select>
					</div>
				
					<h5><?php _e('Ways to buy:', 'woo-au'); ?></h5>
					<div>
					  <input type="checkbox" id="auction" name="auction" <?php if($_GET['auction'] == 'on'){ echo "checked"; } ?> >
					  <label for="auction"><?php _e('Auction', 'woo-au'); ?></label>
					</div>
					<div>
					  <input type="checkbox" id="direct_sell" name="direct_sell" <?php if($_GET['direct_sell'] == 'on'){ echo "checked"; } ?> >
					  <label for="direct_sell"><?php _e('Direct sell', 'woo-au'); ?></label>
					</div>
					<div>
					  <input type="checkbox" id="contact_gallery" name="contact_gallery" <?php if($_GET['contact_gallery'] == 'on'){ echo "checked"; } ?> >
					  <label for="contact_gallery"><?php _e('Contact gallery', 'woo-au'); ?></label>
					</div>
					
					<h5><?php _e('Work type:', 'woo-au'); ?></h5>						
					<?php						
						$categories = get_categories( array(
							'taxonomy'     => 'product_cat',
							'type'         => 'post',
							'child_of'     => 0,
							'parent'       => '',
							'orderby'      => 'name',
							'order'        => 'ASC',
							'hide_empty'   => 0,
							'hierarchical' => 1,
							'exclude'      => '',
							'include'      => '',
							'number'       => 0,
							'pad_counts'   => false,
						) );
						
						foreach ($categories as $category) {
							
							$option = '<div>';
							if($_GET[$category->slug] == 'on'){ 
								$option .= '<input type="checkbox" id="'.$category->slug.'" name="'.$category->slug.'" checked >';
							}else{
								$option .= '<input type="checkbox" id="'.$category->slug.'" name="'.$category->slug.'" >';
							}
															
							$option .= '<label for="'.$category->slug.'">' . $category->cat_name .' ('.$category->category_count.')</label>';
							$option .= '</div>';
	
							echo $option;
						}
					?>
					
					<h5><?php _e('Time period:', 'woo-au'); ?></h5>
					<div>
						<div>
							<label for="time_p_newer"><?php _e('Newer', 'woo-au'); ?></label>
							<input type="checkbox" id="time_p_newer" name="time_p_newer" <?php if($_GET['time_p_newer'] == 'on'){ echo "checked"; } ?> >
						</div>
												
						<div>
							<label for="time_p_1990"><?php _e('1990s', 'woo-au'); ?></label>
							<input type="checkbox" id="time_p_1990" name="time_p_1990" <?php if($_GET['time_p_1990'] == 'on'){ echo "checked"; } ?> >
						</div>
						
						<div>
							<label for="time_p_1980"><?php _e('1980s', 'woo-au'); ?></label>
							<input type="checkbox" id="time_p_1980" name="time_p_1980" <?php if($_GET['time_p_1980'] == 'on'){ echo "checked"; } ?> >
						</div>
						
						<div>
							<label for="time_p_1970"><?php _e('1970s', 'woo-au'); ?></label>
							<input type="checkbox" id="time_p_1970" name="time_p_1970" <?php if($_GET['time_p_1970'] == 'on'){ echo "checked"; } ?> >
						</div>
						
						<div>
							<label for="time_p_1960"><?php _e('1960s', 'woo-au'); ?></label>
							<input type="checkbox" id="time_p_1960" name="time_p_1960" <?php if($_GET['time_p_1960'] == 'on'){ echo "checked"; } ?> >
						</div>
						
						<div>
							<label for="time_p_1950"><?php _e('1950s', 'woo-au'); ?></label>
							<input type="checkbox" id="time_p_1950" name="time_p_1950" <?php if($_GET['time_p_1950'] == 'on'){ echo "checked"; } ?> >
						</div>
						
						<div>
							<label for="time_p_1940"><?php _e('1940s', 'woo-au'); ?></label>
							<input type="checkbox" id="time_p_1940" name="time_p_1940" <?php if($_GET['time_p_1940'] == 'on'){ echo "checked"; } ?> >
						</div>
						
						<div>
							<label for="time_p_earlier"><?php _e('Earlier', 'woo-au'); ?></label>
							<input type="checkbox" id="time_p_earlier" name="time_p_earlier" <?php if($_GET['time_p_earlier'] == 'on'){ echo "checked"; } ?> >	
						</div>							
					</div>
					
					
					<h5><?php _e('Price:', 'woo-au'); ?></h5>
					<div>
						<label for="price_from"><?php _e('Price from', 'woo-au'); ?></label>
						<input type="number" id="price_from" name="price_from" placeholder="1" value="<?php echo $_GET['price_from']; ?>">
						
						<label for="price_to"><?php _e('Price to', 'woo-au'); ?></label>
						<input type="number" id="price_to" name="price_to" placeholder="99999" value="<?php echo $_GET['price_to']; ?>">
					</div>
					
					
					<button type="submit" value=""><?php _e('Filter', 'woo-a'); ?></button>
					<input type="hidden" id="search_filters" name="search_filters" value="yes">
				</form>
			</div>
			<div class="row">
				
			<?php 			
				$artist_id = get_field('artist',get_the_ID());
				$query =  rsm_get_gallery_items($artist_id->ID);
				?>
				<h3 class="text-center" style="    text-align: center !important;">Other artwork by <?php echo get_the_title($artist_id->ID); ?></h3>
				<?php
					$count = 0;
				if($query->have_posts()){ 					
					while (	$query->have_posts() ) {
						if($count%4==0){ echo '</div><div class="row">';}
						$query->the_post();
						?>				
						<div class="col-md-3">
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium'); ?></a>						
							<p class="art_work_mini_descr">£<?php echo get_post_meta(get_the_ID(), 'estimated_value', true); ?><br>
							<?php the_title(); ?>, <?php echo get_post_meta(get_the_ID(), 'years', true); ?><br>
							<?php echo get_post_meta(get_the_ID(), 'classification', true); ?></p>
							
							
							<div class="buy_buttons">
								<p class="underlined"><?php _e('Ways to buy:', 'woo-au'); ?></p>
								<a href="<?php the_permalink(); ?>">
								<?php if( get_post_meta(get_the_ID(), 'allow_auction_sell', true) == 'yes'){
									?> <span class='acution_buy_btn'><i class="fa fa-gavel"></i> <?php _e('auction', 'woo-au'); ?></span> <?php
								} ?>
								<?php if( get_post_meta(get_the_ID(), 'allow_direct_sell', true) == 'yes'){
									?> <span class='direct_buy_btn'><i class="fa fa-cart-arrow-down"></i> <?php _e('direct', 'woo-au'); ?></span> <?php
								} ?>
								<?php if( get_post_meta(get_the_ID(), 'selled_via_admin', true) == 'yes'){
									?> <span class='contact_buy_btn'><i class="fa fa-envelope"></i> <?php _e('contact', 'woo-au'); ?></span> <?php
								} ?>
								</a>
							</div>
						</div>
							
					<?php		
						$count++; }
						wp_reset_postdata();
					}else{
						echo "<p>".__('Sorry, no results found matching your query.', 'woo-au')."</p>";
					}
					?>
				
				
			
		
		</div>
				  </div></div> 
		<?php	
  
	
}