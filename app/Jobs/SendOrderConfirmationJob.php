<?php

namespace App\Jobs;

use App\Mail\OrderCreatedMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->order->customer->email)
            ->send(new OrderCreatedMail($this->order));
    }
}