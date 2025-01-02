@extends('layout.admin')
@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/quill-better-table@1.2.10/dist/quill-better-table.css">

    <style>
        /* Custom styles */
        .switch-container {
            display: flex;
            align-items: center;
        }

        .switch-container label {
            margin-left: 10px;
            font-size: 1.5rem;
        }

        .card {
            display: flex;
            flex-direction: column;
        }

        .profile-card {
            margin: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .profile-card .card-header {
            padding: 0.05rem 1.25rem;
            background-color: #1A2942;
            color: #fff;
            margin-bottom: 10px;
        }

        .btn-primary {
            background-color: #305fa9;
            color: #ffffff;
            border: none;
            font-size: 15px;
            padding: 8px;
            margin: 0.5px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0051cc;
        }

        .ql-editor {
            height: 90%;
        }

        .ql-size-small {
            font-size: 12px;
        }

        .ql-size-medium {
            font-size: 25px;
        }

        .ql-size-large {
            font-size: 20px;
        }

        .ql-size-huge {
            font-size: 28px;
        }

        /* Quill editor custom styles */
        .quill-container {
            height: 500px; /* Adjust the height for A4 size */
            width: 100%;
            overflow: hidden;
        }

        .ql-editor {
            height: 90%;
        }

        /* Print styles */
        @media print {
            @page {
                size: A4;
                margin: 20mm; /* Adjust margins as needed */
            }

            body {
                margin: 0;
                padding: 0;
            }

            .quill-container {
                width: 100%;
                height: auto;
            }
        }

        .custom-select {
            background-color: #046a86;
            color: #ffffff;
            margin: 5px;
            border-radius: 5px;
            padding: 10px;
            font-size: 15px;
            cursor: pointer;
        }

        .custom-select:focus {
            outline: none;
            box-shadow: 0 0 5px #1A2942;
        }

        .custom-select option {
            background-color: #ffffff;
            color: #000000;

        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Create Documents</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="row mt-3">
                                <div class="col-6">
                                    <div class="card" id="leftCard">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="smsTypeSelect">Agreement Type:</label>
                                                <div class="switch-container">
                                                    <select class="form-control mr-3" id="smsTypeSelect"
                                                            onchange="changeCardContent(this.value)">
                                                        <option value="0">Select Type</option>
                                                        <option value="Template 01">Template 01</option>
                                                        <option value="Template 02">Template 02</option>
                                                        <option value="Template 03">Template 03</option>
                                                        <option value="Template 04">Template 04</option>
                                                        <option value="Template 05">Template 05</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                            <button type="button" class="btn btn-dark" style="width: 100%"
                                                    onclick="updateTemplate()">Update
                                                Template
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div id="quillEditor" class="quill-container">
                                    <div id="editor" class="form-control mt-3"></div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-5">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-header row">
                                        <span style="font-size: 20px">Option Type: </span>
                                        <select id="categorySelect" class="form-select custom-select mt-3">
                                            <option value="loanDetails">Loan Details</option>
                                            <option value="customerDetails">Customer Details</option>
                                            <option value="guardianDetails">Guardian Details</option>
                                            <option value="guaranteeDetails">Guarantee Details 01</option>
                                            <option value="guaranteeDetails_02">Guarantee Details 02</option>
                                            <option value="guaranteeDetails_03">Guarantee Details 03</option>
                                            <option value="guaranteeDetails_04">Guarantee Details 04</option>
                                        </select>
                                    </div>
                                    <div id="buttonContainer" class="mt-3">

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div>
@endsection
@section('script')
    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/agreement.js"></script>

    <script>
        // Custom Quill blot for buttons
        const BlockEmbed = Quill.import('blots/block/embed');

        class ButtonBlot extends BlockEmbed {
            static create(value) {
                let node = super.create();
                node.setAttribute('class', 'custom-button');
                node.setAttribute('data-action', value.action);
                node.innerHTML = value.text;
                return node;
            }

            static formats(node) {
                return {
                    action: node.getAttribute('data-action'),
                    text: node.innerHTML,
                };
            }

            static value(node) {
                return {
                    action: node.getAttribute('data-action'),
                    text: node.innerHTML,
                };
            }
        }

        ButtonBlot.blotName = 'button';
        ButtonBlot.tagName = 'button';
        Quill.register(ButtonBlot);

        let quill;

        document.addEventListener('DOMContentLoaded', (event) => {
            quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{'header': '1'}, {'header': '2'}],
                        ['bold', 'italic', 'underline'],
                        [{'list': 'ordered'}, {'list': 'bullet'}],
                        [{'align': []}],
                        [{'size': ['small', 'medium', 'large', 'huge']}]
                    ],

                }
            });

            quill.on('text-change', function (delta, oldDelta, source) {
                // Update template value if needed
            });
        });


        const categorySelect = document.getElementById('categorySelect');
        const buttonContainer = document.getElementById('buttonContainer');

        categorySelect.addEventListener('change', updateButtons);

        function updateButtons() {
            const category = categorySelect.value;
            buttonContainer.innerHTML = '';

            let buttons;
            switch (category) {
                case 'loanDetails':
                    buttons = [
                        {text: 'Loan Number', value: '@Loan_Number@'},
                        {text: 'Product Name', value: '@Product_Name@'},
                        {text: 'Date', value: '@Date@'},
                        {text: 'Loan Amount', value: '@Loan_Amount@'},
                        {text: 'Interest Rate', value: '@Interest_Rate@'},
                        {text: 'Penalty Rate', value: '@Penalty_Rate@'},
                        {text: 'Installment Count', value: '@Installment_Count@'},
                        {text: 'Interest Amount', value: '@Interest_Amount@'},
                        {text: 'Loan Other Charges Total', value: '@Loan_Other_Charges_Total@'},
                        {text: 'Total Loan Amount', value: '@Total_Loan_Amount@'},
                        {text: 'Installment Amount', value: '@Installment_Amount@'},
                        {text: 'Collection Type', value: '@Collection_Type@'},
                        {text: 'Created User', value: '@Created_User@'},
                        {text: 'Lending Officer', value: '@Lending_Officer@'},

                        {text: 'Loan Issue date', value: '@Loan_Issue_Date@'},
                        {text: 'First Installment Date', value: '@First_Installment_Date@'},
                        {text: 'Maturity Date', value: '@Maturity_Date@'},
                        {text: 'Installment Period Type', value: '@Installment_Period_Type@'},
                    ];
                    break;
                case 'customerDetails':
                    buttons = [
                        {text: 'Customer No', value: '@Customer_No@'},
                        {text: 'Customer Title', value: '@Customer_Title@'},
                        {text: 'Customer Full Name', value: '@Customer_Full_Name@'},
                        {text: 'Customer Email', value: '@Customer_Email@'},
                        {text: 'Customer NIC', value: '@Customer_NIC@'},
                        {text: 'Customer Contact No', value: '@Customer_Contact_No@'},
                        {text: 'Customer Lan No', value: '@Customer_Lan_No@'},
                        {text: 'Customer Gender', value: '@Customer_Gender@'},
                        {text: 'Customer DOB', value: '@Customer_DOB@'},
                        {text: 'Customer Current address line 01', value: '@Customer_Current_Address_Line_01@'},
                        {text: 'Customer Current address line 02', value: '@Customer_Current_Address_Line_02@'},
                        {text: 'Customer Current address line 03', value: '@Customer_Current_Address_Line_03@'},
                        {text: 'Customer Permanent address line 01', value: '@Customer_Permanent_Address_Line_01@'},
                        {text: 'Customer Permanent address line 02', value: '@Customer_Permanent_Address_Line_02@'},
                        {text: 'Customer Permanent address line 03', value: '@Customer_Permanent_Address_Line_03@'},
                        {text: 'Customer City', value: '@Customer_City@'},
                        {text: 'Customer State', value: '@Customer_State@'},
                        {text: 'Customer Civil Status', value: '@Customer_Civil_Status@'},
                        {text: 'Customer Occupation', value: '@Customer_Occupation@'},
                    ];
                    break;
                case 'guardianDetails':
                    buttons = [
                        {text: 'Guardian Title', value: '@Guardian_Title@'},
                        {text: 'Guardian First Name', value: '@Guardian_First_Name@'},
                        {text: 'Guardian Last Name', value: '@Guardian_Last_Name@'},
                        {text: 'Guardian Email', value: '@Guardian_Email@'},
                        {text: 'Guardian Relation', value: '@Guardian_Relation@'},
                        {text: 'Guardian NIC', value: '@Guardian_NIC@'},
                        {text: 'Guardian_Address', value: '@Guardian_Address@'},
                        {text: 'Guardian Occupation', value: '@Guardian_Occupation@'},
                        {text: 'Guardian Contact No', value: '@Guardian_Contact_No@'},
                    ];
                    break;
                case 'guaranteeDetails':
                    buttons = [
                        {text: 'Guarantee First Name', value: '@Guarantee_First_Name@'},
                        {text: 'Guarantee Last Name', value: '@Guarantee_Last_Name@'},
                        {text: 'Guarantee Contact No', value: '@Guarantee_Contact_No@'},
                        {text: 'Guarantee NIC', value: '@Guarantee_NIC@'},
                        {text: 'Guarantee Address Line 01', value: '@Guarantee_Address_Line_01@'},
                        {text: 'Guarantee Address Line 02', value: '@Guarantee_Address_Line_02@'},
                        {text: 'Guarantee Address Line 03', value: '@Guarantee_Address_Line_03@'},
                    ];
                    break;
                case 'guaranteeDetails_02':
                    buttons = [
                        {text: 'Guarantee First Name', value: '@Guarantee_First_Name_2@'},
                        {text: 'Guarantee Last Name', value: '@Guarantee_Last_Name_2@'},
                        {text: 'Guarantee Contact No', value: '@Guarantee_Contact_No_2@'},
                        {text: 'Guarantee NIC', value: '@Guarantee_NIC_2@'},
                        {text: 'Guarantee Address Line 01', value: '@Guarantee_Address_Line_01_2@'},
                        {text: 'Guarantee Address Line 02', value: '@Guarantee_Address_Line_02_2@'},
                        {text: 'Guarantee Address Line 03', value: '@Guarantee_Address_Line_03_2@'},
                    ];
                    break;
                case 'guaranteeDetails_03':
                    buttons = [
                        {text: 'Guarantee First Name', value: '@Guarantee_First_Name_3@'},
                        {text: 'Guarantee Last Name', value: '@Guarantee_Last_Name_3@'},
                        {text: 'Guarantee Contact No', value: '@Guarantee_Contact_No_3@'},
                        {text: 'Guarantee NIC', value: '@Guarantee_NIC_3@'},
                        {text: 'Guarantee Address Line 01', value: '@Guarantee_Address_Line_01_3@'},
                        {text: 'Guarantee Address Line 02', value: '@Guarantee_Address_Line_02_3@'},
                        {text: 'Guarantee Address Line 03', value: '@Guarantee_Address_Line_03_3@'},
                    ];
                    break;
                case 'guaranteeDetails_04':
                    buttons = [
                        {text: 'Guarantee First Name', value: '@Guarantee_First_Name_4@'},
                        {text: 'Guarantee Last Name', value: '@Guarantee_Last_Name_4@'},
                        {text: 'Guarantee Contact No', value: '@Guarantee_Contact_No_4@'},
                        {text: 'Guarantee NIC', value: '@Guarantee_NIC_4@'},
                        {text: 'Guarantee Address Line 01', value: '@Guarantee_Address_Line_01_4@'},
                        {text: 'Guarantee Address Line 02', value: '@Guarantee_Address_Line_02_4@'},
                        {text: 'Guarantee Address Line 03', value: '@Guarantee_Address_Line_03_4@'},
                    ];
                    break;
            }

            buttons.forEach(button => {
                const btn = document.createElement('button');
                btn.classList.add('btn', 'btn-primary', 'm-1');
                btn.textContent = button.text;
                btn.onclick = () => insertTextAtCursor(button.value);
                buttonContainer.appendChild(btn);
            });
        }

        updateButtons(); // Initial call to display buttons for the default selected option

        function insertTextAtCursor(text) {
            let range = quill.getSelection();
            if (range) {
                quill.insertText(range.index, text);
                quill.formatText(range.index, text.length, {'bold': true, 'color': '#d20000'});
                quill.setSelection(range.index + text.length);
                quill.insertText(range.index + text.length, " ");
                quill.formatText(range.index + text.length, 1, {'bold': false, 'color': null});

            }
        }


        function changeCardContent(value) {
            $.ajax({
                type: "POST",
                url: "/load_agreement",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    smsTypeSelect: value,
                },
                success: function (data, textStatus, xhr) {
                    console.log(data.sms_template.template);
                    quill.clipboard.dangerouslyPasteHTML(data.sms_template.template);
                },
            });
        }

    </script>
@endsection
