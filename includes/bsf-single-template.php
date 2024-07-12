<?php
/**
 * The template for single doc page
 *
 * @author Pratik Chaskar
 * @package Documentation/SinglePage
 */

get_header();
	// Displays live search box.
	echo do_shortcode( '[doc_wp_live_search]' );
?>

<div class="wrap docs-wraper">
	<div id="primary" class="content-area bsf-options-form-wrap grid-parent mobile-grid-100 grid-75 tablet-grid-75">
		<main id="main" class="docs-single-main" role="main">

			<?php
			/* Start the Loop */

			while ( have_posts() ) :
				the_post();

				?>
				<header class="entry-header">
					<div class="docs-single-title">
						<?php
						if ( is_single() ) {
							the_title( '<h1 class="entry-title">', '</h1>' );
							if ( function_exists( 'yoast_breadcrumb' ) ) {
								yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' );
							}
						}
						/* Hook for external integration */
						do_action( 'bsf_docs_after_single_title' );
						?>
					</div>
				</header><!-- .entry-header -->
				<div class="entry-content bsf-entry-content">
					<?php the_post_thumbnail(); ?>
					<?php the_content(); ?>
					<?php the_terms( $post->ID, 'docs_tag', '<ul class="bsf-docs-tag"><span class="bsf-docs-tag-label">Tagged Under: </span><li>', '</li><li>', '</li></ul>' ); ?>
				</div><!-- .entry-content -->
				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				$is_comment_template_on = get_option( 'bsf_search_has_comments' );
				if ( ! ( '1' === $is_comment_template_on || false === $is_comment_template_on ) ) {
					comments_template();
				}
			endwhile; // End of the loop.
			?>
		</main><!-- #main -->
	</div><!-- #primary -->
	<div itemscope="itemscope" id="secondary" class="widget-area sidebar grid-25 tablet-grid-25 grid-parent docs-sidebar-area secondary" role="complementary">
			<div class="sidebar-main content-area">
					<?php dynamic_sidebar( 'docs-sidebar-1' ); ?>
			</div>
	</div>
</div><!-- .wrap -->

<?php get_footer(); ?>
