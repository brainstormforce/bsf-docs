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
class Bsf_Docs_Widget extends WP_Widget {
	/**
	 * Constructor calling the docs widgets
	 */
	function __construct() {

		add_action( 'widgets_init', array( $this, 'bsf_docs_widgets_area' ) );

		parent::__construct(
			// Base ID of your widget.
			'bsf_docs_widget',
			// Widget name will appear in UI.
			__( 'BSF Docs Widget', 'bsf-docs' ),
			// Widget description.
			array(
				'description' => __( 'Widget for recent Docs', 'bsf-docs' ),
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
		$title = apply_filters( 'widget_title', $instance['title'] );

		// Before and after widget arguments are defined by themes.
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?> 
	<ul> 
		<?php
		$recent_posts = wp_get_recent_posts(
			array(
				'post_type'   => 'docs',
				'post_status' => 'publish',
			)
		);
		foreach ( $recent_posts as $recent ) {
			echo '<li><a href="' . get_permalink( $recent['ID'] ) . '" title="Look ' . esc_attr( $recent['post_title'] ) . '" >' . $recent['post_title'] . '</a> </li> ';
		}

		?>
</ul>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Widget Backend.
	 *
	 * @param int $instance Get the titles for the recent docs in widget area.
	 */
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Recent Docs', 'bsf-docs' );
		}

		// Widget admin form.
		?>
<p>
	<label for="
		<?php
		echo $this->get_field_id( 'title' );
		?>
	">
		<?php
		_e( 'Title:', 'bsf-docs' );
		?>
	</label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value=" <?php echo esc_attr( $title ); ?> "/>
	</p>
		<?php
	}

	/**
	 * Updating widget replacing old instances with new.
	 *
	 * @param int $new_instance Returns the new instance.
	 * @param int $old_instance Returns the old instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
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

		register_widget( 'bsf_docs_widget' );
		register_sidebar(
			array(
				'name'          => __( 'Docs Sidebar', 'bsf-docs' ),
				'id'            => 'docs-sidebar-1',
				'description'   => __( 'Widgets in this area will be shown on all docs single posts and cateory.', 'bsf-docs' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="widget-title docs-widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}

} // Class bsf_docs_widget ends here.

new Bsf_Docs_Widget();




