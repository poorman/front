<?php
/**
 * Job listing in the loop.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/content-job_listing-list-grid.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager
 * @category    Template
 * @since       1.0.0
 * @version     1.27.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $post;

// Ensure visibility.
if ( empty( $post ) ) {
    return;
}

?>
<li <?php job_listing_class(); ?> data-longitude="<?php echo esc_attr( $post->geolocation_lat ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_long ); ?>">
    <?php
        do_action( 'job_listing_list_grid_content_area_before' );
        do_action( 'job_listing_list_grid_start' );
        do_action( 'job_listing_list_grid' );
        do_action( 'job_listing_list_grid_end' );
        do_action( 'job_listing_list_grid_content_area_after' );
    ?>
</li>
