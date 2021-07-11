<?php
class ControllerExtensionPaymentSpotiipaynow extends Controller
{
    private $token = "";

    private function log($var)
    {
        $this->log->write($var);
    }

    private function sendCurl($url, $body)
    {
        //Set up the Header
        $header = array();
        $header[] = 'Accept: application/json';
        $header[] = 'Content-type: application/json';
        $header[] = 'Access-Control-Allow-Origin: *';
        if ($this->token != '') {
            $header[] = 'Authorization: Bearer ' . $this->token;
            //$this->log($this->token);
            //$this->log->write("INSIDE THE SECOND CURL: ".$header[3]);
        }

        //Start the CURL
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body); //JSON body
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        if ($response) {

            list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
            $other = preg_split("/\r\n|\n|\r/", $other);

            if ($this->config->get('spotii_debug')) {
                $this->log('cUrl REQUEST');
                $this->log($url);
                $this->log($header);
                $this->log($body);
                $this->log('------------');
                $this->log('cUrl RESPONSE OTHER');
                $this->log($other);
                $this->log($responseBody);
                $this->log('call end');
                $this->log('**********************');
                $this->log(explode(' ', trim(array_shift($other)), 3));
                $this->log('**********************');
            }
            
            list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
            $other = preg_split("/\r\n|\n|\r/", $other);
            list($protocol, $code) = explode(' ', trim(array_shift($other)), 2);
            return  array('status' => (int) $code, 'ResponseBody' => $responseBody);
        } else {
            return array('status' => 999, 'ResponseBody' => 'error/false');
        }
    }
    /** SPOTII OC CHANGE TO BE DONE - check merchant keys & fields */
    private function setToken()
    {
        $json = array();
        $json['error'] = '';
        $this->load->model('extension/payment/spotii_paynow');
        $auth_url =  $this->config->get('payment_spotii_paynow_test') == "sandbox" ? 'https://auth.sandbox.spotii.me/api/v1.0/merchant/authentication/' : 'https://auth.spotii.me/api/v1.0/merchant/authentication/';
        $this->log("Getting token from: " . $auth_url);
        // This contains the keys required to obtain the auth token ( FAE )
        $body = array(
            'public_key' => $this->config->get('payment_spotii_paynow_spotii_public_key'),
            'private_key' => $this->config->get('payment_spotii_paynow_merchant_private_key')
        );
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        //Use the sendCurl function

        $response = $this->sendCurl($auth_url, $body);
        $this->log->write($response['status']);
        if ($response['status'] != 200) {
            return false; // exit the method, auth has failed
        }
        $response_body = $response['ResponseBody'];
        $response_body_arr = json_decode($response_body, true);
        //$this->log->write("Response Body in SET TOKEN: " . $response_body);

        if (array_key_exists('token', $response_body_arr)) {
            $this->token = $response_body_arr['token'];
            return true;
        } else {
            $this->log->write("Error on authentication: " . $response_body);
            $this->log->write("Suggest Checking the Public/Private Keys in Spotii Payment Details");
            return false;
        }
    }

    public function index()
    {
        $this->log->write("Pay Now");

        try
        {
        //First we get set the Auth Token using the config public & private keys
        $auth_status = $this->setToken();
        $this->log->write($auth_status);
        if (!$auth_status) {
            $this->log->write("Authorization Failed");
            exit();
        }
        //Now we prepare the body to obtain the checkout URL
        $this->load->model('checkout/order');
        $this->load->model('catalog/product');
        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $this->log->write($order_info);
        $converted_total = number_format($order_info['total'] * $order_info['currency_value'], 2, '.', '');
        $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order ASC");
        $tax_amount = 0;
        $shipping_amount = 0;
        $discount_amount = 0;
        //print_r($order_info);

        foreach ($order_total_query->rows as $total) {
            if ($total['code'] == 'shipping') {
                $shipping_amount = number_format($total['value'] * $order_info['currency_value'], 2, '.', '');
            }
            if ($total['code'] == 'tax') {
                $tax_amount = number_format($total['value'] * $order_info['currency_value'], 2, '.', '');
            }
            if ($total['code'] == 'coupon') {
                $discount_amount = number_format($total['value'] * $order_info['currency_value'], 2, '.', '');
            }
        }
        $body2 = array(
            "reference" => $order_id,
            "display_reference" => $order_id,
            "description" => "Order #" . $order_id,
            "total" => $converted_total,
            "plan" => 'pay-now',
            "currency" =>$order_info['currency_code'],
            "confirm_callback_url" => $this->url->link('extension/payment/spotii_paynow/callback'),
            "reject_callback_url" => $this->url->link('checkout/checkout'),      
            // Order
            "order" => array(
                "tax_amount" => $tax_amount, // Need to check this
                "shipping_amount" => $shipping_amount, //Need to check this
                "discount" => $discount_amount,
                "customer" => array(
                    "first_name" => $order_info['firstname'],
                    "last_name" => $order_info['lastname'],
                    "email" => $order_info['email'],
                    "phone" => $order_info['telephone'],
                ),

                "billing_address" => array(
                    "title" => "",
                    "first_name" => $order_info['payment_firstname'],
                    "last_name" => $order_info['payment_lastname'],
                    "line1" => $order_info['payment_address_1'],
                    "line2" => $order_info['payment_address_2'],
                    "line3" => "",
                    "line4" => $order_info['payment_city'],
                    "state" => $order_info['payment_zone'], 
                    "postcode" => $order_info['payment_postcode'],
                    "country" => $order_info['payment_iso_code_2'],
                    "phone" => $order_info['telephone']
                ),

                "shipping_address" => array(
                    "title" => "",
                    "first_name" => $order_info['shipping_firstname'],
                    "last_name" => $order_info['shipping_lastname'],
                    "line1" => $order_info['shipping_address_1'],
                    "line2" => $order_info['shipping_address_2'],
                    "line3" => "",
                    "line4" => $order_info['shipping_city'],
                    "state" => $order_info['shipping_zone'],
                    "postcode" => $order_info['shipping_postcode'],
                    "country" => $order_info['shipping_iso_code_2'],
                    "phone" => $order_info['telephone']
                )
            )
        );
        $products = $this->cart->getProducts();
        foreach ($products as $product) {
            $lines[] = array(
                "sku" => $product["model"],
                "reference" => $product["product_id"],
                "title" => $product["name"],
                "upc" => $product["model"],
                "quantity" => $product["quantity"],
                "price" => number_format($product["price"] * $order_info['currency_value'], 2, '.', ''),
                "currency" => $order_info['currency_code'],
                "image_url" => "" //$this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
            );
        }

        $body2['order']['lines'] = $lines;
        $url = $this->config->get('payment_spotii_paynow_test') == "sandbox" ? 'https://api.sandbox.spotii.me/api/v1.0/checkouts/' :  'https://api.spotii.me/api/v1.0/checkouts/';

        $body2 = json_encode($body2, JSON_UNESCAPED_UNICODE);
        $this->log->write($body2 . "request body");
        $min =10;
        switch($order_info['currency_code']){
            case 'AED':
            case 'SAR':
                break;
            case 'BHD':
            case 'OMR':
            case 'KWD': $min=1;
                break;
            case 'USD': $min=2.72;  
                break;
                    
        }
        if($converted_total && $converted_total >= $min)
        {
            $response2 = $this->sendCurl($url, $body2);
            $response_body2 = $response2['ResponseBody'];
            $this->log->write($response_body2 . "response body");
            $index = strpos($response_body2, '{');
            $json_body = substr($response_body2, $index);
            $response_body_arr2 = json_decode($json_body, true);
            if (array_key_exists('checkout_url', $response_body_arr2)) {
                $checkout_url = $response_body_arr2['checkout_url'];
                $this->log($checkout_url);
                $data['action'] = $checkout_url;
                $params = explode("?", $checkout_url, 2);
                $split_params = explode("=", $params[1], 2);
                $form_param['token'] = $split_params[1];
                $data['form_params'] = $form_param;
                $data['button_confirm'] = 'Confirm Order';
                $data['currency'] = $order_info['currency_code'];
                $data['total'] = number_format($converted_total, 2, '.', '');
                $data['installment'] = number_format($converted_total / 4.00, 2, '.', '');
            } else { // We did not receive a Checkout URL from Spotii, setup to redirect and log failure
                $this->log("Error using Spotii Checkout API: " . $response_body2);
                $this->log($response_body2);
                if(array_key_exists('message', $response_body_arr2)){
                    $error =$response_body_arr2['code'].' - '.$response_body_arr2['message'];
                } else {
                    $error =$response_body2;
                }
                throw new Exception($error);
            }
        }
        else
        {
            $data['error'] = "You don't quite have enough in your basket: Spotii is available for purchases over AED 200. With a little more shopping, you can split your payment over 4 cost-free instalments.";
            $this->log("Error due to order value is less:[Spotii] " . $converted_total);
            $this->log($converted_total);
        }
        
        } catch (Exception $e) {
			$data['error'] = 'Spotii error: '.$e->getMessage();
		}
        return $this->load->view('extension/payment/spotii_paynow', $data);
    }
    public function callback()
    {
        $order_id = $this->session->data['order_id'];
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        //Set up the Capture CURL
        $this->setToken();
        $body = array();
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        $url_part = $this->config->get('payment_spotii_paynow_test') == "sandbox"  ? 'https://api.sandbox.spotii.me/api/v1.0/orders/' : 'https://api.spotii.me/api/v1.0/orders/';
        $url = $url_part . $order_id . '/capture/';
        $response = $this->sendCurl($url, $body);
        $response_body = $response['ResponseBody'];
        $response_body = json_decode($response_body, true);

        //Handler needs to check response and update order history
        //with addOrderHistory() method in order.php

        //Assuming we get the successful response from capture we need to compare amounts
        // Currency comparison needs to be removed, to support conversions
        $converted_total = number_format($order_info['total'] * $order_info['currency_value'], 2);

        if ($response_body['status'] == "SUCCESS"){# && $this->check_amount(number_format($response_body['amount'], 2), $response_body['currency'], $converted_total, $order_info['currency_code'])) { // && $response_body['currency'] == $order_info['currency_code'] && number_format($response_body['amount'], 2) == $converted_total){
            //Here we want to update the Order History to reflect the Order Status chosen in the setup portal
            //Then we can redicrect to the successful checkout screen
            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_spotii_paynow_order_status_id'));
            $this->response->redirect($this->url->link('checkout/success'));
            //$this->load->model('extension/payment/spotii'); REFUND STUFF
            //$this->model_extension_payment_spotii->addOrder($order_info); REFUND STUFF
        } else { // Either the status was failed or our currency / amounts didnt tally
            $this->log("Callback failure");
            $this->log->write($response_body);
            $this->response->redirect($this->url->link('checkout/failure'));
        }
    }
}