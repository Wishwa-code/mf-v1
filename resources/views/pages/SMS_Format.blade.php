@extends('layout.admin')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>


    <style>

        .switch-container {
            display: flex;
            align-items: center;
        }
        .switch-container label {
            margin-left: 10px;
            font-size: 1.5rem; /* Adjust size here */
        }
        .toggle.btn {
            min-width:270px; /* Adjust width here */
            font-size: 0.9rem !important; /* Adjust font size here */
        }
        .toggle-group .btn {
            font-size: 0.9rem !important; /* Ensure both on and off text are smaller */
        }

        .btn-custom-success {
            background-color: #9fffa3 !important;
            border-color: #2e7916 !important;
            color: black;
        }
        .btn-custom-danger {
            background-color: #b0b0b0 !important;
            border-color: #545454 !important;
            color: black;
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
            padding: 0.75rem 1.25rem;
            background-color: #1A2942;
            color: #fff;
        }

        .profile-card .profile-image {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -50px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #3d5977;
        }

        .btn-primary {
            background-color: #1A2942;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0bab32;
        }

        .form-control {
            font-size: 15px; /* Adjust as needed */
        }
    </style>
@endsection

@section('content')

    <div class="row mt-3">
        <div class="col-6">
            <div class="card" id="leftCard">
                <div class="card-body">
                    <div class="form-group">
                        <label for="smsTypeSelect">SMS Type:</label>
                        <div class="switch-container">
                            <select class="form-control mr-3" id="smsTypeSelect" onchange="changeCardContent()">
                                <option value="select">Select Type</option>
                                <option value="customer_registration">Customer Registration</option>
                                <option value="loan_issue">Loan Issue SMS</option>
                                <option value="loan_payment">Loan Payment SMS</option>
                                <option value="payment_undo">Payment Undo SMS</option>
                                <option value="payment_reminder">Payment Reminder SMS</option>
                                <option value="payment_warning">Payment Warning SMS</option>
                                <option value="birthday_greeting">Birthday Greeting SMS</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card" id="rightCard">
                <div class="card-body">
                    <div class="form-group">
                        <label for="smsSendToggle">SMS Sending Option for selected SMS Type:</label>
                        <div class="switch-container" >
                            <div onclick="changeToggle()">
                                <input type="checkbox" id="smsSendToggle"  data-on="SMS Sending Activated"
                                       data-off="SMS Sending Deactivated" data-onstyle="custom-success" data-offstyle="custom-danger">
                            </div>
                            <label for="smsSendToggle" class="ml-2"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-3">
        <div class="col-6">
            <div class="card profile-card">
                <div class="card-header">
                    <h5 id="cardTitle">SMS Format</h5>
                </div>
                <div class="card-body" id="cardBody">
                    <!-- Content will be dynamically replaced here -->
                    Select an SMS type to see details.
                </div>

            </div>

            <br>

            <div class="card-footer">
                <button type="button" class="btn btn-dark" style="width: 100%" onclick="updateTemplate()">Update
                    Template
                </button>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card profile-card">
                <div class="card-header">
                    <h5>SMS Template Sample <span id="textLength" style="float: right; font-size: 1rem;"></span></h5>
                </div>
                <div class="card-body">
                    <h2 id="smstemptext" style="color: black; font-size: 1.2rem;"></h2>
                    <!-- Content for the other card -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/sms.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap Toggle after DOM content is loaded
            $('#smsSendToggle').bootstrapToggle({
                on: 'SMS Sending Activated',
                off: 'SMS Sending Deactivated',
                onstyle: 'custom-success',
                offstyle: 'custom-danger'
            });


        });


        function changeCardContent() {
            var smsType = document.getElementById('smsTypeSelect').value;
            var cardTitle = document.getElementById('cardTitle');
            var cardBody = document.getElementById('cardBody');


            var smsContent = '';

            switch (smsType) {
                case 'loan_issue':
                    cardTitle.innerText = 'Loan Issue SMS';
                    cardBody.innerHTML = `
                <textarea class="form-control mt-3" id="smsContent" rows="10" placeholder="Enter Loan Issue SMS content..." onkeyup="setTemplateValue()">${smsContent}</textarea>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_No')">Member No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_Name')">Member Name</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Loan_No')">Loan No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Loan_Amount')">Loan Amount</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Interest_Amount')">Interest Amount</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Repayment_Type')">Repayment Type</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Installment_Amount')">Installment Amount</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Issue_Date')">Issue Date</button>
            `;

                    break;
                case 'loan_payment':
                    cardTitle.innerText = 'Loan Payment SMS';
                    cardBody.innerHTML = `
              <textarea class="form-control mt-3" id="smsContent" rows="10" placeholder="Enter Loan Issue SMS content..." onkeyup="setTemplateValue()">${smsContent}</textarea>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_No')">Member No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_Name')">Member Name</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Loan_No')">Loan No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Payment_Date')">Payment Date</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Paid_Amount')">Paid Amount</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Loan_Balance')">Loan Balance</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Capital_Balance')">Capital Balance</button>
            `;

                    break;

                case 'payment_undo':
                    cardTitle.innerText = 'Payment Undo SMS';
                    cardBody.innerHTML = `
              <textarea class="form-control mt-3" id="smsContent" rows="10" placeholder="Enter Loan Issue SMS content..." onkeyup="setTemplateValue()">${smsContent}</textarea>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_No')">Member No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_Name')">Member Name</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Loan_No')">Loan No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Loan_Balance')">Loan Balance</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Paid_Amount')">Paid Amount</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Payment_Date')">Payment Date</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Date')">Date</button>
            `;

                    break;
                case 'payment_reminder':
                    cardTitle.innerText = 'Payment Reminder SMS';
                    cardBody.innerHTML = `
                <textarea class="form-control mt-3" id="smsContent" rows="10" placeholder="Enter Loan Issue SMS content..." onkeyup="setTemplateValue()">${smsContent}</textarea>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_No')">Member No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_Name')">Member Name</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Loan_No')">Loan No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Payment_Date')">Payment Date</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Installment_Amount')">Installment Amount</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Pending_Amount')">Pending Amount</button>
            `;

                    break;
                case 'payment_warning':
                    cardTitle.innerText = 'Payment Warning SMS';
                    cardBody.innerHTML = `
               <textarea class="form-control mt-3" id="smsContent" rows="10" placeholder="Enter Loan Issue SMS content..." onkeyup="setTemplateValue()">${smsContent}</textarea>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_No')">Member No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_Name')">Member Name</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Loan_No')">Loan No</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Payment_Date')">Payment Date</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Installment_Amount')">Installment Amount</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Pending_Amount')">Pending Amount</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Penalty_Date')">Penalty Date</button>
            `;

                    break;
                case 'birthday_greeting':
                    cardTitle.innerText = 'Birthday Greeting SMS';
                    cardBody.innerHTML = `
                <textarea class="form-control mt-3" id="smsContent" rows="10" placeholder="Enter Loan Issue SMS content..." onkeyup="setTemplateValue()">${smsContent}</textarea>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_Name')">Member Name</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Age')">Age</button>
            `;

                    break;
                case 'customer_registration':
                    cardTitle.innerText = 'Customer Registration SMS';
                    cardBody.innerHTML = `
                <textarea class="form-control mt-3" id="smsContent" rows="10" placeholder="Enter Loan Issue SMS content..." onkeyup="setTemplateValue()">${smsContent}</textarea>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_Name')">Member Name</button>
                <button type="button" class="btn btn-primary mt-3" onclick="appendToTextArea('Member_No')">Member No</button>
            `;

                    break;
                default:
                    cardTitle.innerText = 'SMS Format';
                    cardBody.innerHTML = 'Select an SMS type to see details.';
                    document.getElementById('smstemptext').innerText = ''; // Clear SMS Template Sample text
                    break;
            }
            document.getElementById('smstemptext').innerText = '';
            var smsTypeSelect = $('#smsTypeSelect').val();
            $.ajax({
                type: "POST",
                url: "/load_sms",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    smsTypeSelect: smsTypeSelect
                },
                success: function (data, textStatus, xhr) {
                    console.log(data);
                    let sms = data.sms_template;
                    document.getElementById('smsContent').value = sms.template;
                    setTemplateValue();
                    if(sms.status==="0"){
                        $('#smsSendToggle').bootstrapToggle('off');
                    }else{
                        $('#smsSendToggle').bootstrapToggle('on');
                    }

                },
            });

            setTemplateValue();


        }

        function appendToTextArea(buttonId) {


            var textArea = document.getElementById('smsContent');
            var buttonText = buttonId;
            var cursorPosition = textArea.selectionStart;
            var textBeforeCursor = textArea.value.substring(0, cursorPosition);
            var textAfterCursor = textArea.value.substring(cursorPosition);

            textArea.value = textBeforeCursor + "@" + buttonText + "@" + textAfterCursor;


            textArea.focus();
            setTemplateValue();
        }

        function setTemplateValue() {
            var smsContentTextarea = document.getElementById('smsContent');
            var smsContent = smsContentTextarea ? smsContentTextarea.value : '';
            console.log(smsContentTextarea);


            // Replace placeholders with styled text
            smsContent = smsContent.replace(/@Member_No@/g, '<span style="color: #ad000d;">001</span>');
            smsContent = smsContent.replace(/@Member_Name@/g, '<span style="color: #ad000d;">Mr. Jhon Doe</span>');
            smsContent = smsContent.replace(/@Loan_No@/g, '<span style="color: #ad000d;">A001/052/006</span>');
            smsContent = smsContent.replace(/@Loan_Amount@/g, '<span style="color: #ad000d;">100,000.00 LKR</span>');
            smsContent = smsContent.replace(/@Interest_Amount@/g, '<span style="color: #ad000d;">3%</span>');
            smsContent = smsContent.replace(/@Repayment_Type@/g, '<span style="color: #ad000d;">Weekly</span>');
            smsContent = smsContent.replace(/@Issue_Date@/g, '<span style="color: #ad000d;">2024-08-20</span>');
            smsContent = smsContent.replace(/@Payment_Date@/g, '<span style="color: #ad000d;">2024-12-23 09:25:32</span>');
            smsContent = smsContent.replace(/@Paid_Amount@/g, '<span style="color: #ad000d;">1,500.00 LKR</span>');
            smsContent = smsContent.replace(/@Loan_Balance@/g, '<span style="color: #ad000d;">88,000.00 LKR</span>');
            smsContent = smsContent.replace(/@Capital_Balance@/g, '<span style="color: #ad000d;">58,225.25 LKR</span>');
            smsContent = smsContent.replace(/@Installment_Amount@/g, '<span style="color: #ad000d;">1,438.50 LKR</span>');
            smsContent = smsContent.replace(/@Pending_Amount@/g, '<span style="color: #ad000d;">2,628.68 LKR</span>');
            smsContent = smsContent.replace(/@Penalty_Date@/g, '<span style="color: #ad000d;">2024-05-11</span>');
            smsContent = smsContent.replace(/@Age@/g, '<span style="color: #ad000d;">29</span>');
            smsContent = smsContent.replace(/@Date@/g, '<span style="color: #ad000d;">2024-05-11</span>');

            smsContent = smsContent.replace(/\n/g, '<br>');

            // Set the HTML content
            $("#smstemptext").html(smsContent);

            // Calculate text length of displayed content
            var displayedText = $("#smstemptext").text();
            var textLength = displayedText.length;


            // Update the text length display
            var textLengthElement = $("#textLength");
            textLengthElement.text(`Text Length: ${textLength}`);

            // Change color based on length
            if (textLength > 160) {
                textLengthElement.css("color", "red");
            } else {
                textLengthElement.css("color", "#f5f500");
            }
        }

    </script>
    <!-- Ensure jQuery and Bootstrap 5 are included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <!-- Include Bootstrap Toggle JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <!-- Custom JS -->


    <script>
        function adjustCardHeight() {
            var leftCardHeight = $('#leftCard').outerHeight();
            $('#rightCard').height(leftCardHeight);
        }
    </script>
@endsection
