<?php
/* Authorize.net AIM Payment Gateway Class */
class Debit_payment extends WC_Payment_Gateway {

	// Setup our Gateway's id, description and other values
	function __construct() {
	
		
		// The global ID for this Payment method
		$this->id = "debit_puerta";

		// The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
		$this->method_title =  __("Debit", "debit_puerta");

		// The description for this Payment Gateway, shown on the actual Payment options page on the backend
		$this->method_description = __("Debit Payment Gateway Plug-in for WooCommerce", "Debit") ;

		// The title to be used for the vertical tabs that can be ordered top to bottom
		$this->title = __("Debit", "Debit");

		// If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
		$this->icon = null;

		// Bool. Can be set to true if you want payment fields to show on the checkout 
		// if doing a direct integration, which we are doing in this case
		$this->has_fields = false;

		// Supports the default credit card form
		//$this->supports = array( 'default_credit_card_form' );

		// This basically defines your settings which are then loaded with init_settings()
	
	 	$this->init_form_fields(); 
	 	

		// After init_settings() is called, you can get the settings and load them into variables, e.g:
		// $this->title = $this->get_option( 'title' );
		$this->init_settings();
		
		// Turn these settings into variables we can use
		
		// Lets check for SSL
		//add_action( 'admin_notices', array( $this,	'do_ssl_check' ) );
		
		// Save settings
		if ( is_admin() ) {
			// Versions over 2.0
			// Save our administration options. Since we are not going to be doing anything special
			
			// we have not defined 'process_admin_options' in this class so the method in the parent
			// class will be used instead
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		}		


	} // End __construct()

	// Build the administration fields for this specific Gateway
	public function init_form_fields() {
		  
      $this->form_fields = array(
        'enabled'            => array(
          'title'   => __( 'Enable/Disable', 'Debit' ),
          'type'    => 'checkbox',
          'label'   => __( 'Enable this email notification', 'Debit' ),
          'default' => 'yes',
        ),
        

        	//Aca evolución 
			'enabled' => array(
				'title'		=> __( 'Enable / Disable', 'Debit' ),
				'label'		=> __( 'Enable this payment gateway', 'Debit' ),
				'type'		=> 'checkbox',
				'default'	=> 'no',
			),
			'title' => array(
				'title'		=> __( 'Title', 'Debit' ),
				'type'		=> 'text',
				'desc_tip'	=> __( 'Payment title the customer will see during the checkout process.', 'Debit' ),
				'default'	=> __( 'Debit peyment', 'Debit' ),
			),
			'description' => array(
				'title'		=> __( 'Description', 'Debit' ),
				'type'		=> 'textarea',
				'desc_tip'	=> __( 'Payment description the customer will see during the checkout process.', 'Debit' ),
				'default'	=> __( 'Pay securely using your credit card.', 'Debit' ),
				'css'		=> 'max-width:350px;'
			),
			'api_login' => array(
				'title'		=> __( 'Debit Public password', 'Debit' ),
				'type'		=> 'text',
				'desc_tip'	=> __( 'This is the API Login provided by Debit when you signed up for an account.', 'Debit' ),
			),
			'trans_key' => array(
				'title'		=> __( 'Debit Private password', 'Debit' ),
				'type'		=> 'password',
				'desc_tip'	=> __( 'This is the Transaction Key provided by Debit when you signed up for an account.', 'Debit' ),
			),
			'environment' => array(
				'title'		=> __( 'Debit Test Mode', 'Debit' ),
				'label'		=> __( 'Enable Test Mode', 'Debit' ),
				'type'		=> 'checkbox',
				'description' => __( 'Place the payment gateway in test mode.', 'Debit' ),
				'default'	=> 'no',
			),
			
		);
	}

	public function admin_options() {
		
		?>
		<div style="display: flex; flex-direction: column; ">
		 <h1>Bienvenidos a Grupo Slant</h1>

			<p style="align-self: center;">Empezamos con la configuración de tu sistema de cobro a traves de grupo slant, sigue los siguientes pasos y podras cobrar con tu ecommerce</p>
		
		 <button style="align-self: center; background-color: #d84747b0; width: 250px; height: 50px; cursor: pointer;" ><a href="http://localhost:4200/inicio/login?redirect=sec-dev" target="_blank" style="text-decoration: none; color: black;">Generar credenciales</a></button> 
		</div>	
		
				
		  <?php
			echo '<table class="form-table">' . $this->generate_settings_html( $this->get_form_fields(), false ) . '</table>';

	}


