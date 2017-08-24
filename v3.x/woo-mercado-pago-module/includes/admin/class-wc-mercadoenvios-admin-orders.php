<?php

/**
 * Part of Woo Mercado Pago Module
 * Author - Mercado Pago
 * Developer - Marcelo Tomio Hama / marcelo.hama@mercadolivre.com, Gabriel Matsuoka / gabriel.matsuoka@mercadopago.com
 * Copyright - Copyright(c) MercadoPago [https://www.mercadopago.com]
 * License - https://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once dirname( __FILE__ ) . '/../sdk/lib/mercadopago.php';

/**
 * MercadoEnvios orders.
 */
class WC_MercadoEnvios_Admin_Orders {

	/**
	 * Initialize the order actions.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
	}

	/**
	 * Register tracking code metabox.
	 */
	public function register_metabox() {
		add_meta_box(
			'wc_mercadoenvios',
			'Mercado Envios',
			array( $this, 'metabox_content' ),
			'shop_order',
			'side',
			'high'
		);
	}

	/**
	 * Tracking code metabox content.
	 *
	 * @param WC_Post $post Post data.
	 */
	public function metabox_content( $post ) {

		$order = wc_get_order( $post->ID );
		$shipment_id = ( method_exists( $order, 'get_meta' ) ) ?
			$order->get_meta( '_mercadoenvios_shipment_id' ) :
			get_post_meta( $post->ID, '_mercadoenvios_shipment_id', true );
		$status = ( method_exists( $order, 'get_meta' ) ) ?
			$order->get_meta( '_mercadoenvios_status' ) :
			get_post_meta( $post->ID, '_mercadoenvios_status', true );
		$tracking_number = ( method_exists( $order, 'get_meta' ) ) ?
			$order->get_meta( '_mercadoenvios_tracking_number' ) :
			get_post_meta( $post->ID, '_mercadoenvios_tracking_number', true );

		if ( isset( $status ) && $status != '' && $status != 'pending' ) {
			echo '<label for="mercadoenvios_tracking_code">' . esc_html__( 'Tracking code:', 'woo-mercado-pago-module' ) . '</label><br />';
			echo '<input type="text" id="mercadoenvios_tracking_code" name="mercadoenvios_tracking_code" value="' .
				esc_attr( $tracking_number ) . '" style="width:100%; style="width: 100%;" />';
			// Check exist shipment_id
			if ( isset( $shipment_id ) && $shipment_id != '' ) {
				$client_id = get_option( '_mp_client_id', '' );
				$client_secret = get_option( '_mp_client_secret', '' );
				$this->mp = new MP(
					WC_Woo_Mercado_Pago_Module::get_module_version(),
					$client_id,
					$client_secret
				);
				echo '<br /><label for="mercadoenvios_tracking_number">' . esc_html__( 'Tag:', 'woo-mercado-pago-module' ) . '</label><br />';
				echo '<a href="https://api.mercadolibre.com/shipment_labels?shipment_ids=' . esc_attr( $shipment_id ) .
					'&savePdf=Y&access_token=' . $this->mp->get_access_token() .
					'" style="width:100%;" class="button" target="_blank">' . esc_html__( 'Print', 'woo-mercado-pago-module' ) . '</a>';
			}
		} else {
			echo '<label for="mercadoenvios_tracking_number">' . esc_html__( 'Shipping is pending', 'woo-mercado-pago-module' ) . '</label><br />';
		}
	}
}

new WC_MercadoEnvios_Admin_Orders();
