<?php  
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
?>
<div class="wrap rcdf-checkout-options">
    <h1><?php esc_html_e( 'Record custom data from checkout page', 'rcdf-checkout' ) ?></h1>
    <h2><?php esc_html_e( 'List of data from buyers', 'rcdf-checkout' ) ?></h2>
<?php
    global $wpdb;
    $table_name = $wpdb->get_blog_prefix() . 'rcdf_checkout';

    // Delete selected rows

    if( ( isset( $_POST["rcdf_checkout_row"] ) && isset( $_POST['rcdf_checkout_nonce'] ) && wp_verify_nonce( sanitize_text_field ( wp_unslash( $_POST['rcdf_checkout_nonce'] ) ), 'rcdf-checkout-options' ) ) ) {
        $ids = implode( ',', array_map( 'absint', $_POST["rcdf_checkout_row"] ) );
        $wpdb->query( $wpdb->prepare( "DELETE FROM %1i WHERE id IN (%2s)", [$table_name, $ids] ) );
    }

    $data_from_table = wp_cache_get('get-rcdf-checkout', 'rcdf_checkout');
    if( false === $data_from_table) {
        $data_from_table = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i", $table_name ) );
        wp_cache_add('get-rcdf-checkout', $data_from_table, 'rcdf_checkout');
    }
?>
    <form method="post">
        <?php wp_nonce_field('rcdf-checkout-options', 'rcdf_checkout_nonce') ?>
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th><?php esc_html_e( 'First Name', 'rcdf-checkout' ) ?></th>
                    <th ><?php esc_html_e( 'Last Name', 'rcdf-checkout' ) ?></th>
                    <th><?php esc_html_e( 'Phone', 'rcdf-checkout' ) ?></th>
                    <th><?php esc_html_e( 'Email', 'rcdf-checkout' ) ?></th>
                    <th><?php esc_html_e( 'Product name', 'rcdf-checkout' ) ?></th>
                    <th><?php esc_html_e( 'Total price', 'rcdf-checkout' ) ?></th>
                    <th><?php esc_html_e ('Time', 'rcdf-checkout' ) ?></th>
                </tr>
            </thead>
            <tbody>
    <?php

    foreach ( $data_from_table as $value ) {
        echo '<tr>';
        echo '<td style="text-align:center"><input class="one_row_input" type="checkbox" name="rcdf_checkout_row[]" value="' . esc_attr( $value->id ) . '" /></td>';
        echo '<td data-label="' . esc_html__( "First Name", "rcdf-checkout" ) . '">' . esc_attr( $value->first_name ) . "</td>";
        echo '<td data-label="' . esc_html__( "Last Name", "rcdf-checkout" ) . '">' . esc_attr( $value->last_name ) . '</td>';
        echo '<td data-label="' . esc_html__( "Phone", "rcdf-checkout" ) . '">' . esc_attr( $value->phone ) . '</td>';
        echo '<td data-label="' . esc_html__( "Email", "rcdf-checkout" ) . '">' . esc_attr( $value->email ) . '</td>';
        echo '<td data-label="' . esc_html__( "Product Name", "rcdf-checkout" ) . '">' . esc_attr( $value->product_name ) . '</td>';
        echo '<td data-label="' . esc_html__( "Price", "rcdf-checkout" ) . '">' . esc_attr( $value->price ) . '</td>';
        echo '<td data-label="' . esc_html__( "Time", "rcdf-checkout" ) . '">' . esc_attr( $value->time ) . '</td>';
        echo '</tr>';
    }

    ?>
     </tbody>
    <tr>
        <td colspan="8">
            <button type="submit"><?php esc_html_e( 'Delete selected lines', 'rcdf-checkout' ) ?></button>
        </td>
    </tr>
    </table>
    </form>
</div>