<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

class PayPalController extends Controller
{

    public function index()
    {
        return view('auth.paypal');
    }

    public function payment(Request $request): RedirectResponse
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();


        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.payment.success'),
                "cancel_url" => route('paypal.payment/cancel'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "100.00"
                    ]
                ]
            ]
        ]);


        /*

        return $response['links'];

        get link payment from this response in api

        https://www.sandbox.paypal.com/checkoutnow?token=44G517110A354170H

        "value" => "100.00" ? add request of money from product details

        */

        if (isset($response['id']) && $response['id'] != null) {

            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            return redirect()
                ->route('cancel.payment')
                ->with('error', 'Something went wrong.');

        } else {

            return redirect()
                ->route('create.payment')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }

    }


    public function paymentCancel(): RedirectResponse
    {
        return redirect()
            ->route('paypal')
            ->with('error', $response['message'] ?? 'You have canceled the transaction.');
    }

    public function paymentSuccess(Request $request): RedirectResponse
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            return redirect()
                ->route('paypal')
                ->with('success', 'Transaction complete.');
        } else {
            return redirect()
                ->route('paypal')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }
}
