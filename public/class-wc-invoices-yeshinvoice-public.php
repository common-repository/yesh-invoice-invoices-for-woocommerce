<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://yeshinvoice.co.il/contact.html
 * @since      1.0.0
 *
 * @package    Wc_Invoices_Yeshinvoice
 * @subpackage Wc_Invoices_Yeshinvoice/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wc_Invoices_Yeshinvoice
 * @subpackage Wc_Invoices_Yeshinvoice/public
 * @author     Yesh Invoice <support@yeshinvoice.co.il>
 */
class Wc_Invoices_Yeshinvoice_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	// init function for frontend 
	public function wc_yeshinvoices_init(){

		add_action( 'woocommerce_payment_complete', array($this, 'wc_yeshinvoices_payment_complete') );

		add_action( 'woocommerce_thankyou', array($this, 'wc_yeshinvoices_view_order_and_thankyou_page'), 20 );

		add_action( 'woocommerce_view_order', array($this, 'wc_yeshinvoices_view_order_and_thankyou_page'), 20 );

		add_filter( 'woocommerce_my_account_my_orders_columns', array($this, 'wc_yeshinvoices_account_my_orders_columns')) ;

		add_action( 'woocommerce_my_account_my_orders_column_yeshinvoice_link', array($this, 'wc_yeshinvoices_account_my_orders_columns_link'));
		
		add_action( 'woocommerce_subscription_renewal_payment_complete_', array($this, 'my_custom_function_when_subscription_renewed'));
		
		add_action( 'woocommerce_scheduled_subscription_payment_', array($this, 'my_custom_function_when_subscription_renewed'));
		
	}

    public function my_custom_function_when_subscription_renewed($subscription) {
		
		try {
			$subscription_id = $subscription->get_id();
			$order_id = $subscription->get_parent_id();
			if ($order_id) {
				wc_yeshinvoices_payment_complete($order_id);
			}	
		}
		//catch exception
		catch(Exception $e) {
		 
		}
		
		
		wc_yeshinvoices_payment_complete($subscription);
	}

     public function woocommerce_subscription_payment_complete($subscription) {

      $subscription_id = $subscription->get_id();
       $order_id = $subscription->get_parent_id();
	   
	   if ($order_id) {
		   wc_yeshinvoices_payment_complete($order_id);
	   }
    }

	//Add column on my account page in order table
	public function wc_yeshinvoices_account_my_orders_columns( $columns ){
				
		$columns['yeshinvoice_link'] = esc_html__( 'YeshInvoice Download', 'wc-invoices-yeshinvoice' );
	
		return $columns;
	}

	// Add download order invoice button to my account page in order table
	public function wc_yeshinvoices_account_my_orders_columns_link( $order ) {

		$invoice_url = get_post_meta($order->get_id(), '_wc_yeshinvoice_invoice_url', true);

		echo sprintf('<a href="%s" class="button" target="_blank">%s</a>', esc_attr($invoice_url), __('Download Invoice here', 'wc-invoices-yeshinvoice'));
	}
	
	//Api "POST" request on payment complete
	public function wc_yeshinvoices_payment_complete( $order_id ){

	    $order = wc_get_order( $order_id );  
		$order_data = $order->get_data();
		$items = array();
		  	
		foreach ( $order->get_items() as $item ) {

			$product = $item->get_product();
			$tax     = is_callable(
				[
					$item,
					'get_subtotal_tax',
				]
			) ? ( $item->get_subtotal_tax() / $item->get_quantity() ) : $order->get_item_tax( $item, false );

			$items[] = [
				'description' => $item->get_name(),
				'quantity'    => $item->get_quantity(),
				'price'       => $order->get_item_subtotal( $item, true, false ),
				'sku'         => $product->get_sku(),
				'taxable'     => $product->is_taxable(),
				'tax'         => $tax,
			];
		}
		
		$itemsCoupons = array();
		
		// Order coupons.
		if ( ! empty( $order->get_items( 'coupon' ) ) ) {

			foreach ( $order->get_items( 'coupon' ) as $coupon ) {

				$itemsCoupons[] = [
					'description' => $coupon->get_name(),
					'price'       => $coupon->get_discount(),
					'taxable'     => ! ! ( $coupon->get_discount_tax() ),
					'tax'         => $coupon->get_discount_tax(),
				];

			}
		}
		
		
    	$data = array(
				'SecretKey' 			=> esc_attr(get_option('_wc_yeshinvoices_test_secret_key', true)),
				'UserKey'				=> esc_attr(get_option('_wc_yeshinvoices_test_user_key', true)),
				'InvoiceTitle'			=> esc_attr(get_option('_wc_yeshinvoices_title', true)),
				'InvoiceNotes'			=> esc_attr(get_option('_wc_yeshinvoices_notes', true)),
				'InvoiceNotesBottom'	=> esc_attr(get_option('_wc_yeshinvoices_notes_bottom', true)),
				'CurrencyID'			=> esc_attr(get_option('_wc_yeshinvoices_currency_id', true)),
				'LangID'				=> esc_attr(get_option('_wc_yeshinvoices_invoice_lang_id', true)),
				'DocumentType'			=> esc_attr(get_option('_wc_yeshinvoices_document_type', true)),
				'sendinvoice'			=> esc_attr(get_option('_wc_yeshinvoices_send_invoice_sms', true)),
				'sendemail'			    => esc_attr(get_option('_wc_yeshinvoices_send_invoice_email', true)),
				'createshipping'	    => esc_attr(get_option('_wc_yeshinvoices_send_invoice_shipping', true)),
				'OrderDetails'		    => $order_data,
				'Products'              => $items,
				'Coupons'               => $itemsCoupons,
                'includeVat'			=> esc_attr(get_option('_wc_yeshinvoices_includetax', true)),
		);

	    $body = wp_json_encode( $data );

		
	    $url = 'https://api.yeshinvoice.co.il/api/v1/wcOrder';

	 	$response = wp_remote_post( esc_url($url), array(
		    'method'      => 'POST',
		    'timeout'     => 45,
		    'headers'     => array('Content-Type' => 'application/json'),
		    'body'        => $body,
		    )
		);

	 	if ( ! is_wp_error( $response )  && !empty($response)) {
		    
			$responsebody = wp_remote_retrieve_body($response);
			$value = json_decode($responsebody);
		    update_post_meta($order_id, '_wc_yeshinvoice_response', $responsebody);

		    if($value->Success == true){

                $result = $value->ReturnValue;
		    	update_post_meta($order_id, '_wc_yeshinvoice_invoice_url', esc_url($result->pdfurl));
		    	
		    } else {
		    	//add order notes
				$order->add_order_note( esc_attr($value->ErrorMessage) );
		    }
		}
	}

	// Download order invoice button on thank you page
	public function wc_yeshinvoices_view_order_and_thankyou_page( $order_id ){ 
   
	    $invoice_url = get_post_meta( $order_id, '_wc_yeshinvoice_invoice_url', true );

	    if($invoice_url){

		    echo '<h2>'.esc_html__('YeshInvoice', 'wc-invoices-yeshinvoice').'</h2>
		    	  <p>'.esc_html__('Your Invoice has been created successfully with Yesh Invoice.', 'wc-invoices-yeshinvoice').'</p>';

		   	echo sprintf('<a href="%s" class="button" target="_blank">%s</a>', esc_attr($invoice_url), __('Download Invoice here', 'wc-invoices-yeshinvoice'));

		}

	}

}
