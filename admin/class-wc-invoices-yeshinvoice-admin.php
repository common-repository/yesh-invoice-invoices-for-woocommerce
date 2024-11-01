<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://yeshinvoice.co.il/contact
 * @since      1.0.2
 *
 * @package    Wc_Invoices_Yeshinvoice
 * @subpackage Wc_Invoices_Yeshinvoice/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Invoices_Yeshinvoice
 * @subpackage Wc_Invoices_Yeshinvoice/admin
 * @author     Yesh Invoice <support@yeshinvoice.co.il>
 */
class Wc_Invoices_Yeshinvoice_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.2
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


	}

	// Admin init function
	public function wc_yeshinvoices_admin_init() {
          global $woocommerce;

        $woocommerce_version = $woocommerce->version;

        add_filter( 'woocommerce_settings_tabs_array', array($this, 'wc_yeshinvoices_add_settings_tab'), 50 );

        add_action( 'woocommerce_settings_tabs_yesh_invoice_settings', array($this, 'wc_yeshinvoices_settings_tabs_calllback') );

        add_action( 'woocommerce_update_options_yesh_invoice_settings', array($this, 'wc_yeshinvoices_update_options_calllback') );

        if ($woocommerce_version >= '8.2.1') {
	        add_filter( 'manage_woocommerce_page_wc-orders_columns', array($this, 'wc_yeshinvoices_shop_order_column'), 20 );
	        add_action( 'manage_woocommerce_page_wc-orders_custom_column' , array($this, 'wc_yeshinvoices_orders_list_column_content'), 20, 2 );
        	add_filter( 'add_meta_boxes', array($this, 'wc_yeshinvoices_add_meta_boxes')) ;
        }else{
        	add_filter( 'manage_edit-shop_order_columns', array($this, 'wc_old_yeshinvoices_shop_order_column'), 20 );
        	add_action( 'manage_shop_order_posts_custom_column' , array($this, 'wc_old_yeshinvoices_orders_list_column_content'), 20, 2 );
        	add_filter( 'add_meta_boxes', array($this, 'wc_old_yeshinvoices_add_meta_boxes'));
        }

        add_action( 'woocommerce_order_status_changed', array($this,'wc_yeshinvoice_order_status_change'), 10, 4 );



    }

    //Add yesh_invoice_settings tab in woocommrce setting
	public function wc_yeshinvoices_add_settings_tab( $settings_tabs ) {

        $settings_tabs['yesh_invoice_settings'] = __( 'Yesh Invoice', 'wc-invoices-yeshinvoice' );

        return $settings_tabs;
    }
 	
 	//Callback function for yesh_invoice_settings
	public function wc_yeshinvoices_settings_tabs_calllback( $sections ) {
		
		  woocommerce_admin_fields( $this->wc_yeshinvoices_get_settings() );
		
	}

	//Update option of yesh_invoice_settings
	public function wc_yeshinvoices_update_options_calllback() {

		woocommerce_update_options( $this->wc_yeshinvoices_get_settings() );
	
	}

	//Get settings on yesh_invoice_settings tab
	public function wc_yeshinvoices_get_settings() {

    	$settings = array(

	        'section_title' => array(
	            'name'     => esc_html__( 'Yesh Invoice Settings', 'wc-invoices-yeshinvoice' ),
	            'type'     => 'title',
	            'desc'     => '',
	            'id'       => 'wc-yeshinvoices_section_title'
	    	),

            'wc_yeshinvoices_test_secret_key' => array(

				'title'       => esc_html__('Secret Key','wc-invoices-yeshinvoice'),

				'description' => esc_html__('YeshInvoice SecretKey goes here.','wc-invoices-yeshinvoice'),

				'type'        => 'text',

				'id'       => '_wc_yeshinvoices_test_secret_key'

			),

			'wc_yeshinvoices_test_user_key' => array(

				'title'       => esc_html__('User Key','wc-invoices-yeshinvoice'),

				'description' => esc_html__('YeshInvoice UserKey goes here.','wc-invoices-yeshinvoice'),

				'type'        => 'text',

				'id'       => '_wc_yeshinvoices_test_user_key'

			),
			
			'wc_yeshinvoices_title' => array(

					'title'       => esc_html__('Invoice Title','wc-invoices-yeshinvoice'),

					'type'        => 'text',

					'description' => esc_html__('This controls the description which the user sees on yesh invoice.','wc-invoices-yeshinvoice'),

					'default'     => esc_html__(' Yesh Invoice Invoices for WooCommerce','wc-invoices-yeshinvoice'),

					'desc_tip'    => true,

	            	'id'       => '_wc_yeshinvoices_title'

			),

			'wc_yeshinvoices_notes_bottom' => array(

					'title'       => esc_html__('InvoiceNotesBottom','wc-invoices-yeshinvoice'),

					'type'        => 'text',

					'description' => esc_html__('This controls the description which the user sees on yesh invoice.','wc-invoices-yeshinvoice'),

					'desc_tip'    => true,

	            	'id'       => '_wc_yeshinvoices_notes_bottom'

				),
			'wc_yeshinvoices_notes' => array(

				'title'       => esc_html__('InvoiceNotes','wc-invoices-yeshinvoice'),

				'type'        => 'textarea',

				'description' => esc_html__('This controls the description which the user sees on yesh invoice.','wc-invoices-yeshinvoice'),
				
				'desc_tip'    => true,

				'id'       => '_wc_yeshinvoices_notes'

			),
				
	        
			


			'wc_yeshinvoices_currency_id' => array(

				'title'       => esc_html__('Currency ID ','wc-invoices-yeshinvoice'),

				'type'        => 'select',

				'options'	=>  array(1 => sanitize_text_field("ILS (₪)"),2 => sanitize_text_field("USD ($)"),3 => sanitize_text_field("EUR (€)"),4 => sanitize_text_field("GBP (£)"),5 => sanitize_text_field("AUD ($)"),6 => sanitize_text_field("RUB (₽)"),7 => sanitize_text_field("HKD ($)"),8 => sanitize_text_field("TRY (₺)"),9 => sanitize_text_field("CAD ($)"), 10 => sanitize_text_field("JPY (¥)"),11 => sanitize_text_field("INR (₹)")),  

				'default'     => 2,

				'id'       => '_wc_yeshinvoices_currency_id'
			),

			'wc_yeshinvoices_invoice_lang_id' => array(

				'title'       => esc_html__('Invoice Language ID','wc-invoices-yeshinvoice'),

				'type'        => 'select',

				'options'	=>  array(139 => sanitize_text_field("English"), 359 => sanitize_text_field("Hebrew"), 606 => sanitize_text_field("Russian") , 282 => sanitize_text_field("French"), 15 =>  sanitize_text_field("Arabic")),

				'default'     => 139,

				'id'       => '_wc_yeshinvoices_invoice_lang_id'

			),

			'wc_yeshinvoices_document_type' => array(

				'title'       => esc_html__('Invoice Type','wc-invoices-yeshinvoice'),

				'type'        => 'select',

				'options'	  => array( 6 => esc_html__('Reciept','wc-invoices-yeshinvoice'), 11 => esc_html__('Donation','wc-yeshin'), 9 => esc_html__('Tax Invoice/Receipt','wc-yeshin')),

				'default'     => 6,
				
				'id'          => '_wc_yeshinvoices_document_type'
			),
			
			'wc_yeshinvoices_includetax' => array(

				'title'       => esc_html__('Include Tax','wc-invoices-yeshinvoice'),

				'type'        => 'select',

				'options'	  => array( 1 => esc_html__('Automatic','wc-invoices-yeshinvoice'), 2 => esc_html__('Add taxes','wc-yeshin')),

				'default'     => 1,
				
				'description' => 'if you choose "Add taxes" it adds only to tax invoice type and if WooCommerce sent zero vat.',
				'desc' => 'if you choose "Add taxes" it adds only to tax invoice type and if WooCommerce sent zero vat.',
				
				
				'id'          => '_wc_yeshinvoices_includetax'
			),


			'wc_yeshinvoices_send_invoice_sms' => array(

				'title'       => esc_html__('Send Invoice SMS','wc-invoices-yeshinvoice'),

				'type'        => 'checkbox',

				'label'        => esc_html__('Do you want to send Invoice SMS to customer?','wc-invoices-yeshinvoice'),

				'id'       => '_wc_yeshinvoices_send_invoice_sms'

			),

			'wc_yeshinvoices_send_invoice_email' => array(

				'title'       => esc_html__('Send Invoice Email','wc-invoices-yeshinvoice'),

				'type'        => 'checkbox',

				'label'        => esc_html__('Do you want to send Invoice Email to customer?','wc-invoices-yeshinvoice'),

				'id'       => '_wc_yeshinvoices_send_invoice_email'

			),
			
			'wc_yeshinvoices_send_invoice_shipping' => array(

				'title'       => esc_html__('Create shipping certificate','wc-invoices-yeshinvoice'),

				'type'        => 'checkbox',

				'label'        => esc_html__('Do you want to create shipping certificate also?','wc-invoices-yeshinvoice'),

				'id'       => '_wc_yeshinvoices_send_invoice_shipping'

			),

		    'section_end' => array(

		        'type' => 'sectionend',

		        'id' => 'wc-yeshinvoices_section_end'
		    )
	    );
    	return apply_filters( 'wc-yeshinvoices_settings', $settings );
	}
    

	// Yesh invoice column in order table
	public function wc_yeshinvoices_shop_order_column($columns){

	    $reordered_columns = array();
	    foreach( $columns as $key => $column){
	        $reordered_columns[$key] = $column;
	        if( $key ==  'order_status' ){
	            $reordered_columns['wc-yeshinvoices-invoice'] = esc_html__( 'YeshInvoice','wc-invoices-yeshinvoice');
	        }
	    }
	    return $reordered_columns;

	}

	// Yesh invoice Download button in column in order table
	public function wc_yeshinvoices_orders_list_column_content( $column, $order ){

	    switch ( $column )
	    {
	        case 'wc-yeshinvoices-invoice' :

	        	$order = wc_get_order( $order->ID );
	            $invoice_url = get_post_meta( $order->ID, '_wc_yeshinvoice_invoice_url', true );

	            if($invoice_url){

	            	echo sprintf('<a href="%s" class="button" target="_blank">%s</a>', esc_attr($invoice_url), __('Download Invoice here', 'wc-invoices-yeshinvoice'));

	            }

	        break;
	    }
	}

	// Yesh invoice metabox for Order detail page
	public function wc_yeshinvoices_add_meta_boxes(){

		add_meta_box( 'wc_yeshinvoices_link_button', esc_html__('YeshInvoice Download','wc-invoices-yeshinvoice'), array( $this, 'wc_yeshinvoices_for_download' ), wc_get_page_screen_id( 'shop-order' ) );

	}

	// Callback function for Yesh invoice metabox
	public function wc_yeshinvoices_for_download( $order ){

		$invoice_url = esc_attr(get_post_meta( $order->ID, '_wc_yeshinvoice_invoice_url', true )) ? esc_attr(get_post_meta( $order->ID, '_wc_yeshinvoice_invoice_url', true )) : '';
		
		if($invoice_url == ''){

			echo '<div class="yeshinvoices-box">'.esc_html__('No Invoice genrated yet.','wc-invoices-yeshinvoice').'</div>';

		
		} else {

			echo '<div class="yeshinvoices-box"><p>'.__('Thank you! Your invoice has been created with YeshInvoice.','wc-invoices-yeshinvoice').'</p>';
			echo sprintf('<a href="%s" class="button" target="_blank">%s</a></div>', esc_attr($invoice_url), __('Download Invoice here', 'wc-invoices-yeshinvoice'));
		
		}

	}

	 // Yesh invoice metabox for Order detail page
	public function wc_old_yeshinvoices_add_meta_boxes(){

		add_meta_box( 'wc_yeshinvoices_link_button', esc_html__('YeshInvoice Download','wc-invoices-yeshinvoice'), array( $this, 'wc_old_yeshinvoices_for_download' ), 'shop_order', 'side', 'core' );

	}

		// Yesh invoice Download button in column in order table
	public function wc_old_yeshinvoices_orders_list_column_content( $column, $post_id ){

	    switch ( $column )
	    {
	        case 'wc-yeshinvoices-invoice' :

	        	$order = wc_get_order( $post_id );
	            $invoice_url = get_post_meta( $post_id, '_wc_yeshinvoice_invoice_url', true );

	            if($invoice_url){

	            	echo sprintf('<a href="%s" class="button" target="_blank">%s</a>', esc_attr($invoice_url), __('Download Invoice here', 'wc-invoices-yeshinvoice'));

	            }

	        break;
	    }
	}

	// Yesh invoice column in order table
	public function wc_old_yeshinvoices_shop_order_column($columns){

	    $reordered_columns = array();
	    foreach( $columns as $key => $column){
	        $reordered_columns[$key] = $column;
	        if( $key ==  'order_status' ){
	            $reordered_columns['wc-yeshinvoices-invoice'] = esc_html__( 'YeshInvoice','wc-invoices-yeshinvoice');
	        }
	    }
	    return $reordered_columns;

	}


	// Callback function for Yesh invoice metabox
	public function wc_old_yeshinvoices_for_download(){

		global $post;

		$invoice_url = esc_attr(get_post_meta( $post->ID, '_wc_yeshinvoice_invoice_url', true )) ? esc_attr(get_post_meta( $post->ID, '_wc_yeshinvoice_invoice_url', true )) : '';
		
		if($invoice_url == ''){

			echo '<div class="yeshinvoices-box">'.esc_html__('No Invoice genrated yet.','wc-invoices-yeshinvoice').'</div>';

		
		} else {

			echo '<div class="yeshinvoices-box"><p>'.__('Thank you! Your invoice has been created with YeshInvoice.','wc-invoices-yeshinvoice').'</p>';
			echo sprintf('<a href="%s" class="button" target="_blank">%s</a></div>', esc_attr($invoice_url), __('Download Invoice here', 'wc-invoices-yeshinvoice'));
		
		}

	}
    
    // Send yesh invoice on change order status to complete
	public function wc_yeshinvoice_order_status_change ( $order_id, $old_status, $new_status, $order ) 
    {   
    	$invoice_url = get_post_meta( $order_id, '_wc_yeshinvoice_invoice_url', true );
    	
    	if($new_status != "completed" || $invoice_url)
    	{
    		return; 
    	}
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
				'Coupons'               => $itemsCoupons				
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
	

}
