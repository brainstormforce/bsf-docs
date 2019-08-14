<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>
<?php
	// display live search box.
	echo do_shortcode( '[doc_wp_live_search]' );

?>
<div class="wrap docs-wraper">

	<div id="primary" class="content-area grid-parent mobile-grid-100 grid-75 tablet-grid-75">
		<main id="main" class="in-wrap" role="main">


		<?php

		if ( have_posts() ) :
			?>
		<div class="bsf-page-header">
			<?php

				echo '<h1 class="entry-title">' . single_tag_title( '', false ) . '</h1>';
				the_archive_description( '<div class="bsf-taxonomy-description">', '</div>' );

			if ( function_exists( 'yoast_breadcrumb' ) ) {
				echo '<div class="bsf-tax-breadcrumb">' . do_shortcode( '[wpseo_breadcrumb]' ) . '</div>';
			}
			/* Hook for external integration */
			do_action( 'bsf_docs_after_tag_term_title' );
			?>
		</div><!-- .page-header -->
			<?php
	endif;
		?>

		<?php
		if ( have_posts() ) :
			?>
			<?php
			/* Start the Loop */

			$current_tag = get_queried_object();
			$paged       = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			$args = array(
				'post_type'      => 'docs',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'paged'          => ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ),
				'tax_query'      => array(
					array(
						'taxonomy'         => 'docs_tag',
						'field'            => $current_tag->slug,
						'terms'            => $current_tag->term_id,
						'include_children' => false,
					),
				),
			);

			$query = new WP_Query( $args );

			query_posts( $args );

			while ( $query->have_posts() ) :

				$query->the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file.
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */

				?>
				<article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> post type-docs status-publish format-standard docs_category">
					<h2 class="bsf-entry-title">
						<a rel="bookmark" href="<?php echo esc_url( the_permalink() ); ?>"><?php the_title(); ?></a>
					</h2>
				</article>

				<?php
			endwhile;

			else :

				get_template_part( 'template-parts/post/content', 'none' );

		endif;
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
