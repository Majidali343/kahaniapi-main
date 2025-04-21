<?php

namespace App\Services;

use bSecure\UniversalCheckout\BsecureCheckout;
use Exception;

class BsecureService
{
    protected $bsecure;

     public function __construct($storeId = null)
    {
        $this->bsecure = new BsecureCheckout([
            'client_id' => config('bsecure.client_id'),
            'client_secret' => config('bsecure.client_secret'),
            'environment' => config('bsecure.environment'),
            'store_id' => $storeId ?? config('bsecure.store_id'), // Use the passed store ID, or fallback to default
        ]);
    }

    public function createOrder($orderId, $customer, $products, $shipment)
{
    try {
        $orderData = [
            'store_id' => 'ST-008049908', // Pass the store ID here
            'order_id' => $orderId,
            'currency_code' => 'PKR',
            'total_amount' => array_sum(array_column($products, 'sale_price')),
            'sub_total_amount' => array_sum(array_column($products, 'price')),
            'discount_amount' => array_sum(array_column($products, 'discount')),
            'shipment_charges' => $shipment['charges'],
            'shipment_method_name' => $shipment['method_name'],
            'products' => $products,
            'customer' => $customer
        ];

        // Set the order details in the bSecure checkout service
        $this->bsecure->setOrderId($orderId);
        $this->bsecure->setCustomer($customer);
        $this->bsecure->setCartItems($products);
        $this->bsecure->setShipmentDetails($shipment);

        // Create the order
        $result = $this->bsecure->createOrder($orderData);

        return $result;
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

   
}
