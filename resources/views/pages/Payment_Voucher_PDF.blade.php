<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Voucher</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            margin: 0;
            /*border: 1px solid #000;*/
            padding: 10px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 40px;
        }
        .header h1 {
            margin: 0;

            font-size: 50px;
        }
        .subtitles {
            font-size: 18px;
            font-weight: bold;
            font-style: italic;
            text-align: right;
        }
        .line {
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table td, table th {
            padding: 8px;
            vertical-align: top;
        }
        .title {
            font-size: 30px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
       .title2 {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }
        .center-text {
            text-align: center;
        }
        .bg-purple th {
            color: #e1e1e1 !important;
        }
        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }
        .center-table {
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header" style="font-size: 50px">
        <h1><strong>{{ $company->company_name }}</strong></h1>
    </div>

    <table>
        <tr>
            <td style="width: 7%;"><span class="subtitles">Date:</span></td>
            <td style="width: 20%;"><span class="subtitles">{{ $loan->Date_Time }}</span></td>
            <td style="width: 2%;"></td>
            <td style="width: 10%;"></td>
            <td style="width: 38%;"></td>
            <td style="width: 23%;"><span class="subtitles">Reg No: PB 00281454</span></td>
        </tr>
    </table>

{{--    <div class="line"></div>--}}

    <table >
        <tr class="bg-purple">
            <td colspan="6" class="title">Payment Voucher</td>
        </tr>

        <br>
        <tr>
            <td width="13%" style="width: 12%;"><strong>Customer ID</strong></td>
            <td width="16%" style="width: 20%;">{{ $customers->cus_number }}</td>
            <td width="29%" style="width: 13%;"><strong>Loan ID</strong></td>
            <td width="17%" style="width: 16%;">{{ $loan->Loan_No }}</td>
            <td width="9%" style="width: 8%;"><strong>Mobile No</strong></td>
            <td width="16%" style="width: 15%;">{{ $customers->Contact_No }}</td>
        </tr>
        <tr>
            <td><strong>Customer</strong></td>
            <td colspan="5">{{ $customers->Title }}.{{ $customers->First_Name }} {{ $customers->Last_Name }}</td>
        </tr>
        <tr>
            <td><strong>Loan Amount</strong></td>
            <td colspan="3">{{ strtoupper(numberToWords($loan->Amount)) }} ONLY <strong>( {{ number_format($loan->Amount, 2, '.', ',') }} )</strong></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td><strong>Bank A/C No</strong></td>
            <td>{{ $Customer_Bank->account_number ?? 'N/A' }}</td>
            <td><strong>Acc Name</strong></td>
            <td>{{ $Customer_Bank->account_name ?? 'N/A' }}</td>
            <td><strong>Bank & Branch</strong></td>
            <td>{{ $Customer_Bank->bank_name ?? 'N/A' }} - {{ $Customer_Bank->branch ?? 'N/A' }}</td>
        </tr>
    </table>

{{--    <div class="line"></div>--}}

    <table>
        <tr class="bg-purple">
            <td colspan="6" class="title2">Office Use Only</td>
        </tr>
     </table>

    <table style="border: none;">
        <tr>
            <td style="width: 60%;">
                <div style="text-align: center;">
                    <p><strong>Description</strong></p>
                    <div style="border: 1px solid #000; height: 80px; width: 100%;"></div>
                </div>
            </td>
            <td style="width: 40%;">
                <div style="text-align: center;">
                    <p><strong>Payment Ref Number</strong></p>
                    <div style="border: 1px solid #000; height: 50px; width: 80%; margin: 0 auto;"></div>
                </div>
            </td>
        </tr>
    </table>
    <br>

    <table class="center-table">
        <tr>
            <td>......................................................</td>
            <td>......................................................</td>
            <td>......................................................</td>
        </tr>
        <tr>
            <td>Requested By</td>
            <td>Authorized Signature</td>
            <td>Accountant</td>
        </tr>
    </table>


</div>

</body>
</html>
