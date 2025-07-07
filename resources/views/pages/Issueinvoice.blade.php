<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <title>Loan Invoice ({{$loan->Loan_No}})</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 12px; /* Reduced base font size */
        }

        /* Ensure A4 size for printing */
        @page {
            size: A4;
            margin: 20mm;
        }

        /* Invoice container styles */
        .invoice-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px; /* Reduced padding */
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .invoice-header img {
            width: 80px; /* Reduced logo width */
            height: auto;
            margin-right: auto; /* Push logo to the left */
        }

        .invoice-header .invoice-info {
            text-align: right;
            flex: 1; /* Allow date and invoice number to take remaining space */
        }

        .invoice-header .invoice-info p {
            margin: 0;
            font-size: 10px; /* Reduced font size */
            color: #666;
        }

        .invoice-header .invoice-info .invoice-date {
            font-size: 12px; /* Reduced font size */
            margin-bottom: 5px;
        }

        .invoice-header .invoice-info .invoice-no {
            font-size: 12px; /* Reduced font size */
        }

        section {
            margin-bottom: 20px; /* Reduced margin */
        }

        h2 {
            font-size: 18px; /* Reduced font size */
            margin-bottom: 10px; /* Reduced margin */
            color: #333;
        }

        p {
            margin-bottom: 8px; /* Reduced margin */
            line-height: 1.4; /* Adjusted line height */
        }

        strong {
            color: #555;
        }

        .details-grid, .charges-grid, .guarantee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px; /* Reduced gap */
            margin-bottom: 15px; /* Reduced margin */
        }

        .details-grid div, .charges-grid p, .guarantee-grid div {
            padding: 8px; /* Reduced padding */
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .installment-table {
            margin-bottom: 20px; /* Reduced margin */
            page-break-inside: avoid; /* Ensures the entire section doesn't break across pages */
        }

        .installment-table table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            table-layout: fixed;
            page-break-inside: auto; /* Ensures table rows don't break across pages */
        }

        .installment-table th, .installment-table td {
            border: 1px solid #ddd;
            padding: 8px; /* Reduced padding */
            text-align: left;
            font-size: 10px; /* Reduced font size */
        }

        .invoice-footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px; /* Reduced padding */
            margin-top: 15px; /* Reduced margin */
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .left-signature, .right-signature {
            width: 40%; /* Adjust width as needed */
            border-top: 1px solid #333; /* Example border for signature line */
            margin-top: 10px; /* Adjust spacing */
            text-align: center; /* Center align text */
            margin: 0 auto; /* Center horizontally */
        }

        .date {
            text-align: center;
            margin-top: 10px;
        }

        .buttons {
            text-align: center;
            margin-top: 20px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px; /* Reduced padding */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-size: 14px; /* Reduced font size */
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .required-documents {
            margin-bottom: 20px; /* Reduced margin */
        }

        .required-documents h2 {
            font-size: 18px; /* Reduced font size */
            margin-bottom: 10px; /* Reduced margin */
            color: #333;
        }

        .required-documents ul {
            list-style-type: none;
            padding: 0;
        }

        .required-documents li {
            margin-bottom: 8px; /* Reduced margin */
            padding: 8px; /* Reduced padding */
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .charges-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px; /* Reduced margin */
        }

        .charges-table th, .charges-table td {
            border: 1px solid #ddd;
            padding: 8px; /* Reduced padding */
        }

        .charges-table th {
            text-align: center;
            background-color: #f2f2f2;
            color: #555;
            text-transform: uppercase;
            font-size: 10px; /* Reduced font size */
        }

        .charges-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .charges-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Print-specific styles */
        @media print {
            @page {
                margin: 0;
            }

            body {
                margin: 0;
            }

            .buttons {
                display: none;
            }

            header, footer {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="buttons">
    <label for="dataToggle">Select Mode:</label>
    <select id="dataToggle" onchange="toggleDataMode()">
        <option value="fill">Fill Data</option>
        <option value="empty">Without Data</option>
    </select>
    <button onclick="printInvoice()">Print</button>
    <button onclick="downloadPDF()">Download as PDF</button>
</div>

<div class="invoice-container" id="invoice">
    <header class="invoice-header">
        <img src="{{ $company->logo ? asset('storage/' . $company->logo) : 'https://via.placeholder.com/100' }}" alt="Logo">
        <div class="invoice-info">
            <p class="invoice-date">Date: {{ date('Y-m-d') }}</p>
            {{--            <p class="invoice-no">Invoice No: {{$loan->Loan_No}}</p>--}}
        </div>
    </header>

    <section class="loan-details">
        <h2>Loan Details</h2>
        <div class="details-grid">
            <div>
                <p><strong>Loan Number:</strong> {{$loan->Loan_No}}</p>
                <p><strong>Loan Amount:</strong> Rs.{{ number_format($loan->Amount, 2) }}</p>

                @php
                    $Total_Other_Amount = $loan->Total_Other_Amount;
                    $loan_amount = $loan->Amount;

                    // Calculate the issued amount based on Other_Amount_Balance
                    if ($loan->Other_Amount_Balance == "0") {
                        $issued_amount = $loan_amount - $Total_Other_Amount;
                    } elseif ($loan->Other_Amount_Balance == "1") {
                        $issued_amount = $loan_amount + $Total_Other_Amount;
                    } elseif ($loan->Other_Amount_Balance == "-1") {
                        $issued_amount = $loan_amount;
                    } else {
                        // Default case if Other_Amount_Balance has an unexpected value
                        $issued_amount = $loan_amount;
                    }
                @endphp

                <p><strong>Issued Amount:</strong> Rs.{{ number_format($issued_amount, 2) }}</p>
            </div>
            <div>
                <p><strong>Interest Rate:</strong> {{$loan->Interest_Rate}}%</p>
                <p><strong>Loan Term:</strong> {{$loan->Installment_Count}}</p>
                <p><strong>Date:</strong> {{$loan->Date_Time}}</p>
            </div>
        </div>
    </section>

    <section class="customer-details">
        <h2>Customer Details</h2>
        <div class="details-grid">
            <div>
                <p><strong>Customer Number:</strong> {{$customers->cus_number}}</p>
                <p><strong>Contact Number:</strong> {{$customers->Contact_No}}</p>
                <p><strong>NIC:</strong> {{$customers->Nic}}</p>
                <p><strong>Gender:</strong> {{$customers->Gender}}</p>
                <p><strong>Date of Birth:</strong> {{$customers->Dob}}</p>
            </div>
            <div>
                <p><strong>Customer Name:</strong> {{$customers->First_Name}} {{$customers->Last_Name}}</p>
                <p><strong>Contact Number 2:</strong> {{$customers->contact_number_2}}</p>
                <p><strong>Address:</strong> {{$customers->Address}},{{$customers->Address_02}},{{$customers->Address_03}}</p>
                <p><strong>Landline:</strong> {{$customers->Landline}}</p>
            </div>
        </div>
    </section>

    <section class="document-charges">
        <h2>Other Charges

            @if($loan->Other_Amount_Balance == "0")
                (Deduct Other Charges from Capital)
            @elseif($loan->Other_Amount_Balance == "1")
                (Add Loan Charges to the Capital)
            @else
                (Loan Charges Separate from Loan)
            @endif



        </h2>
        <table class="charges-table">
            <thead>
            <tr>
                <th>Description</th>
                <th>Amount (Rs.)</th>
            </tr>
            </thead>
            <tbody>
            @php
                $totalAmount = 0;
            @endphp

            @foreach($other_charges as $item)
                @if($item->charge_type === "Percentage")
                    @php
                        // Assuming $loan->Amount is the loan amount and $item->Amount is the percentage value
                        $percentage = $item->Amount;
                        $loan_amount = $loan->Amount;
                        // Calculate the percentage amount
                        $calculated_amount = ($percentage / 100) * $loan_amount;
                        // Add the calculated amount to the total
                        $totalAmount += $calculated_amount;
                        // Append the percentage to the description
                        $description = $item->Description . ' (' . $percentage . '%)';
                    @endphp

                    <tr>
                        <td><strong>{{ $description }}</strong></td>
                        <td style="text-align: center;">{{ number_format($calculated_amount, 2) }}</td>
                    </tr>
                @else
                    @php
                        // Add the fixed amount to the total
                        $totalAmount += $item->Amount;
                    @endphp

                    <tr>
                        <td><strong>{{ $item->Description }}</strong></td>
                        <td style="text-align: center;">{{ number_format($item->Amount, 2) }}</td>
                    </tr>
                @endif
            @endforeach

            </tbody>
            <tfoot>
            <tr>
                <td><strong>Total</strong></td>
                <td style="text-align: center;"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
            </tfoot>
        </table>
    </section>

    @if($company->product_editable=="1")
        <section class="installment-table">
            <h2>Installment Schedule</h2>
            <table>
                <thead>
                <tr>
                    <th>No</th>
                    <th>Ins. Date</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Total Installment Amount</th>
                </tr>
                </thead>
                <tbody class="installment-data">
                @foreach($installments as $item)
                    <tr>
                        <td>{{ $item->No }}</td>
                        <td>{{ $item->Installment_Date }}</td>
                        <td>{{ number_format($item->Total_Amount, 2) }}</td>
                        <td>{{ number_format($item->Paid_Amount, 2) }}</td>
                        <td>{{ number_format($item->Total_Balance, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    @else
        <section class="installment-table">
            <h2>Installment Schedule</h2>
            <table>
                <thead>
                <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Capital Balance</th>
                    <th>Interest Balance</th>
                    <th>Total Installment Amount</th>
                    <th>Penalty Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($installments as $item)
                    <tr>
                        <td>{{ $item->No }}</td>
                        <td>{{ $item->Installment_Date }}</td>
                        <td>{{ number_format($item->capital_amount, 2) }}</td>
                        <td>{{ number_format($item->interest_amount, 2) }}</td>
                        <td>{{ number_format($item->Total_Balance, 2) }}</td>
                        <td>{{ $item->Panelty_date }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    @endif



    <section class="required-documents">
        <h2>Required Documents</h2>
        <ul>
            @foreach($required_documents as $item)
                <li><strong>{{$item->Name}}</strong></li>
            @endforeach
        </ul>
    </section>

    <section class="guarantee-details">
        <h2>Guarantee Details</h2>
        <div class="details-grid">
            @foreach($witnessData as $item)
                <div>
                    <p><strong>{{ $item['type'] }} Name:</strong> {{ $item['First_Name'] }} {{ $item['Last_Name'] }}</p>
                    <p><strong>{{ $item['type'] }} Contact:</strong> {{ $item['Contact_No'] }}</p>
                    <p><strong>{{ $item['type'] }} NIC:</strong> {{ $item['Nic'] }}</p>
                    <p><strong>{{ $item['type'] }} Gender:</strong> {{ $item['Gender'] }}</p>
                    <p><strong>{{ $item['type'] }} Date of Birth:</strong> {{ $item['Dob'] }}</p>
                    <p><strong>{{ $item['type'] }} Address:</strong> {{ $item['Address'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
    <br><br><br>
    <div class="signatures" id="signatures">
        <div class="left-signature">
            <span>{{$customers->First_Name}} {{$customers->Last_Name}}</span>
        </div>
        <div class="right-signature">
            <span>Company</span>
        </div>
        <div class="date">

        </div>
    </div>

    <footer class="invoice-footer">
        <p>Thank you for choosing our services.</p>
    </footer>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>

    function printInvoice() {
        window.print();
    }

    async function downloadPDF() {
        const { jsPDF } = window.jspdf;

        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        const invoiceElement = document.getElementById('invoice');
        const canvas = await html2canvas(invoiceElement, { scale: 2 });

        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 210; // Width of the A4 page in mm
        const pageHeight = 297; // Height of the A4 page in mm
        const imgProps = doc.getImageProperties(imgData);
        const imgHeight = (imgProps.height * imgWidth) / imgProps.width;
        let heightLeft = imgHeight;
        let position = 0;

        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        doc.save('{{$loan->Loan_No}}.pdf');
    }

</script>
<script>
    function toggleDataMode() {
        const mode = document.getElementById("dataToggle").value;

        const installmentData = document.querySelectorAll(".installment-data tr");
        const chargeData = document.querySelectorAll(".charge-data");

        if (mode === "empty") {
            installmentData.forEach(row => {
                const cells = row.querySelectorAll("td");
                if (cells.length > 1) {
                    // Store original values before clearing
                    if (!cells[cells.length - 2].hasAttribute('data-original')) {
                        cells[cells.length - 2].setAttribute('data-original', cells[cells.length - 2].textContent);
                    }
                    if (!cells[cells.length - 1].hasAttribute('data-original')) {
                        cells[cells.length - 1].setAttribute('data-original', cells[cells.length - 1].textContent);
                    }

                    cells[cells.length - 2].textContent = '';
                    cells[cells.length - 1].textContent = '';
                }
            });

            chargeData.forEach(row => {
                row.querySelectorAll('td').forEach(cell => {
                    if (!cell.hasAttribute('data-original')) {
                        cell.setAttribute('data-original', cell.textContent);
                    }
                    cell.textContent = '';
                });
            });

        } else {
            // Restore installment data
            installmentData.forEach(row => {
                const cells = row.querySelectorAll("td");
                if (cells.length > 1) {
                    const original2 = cells[cells.length - 2].getAttribute('data-original');
                    const original1 = cells[cells.length - 1].getAttribute('data-original');

                    if (original2 !== null) cells[cells.length - 2].textContent = original2;
                    if (original1 !== null) cells[cells.length - 1].textContent = original1;
                }
            });

            // Restore charge table
            chargeData.forEach(row => {
                row.querySelectorAll('td').forEach(cell => {
                    const original = cell.getAttribute('data-original');
                    if (original !== null) cell.textContent = original;
                });
            });
        }
    }

    // ✅ On initial load, force "empty" mode
    window.addEventListener('DOMContentLoaded', function () {
        const dataToggle = document.getElementById("dataToggle");
        dataToggle.value = "empty";
        toggleDataMode();
    });
</script>




</body>
</html>
