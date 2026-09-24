<?php

namespace App\Controllers;

use Razorpay\Api\Api;

class PaymentController extends BaseController
{
    public function verifyPayment()
    {
        $apiKey = "rzp_live_S0bV3KqHlmJZvU";
        $apiSecret = "duNgdkXsTiLfYWcwfqcqIMyg";

        $api = new Api($apiKey, $apiSecret);

        $input = $this->request->getPost();

        $razorpayOrderId = $input['razorpay_order_id'];
        $razorpayPaymentId = $input['razorpay_payment_id'];
        $razorpaySignature = $input['razorpay_signature'];

        $attributes = [
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $razorpaySignature,
        ];

        try {
            $api->utility->verifyPaymentSignature($attributes);

            // Payment verified, save payment details to database
            // Example:
            // $this->db->table('payments')->insert([...]);

            return json_encode(['success' => true, 'message' => 'Payment verified successfully']);
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
