<?php
/*
	The Template for displaying all single exhibitions
*/
get_header();
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>

<section class="fw-main-row" role="main" itemprop="mainEntity" itemscope="itemscope" itemtype="https://schema.org/Blog">
	<div class="fw-container">
		<div class="fw-row">
			<div class="fw-content-area">
				<div class="fw-col-inner">				
					<?php 
					$coordinates = '';
					while ( have_posts() ) : the_post();					
						echo "<h2>".get_the_title()."</h2>";
						?>
						<div class="row">
						<div class="col-sm-2">
						<div class="exhibition_data_block exhibition_data_block1 ">
						<?php
							//start_date
							echo "<label><b>".__('Start date : ', 'woo-au')."</b> </label><br> ".get_field('start_date');
							
						?><br />
						<?php
							//end_date
							echo "<label><b>".__('End date : ', 'woo-au')."</b> </label><br> ".get_field('end_date'); 
							
						?>
						</div>
						</div>
					    <div class="col-sm-10"><?php the_excerpt();  ?>
						</div>
						</div>
						<?php
							
						
						
					endwhile; ?>
					
					<hr>
					<div class="exhibition_data_block">
					   <div class="row">
					   <div class="col-sm-12">
					  <?php  echo "<label style='padding-left: 0px;'><b>".__('Artists : ', 'woo-au')."</b></label><br>"; ?>
					   <ul class="exh_artist_list">
					   <?php
					   
						
      					   $artist_list =  get_field('artist');
                           if(!empty($artist_list)){
							    foreach($artist_list as $artist){ 
								  echo '<li><a href="'.get_the_permalink($artist->ID).'">'.get_the_title($artist->ID).'</a></li>';
								}
						   }
					   ?>
					   </ul></div>
					   </div>
					</div>
					
					<div class="exhibition_data_block">
						<?php
							//place
							echo "<label><b>".__('Place:', 'woo-au')."</b></label><br>";							
							$coordinates = get_field('place');	
							echo '<span><b>'.__('We are here: ', 'woo-au').'</b>'.get_field('address').'</span>';
							//echo '<div id="map" style="height: 400px" ></div>';
						?>
					</div>
								<?php echo "<label><b>".__('Artworks:', 'woo-au')."</b></label><br>"; ?>	
					<div class="exhibition_data_block">
					  <div class="row">
						<?php
							//artworks
							
							$arts = get_field('artworks');
							$count = 0;
							foreach($arts as $art){	
							 if($count%3==0){ echo '</div><div class="row">'; }
							?>				
								<div class="col-md-4">
									<a href="<?php echo get_permalink($art->ID); ?>"><?php echo get_the_post_thumbnail($art->ID, 'medium'); ?></a>						
									
									<p class="art_work_mini_descr">
									   <?php echo "".get_the_title(get_post_meta($art->ID, 'artist', true)); ?><br>
										<?php echo "&pound;".get_post_meta($art->ID, 'estimated_value', true); ?><br>
										<?php echo get_the_title($art->ID); ?>, <?php echo get_post_meta($art->ID, 'years', true); ?><br>
										<?php echo get_post_meta($art->ID, 'classification', true); ?>
									</p>									
									
									<div class="buy_buttons">
										<br><p class="underlined"><?php _e('Ways to buy:', 'woo-au'); ?></p>
										<a href="<?php echo get_permalink($art->ID); ?>">
											<?php if( get_post_meta($art->ID, 'allow_auction_sell', true) == 'yes'){
												?> <span class='acution_buy_btn'><i class="fa fa-gavel"></i> <?php _e('auction', 'woo-au'); ?></span> <?php
											} ?>
											<?php if( get_post_meta($art->ID, 'allow_direct_sell', true) == 'yes'){
												?> <span class='direct_buy_btn'><i class="fa fa-cart-arrow-down"></i> <?php _e('direct', 'woo-au'); ?></span> <?php
											} ?>
											<?php if( get_post_meta($art->ID, 'selled_via_admin', true) == 'yes'){
												?> <span class='contact_buy_btn'><i class="fa fa-envelope"></i> <?php _e('contact', 'woo-au'); ?></span> <?php
											} ?> 
										</a> 
									</div>
								</div>
							<?php	
							$count++; }
						?>
					</div>
					</div>
				</div><!-- /.inner -->
			</div><!-- /.content-area -->
		</div><!-- /.row -->
	</div><!-- /.container -->
</section>
<?php get_footer(); ?>

<script language="javascript" type="text/javascript">
    var map;
    var geocoder;
    function InitializeMap() {

        var latlng = new google.maps.LatLng(<?php echo $coordinates['lat']; ?>, <?php echo $coordinates['lng']; ?>);
        var myOptions =
        {
            zoom: 10,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDefaultUI: true
        };
        map = new google.maps.Map(document.getElementById("map"), myOptions);
    }

    function FindLocaiton() {
        geocoder = new google.maps.Geocoder();
        InitializeMap();

        var address = document.getElementById("addressinput").value;
        geocoder.geocode({ 'address': address }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });

            }
            else {
                alert("Geocode was not successful for the following reason: " + status);
            }
        });

    }

    function Button1_onclick() {
        FindLocaiton();
    }
    window.onload = InitializeMap;
</script>