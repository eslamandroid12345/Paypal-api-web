<?php

namespace App\Http\Controllers\Api\Paypal;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

class PayPalController extends Controller
{

    public function payment()
    {

        try {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();


        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => url('api/paypal/payment/success'),
                "cancel_url" => url('api/paypal/payment/cancel'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => request()->input('total')
                    ]
                ]
            ]
        ]);


        Log::info('Payment::::: '. print_r($response, true));

        /*
        return $response['links'];

        get link payment from this response in api

        https://www.sandbox.paypal.com/checkoutnow?token=44G517110A354170H

        "value" => "100.00" ? add request of money from product details
        */

        $paymentLink = [];

        if (isset($response['id']) && $response['id'] != null) {

            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    $paymentLink[] = $links['href'];
                    return $this->sendSuccessResponse($paymentLink[0],200,'تم الوصول الي لينك الدفع يرجي التوجهه الان لاستكمال الدفع');
                }
            }

            return $this->sendFailResponse([],404,'Something went wrong');

        } else {

            return $this->sendFailResponse([],404,$response['message'] ?? 'Something went wrong.');
        }

        } catch (\Exception $ex) {
            Log::error("Payment Exception::::::: " . $ex->getMessage());
            return $this->sendFailResponse([],500,'Something went wrong');
        }

    }


    public function paymentCancel()
    {
        return redirect()
            ->route('paypal')
            ->with('error', $response['message'] ?? 'You have canceled the transaction.');


    }

    public function paymentSuccess(Request $request)
    {
        try {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            return $this->sendSuccessResponse([],200,'Transaction complete.');

        } else {
            return $this->sendFailResponse([],404,$response['message'] ?? 'Something went wrong.');

         }
        } catch (\Exception $ex) {
            Log::error("Payment Pay Exception::::::: " . $ex->getMessage());
            return $this->sendFailResponse([],500,'Something went wrong');
        }
    }


    public function sendSuccessResponse($data,$code = 200,$message): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'code' => $code,
            'message' => $message

        ],200);
    }


    public function sendFailResponse($data = [],$code = 404,$message): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'code' => $code,
            'message' => $message

        ],404);
    }


}
