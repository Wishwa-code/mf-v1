<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade as PDF;

class PDFController extends Controller
{


    public function generateLoanPDF(string $id)
    {
        // Fetch the loan and associated data (similar to loan_view_Np)
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        if (!$loan) {
            return Redirect::back()->with('error', 'Loan not found.');
        }

        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $customer_payments = tableWithBranch('customer_payments')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $total_paid_amount = $customer_payments->sum('Amount');
        $customers = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();
        $User = tableWithBranch('user')->where('id', $loan->User_idUser)->first();
        $Lending_Officer = tableWithBranch('user')->where('id', $loan->lending_officer_id)->first() ?? '-';
        $Loan_Category = tableWithBranch('loan_category')->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->first();
        $Customer_Bank = tableWithBranch('customer_has_bank')->where([
            ['id', $loan->cus_bank_account],
            ['cus_id', $loan->Customer_idCustomer]
        ])->first();

        $Other_Charges = tableWithBranch('loan_other_charges')->where([
            ['Customer_Loan_idCustomer_Loan', $id]
        ])->get();

        $documents = tableWithBranch('documents')->where([
            ['Customer_Loan_idCustomer_Loan', $id]
        ])->get();

        // Fetch witnesses
        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessDetails = [];
        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idGuardian', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null]
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idCustomer', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null]
                ];
            }
        }

        // Load the view for PDF generation
        $pdf = app('dompdf.wrapper')->loadView('pages/LoanSummaryView', compact(
            'customers',
            'loan',
            'installments',
            'witnessDetails',
            'User',
            'Lending_Officer',
            'Loan_Category',
            'Other_Charges',
            'documents',
            'customer_payments',
            'total_paid_amount',
            'Customer_Bank'
        ));

        // Set paper size, orientation, and custom options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true);

        $loanNumber = $loan->Loan_No;
        // Return the generated PDF with headers and footers
        return $pdf->download("FAF_{$loanNumber}.pdf");
    }


    public function generateLoanPDFFull_Summery(string $id)    {

        // Fetch the loan and associated data (similar to loan_view_Np)
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        if (!$loan) {
            return Redirect::back()->with('error', 'Loan not found.');
        }

        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();

        $customer_payments = tableWithBranch('customer_payments','customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->where('Customer_Loan_idCustomer_Loan', $id)->get();

        $total_paid_amount = $customer_payments->sum('Amount');
        $customers = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();
        $User = tableWithBranch('user')->where('id', $loan->User_idUser)->first();
        $Lending_Officer = tableWithBranch('user')->where('id', $loan->lending_officer_id)->first();
        $Loan_Category = tableWithBranch('loan_category')->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->first();
        $Customer_Bank = tableWithBranch('customer_has_bank')->where([
            ['id', $loan->cus_bank_account],
            ['cus_id', $loan->Customer_idCustomer]
        ])->first();

        $Other_Charges = tableWithBranch('loan_other_charges')->where([
            ['Customer_Loan_idCustomer_Loan', $id]
        ])->get();

        $documents = tableWithBranch('documents')->where([
            ['Customer_Loan_idCustomer_Loan', $id]
        ])->get();

        // Fetch witnesses
        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessDetails = [];
        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idGuardian', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null]
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idCustomer', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null]
                ];
            }
        }

        // Load the view for PDF generation
        $pdf = app('dompdf.wrapper')->loadView('pages/LoanFullPDFView', compact(
            'customers',
            'loan',
            'installments',
            'witnessDetails',
            'User',
            'Lending_Officer',
            'Loan_Category',
            'Other_Charges',
            'documents',
            'customer_payments',
            'total_paid_amount',
            'Customer_Bank'
        ));

        // Set paper size, orientation, and custom options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true);

        $loanNumber = $loan->Loan_No;
        // Return the generated PDF with headers and footers
        return $pdf->download("FullLoanDetails_{$loanNumber}.pdf");
    }

    public function generatePaymentVoucherPDF(string $id)
    {
        $company = tableWithBranch('company')->first();
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        if (!$loan) {
            return Redirect::back()->with('error', 'Loan not found.');
        }

        $customers = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();
        $User = tableWithBranch('user')->where('id', $loan->User_idUser)->first();
        $Customer_Bank = tableWithBranch('customer_has_bank')->where([
            ['id', $loan->cus_bank_account],
            ['cus_id', $loan->Customer_idCustomer]
        ])->first();

        $disburse_date=tableWithBranch('Loan_Log')->where('Loan_ID', $id)->where('Type','=', 'Issue Loan')->value('Date_Time') ?? $loan->Date_Time;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pages/Payment_Voucher_PDF', compact('customers', 'loan', 'User', 'Customer_Bank','company','disburse_date'))
            ->setPaper('A4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);



        // Remove margins
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isPhpEnabled', true);
        $pdf->getDomPDF()->getCanvas()->set_opacity(0);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);

        return $pdf->download("PaymentVoucher_{$loan->cus_bank_account}.pdf");
    }


    public function loadMonthlyCollectionSummaryPDF()
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pages/MonthlyCollectionSummaryView')
            ->setPaper('A2', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        // Remove margins
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isPhpEnabled', true);
        $pdf->getDomPDF()->getCanvas()->set_opacity(0);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);

        return $pdf->download("MonthlyCollectionSummary.pdf");
    }


    private function generatePDFContent($data)
    {
//
//        // Initialize an array to hold group totals
//        $groupTotals = [];
//
//        foreach ($data['items'] as $item) {
//            // Assuming the group number is a key to aggregate totals
//            $groupNo = $item['groupNo'];
//
//            if (!isset($groupTotals[$groupNo])) {
//                $groupTotals[$groupNo] = [
//                    'loanAmount' => 0,
//                    'installment' => 0,
//                    'loanBalance' => 0,
//                    'capitalAmount' => 0,
//                    'capitalBalance' => 0,
//                ];
//            }
//
//            $groupTotals[$groupNo]['loanAmount'] += $item['loanAmount'];
//            $groupTotals[$groupNo]['installment'] += $item['installment'];
//            $groupTotals[$groupNo]['loanBalance'] += $item['loanBalance'];
//            $groupTotals[$groupNo]['capitalAmount'] += $item['capitalAmount'];
//            $groupTotals[$groupNo]['capitalBalance'] += $item['capitalBalance'];
//        }
//
//        // Prepare PDF content
//        $docDefinition = [
//            'pageOrientation' => 'landscape',
//            'pageSize' => 'A4',
//            'content' => [
//                [
//                    'table' => [
//                        'headerRow' => true,
//                        'widths' => ['auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto'],
//                        'body' => $this->formatDataForPDF($data, $groupTotals)
//                    ],
//                    'layout' => 'lightHorizontalLines'
//                ]
//            ]
//        ];
//
//        return $docDefinition;
    }

    private function formatDataForPDF($data, $groupTotals)
    {
//        $rows = [];
//
//        foreach ($data['items'] as $item) {
//            $rows[] = [
//                $item['centerNo'],
//                $item['groupNo'],
//                $item['memberNo'],
//                $item['memberName'],
//                number_format($item['loanAmount'], 2),
//                number_format($item['installment'], 2),
//                number_format($item['loanBalance'], 2),
//                number_format($item['capitalAmount'], 2),
//                number_format($item['capitalBalance'], 2),
//                // Add payment and payid details here
//            ];
//        }
//
//        // Add group totals
//        foreach ($groupTotals as $groupNo => $totals) {
//            $rows[] = [
//                '', '', '', 'Group Total',
//                number_format($totals['loanAmount'], 2),
//                number_format($totals['installment'], 2),
//                number_format($totals['loanBalance'], 2),
//                number_format($totals['capitalAmount'], 2),
//                number_format($totals['capitalBalance'], 2),
//                '', // Payment and payid details if applicable
//            ];
//        }
//
//        return $rows;
    }


}
