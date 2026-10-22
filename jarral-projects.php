<?php
/**
 * Plugin Name:       Jarral Projects
 * Description:       Registers the Projects post type and Service Type taxonomy used by the portfolio grid.
 * Version:     1.2.2
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Update URI:        https://github.com/Jarral05/jarral-projects
 * Author:            Mohsin Jarral
 * License:           GPL-2.0-or-later
 * Text Domain:       jarral-projects
 *
 * WHY THIS IS A PLUGIN AND NOT PART OF THE THEME
 * ----------------------------------------------
 * Content must outlive its design. If you registered Projects inside the
 * theme and later switched themes, every project would vanish from the
 * admin. The rule: themes handle appearance, plugins handle content.
 *
 * @package JarralProjects
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Projects post type.
 */
function jdp_register_post_type() {

	register_post_type(
		'jd_project',
		array(
			'labels'       => array(
				'name'          => __( 'Projects', 'jarral-projects' ),
				'singular_name' => __( 'Project', 'jarral-projects' ),
				'add_new_item'  => __( 'Add New Project', 'jarral-projects' ),
				'edit_item'     => __( 'Edit Project', 'jarral-projects' ),
				'menu_name'     => __( 'Projects', 'jarral-projects' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-portfolio',
			'menu_position'=> 20,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'rewrite'      => array( 'slug' => 'work' ),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'jd_service_type',
		'jd_project',
		array(
			'labels'            => array(
				'name'          => __( 'Service Types', 'jarral-projects' ),
				'singular_name' => __( 'Service Type', 'jarral-projects' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'service-type' ),
		)
	);
}
add_action( 'init', 'jdp_register_post_type' );

/**
 * Two extra fields per project: the client's industry and the live URL.
 */
function jdp_meta_box() {
	add_meta_box(
		'jdp_details',
		__( 'Project details', 'jarral-projects' ),
		'jdp_meta_box_html',
		'jd_project',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'jdp_meta_box' );

/**
 * Render the meta box.
 *
 * @param WP_Post $post Current post.
 */
function jdp_meta_box_html( $post ) {

	// A nonce proves the form came from this screen and not from an attacker's
	// site. Without it, anyone could trick a logged-in admin into saving data.
	wp_nonce_field( 'jdp_save_meta', 'jdp_nonce' );

	$type = get_post_meta( $post->ID, '_jd_client_type', true );
	$url  = get_post_meta( $post->ID, '_jd_live_url', true );
	?>
	<p>
		<label for="jd_client_type"><strong><?php esc_html_e( 'Client industry', 'jarral-projects' ); ?></strong></label><br>
		<input type="text" id="jd_client_type" name="jd_client_type" class="widefat"
		       value="<?php echo esc_attr( $type ); ?>" placeholder="B2B strategy consultancy">
	</p>
	<p>
		<label for="jd_live_url"><strong><?php esc_html_e( 'Live URL', 'jarral-projects' ); ?></strong></label><br>
		<input type="url" id="jd_live_url" name="jd_live_url" class="widefat"
		       value="<?php echo esc_url( $url ); ?>" placeholder="https://example.com">
	</p>
	<?php
}

/**
 * Save the meta box, safely.
 *
 * Four checks before writing anything. Skip any one of them and you have
 * a vulnerability.
 *
 * @param int $post_id Post being saved.
 */
function jdp_save_meta( $post_id ) {

	// 1. Is this a real submission, or WordPress autosaving?
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// 2. Did the request actually come from our form?
	if ( ! isset( $_POST['jdp_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jdp_nonce'] ) ), 'jdp_save_meta' ) ) {
		return;
	}

	// 3. Is this user allowed to edit this post?
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// 4. Clean every value before it touches the database.
	if ( isset( $_POST['jd_client_type'] ) ) {
		update_post_meta(
			$post_id,
			'_jd_client_type',
			sanitize_text_field( wp_unslash( $_POST['jd_client_type'] ) )
		);
	}

	if ( isset( $_POST['jd_live_url'] ) ) {
		update_post_meta(
			$post_id,
			'_jd_live_url',
			esc_url_raw( wp_unslash( $_POST['jd_live_url'] ) )
		);
	}
}
add_action( 'save_post_jd_project', 'jdp_save_meta' );

/**
 * Flush permalinks once on activation so /work/ URLs resolve immediately.
 */
function jdp_activate() {
	jdp_register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'jdp_activate' );

/**
 * And clean up on deactivation.
 */
function jdp_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'jdp_deactivate' );

/* =========================================================================
   2. UPDATES

   Custom plugins get no updates from wordpress.org, so this wires WordPress's
   normal update screen to a GitHub repository instead. You tag a release on
   GitHub, and every site running this plugin sees an update notice.

   The "Update URI" header above is the other half of this. Without it, if
   anyone ever publishes a plugin on wordpress.org with the same folder name,
   WordPress would offer THEIR code as an update to YOURS. That is a real
   supply-chain hijack, and the header is what prevents it.
   ========================================================================= */

/**
 * Register the update checker.
 */
function jdp_updates() {

	$lib = __DIR__ . '/lib/plugin-update-checker/plugin-update-checker.php';

	if ( ! file_exists( $lib ) ) {
		return; // Library missing: fail quietly rather than fatally.
	}

	require_once $lib;

	$factory = '\\YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory';

	if ( ! class_exists( $factory ) ) {
		return;
	}

	$checker = $factory::buildUpdateChecker(
		'https://github.com/Jarral05/jarral-projects/',
		__FILE__,
		'jarral-projects'
	);

	// Use the zip attached to a GitHub release rather than the auto-generated
	// source zip (falling back to the source zip if a release has none). This
	// only changes which file is downloaded, not which version is offered.
	$api = $checker->getVcsApi();
	if ( $api && method_exists( $api, 'enableReleaseAssets' ) ) {
		$api->enableReleaseAssets();
	}

	// The library tries the latest release, then the latest tag, then falls
	// back to the branch. Dropping the branch step means only releases and
	// tags are ever offered, so untagged work never reaches client sites.
	add_filter(
		'puc_vcs_update_detection_strategies-jarral-projects',
		static function ( $strategies ) {
			unset( $strategies['branch'] );
			return $strategies;
		}
	);
}
add_action( 'init', 'jdp_updates' );
