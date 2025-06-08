<?php

namespace App\Services;

use App\Contracts\Services\PaymentServiceInterface;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class PaymentService implements PaymentServiceInterface
{
    public function processPayment(Order $order, array $paymentData): bool
    {
        try {
            $methodCode = $paymentData['method'] ?? null;

            if (!$methodCode) {
                throw new OrderException('Phương thức thanh toán không được xác định');
            }

            $method = PaymentMethod::where('code', $methodCode)->first();

            if (!$method) {
                throw new OrderException("Phương thức thanh toán '{$methodCode}' không tồn tại");
            }

            if (!$method->is_active) {
                throw new OrderException("Phương thức thanh toán '{$methodCode}' hiện không hoạt động");
            }

            // Get the corresponding payment gateway
            $gateway = $this->getPaymentGateway($methodCode);

            if (!$gateway) {
                // Simulate successful payment for method without gateway
                Log::info("Xử lý thanh toán cho đơn hàng #{$order->order_number} với phương thức {$methodCode}");
                return true;
            }

            // Payment processing via respective gateway
            $result = $gateway->processPayment($order, $paymentData);

            // Save payment information
            if ($result) {
                $order->payment_method_id = $method->id;
                $order->payment_status = 'paid';
                $order->save();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'payment_data' => $paymentData,
                'exception' => $e
            ]);

            return false;
        }
    }

    public function refundPayment(Order $order, float $amount = null): bool
    {
        try {
            if (!$order->isPaid()) {
                throw new OrderException('Unpaid orders cannot be refunded.');
            }

            $method = $order->paymentMethod;

            if (!$method) {
                throw new OrderException('Payment method not found');
            }

            // If amount not provided, full refund
            if ($amount === null) {
                $amount = $order->total;
            }

            // Kiểm tra số tiền hoàn lại
            if ($amount <= 0 || $amount > $order->total) {
                throw new OrderException('Check refund amount');
            }

            // Get the corresponding payment gateway
            $gateway = $this->getPaymentGateway($method->code);

            if (!$gateway) {
                // Giả lập hoàn tiền thành công cho phương thức không có gateway
                Log::info("Process refunds for orders #{$order->order_number} with money {$amount}");

                $order->payment_status = $amount < $order->total ? 'partially_refunded' : 'refunded';
                $order->save();

                return true;
            }

            // Processing refunds via gateway
            $result = $gateway->refundPayment($order, $amount);

            // Update payment status
            if ($result) {
                $order->payment_status = $amount < $order->total ? 'partially_refunded' : 'refunded';
                $order->save();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Payment refund failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'amount' => $amount,
                'exception' => $e
            ]);

            return false;
        }
    }

    public function getPaymentMethods(): array
    {
        return PaymentMethod::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->toArray();
    }

    public function validatePaymentMethod(string $methodCode): bool
    {
        return PaymentMethod::where('code', $methodCode)
            ->where('is_active', true)
            ->exists();
    }

    public function getPaymentGateway(string $methodCode): ?object
    {
        // This is where you can integrate with different payment gateways
        // For example: Stripe, PayPal, VNPay, etc.
        switch ($methodCode) {
            case 'cash_on_delivery':
                return null; // No gateway required for cash on delivery

            case 'bank_transfer':
                return null; //Can be processed manually

            case 'credit_card':
                // return new CreditCardGateway();
                return null; // Unintegrated Assumptions

            case 'paypal':
                // return new PayPalGateway();
                return null; // Unintegrated Assumptions

            default:
                return null;
        }
    }
}
