<?php  
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
?>

<div class="ao-admin-menu_options">
    <?php settings_errors(); ?>
    <form action="options.php" method="post">
        <?php settings_fields( 'rcdf_checkout_general_group' ); ?>
        <?php do_settings_sections( 'rcdf_checkout' ); ?>
        <?php submit_button(); ?>
    </form>
</div>