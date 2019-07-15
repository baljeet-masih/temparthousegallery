<!doctype html>
<html <?php global $post; language_attributes(); ?> >
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> itemscope="itemscope" itemtype="https://schema.org/WebPage">
    <?php do_action( 'the_core_action_after_body_open_tag' ); ?>
	<?php the_core_page_transition_loader(); ?>
	<?php the_core_page_transition_begin(); ?>
		<div id="page" class="hfeed site">
			<?php do_action( 'the_core_action_before_header' ); ?>
			<?php the_core_header(); ?>
			<?php do_action( 'the_core_action_after_header' ); ?>
		
			<div id="main" class="site-main">