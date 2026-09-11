<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    public function orders($email)
    {
        $customer = Customer::where('email', $email)->firstOrFail();

        return response()->json([
            'customer' => $customer,
            'orders' => $customer->orders
        ]);
    }
}
