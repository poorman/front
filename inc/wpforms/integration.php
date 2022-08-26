<?php
/**
 * WP Forms Integration
 *
 */
add_filter( 'transient_wpforms_activation_redirect', '__return_false' );