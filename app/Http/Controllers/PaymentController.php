<?php

// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Unicodeveloper\Paystack\Paystack;

class PaymentController extends Controller
{
    public function redirectToGateway(Request $request)
    {
        $paystack = new Paystack();

        $response = $paystack->transaction->initialize([
            'amount' => $request->amount, // amount in kobo
            'email' => $request->email,
            'reference' => 'unique_transaction_reference',
            'currency' => 'NGN',

        ]);

        return $paystack->getAuthorizationUrl()->redirectNow();
    }

    public function handleGatewayCallback(Request $request)
    {
        $paystack = new Paystack();
        $paymentDetails = $paystack->getPaymentData();
        $payment = Payment::create([
            'transaction_reference' => $paymentDetails->data->reference,
            'amount' => $paymentDetails->data->amount,
            'status' => $paymentDetails->data->status,
        ]);

        return redirect()->route('dashboard')->with('success', 'Payment successful!');
    }



    public function initiateRepayment(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string',
            'expiry_month' => 'required|string',
            'expiry_year' => 'required|string',
            'cvv' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $paymentDetails = [
            'amount' => $request->input('amount') * 100, // Paystack API expects amount in kobo
            'email' => auth()->user()->email, // Assuming you have authentication
            'metadata' => [
                'loan_id' => $request->input('loan_id'),
            ],
            'card' => [
                'number' => Crypt::encrypt($request->input('card_number')),
                'cvv' => $request->input('cvv'),
                'expiry_month' => $request->input('expiry_month'),
                'expiry_year' => $request->input('expiry_year'),
            ],
        ];

        try {
            $paymentResponse = Paystack::charge()->card($paymentDetails);

            $payment = Payment::create([
                'transaction_reference' => $paymentResponse->data->reference,
                'amount' => $paymentResponse->data->amount,
                'status' => $paymentResponse->data->status,
            ]);
            return response()->json($paymentResponse, 200);
        } catch (\Throwable $th) {
            $payment = Payment::create([
                'transaction_reference' => $paymentResponse->data->reference,
                'amount' => $paymentResponse->data->amount,
                'status' => $paymentResponse->data->status,
            ]);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

}
