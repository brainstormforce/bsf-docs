<?php
/**
 * Live search options page
 *
 * @package Live search options page
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
wp_enqueue_style( 'bsf-options-style' );


?>

<div class="wrap">
	<div class="bsf-options-form-wrap clearfix">

		<h1><?php esc_html_e( 'Docs Settings', 'bsf-docs' ); ?></h1>
		<form method="post" action="options.php"> 
				<h2 class="title"><?php _e( 'Live Search', 'bsf-docs' ); ?></h2>
				<p><?php _e( "Settings to control the live search functionality & it's search area.", 'bsf-docs' ); ?></p>
					<?php settings_fields( 'bsf-docs-settings-group' ); ?>
					<?php do_settings_sections( 'bsf-docs-settings-group' ); ?>

					<table  class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e( 'Enable Live Search', 'bsf-docs' ); ?></th>
							<td>
								<?php
								$checked        = '';
								$bsf_ls_enabled = get_option( 'bsf_ls_enabled' );
								$checked        = ( false === $bsf_ls_enabled ) ? " checked='checked' " : ( ( 1 == $bsf_ls_enabled ) ? " checked='checked' " : '' );// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
								?>
								<input type="checkbox" <?php echo $checked; ?> name="bsf_ls_enabled" value="1" <?php echo checked( 1, $checked, false ); ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e( 'Search Within Post Types', 'bsf-docs' ); ?></th>
							<td>	
								<fieldset>
									<?php

									$selected_post_types = get_option( 'bsf_search_post_types' );

									$selected_post_types = ! $selected_post_types ? array( 'docs' ) : $selected_post_types;

									$post_types = get_post_types(
										array(
											'public'  => true,
											'show_ui' => true,
										),
										'objects'
									);

									unset( $post_types['attachment'] );
									unset( $post_types['fl-builder-template'] );
									unset( $post_types['fl-theme-layout'] );

									foreach ( $post_types as $key => $post_type ) {
										?>
										<input type="checkbox" 
										<?php
										if ( in_array( $key, $selected_post_types ) ) {// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
											echo "checked='checked' "; }
										?>
name="bsf_search_post_types[]" value="<?php echo esc_attr( $key ); ?>" />
										<label>
											<?php echo ucfirst( $post_type->label ); ?>
										</label><br>
									<?php } ?>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e( 'Enable built-in single page template', 'bsf-docs' ); ?></th>
							<td>
								<?php
								$checked                      = '';
								$bsf_override_single_template = get_option( 'bsf_override_single_template' );
								$checked                      = ( false === $bsf_override_single_template ) ? " checked='checked' " : ( ( 1 == $bsf_override_single_template ) ? " checked='checked' " : '' );// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
								?>
								<input type="checkbox" <?php echo $checked; ?> name="bsf_override_single_template" value="1" <?php echo checked( 1, $checked, false ); ?> />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Enable built-in category & tag page template', 'bsf-docs' ); ?></th>
							<td>
								<?php
								$checked                        = '';
								$bsf_override_category_template = get_option( 'bsf_override_category_template' );
								$checked                        = ( false === $bsf_override_category_template ) ? " checked='checked' " : ( ( 1 == $bsf_override_category_template ) ? " checked='checked' " : '' );// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
								?>
								<input type="checkbox" <?php echo $checked; ?> name="bsf_override_category_template" value="1" <?php echo checked( 1, $checked, false ); ?> />
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php _e( "Turn Off Doc's Comments", 'bsf-docs' ); ?></th>
							<td>
								<?php
								$checked                 = '';
								$bsf_search_has_comments = get_option( 'bsf_search_has_comments' );
								$checked                 = ( false === $bsf_search_has_comments ) ? " checked='checked' " : ( ( 1 == $bsf_search_has_comments ) ? " checked='checked' " : '' );// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison

								?>
								<input type="checkbox" <?php echo $checked; ?> name="bsf_search_has_comments" value="1" <?php echo checked( 1, $checked, false ); ?> />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Doc Archive Page Title', 'bsf-docs' ); ?></th>
							<td>
								<input type="text" class="regular-text code" name="bsf_doc_title" value="<?php echo get_option( 'bsf_doc_title' ); ?> "/>
							</td>
						</tr>	


					</table>
						<?php submit_button(); ?>
		</form>
	</div>
	<div class="bsf-shortcodes-wrap">

		<h2 class="title"><?php _e( 'Shortcodes', 'bsf-docs' ); ?></h2>
		<p><?php _e( 'Copy below shortcode and paste it into your post, page, or text widget.', 'bsf-docs' ); ?></p>

		<div class="bsf-shortcode-container">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Display Live Search Box', 'bsf-docs' ); ?></th>
					<td>
						<div class="bsf-shortcode-container wp-ui-text-highlight">
							[doc_wp_live_search placeholder="Have a question?"]
						</div>  
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Display Doc's Category List", 'bsf-docs' ); ?></th>
					<td>
						<div class="bsf-shortcode-container wp-ui-text-highlight">
							[doc_wp_category_list]
						</div>  
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

