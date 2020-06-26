<?php
/**
 * Register and load the widget
 *
 * @package Documentation/Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}





/**
 * Creating the widget.
 */
class Bsf_Docs_Cat_Widget extends WP_Widget {
	/**
	 * Constructor calling the docs widgets
	 */
	function __construct() {

		add_action( 'widgets_init', array( $this, 'bsf_docs_widgets_area' ) );

		parent::__construct(
			// Base ID of your widget.
			'bsf_docs_cat_widget',
			// Widget name will appear in UI.
			__( 'BSF Docs Category Widget', 'bsf-docs' ),
			// Widget description.
			array(
				'description' => __( 'A list or dropdown of BSF Docs categories.', 'bsf-docs' ),
			)
		);
	}

	/**
	 * Creating widget front-end.
	 *
	 * @param int $args Get the before and after widget arguments.
	 * @param int $instance Get the title of the widget title.
	 */
	public function widget( $args, $instance ) {
		static $first_dropdown = true;

		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Categories', 'bsf-docs' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$cat_args = array(
			'orderby'      => 'name',
			'show_count'   => $c,
			'hierarchical' => $h,
			'taxonomy'     => 'docs_category',
		);

		if ( $d ) {
			echo sprintf( '<form action="%s" method="get">', esc_url( home_url() ) );
			$dropdown_id    = ( $first_dropdown ) ? 'docs-category' : "{$this->id_base}-dropdown-{$this->number}";
			$first_dropdown = false;

			$current_category      = get_queried_object();
			$current_category_slug = $current_category->slug;

			echo '<label class="bsf-screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $title . '</label>';

			$cat_args['show_option_none'] = __( 'Select Category', 'bsf-docs' );
			$cat_args['id']               = $dropdown_id;
			$cat_args['value_field']      = 'slug';
			$cat_args['selected']         = $current_category_slug;

			/**
				 * Filters the arguments for the Categories widget drop-down.
				 *
				 * @since 2.8.0
				 * @since 4.9.0 Added the `$instance` parameter.
				 *
				 * @see wp_dropdown_categories()
				 *
				 * @param array $cat_args An array of Categories widget drop-down arguments.
				 * @param array $instance Array of settings for the current widget.
				 */
			wp_dropdown_categories( apply_filters( '', $cat_args, $instance ) );

			echo '</form>';
			?>

			<script type='text/javascript'>
			/* <![CDATA[ */
			(function() {
			var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
				function onCatChange() {
				if ( dropdown.options[ dropdown.selectedIndex ].value != '' ) {
					location.href = "<?php echo home_url(); ?>/docs-category/"+dropdown.options[dropdown.selectedIndex].value;
				}
				}
				dropdown.onchange = onCatChange;
			})();
			/* ]]> */
			</script>

			<?php
		} else {
			?>
		<ul>
			<?php
			$cat_args['title_li'] = '';

			/**
			 * Filters the arguments for the Categories widget.
			 *
			 * @since 2.8.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @param array $cat_args An array of Categories widget options.
			 * @param array $instance Array of settings for the current widget.
			 */
			wp_list_categories( apply_filters( 'widget_categories_args', $cat_args, $instance ) );
			?>
</ul>
			<?php
		}

				echo $args['after_widget'];
	}

	/**
	 * Widget Backend.
	 *
	 * @param int $instance Get the titles for the recent docs in widget area.
	 */
	public function form( $instance ) {
		// Defaults.
		$instance     = wp_parse_args(
			(array) $instance,
			array(
				'title' => '',
			)
		);
		$title        = sanitize_text_field( $instance['title'] );
		$count        = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown     = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bsf-docs' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown', 'bsf-docs' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts', 'bsf-docs' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e( 'Show hierarchy', 'bsf-docs' ); ?></label></p>
		<?php
	}

	/**
	 * Updating widget replacing old instances with new.
	 *
	 * @param int $new_instance Returns the new instance.
	 * @param int $old_instance Returns the old instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['count']        = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['hierarchical'] = ! empty( $new_instance['hierarchical'] ) ? 1 : 0;
		$instance['dropdown']     = ! empty( $new_instance['dropdown'] ) ? 1 : 0;

		return $instance;
	}


	/**
	 * Returns array of classes of override single page template enabled
	 *
	 * @param int $classes returns an array of classes.
	 */
	function docs_body_classes( $classes ) {
		$classes[] = 'override-single-page-template-enabled';

		return $classes;
	}

	/**
	 * Rgister Doc widget area
	 */
	function bsf_docs_widgets_area() {

		register_widget( 'bsf_docs_cat_widget' );
	}

} // Class bsf_docs_widget ends here.

new Bsf_Docs_Cat_Widget();




