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

		<?php if ( have_posts() ) : ?>
		<div class="bsf-page-header">
			<?php

				echo '<h1 class="entry-title">' . single_cat_title( '', false ) . '</h1>';
				the_archive_description( '<div class="bsf-taxonomy-description">', '</div>' );

			if ( function_exists( 'yoast_breadcrumb' ) ) {
				echo '<div class="bsf-tax-breadcrumb">' . do_shortcode( '[wpseo_breadcrumb]' ) . '</div>';
			}
			/* Hook for external integration */
			do_action( 'bsf_docs_after_cat_term_title' );
			?>
		</div><!-- .page-header -->
			<?php
	endif;
		?>

		<?php if ( have_posts() ) : ?>
			<?php
				$current_category      = get_queried_object();
				$current_category_id   = $current_category->term_id;
				$current_category_slug = $current_category->slug;
				$count                 = '';

				$termchildren = get_terms(
					'docs_category',
					array(
						'parent'     => $current_category_id,
						'pad_counts' => 1,
						'hide_empty' => false,
					)
				);

			if ( $termchildren && ! is_wp_error( $termchildren ) ) :
				$termchildren_1 = get_terms(
					'docs_category',
					array(
						'pad_counts' => 1,
					)
				);
				foreach ( $termchildren_1 as $key => $object ) {

					$slug = $termchildren_1;
				}
				?>

				<div class="bsf-categories-wrap clearfix">
				<?php
				foreach ( $termchildren as $key => $object ) {

					for ( $i = 0; $i <= count( $slug ); $i++ ) {

						if ( isset( $slug[ $i ]->slug ) && isset( $object->slug ) ) {
							if ( $slug[ $i ]->slug == $object->slug ) {// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
								$count = $slug[ $i ]->count;
							}
						}
					}

					if ( $slug[ $key ]->slug == $object->slug ) {// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison

						$count = $object->count;
					}
					?>
					<div class="bsf-cat-col" >
						<a class="bsf-cat-link" href="<?php echo esc_url( get_term_link( $object->slug, $object->taxonomy ) ); ?>">
							<h4><?php echo esc_html( $object->name ); ?></h4>
							<span class="bsf-cat-count">
								<?php /* translators: %s: article count term */ ?>
								<?php printf( __( '%1$s Articles', 'bsf-docs' ), $count ); ?>
							</span>
						</a>
						</div>

					<?php
				}
				?>
				</div>

				<?php
				endif;
			?>
			<?php
		endif;
		?>

		<?php
		if ( have_posts() ) :
			?>
			<?php
			/* Start the Loop */

			$current_category = get_queried_object();
			$paged            = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			$args = array(
				'post_type'      => 'docs',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'paged'          => ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ),
				'tax_query'      => array(
					array(
						'taxonomy'         => 'docs_category',
						'field'            => $current_category->slug,
						'terms'            => $current_category->term_id,
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
