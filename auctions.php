<?php
//Template Name: auctions

get_header();
?>
<div class="fw-container">	
	<div id="primary" class="fw-row">
		<main id="main" class="site-main" role="main" style="padding-top: 160px;">	
			<?php		
			if ( have_posts() ) : while ( have_posts() ) : the_post();
			echo "<h1>";
			the_title();
			echo "</h1>";
			the_content();
			endwhile; else: ?>
			<p>Sorry, no posts matched your criteria.</p>
			<?php endif; ?>		
		
			<div class="col-md-2">
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
			
			<div class="col-md-10">		
			<?php
				if( $_GET['search_filters'] == 'yes'){
					
					
					$search_params = array();										
					//Way to buy:								
					if( !empty($_GET['auction']) ){
						$search_params['auction_sell'] = 'yes';
					}
					if( !empty($_GET['direct_sell']) ){
						$search_params['direct_sell'] = 'yes';
					}
					if( !empty($_GET['contact_gallery']) ){
						$search_params['contact_gallery'] = 'yes';
					}
					
					//Cats:									
					if( !empty($_GET['drawing']) ){
						$search_params['drawing'] = 'yes';
						$search_params['search_by_category'] = 'yes';
					}
					if( !empty($_GET['other']) ){
						$search_params['other'] = 'yes';
						$search_params['search_by_category'] = 'yes';
					}
					if( !empty($_GET['painting']) ){
						$search_params['painting'] = 'yes';
						$search_params['search_by_category'] = 'yes';
					}
					if( !empty($_GET['photography']) ){
						$search_params['photography'] = 'yes';
						$search_params['search_by_category'] = 'yes';
					}
					if( !empty($_GET['prints']) ){
						$search_params['prints'] = 'yes';
						$search_params['search_by_category'] = 'yes';
					}
					if( !empty($_GET['sculpture']) ){
						$search_params['sculpture'] = 'yes';
						$search_params['search_by_category'] = 'yes';
					}
					if( !empty($_GET['work-on-paper']) ){
						$search_params['work-on-paper'] = 'yes';
						$search_params['search_by_category'] = 'yes';
					}					

					//Time:	
					if( !empty($_GET['time_p_newer']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['time_p_newer'] = 'yes';
					}
					if( !empty($_GET['time_p_earlier']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['time_p_earlier'] = 'yes';
					}					
					if( !empty($_GET['time_p_1940']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['time_p_1940'] = 'yes';
					}
					if( !empty($_GET['time_p_1950']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['time_p_1950'] = 'yes';
					}
					if( !empty($_GET['time_p_1960']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['time_p_1960'] = 'yes';
					}
					if( !empty($_GET['sculpture']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['sculpture'] = 'yes';
					}
					if( !empty($_GET['time_p_1970']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['time_p_1970'] = 'yes';
					}
					if( !empty($_GET['time_p_1980']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['time_p_1970'] = 'yes';
					}
					if( !empty($_GET['time_p_1990']) ){
						$search_params['add_array_for_time'] = 'yes';
						$search_params['time_p_1970'] = 'yes';
					}
					

					//Price:				
					if( !empty($_GET['price_from']) ){
						$search_params['price_from'] = $_GET['price_from'];
					}
					if( !empty($_GET['price_to']) ){
						$search_params['price_to'] = $_GET['price_to'];
					}
					
					//ORDERBY:					
					$search_params['order_by'] = $_GET['order_by'];
					
					//the query
					$ress = rsm_get_auctions_items( $search_params );
					
				}else{
					
					$ress = rsm_get_auctions_items();
				}
				
				
						
			
				if($ress->have_posts()){ 
					while (	$ress->have_posts() ) {
						$ress->the_post();
						?>				
						<div class="col-md-4">
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
					}
				}else{
					echo "<p>".__('Sorry, no results found matching your query.', 'woo-au')."</p>";
				}
				wp_reset_postdata();
				?>
								
				<div class="pagination">
					<?php										
						$big = 999999999;
						$args = array(
							'base' 		   => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
							'format'       => '/page/%#%',
							'total'        => $ress->max_num_pages,
							'current'      => max( 1, get_query_var( 'paged' ) ),
							'show_all'     => false,
							'end_size'     => 1, 
							'mid_size'     => 2,
							'prev_next'    => true,
							'prev_text'    => __('« Previous', 'woo-au'),
							'next_text'    => __('Next »', 'woo-au'),
							'type'         => 'plain', 
							'add_args'     =>  false,
							'add_fragment' => '',
							'before_page_number' => '',
							'after_page_number'  => ''
						); 
					
						echo paginate_links( $args );
					?>
				</div>
				
			</div>	
		</main><!-- #main -->
	</div><!-- #primary -->	
</div>

<?php
get_footer();