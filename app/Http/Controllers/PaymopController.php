<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymopController extends Controller
{


public function createIntention(Request $request, $encrypted_id)
{
    // فك تشفير الـ order_id
       try {
        $id = Crypt::decrypt($encrypted_id); // فك التشفير
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid order ID'], 400);
    }

    $order = Order::find($id); // التحقق هنا فقط
    if (!$order) {
        return response()->json(['error' => 'Order not found'], 404);
    }

    $order = Order::find($id);
    if (!$order) {
        return response()->json(['error' => 'Order not found'], 404);
    }

    $amount = $order->total;
    $method = $request->input('method', 'card');
     $nameParts = preg_split('/\s+/', trim($order->user_name), 2);
    $integrationId = $method === 'wallet'
        ? (int) env('PAYMOB_WALLET_INTEGRATION_ID')
        : (int) env('PAYMOB_CARD_INTEGRATION_ID');

    $response = Http::withToken(env('PAYMOB_SECRET_KEY'))
        ->post("https://accept.paymob.com/v1/intention/", [
            "amount" => (int) ($amount * 100),
            "currency" => "EGP",
            "payment_methods" => [$integrationId],
            "items" => [
                [
                    "name" => "Custom Payment",
                    "amount" => (int) ($amount * 100),
                    "description" => "Manual amount from form",
                    "quantity" => 1
                ]
            ],
            "billing_data" => [
                "first_name" => $nameParts[0] ?? $order->user_name,
                "last_name"  => $nameParts[1] ?? $order->user_name,

                "street" => $order ->address,
                "building" => "1",
                "phone_number" => substr(preg_replace('/\D/', '', $order->phone), 0, 15),
                "city" => $order ->address,
                "country" => "EG",
                "state" => $order ->status
            ],
            "special_reference" => "order_" . uniqid(),
            "notification_url" => url("/payment/webhook"),
            "redirection_url" => url("/pay/checkout")
        ]);

    $data = $response->json();

    Payment::create([
        'order_id' => $id,
        'paymob_order_id' => $data['intention_order_id'] ?? null,
        'amount' => (int) ($amount * 100),
        'currency' => 'EGP',
        'payment_method' => $method,
        'status' => isset($data['client_secret']) ? 'pending' : 'failed',
        'transaction_id' => $data['id'] ?? null,
        'raw_response' => $data,
    ]);

    if (isset($data['client_secret'])) {
        return redirect()->away(
            "https://accept.paymob.com/unifiedcheckout/?publicKey=" .
            env('PAYMOB_PUBLIC_KEY') .
            "&clientSecret=" . $data['client_secret']
        );
    }

    return response()->json($data);
}

    public function checkout(Request $request)
    {
        return view('website.cart', [
            'query' => $request->all()
        ]);
    }

    // Webhook من Paymob
  public function callback(Request $request)
    {
        // ✅ استقبل البيانات من Paymob
        $data = $request->all();

        // dd(vars: $data);
        Log::info("Paymob Callback Received", $data);

        // ✅ تحقق من HMAC
        $hmac   = $data['hmac'] ?? null;
        $secret = env('PAYMOB_HMAC_SECRET');

        $fields = [
            "amount_cents", "created_at", "currency", "error_occured",
            "has_parent_transaction", "id", "integration_id", "is_3d_secure",
            "is_auth", "is_capture", "is_refunded", "is_standalone_payment",
            "is_voided", "order", "owner", "pending",
            "source_data.pan", "source_data.sub_type",
            "source_data.type", "success"
        ];


        $concatenatedString = '';
        foreach ($fields as $field) {
            $concatenatedString .= $data[$field] ?? '';
        }

        // $calculatedHmac = hash_hmac('sha512', $concatenatedString, $secret);

        // if ($hmac !== $calculatedHmac) {
        //     Log::warning("❌ Invalid HMAC in Callback", [
        //         'received'   => $hmac,
        //         'calculated' => $calculatedHmac
        //     ]);
        //     return response()->json(['error' => 'Invalid HMAC'], 403);
        // }

        // ✅ تحديث حالة الدفع في DB
        $orderId = $data['order'] ?? null;

        // dd($orderId);

        if ($orderId) {
            $payment = Payment::where('paymob_order_id', $orderId)->first();

            if ($payment) {
                $isSuccess = ($data['success'] === "true" || $data['success'] == 1);

                $payment->update([
                    'status'         => $isSuccess ? 'paid' : 'failed',
                    'transaction_id' => $data['id'] ?? $payment->transaction_id,
                    'raw_response'   => json_encode($data, JSON_UNESCAPED_UNICODE),
                ]);

                Log::info("✅ Payment updated from Callback", [
                    'paymob_order_id' => $payment->paymob_order_id,
                    'status'   => $payment->status
                ]);

                // رجع العميل على صفحة نجاح/فشل
                if ($isSuccess) {

                    Order::where('id', $payment->order_id)->update(['payment_method'=> $payment->status]);
                    return redirect()->route('website.success');

                } else {
                    return redirect()->route('website.failed');
                }

            }


        }

        return response()->json(['message' => 'Callback processed']);
    }



    // صفحة رجوع العميل بعد الدفع
}
