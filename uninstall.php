<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'adcode_1' );
delete_option( 'adoption_1' );
delete_option( 'adcode_2' );
delete_option( 'adoption_2' );
delete_option( 'adcode_header' );
delete_option( 'adoption_header' );
delete_option( 'adcode_footer' );
delete_option( 'adoption_footer' );
