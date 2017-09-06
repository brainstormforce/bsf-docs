<?php
/**
 * The template for archive docs page
 *
  * @author Brainstormforce
 */

get_header(); ?>

<?php
	// display live search box
	echo do_shortcode( '[wp_docs_live_search]' );
?>
<div class="wrap docs-archive-wraper">

	<?php 

	// Display category list 
	echo do_shortcode( '[wp_docs_category_list]' );

	?>

</div><!-- .wrap -->

<?php get_footer();