	// Submit payment and handle response
	public function process_payment( $order_id ) {

		    global $woocommerce;
		    $customer_order = new WC_Order( $order_id );

		    // Mark as on-hold (we're awaiting the cheque)
		   

		    // Remove cart
		    $woocommerce->cart->empty_cart();
		   	$public_key = $this->settings['trans_key'];

		$payload = array(
			// Authorize.net Credentials and API Info
			"x_tran_key"           	=> $this->settings['api_login'],
			"x_login"              	=> $this->settings['trans_key'],
			"x_version"            	=> "3.1",
			
			// Order total
			"amount"             	=> $customer_order->order_total,
			
			
			// Billing Information
			"name"         	=> $customer_order->billing_first_name,
			"surname"          	=> $customer_order->billing_last_name,
			"address"            	=> $customer_order->billing_address_1,
			"city"              	=> $customer_order->billing_city,
			"state"              	=> $customer_order->billing_state,
			"zip"                	=> $customer_order->billing_postcode,
			"country"            	=> $customer_order->billing_country,
			"phone"              	=> $customer_order->billing_phone,
			"email"              	=> $customer_order->billing_email,
			
			// Shipping Information
			"x_ship_to_first_name" 	=> $customer_order->shipping_first_name,
			"x_ship_to_last_name"  	=> $customer_order->shipping_last_name,
			"x_ship_to_company"    	=> $customer_order->shipping_company,
			"x_ship_to_address"    	=> $customer_order->shipping_address_1,
			"x_ship_to_city"       	=> $customer_order->shipping_city,
			"x_ship_to_country"    	=> $customer_order->shipping_country,
			"x_ship_to_state"      	=> $customer_order->shipping_state,
			"x_ship_to_zip"        	=> $customer_order->shipping_postcode,
			
			// Some Customer Information
			"x_cust_id"            	=> $customer_order->user_id,
			"url"        	=> $_SERVER['REMOTE_ADDR'],
			
		);
		
		  
		
		$payload = wp_json_encode( $payload );
		

		// Send this payload to Authorize.net for processing
		$response = wp_remote_post( 'http://localhost:3999/api/ecommerce/pay', array(
			'method'    => 'POST',
			'headers'    => [  'Content-Type' => 'application/json',
			],
			'body'      => $payload,
			'timeout'   => 90,
			'sslverify' => false,
		) );
		

			 
			   
		if ( is_wp_error( $response ) ) {
		   $error_message = $response->get_error_message();
		   echo "Something went wrong: $error_message";
		}
		else {
			$id = json_decode($response[body]);
			 $redirect = $id->wallet->_id;
			

		   echo 'Response: <pre>';
		  
		   echo '</pre>';
		   return array(
				'result'   => 'success',
				'redirect' => 'http://localhost:4200/pagar/ecommerce/'.$this->settings['api_login'].'/'.$redirect,
			);
		}
			
				
			
		  /* 

		 global $woocommerce;
		
		// Get this Order's information so that we know
		// who to charge and how much
		$customer_order = new WC_Order( $order_id );
		
		// Are we testing right now or is it a real transaction
		$environment = ( $this->environment == "yes" ) ? 'TRUE' : 'FALSE';

		// Decide which URL to post to
		$environment_url = ( "FALSE" == $environment ) 
						   ? 'https://secure.authorize.net/gateway/transact.dll'
						   : 'https://test.authorize.net/gateway/transact.dll';

		// This is where the fun stuff begins
		$payload = array(
			// Authorize.net Credentials and API Info
			"x_tran_key"           	=> $this->trans_key,
			"x_login"              	=> $this->api_login,
			"x_version"            	=> "3.1",
			
			// Order total
			"x_amount"             	=> $customer_order->order_total,
			
			// Credit Card Information
			"x_card_num"           	=> str_replace( array(' ', '-' ), '', $_POST['debit-card-number'] ),
			"x_card_code"          	=> ( isset( $_POST['debit-card-cvc'] ) ) ? $_POST['spyr_authorizenet_aim-card-cvc'] : '',
			"x_exp_date"           	=> str_replace( array( '/', ' '), '', $_POST['spyr_authorizenet_aim-card-expiry'] ),
			
			"x_type"               	=> 'AUTH_CAPTURE',
			"x_invoice_num"        	=> str_replace( "#", "", $customer_order->get_order_number() ),
			"x_test_request"       	=> $environment,
			"x_delim_char"         	=> '|',
			"x_encap_char"         	=> '',
			"x_delim_data"         	=> "TRUE",
			"x_relay_response"     	=> "FALSE",
			"x_method"             	=> "CC",
			
			// Billing Information
			"x_first_name"         	=> $customer_order->billing_first_name,
			"x_last_name"          	=> $customer_order->billing_last_name,
			"x_address"            	=> $customer_order->billing_address_1,
			"x_city"              	=> $customer_order->billing_city,
			"x_state"              	=> $customer_order->billing_state,
			"x_zip"                	=> $customer_order->billing_postcode,
			"x_country"            	=> $customer_order->billing_country,
			"x_phone"              	=> $customer_order->billing_phone,
			"x_email"              	=> $customer_order->billing_email,
			
			// Shipping Information
			"x_ship_to_first_name" 	=> $customer_order->shipping_first_name,
			"x_ship_to_last_name"  	=> $customer_order->shipping_last_name,
			"x_ship_to_company"    	=> $customer_order->shipping_company,
			"x_ship_to_address"    	=> $customer_order->shipping_address_1,
			"x_ship_to_city"       	=> $customer_order->shipping_city,
			"x_ship_to_country"    	=> $customer_order->shipping_country,
			"x_ship_to_state"      	=> $customer_order->shipping_state,
			"x_ship_to_zip"        	=> $customer_order->shipping_postcode,
			
			// Some Customer Information
			"x_cust_id"            	=> $customer_order->user_id,
			"x_customer_ip"        	=> $_SERVER['REMOTE_ADDR'],
			
		);
	
		// Send this payload to Authorize.net for processing
		$response = wp_remote_post( 'http://localhost:3999/api/ecommerce/?wc-api=CALLBACK', array(
			'method'    => 'POST',
			'body'      => http_build_query( $payload ),
			'timeout'   => 90,
			'sslverify' => false,
		) );
	
		if ( is_wp_error( $response ) ) 
			throw new Exception( __( 'We are currently experiencing problems trying to connect to this payment gateway. Sorry for the inconvenience.', 'spyr-authorizenet-aim' ) );

		if ( empty( $response['body'] ) )
			throw new Exception( __( 'Authorize.net\'s Response was empty.', 'spyr-authorizenet-aim' ) );
			echo $response;
		// Retrieve the body's resopnse if no errors found
		$response_body = wp_remote_retrieve_body( $response );

		// Parse the response into something we can read
		foreach ( preg_split( "/\r?\n/", $response_body ) as $line ) {
			$resp = explode( "|", $line );
		}

		// Get the values we need
		$r['response_code']             = $resp[0];
		$r['response_sub_code']         = $resp[1];
		$r['response_reason_code']      = $resp[2];
		$r['response_reason_text']      = $resp[3];

		// Test the code to know if the transaction went through or not.
		// 1 or 4 means the transaction was a success
		if ( ( $r['response_code'] == 1 ) || ( $r['response_code'] == 4 ) ) {
			// Payment has been successful
			$customer_order->add_order_note( __( 'Authorize.net payment completed.', 'spyr-authorizenet-aim' ) );
												 
			// Mark order as Paid
			$customer_order->payment_complete();

			// Empty the cart (Very important step)
			$woocommerce->cart->empty_cart();

			// Redirect to thank you page
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $customer_order ),
			);
		} else {
			// Transaction was not succesful
			// Add notice to the cart
			wc_add_notice( $r['response_reason_text'], 'error' );
			// Add note to the order for your reference
			$customer_order->add_order_note( 'Error: '. $r['response_reason_text'] );
		} */

	}
	
	// Validate fields
	public function validate_fields() {
		return true;
	}
	
	// Check if we are forcing SSL on checkout pages
	// Custom function not required by the Gateway
	public function do_ssl_check() {
		if( $this->enabled == "yes" ) {
			if( get_option( 'woocommerce_force_ssl_checkout' ) == "no" ) {
				echo "<div class=\"error\"><p>". sprintf( __( "<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>" ), $this->method_title, admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) ."</p></div>";	
			}
		}		
	}

} // End of SPYR_AuthorizeNet_AIM


