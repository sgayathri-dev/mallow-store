<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Created</title>
</head>
<body>

    <h2>Order Created Successfully</h2>

    <p>Hello {{ $order->customer->name }},</p>

    <p>Your order has been created successfully.</p>

    <h3>Order Details</h3>

    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Subtotal:</strong> {{ $order->subtotal }}</p>
    <p><strong>Tax:</strong> {{ $order->tax }}</p>
    <p><strong>Grand Total:</strong> {{ $order->grand_total }}</p>

    <h3>Items</h3>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit_price }}</td>
                    <td>{{ $item->line_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Thank you for your order!</p>

</body>
</html>