<?php
/*
*
Template Name: Exhibation Template
*/

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 */

get_header();
the_core_header_image();
$the_core_sidebar_position = function_exists( 'fw_ext_sidebars_get_current_position' ) ? fw_ext_sidebars_get_current_position() : 'right';
?>
<section class="fw-main-row <?php the_core_get_content_class( 'main', $the_core_sidebar_position ); ?>" role="main" itemprop="mainEntity" itemscope="itemscope" itemtype="https://schema.org/Blog">
	<div class="fw-container">
		<div class="fw-row">
			<div class="fw-content-area">
				<div class="fw-col-inner">
					<?php if( function_exists('fw_ext_breadcrumbs') ) fw_ext_breadcrumbs(); ?>
         <?php while(have_posts()) { the_post(); the_content(); } ?>
					<?php 
   $new_args = array('post_type'=>'exhibition');
    $new_query = new wp_query($new_args);    
    
    
     while ($new_query->have_posts() ) : $new_query->the_post();
	      if(get_field('featured_exhibition')){ get_template_part( 'templates/content', 'exhibations' ); }
                  

						if ( comments_open() ) comments_template();
					
					endwhile; ?>
				</div><!-- /.inner -->
			</div><!-- /.content-area -->
		</div><!-- /.row -->
	</div><!-- /.container -->
</section>
<?php get_footer(); ?>