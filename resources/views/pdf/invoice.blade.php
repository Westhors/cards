<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
        }

        body {
            font-family: Cairo;
            background: #f2f4f7;
            padding: 8px;
            margin: 0;
            color: #333;
        }


        .invoice-box {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 18px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            page-break-inside: avoid;

        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* يجعل النص واللوجو في نفس المستوى */
            gap: 15px;
            /* border-bottom: 2px solid #eee;
            padding-bottom: 15px; */
        }

        .logo img {
            height: 90px;
            /* ارتفاع ثابت */
            object-fit: contain;
            display: block;
            /* إزالة أي مسافة أسفل الصورة */
        }


        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #1f4aa8;
        }

        .company-info {
            font-size: 13px;
            text-align: right;
            color: #666;
        }

        .section {
            margin-top: 20px;
            font-size: 14px;
        }

        .section strong {
            color: #222;
        }

        .invoice-details {

            margin-top: 15px;
        }

        .table-box {
            margin-top: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #1f4aa8;
            color: #fff;
            padding: 12px;
            font-size: 14px;
        }

        tbody td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .summary {
            width: 45%;
            margin-left: auto;
            margin-top: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #ddd;
            padding-top: 12px;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }

        .detail-col {
            font-size: 13px;
            line-height: 1.7;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            border-bottom: 1px solid #eee;
            /* الخط الخفيف بين الأسطر */
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #ddd;
            /* خط أعلى السطر الكلي */
            border-bottom: none;
            /* إزالة الخط السفلي للسطر الأخير */
            padding-top: 12px;
        }
    </style>
</head>

<body>

    <div class="invoice-box">

        <!-- HEADER -->
        <div class="header">
            <div class="logo">
                <img src="https://api.wsa-network.com/storage/media/files/b24dae41-7375-4bc4-b427-006281ec10c9-removebg-preview-46941446-43ed-49af-af63-10399d26249b.png"
                    alt="IAM Smart Store Logo" style="height:90px; object-fit:contain;">
            </div>

            <div class="company-info">
                IAM SOFTWARE PUBLISHING - L.L.C - S.P.C <br>
                Trade License No: CN-6154365 <br>
                Country: UAE - Abu Dhabi <br>
                info@iamsoftwarepublishing.net
            </div>
        </div>
        <table width="100%" style="margin-top:15px; border-top:1px solid #ddd; padding-top:12px;">
            <tr>
                <td width="33%" style="font-size:13px; line-height:1.7; vertical-align:top;">
                    <strong>Invoice #:</strong> {{ $order->order_number }} <br>
                    <strong>Order ID:</strong> {{ $order->id }} <br>
                </td>

                <td width="33%" style="font-size:13px; line-height:1.7; vertical-align:top;">
                    <strong>Invoice Date:</strong> {{ $order->created_at->format('d/m/Y') }} <br>
                    <strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'pending') }} <br>
                </td>

                <td width="40%" style="font-size:13px; line-height:1.7; vertical-align:top;">
                    <strong>Payment Reference:</strong> TXN-{{ substr($order->order_number ?? 'N/A', -7) }} <br>
                    <strong>Currency:</strong> AED
                </td>
            </tr>
        </table>

        <!-- CUSTOMER -->
        <div class="section">
            <strong>Customer:</strong>
            {{ $order->user->name ?? 'Guest User' }} 
        </div>
        <!-- ITEMS -->
        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th align="left">Item</th>
                        <th align="center">Qty</th>
                        <th align="right">Unit Price</th>
                        <th align="right">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->card->name ?? 'Deleted Product' }}</td>
                            <td align="center">{{ $item->qty }}</td>
                            <td align="right">{{ number_format($item->card->price ?? 0, 2) }} AED</td>
                            <td align="right">{{ number_format($item->qty * ($item->card->price ?? 0), 2) }} AED
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- SUMMARY -->
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>{{ number_format($order->subtotal, 2) }} AED</span>
        </div>

        @if($order->discount > 0)
        <div class="summary-row">
            <span>Active Member Discount (20%):</span>
            <span>- {{ number_format($order->discount, 2) }} AED</span>
        </div>
        @endif



        @if($couponDiscount > 0)
        <div class="summary-row">
            <span>Coupon Discount ({{ $order->promo_code }}):</span>
            <span>- {{ number_format($couponDiscount, 2) }} AED</span>
        </div>
        @endif



        <div class="summary-row">
            <span>VAT (0%):</span>
            <span>0.00 AED</span>
        </div>

        <div class="summary-row total">
            <span>Order Total:</span>
            <span style="color:#1f4aa8">
                {{ number_format($order->total_amount, 2) }} AED
            </span>
        </div>


        <!-- FOOTER -->
        <div class="footer">
            This is an electronic invoice generated automatically by IAM Smart Store.<br>
            This company is not registered for VAT. No VAT has been charged.
        </div>

    </div>

</body>

</html>
