<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 */
?>

		<?php do_action( 'the_core_action_before_close_main' ); ?>

		</div><!-- /.site-main -->

		<?php do_action( 'the_core_action_before_footer' ); ?>
		<!-- Footer -->
		<footer id="colophon" class="site-footer fw-footer <?php the_core_get_footer_class(); ?>" itemscope="itemscope" itemtype="https://schema.org/WPFooter">
			<?php the_core_footer(); ?>
		</footer>
		<?php do_action( 'the_core_action_after_footer' ); ?>

	</div><!-- /#page -->
<?php the_core_page_transition_end(); ?>
<?php the_core_go_to_top_button(); ?>
<?php wp_footer(); ?>
<script>
$('.nav-tabs a').click(function (e) {
    e.preventDefault();
    if ($(this).closest('li').is('.active')) { //stop sending another query when tab active
        return;
    }

    var ts = +new Date();
    var tabUrlAddress = $(this).attr("data-url") + '?timestamp=' + ts;
    var href = this.hash;
    var pane = $(this);
    pane.show();

    //Show the loader
    $(href).find('.tabs_content').css({'opacity':'0'});
    $(".single-artist .loading").show();

    $(href).load(tabUrlAddress, function (result) {
        //Hide when complete
        
       $(href).find('.tabs_content').css({'opacity':'1'});
	   $(".single-artist .loading").hide();
    });
});
</script>
</body>
</html>