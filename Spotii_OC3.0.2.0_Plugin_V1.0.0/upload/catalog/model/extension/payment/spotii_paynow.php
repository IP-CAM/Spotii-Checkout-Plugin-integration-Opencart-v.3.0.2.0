<?php

class ModelExtensionPaymentSpotiipaynow extends Model
{


    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/spotii_paynow');
        if ($this->config->get('payment_spotii_paynow_total') > 0 && $this->config->get('payment_spotii_paynow_total') > $total) {
            $status = false;
        } else {
            $status = true;
        }
        $status = true;
        $method_data = array();
        //If the order meets the minimum size

        //Using this for development - remove for deploy
        if($status){
            $method_data = array(
                'code'     => 'spotii_paynow',
                'title'    => 'Spotii: Pay Now',
                'sort_order' => $this->config->get('payment_spotii_paynow_sort_order')
        );
    }
        return $method_data;
    }

    /** SPOTII OC CHANGE TO BE DONE - check refund capability */

    //For REFUND capability
    // public function addOrder($order_info)
    // {
    //     $this->db->query("INSERT INTO `" . DB_PREFIX . "spotii_order` SET `order_id` = '" . (int) $order_info['order_id'] . "', `order_code` = '" . (int) $order_info['order_id'] . "', `date_added` = now(), `date_modified` = now(), `currency_code` = '" . $this->db->escape($order_info['currency_code']) . "', `total` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false) . "'");

    //     return $this->db->getLastId();
    // }


}

