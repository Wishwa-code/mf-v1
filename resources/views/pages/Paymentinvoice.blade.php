<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Payment Receipt</title>
    <style>
        /* General styles */
        .container {
            text-align: center;
            padding-top: 50px;
        }

        #showModalBtn {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        #showModalBtn:hover {
            background-color: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #ccc;
            width: 90%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .printer-design {
            text-align: center;
        }

        .receipt {
            font-family: 'Arial', sans-serif;
            text-align: left;
            margin: 0;
        }

        .receipt .header {
            text-align: center;
        }

        .receipt .logo {
            width: 80px;
            margin: 0 auto 10px;
        }

        .receipt h1, .receipt h2 {
            margin: 5px 0;
        }

        .receipt p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .receipt .details p {
            margin: 3px 0;
        }

        .receipt .payment-info {
            margin: 10px 0;
        }

        .receipt .payment-info .item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .receipt .payment-info .description {
            font-weight: bold;
        }

        .receipt .payment-info .amount {
            text-align: right;
        }

        .receipt hr {
            border: 0;
            border-top: 1px dashed #ddd;
            margin: 10px 0;
        }

        .receipt .totals p {
            margin: 5px 0;
            font-weight: bold;
        }

        .receipt .signature {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin: 20px 0;
        }

        .receipt .signature-line {
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
        }

        .receipt .thank-you {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .printer-design, .printer-design * {
                visibility: visible;
            }
            .printer-design {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm; /* 80mm width for thermal printer */
                background: white;
            }
            .modal-content {
                width: 80mm; /* Ensures the modal content fits the thermal printer paper */
                border: none; /* Removes border during print */
            }
            .receipt {
                width: 100%;
                padding: 10px;
            }
            .printer-design button {
                display: none;
            }
        }

    </style>
</head>
<body>

<div class="container">
    <button id="showModalBtn">Show Payment Receipt</button>
    <div id="printerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="printer-design">
                <div class="receipt">
                    <div class="header">
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" class="logo">
                        <h1>{{$company->company_name}}</h1>
                        <p>Phone Number: {{$company->contact_no}}</p>
                        {{--                        <p>Email: info@company.com</p>--}}
                    </div>
                    <hr>
                    <h2>Payment Receipt</h2>
                    <div class="details">
                        <p><strong>Customer Name:</strong> John Doe</p>
                        <p><strong>Loan Number:</strong> 12345</p>
                        <p><strong>Payment Date:</strong> 21/06/2024</p>
                    </div>
                    <hr>
                    <div class="payment-info">
                        <div class="item">
                            <span class="description">Loan Amount</span>
                            <span class="amount">10,000.00</span>
                        </div>
                        <div class="item">
                            <span class="description">Payment Made</span>
                            <span class="amount">1,000.00</span>
                        </div>
                        <div class="item">
                            <span class="description">Remaining Balance</span>
                            <span class="amount">9,000.00</span>
                        </div>
                    </div>
                    <hr>
                    <div class="payment-info">
                        <div class="item">
                            <span class="description">Total Paid:</span>
                            <b><span class="amount">1,000.00</span></b>
                        </div>
                        <div class="item">
                            <span class="description">Remaining Balance:</span>
                            <b><span class="amount">9,000.00</span></b>
                        </div>
                    </div>
                    <hr>
                    <br>
                    <div class="signature">
                        <p class="signature-line">________________________</p>
                        <p class="signature-line">John Doe</p>
                    </div>
                    <hr>
                    <p class="thank-you">Thank you for your payment!</p>
                    <hr>
                </div>
            </div>
            <button onclick="printReceipt()">Print Receipt</button>
        </div>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("printerModal");

    // Get the button that opens the modal
    var btn = document.getElementById("showModalBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Print the receipt
    function printReceipt() {
        window.print();
    }

</script>
</body>
</html>
