<?php
/**
 * Runs when the plugin is DELETED from the Plugins screen (not deactivated).
 *
 * WordPress loads this file on its own, without the plugin running, which is
 * why the security check below matters: without it, the file could be hit
 * directly over the web and would wipe data on demand.
 *
 * @package JarralProjects
 */

// WP_UNINSTALL_PLUGIN is only defined by WordPress during a real uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/*
 * Projects are content, so they are left in place. Deleting the plugin hides
 * them from the admin; reactivating brings them straight back.
 *
 * Only the rewrite rules are cleared, so /work/ URLs stop 404ing oddly.
 */
flush_rewrite_rules();
