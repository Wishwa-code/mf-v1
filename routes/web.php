<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\AssetManagementController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KYCController;
use App\Http\Controllers\LoanCategoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PendingLoanController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\TodayPaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::get('/storage_link', function () {
    Artisan::call('storage:link');
});

// API for branch hierarchy dropdown
Route::get('/api/branch-hierarchy/{branchId}', '\App\Http\Controllers\CenterController@getBranchHierarchy');


Route::get('/','\App\Http\Controllers\UserController@showdashboard')->name('home');
//user
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::get('/user','\App\Http\Controllers\UserController@index')->name('pages.user');
Route::get('/user/update/{id}','\App\Http\Controllers\UserController@edit')->name('pages.edit');
Route::post('/login/store','\App\Http\Controllers\UserController@store')->name('user.store');
Route::post('/signup','\App\Http\Controllers\UserController@create')->name('user.signup');
Route::get('/logout','\App\Http\Controllers\UserController@logout')->name('user.logout');

Route::get('/forget_password_view', function () {
    return view('forget_password');
})->name('forget_password');
Route::post('/check_mail','\App\Http\Controllers\UserController@check_mail')->name('user.check_mail');


Route::get('/recover_password', function () {
    return view('forget_password');
})->name('recover_password');
Route::post('/recover_password','\App\Http\Controllers\UserController@recover_password')->name('user.recover_password');

Route::get('/privileges', function () {
    return view('pages.Privilages');
});
Route::post('/privileges', [UserController::class, 'privileges'])->name('privileges.save');
Route::get('/privileges/load/{id}', [UserController::class, 'showprivileges'])->name('privileges.load');

Route::get('/designation-privileges', function () {
    return view('pages.DesignationPrivileges');
});

// designation privileges JSON
Route::post('/designation/privileges/save', [UserController::class, 'saveDesignationPrivileges']);
Route::get('/designation/privileges/load/{id}', [UserController::class, 'loadDesignationPrivileges']);

// designation utility
Route::get('/designation/exists', [UserController::class, 'designationExists']);
Route::post('/designation/create-for-branch', [UserController::class, 'createDesignationForBranch']);

//designation
Route::post('/user/designation','\App\Http\Controllers\UserController@designation')->name('privileges.designation');
Route::post('/update-designation','\App\Http\Controllers\UserController@updatedesignation')->name('privileges.updatedesignation');
Route::post('/designation/delete','\App\Http\Controllers\UserController@deleteDesignation')->name('designation.delete');


//Group

Route::get('/customergroupassign','\App\Http\Controllers\GroupController@assign_group')->name('customergroupassign');
Route::get('/customergroup','\App\Http\Controllers\GroupController@load_group')->name('customergroup.load_group');
Route::get('/viewgroups','\App\Http\Controllers\GroupController@index')->name('customergroup');
Route::post('/customergroup','\App\Http\Controllers\GroupController@store')->name('customergroup.store');
Route::post('/group/update','\App\Http\Controllers\GroupController@update')->name('center.update');
Route::get('/group/delete/{id}','\App\Http\Controllers\GroupController@destroy')->name('center.destroy');
Route::post('/assigngrouomember','\App\Http\Controllers\GroupController@assigngrouomember')->name('getcustomergroup.assigngrouomember');
Route::get('/load_group/{id}','\App\Http\Controllers\GroupController@load_group_details')->name('load_group_details');
Route::post('/get_customer_details', '\App\Http\Controllers\GroupController@getCustomerDetails')->name('getCustomerDetails');
Route::post('/remove_customer_from_group', '\App\Http\Controllers\GroupController@removeCustomerFromGroup')->name('removeCustomerFromGroup');

//customer
Route::get('/customers','\App\Http\Controllers\CustomerController@load')->name('customers.load');
Route::post('/customers/status','\App\Http\Controllers\CustomerController@change_status')->name('customers.change_status');
Route::get('/viewcustomer','\App\Http\Controllers\CustomerController@edit')->name('customers.edit');
Route::post('/customers','\App\Http\Controllers\CustomerController@store')->name('customers.store');
Route::post('/update-customer','\App\Http\Controllers\CustomerController@updateCustomer')->name('customers.updateCustomer');
Route::post('/update-customer-location','\App\Http\Controllers\CustomerController@updateCustomerLocation')->name('customers.updateCustomerLocation');
Route::get('/customers/{id}','\App\Http\Controllers\CustomerController@index')->name('customers.index');
Route::post('/savecustomerdocument','\App\Http\Controllers\CustomerController@create')->name('customers.create');
Route::get('/customerdoc/{id}','\App\Http\Controllers\CustomerController@show')->name('customers.show');
Route::get('/cusdocument/delete/{id}','\App\Http\Controllers\CustomerController@destroy')->name('customers.destroy');
Route::get('/showcustomers','\App\Http\Controllers\CustomerController@edit')->name('customers.edit');
Route::post('/save-files-customer', 'App\Http\Controllers\CustomerController@saveFiles')->name('customers.files');
Route::get('/load_customers/{id}','\App\Http\Controllers\CustomerController@load_customers')->name('customers.load_customers');
Route::get('/load_individual_customer','\App\Http\Controllers\CustomerController@load_individual_customer')->name('customers.load_individual_customer');
Route::get('/customer_road_map/{id}','\App\Http\Controllers\CustomerController@customer_road_map')->name('customers.customer_road_map');
Route::get('/showcustomerssaving','\App\Http\Controllers\CustomerController@edit_saving')->name('customers.edit_saving');
Route::get('/customer_saving/{id}','\App\Http\Controllers\CustomerController@customer_saving')->name('customers.customer_saving');
Route::get('/get-account-transactions/{id}','\App\Http\Controllers\CustomerController@get_account_transactions')->name('customers.get_account_transactions');

//Route::get('/customer_road_map', function () {
//    return view('pages.CustomerRoadMap');
//});

Route::post('/save-bank-details', 'App\Http\Controllers\CustomerController@saveBank')->name('customers.saveBank');
Route::post('/save-bank-details-single', 'App\Http\Controllers\CustomerController@saveBankSingle')->name('customers.saveBankSingle');
Route::post('/customers/bank/remove/{id}', 'App\Http\Controllers\CustomerController@remove_bank')->name('customers.remove_bank');

Route::get('/customers/delete/{id}','\App\Http\Controllers\CustomerController@deletecus')->name('customers.deletecus');
Route::get('/customers/bank/{id}','\App\Http\Controllers\CustomerController@load_bank')->name('load_bank');


Route::get('/calender', function () {
    return view('pages.Calender');
});


//loan product
Route::get('/product','\App\Http\Controllers\LoanCategoryController@create')->name('loancategory.create');
Route::get('/viewproduct','\App\Http\Controllers\LoanCategoryController@index')->name('loancategory.index');
Route::post('/loancategory','\App\Http\Controllers\LoanCategoryController@store')->name('loancategory.store');
Route::get('/savecustomerdocument/remove/{id}','\App\Http\Controllers\LoanCategoryController@destroy')->name('loancategory.destroy');
Route::get('/loancatedoc/{id}','\App\Http\Controllers\LoanCategoryController@show')->name('loancategory.show');
Route::get('/othercharge/remove/{id}','\App\Http\Controllers\LoanCategoryController@remove_other_charges')->name('loancategory.remove_other_charges');
Route::get('/remove_doc/remove/{id}','\App\Http\Controllers\LoanCategoryController@remove_doc')->name('loancategory.remove_doc');
Route::get('/loancategory/{id}','\App\Http\Controllers\LoanCategoryController@edit')->name('loancategory.edit');
Route::get('/open-file/{filename}', '\App\Http\Controllers\LoanCategoryController@openFile')->name('open.file');
Route::get('/load_product_details/{id}', '\App\Http\Controllers\LoanCategoryController@load_product_details')->name('open.load_product_details');

Route::get('/loan-products/{id}/edit','\App\Http\Controllers\LoanCategoryController@editProduct')->name('loan-products.edit');
Route::put('/loan-products/{id}', '\App\Http\Controllers\LoanCategoryController@update')->name('loan-products.update');

Route::get('/update_product_with_branch/{id}','\App\Http\Controllers\LoanCategoryController@update_product_for_branch')->name('loancategory.update_product_for_branch');


//loan
Route::get('/loan','\App\Http\Controllers\LoanController@index')->name('loan.index');
Route::get('/loanview/{id}','\App\Http\Controllers\LoanController@loan_view_Np')->name('loan.loan_view_Np');
Route::get('/loanview/{id}/{type}','\App\Http\Controllers\LoanController@loan_view_Np')->name('loan.loan_view_Np');
Route::get('/loan_step_2/{id}','\App\Http\Controllers\LoanController@show')->name('loan.show');
Route::get('/loancategory/cost/{id}','\App\Http\Controllers\LoanCategoryController@load_charger')->name('loancategory.load_charger');
Route::post('/loan','\App\Http\Controllers\LoanController@store')->name('loan.store');
Route::post('/save-files', 'App\Http\Controllers\LoanController@saveFiles')->name('save.files');

Route::get('/loanviewajax/{id}','\App\Http\Controllers\LoanController@loan_view_ajax')->name('loan.loan_view_ajax');
Route::get('/loadbank/{id}','\App\Http\Controllers\LoanController@loadbank')->name('loan.loadbank');


Route::post('/reduce_capital', [LoanController::class, 'reduce_capital'])->name('loan.reduce_capital');
Route::get('/reduce_capital_view/{id}', [LoanController::class, 'reduce_capital_view'])->name('loan.reduce_capital_view');




//Pending Loan
Route::get('/pendingloan','\App\Http\Controllers\PendingLoanController@index')->name('pendingloan.index');
Route::get('/pendingloanload','\App\Http\Controllers\PendingLoanController@create')->name('pendingloan.create');
Route::post('/pendingloanissue','\App\Http\Controllers\PendingLoanController@show')->name('pendingloan.show');
Route::get('/pendingloandelete/{id}','\App\Http\Controllers\PendingLoanController@destroy')->name('pendingloan.destroy');
Route::get('/show_loan/{id}/{loan}','\App\Http\Controllers\PendingLoanController@edit')->name('loan.show_loan');

//Total Outstanding
Route::get('/total-outstanding-data','\App\Http\Controllers\UserController@totalOutstandingData')->name('total-outstanding.data');

//Weekly Not Paid
Route::get('/weekly-not-paid-data','\App\Http\Controllers\UserController@weeklyNotPaidData')->name('weekly-not-paid.data');

//Penalty Balance
Route::get('/penalty-balance-data','\App\Http\Controllers\UserController@penaltyBalanceData')->name('penalty-balance.data');


//payment
Route::get('/payment_step_1','\App\Http\Controllers\PaymentLoanController@index')->name('payment_step_1.index');
Route::get('/payment_step_1_loan_load','\App\Http\Controllers\PaymentLoanController@create')->name('payment_step_1.create');
Route::get('/payment_step_1_show_loan/{id}/{loan}','\App\Http\Controllers\PaymentLoanController@edit')->name('payment_step_1.edit');
Route::get('/payment_step_2/{id}','\App\Http\Controllers\PaymentLoanController@show')->name('payment_step_2.index');
Route::post('/payment_save','\App\Http\Controllers\PaymentLoanController@store')->name('payment_save.store');
Route::get('/installment_log/{id}','\App\Http\Controllers\PaymentLoanController@ins_log')->name('payment_save.ins_log');
Route::get('/payment','\App\Http\Controllers\TodayPaymentController@index')->name('payment_save.index');
Route::post('/today_payment_load_check','\App\Http\Controllers\TodayPaymentController@create')->name('payment_save.create');
Route::post('/latePayment_load_check','\App\Http\Controllers\TodayPaymentController@latePayment')->name('latePayment');
Route::post('/today_payment_load_check_bulk','\App\Http\Controllers\TodayPaymentController@Bulk_create')->name('payment_save.Bulk_create');


Route::get('/today_payment_load_check_loan/{id}','\App\Http\Controllers\TodayPaymentController@create_view')->name('payment_save.load');


Route::post('/payment_save_today','\App\Http\Controllers\TodayPaymentController@store')->name('payment_save.store');
Route::get('/latePayment','\App\Http\Controllers\TodayPaymentController@show')->name('payment_save.show');
Route::get('/viewpayment','\App\Http\Controllers\TodayPaymentController@view_payment')->name('payment_save.view_payment');
Route::get('/date_wise_installment','\App\Http\Controllers\TodayPaymentController@date_wise_installment_view')->name('payment_save.date_wise_installment_view');
Route::post('/view_payment_load','\App\Http\Controllers\TodayPaymentController@view_payment_load')->name('payment_save.view_payment_load');
Route::post('/view_date_wise_installment','\App\Http\Controllers\TodayPaymentController@view_date_wise_installment')->name('payment_save.view_date_wise_installment');
Route::post('/add_slip','\App\Http\Controllers\TodayPaymentController@addSlip')->name('payment_save.addSlip');
//Route::post('/payment_save_today_2','\App\Http\Controllers\TodayPaymentController@edit')->name('payment_save.edit');
Route::post('/update_reduce_balance','\App\Http\Controllers\TodayPaymentController@update_reduce_balance')->name('payment_save.update_reduce_balance');
Route::get('/deduct_report','\App\Http\Controllers\PaymentLoanController@deduct_report')->name('payment_step_1.deduct_report');
Route::get('/deduct_report_view','\App\Http\Controllers\PaymentLoanController@deduct_report_view')->name('payment_step_1.deduct_report_view');

Route::get('/bulk_repayment','\App\Http\Controllers\TodayPaymentController@bulk_repayment')->name('payment_save.bulk_repayment');
Route::get('/daily','\App\Http\Controllers\TodayPaymentController@daily_repayment')->name('payment_save.daily_repayment');
Route::post('/dailycollection','\App\Http\Controllers\TodayPaymentController@dailycollection')->name('payment_save.dailycollection');
Route::post('/dailycollection/change_collector','\App\Http\Controllers\TodayPaymentController@change_collector')->name('payment_save.change_collector');


Route::get('/get-loan-details-model','\App\Http\Controllers\TodayPaymentController@getLoanDetails')->name('payment_save.getLoanDetails');


//undo payment
Route::post('/undoPayment/{id}','\App\Http\Controllers\TodayPaymentController@undoPayment')->name('payment_save.undoPayment');




//center
Route::get('/center','\App\Http\Controllers\CenterController@show')->name('center.show');
Route::get('/viewcenter','\App\Http\Controllers\CenterController@index')->name('center.index');
Route::post('/center','\App\Http\Controllers\CenterController@store')->name('center.store');
Route::post('/center/update','\App\Http\Controllers\CenterController@update')->name('center.update');
Route::get('/center/delete/{id}','\App\Http\Controllers\CenterController@destroy')->name('center.destroy');
Route::get('/viewloancenter/{id}','\App\Http\Controllers\CenterController@create')->name('viewloancenter');

//collection
Route::get('/collection','\App\Http\Controllers\CollectionController@index')->name('collection.index');
Route::post('/collection','\App\Http\Controllers\CollectionController@create')->name('collection.create');
Route::post('/collection_view', [\App\Http\Controllers\CollectionController::class, 'show'])->name('collection.show');
Route::post('/collection_view_2', [\App\Http\Controllers\CollectionController::class, 'show_2'])->name('collection.show_2');
Route::post('/collection_confirm', [\App\Http\Controllers\CollectionController::class, 'store'])->name('collection.store');
Route::get('/pending_collection', [\App\Http\Controllers\CollectionController::class, 'pending_payment'])->name('collection.pending_payment');
Route::post('/pending_collection', [\App\Http\Controllers\CollectionController::class, 'create_pending_payment'])->name('collection.pending_collection_filter');
Route::get('/approved_collection', [\App\Http\Controllers\CollectionController::class, 'approved_payment'])->name('collection.approved_payment');
Route::post('/approved_collection', [\App\Http\Controllers\CollectionController::class, 'create_approved_payment'])->name('collection.approved_collection_filter');


//guardian
Route::get('/guardian','\App\Http\Controllers\GuardianController@load')->name('customers.load');
Route::get('/guardian/status/{id}','\App\Http\Controllers\GuardianController@change_status')->name('customers.change_status');

Route::get('/guardian/delete/{id}','\App\Http\Controllers\GuardianController@delete_guardian')->name('customers.delete_guardian');


Route::get('/viewguardian','\App\Http\Controllers\GuardianController@edit')->name('customers.edit');
Route::post('/guardian','\App\Http\Controllers\GuardianController@store')->name('customers.store');
Route::post('/update-guardian','\App\Http\Controllers\GuardianController@updateCustomer')->name('customers.updateCustomer');
Route::get('/guardian/{id}','\App\Http\Controllers\GuardianController@index')->name('customers.index');
Route::post('/saveguardiandocument','\App\Http\Controllers\GuardianController@create')->name('customers.create');
Route::get('/guardiandoc/{id}','\App\Http\Controllers\GuardianController@show')->name('customers.show');
Route::get('/guardiandocument/delete/{id}','\App\Http\Controllers\GuardianController@destroy')->name('customers.destroy');
Route::get('/showguardian','\App\Http\Controllers\GuardianController@edit')->name('customers.edit');
Route::post('/save-files-guardian', 'App\Http\Controllers\GuardianController@saveFiles')->name('customers.files');
Route::get('/guarantor/load/{id}/{cus}', 'App\Http\Controllers\GuardianController@load_customer')->name('customers.load_customer');
Route::get('/guarantor/load/details/{id}/{type}', 'App\Http\Controllers\GuardianController@load_customer_details')->name('customers.load_customer_details');


Route::get('/calculator','\App\Http\Controllers\LoanController@create')->name('loan.create');



//report
Route::get('/customerreport_details','\App\Http\Controllers\ReportController@index')->name('report.index');
Route::get('/customerreport_details_recover_officer','\App\Http\Controllers\ReportController@recover_officer_wise_index')->name('report.recover_officer_wise_index');


Route::get('/AllLoanDetailReport','\App\Http\Controllers\ReportController@showFullLoanDetailReport')->name('fullLoanDetailReport');

Route::get('/loanreport','\App\Http\Controllers\ReportController@edit')->name('report.edit');
Route::post('/report','\App\Http\Controllers\ReportController@store')->name('report.store');
Route::get('/late_payment_report', function () {
    return view('pages.ArreaseReport');
});
Route::post('/late_payment_report','\App\Http\Controllers\TodayPaymentController@edit')->name('payment_save.edit');
Route::get('/customerreport','\App\Http\Controllers\CustomerController@update')->name('customers.update');
Route::get('/borrowerreport','\App\Http\Controllers\CustomerController@borrower')->name('customers.borrower');
Route::get('/loanreport','\App\Http\Controllers\LoanController@edit')->name('loan.edit');
Route::get('/repaymentreport','\App\Http\Controllers\CollectionController@repaymentreportindex')->name('repaymentreport.index');
Route::post('/repaymentreport','\App\Http\Controllers\CollectionController@createrepaymentreport')->name('repaymentreport.create');
Route::get('/customerrepaymentreport','\App\Http\Controllers\CollectionController@customerrepaymentreport')->name('customerrepaymentreport.index');
Route::post('/customerrepaymentreport','\App\Http\Controllers\CollectionController@customerrepaymentreportview')->name('customerrepaymentreportview.create');


Route::get('/cashbook','\App\Http\Controllers\CollectionController@cashbookreport')->name('cashbookreport.index');
Route::post('/cashbook','\App\Http\Controllers\CollectionController@cashbookreportview')->name('cashbookreportview.create');

Route::get('/par','\App\Http\Controllers\CollectionController@parreport')->name('parreport.index');
Route::post('/par','\App\Http\Controllers\CollectionController@partview')->name('partview.create');

Route::get('/par_weekly','\App\Http\Controllers\CollectionController@parweeklyreport')->name('parweeklyreport.index');
Route::post('/par_weekly','\App\Http\Controllers\CollectionController@partweeklyview')->name('partweeklyview.create');

Route::get('/sms_history','\App\Http\Controllers\SmsController@SMS_History')->name('partview.SMS_History');


Route::get('/generate-loan-pdf/{loanId}', [PDFController::class, 'generateLoanPDF'])->name('generate-loan-pdf');
Route::get('/generate-loan-pdf/{loanId}', [PDFController::class, 'generateLoanPDFFull_Summery'])->name('generate-loan-pdf');
Route::get('/generate-voucher-pdf/{loanId}', [PDFController::class, 'generatePaymentVoucherPDF'])->name('generate-voucher-pdf');


Route::get('/ViewDateWiseCashFlow','\App\Http\Controllers\TodayPaymentController@view_DateWise_CashFlow')->name('payment_save.view_payment');
Route::post('/cashflow/datewise','\App\Http\Controllers\TodayPaymentController@view_DateWise_CashFlow_Data')->name('payment_save.view_DateWise_CashFlow_Data');

Route::get('/MonthlyCollectionSummary','\App\Http\Controllers\TodayPaymentController@MonthlyCollectionSummary')->name('payment_save.MonthlyCollectionSummary');
Route::get('/MonthlyCollectionSummary/{center}', [TodayPaymentController::class, 'loadMonthlyCollectionSummary'])->name('generateMonthlyCollectionSummaryPDF');
Route::get('/MonthlyCollectionSummaryView', function () {
    return view('pages.MonthlyCollectionSummaryView');
});
Route::get('/monthly-collection-pdf', [PDFController::class, 'loadMonthlyCollectionSummaryPDF'])->name('loadMonthlyCollectionSummaryPDF');



Route::get('/totalloanstatus', function () {
    return view('pages.loanStatusReport');
});

Route::get('/totalLoanStatusReport', function () {
    return view('pages.totalLoanStatusReport');
});







// routes/web.php
Route::get('/generateMonthlyCollectionSummaryPDF', [PDFController::class, 'generateMonthlyCollectionSummaryPDF'])->name('generateMonthly');


//expenses
Route::get('/income','\App\Http\Controllers\ReportController@income')->name('report.income');
Route::post('/income','\App\Http\Controllers\ReportController@saveexpenses')->name('report.saveexpenses');
Route::get('/view_income','\App\Http\Controllers\ReportController@viewincome')->name('report.viewincome');

Route::get('/expenses','\App\Http\Controllers\ReportController@create')->name('report.create');
Route::post('/expenses_categories', '\App\Http\Controllers\ReportController@store')->name('expenses_categories.store');
Route::delete('/expenses_categories/{id}', '\App\Http\Controllers\ReportController@destroy')->name('categories.destroy');

Route::post('/income_categories', '\App\Http\Controllers\ReportController@income_store')->name('expenses_categories.income_store');
Route::delete('/income_categories/{id}', '\App\Http\Controllers\ReportController@income_destroy')->name('categories.income_destroy');


Route::get('/view_expenses','\App\Http\Controllers\ReportController@viewexpenses')->name('report.viewexpenses');
Route::get('/deleteexpenses/{id}','\App\Http\Controllers\ReportController@deleteexpenses')->name('report.deleteexpenses');
Route::get('/deleteincome/{id}','\App\Http\Controllers\ReportController@deleteincome')->name('report.deleteincome');


//invoice
Route::get('/invoice/{id}','\App\Http\Controllers\LoanController@invoice')->name('loan.invoice');
Route::get('/payment-print','\App\Http\Controllers\PaymentLoanController@payment_print')->name('payment.payment_print');
Route::post('/view_payment_load_reciept/{id}/{status}','\App\Http\Controllers\TodayPaymentController@payment_reciept')->name('payment_save.payment_reciept');

//SMS
//Route::post('/send-sms','\App\Http\Controllers\SmsController@index')->name('sms.index');
Route::get('/sms','\App\Http\Controllers\SmsController@create')->name('sms.create');
Route::post('/save_sms','\App\Http\Controllers\SmsController@store')->name('sms.store');
Route::post('/load_sms','\App\Http\Controllers\SmsController@show')->name('sms.show');
Route::post('/updatesmsstatus','\App\Http\Controllers\SmsController@edit')->name('sms.edit');


//Backup
Route::get('/backup', '\App\Http\Controllers\BackupController@createBackup')->name('backup.create');
Route::get('/backupdb', '\App\Http\Controllers\BackupController@showBackupPage')->name('backup.show');
Route::post('/backupdb', '\App\Http\Controllers\BackupController@runBackup')->name('backup.run');



//company
Route::get('/company','\App\Http\Controllers\CompanyController@index')->name('company.index');
Route::post('/company-profile','\App\Http\Controllers\CompanyController@store')->name('company.store');


//settings
Route::get('/setting','\App\Http\Controllers\CompanyController@setting')->name('company.setting');
Route::post('/shortcuts','\App\Http\Controllers\CompanyController@shortcuts')->name('company.shortcuts');
Route::get('/shortcuts/all','\App\Http\Controllers\CompanyController@show')->name('company.show');

//Bank
Route::get('/bank_account','\App\Http\Controllers\BankController@index')->name('bank.index');
Route::post('/bank_account','\App\Http\Controllers\BankController@store')->name('bank.store');
Route::get('/bank/change/{id}','\App\Http\Controllers\BankController@destroy')->name('bank.destroy');
Route::get('/bank/view_log/{id}','\App\Http\Controllers\BankController@create')->name('bank.create');

//Chq
Route::get('/chq','\App\Http\Controllers\BankController@chq')->name('bank.chq');
Route::get('/process_chq/{id}','\App\Http\Controllers\BankController@chq_process')->name('bank.chq_process');
Route::get('/return_chq/{id}','\App\Http\Controllers\BankController@return_chq')->name('bank.return_chq');
Route::get('/cancel_chq/{id}','\App\Http\Controllers\BankController@cancel_chq')->name('bank.cancel_chq');


//loan document
Route::get('/load_loan_document/{id}','\App\Http\Controllers\PendingLoanController@check_the_document')->name('bank.check_the_document');
Route::get('/load_loan_approval/{id}','\App\Http\Controllers\PendingLoanController@check_the_approval')->name('bank.check_the_approval');
Route::post('/approve_loan','\App\Http\Controllers\PendingLoanController@approve_loan')->name('bank.approve_loan');

//agreement
Route::get('/agreement', function () {
    return view('pages.Agreement');
});
Route::get('/agreement_view/{type}/{id}','\App\Http\Controllers\AgreementController@agreement_view')->name('sms.agreement_view');
Route::post('/save_agreement','\App\Http\Controllers\AgreementController@store')->name('sms.store');
Route::post('/load_agreement','\App\Http\Controllers\AgreementController@show')->name('sms.show');
Route::get('/load_agreement_doc','\App\Http\Controllers\AgreementController@load_agreement_doc')->name('sms.load_agreement_doc');

//loan_settlement
Route::get('/loan_settlement','\App\Http\Controllers\PaymentLoanController@settlment')->name('loan_settlement.index');
Route::get('/loan_settlement_load','\App\Http\Controllers\PaymentLoanController@settlement_create')->name('loan_settlement.create');
Route::get('/get-loan-details/{id}','\App\Http\Controllers\PaymentLoanController@getLoanDetails')->name('loan_settlement.getLoanDetails');
Route::post('/settle-loan','\App\Http\Controllers\PaymentLoanController@settleLoan')->name('loan_settlement.settleLoan');




Route::post('/capitalbalance','\App\Http\Controllers\TodayPaymentController@check_all_capital')->name('loan_settlement.capitalbalance');
Route::get('/loan-log/{loanId}','\App\Http\Controllers\TodayPaymentController@fetchLoanLog')->name('loan-log.fetch');



//Accounting
Route::post('/assets/store', [AssetManagementController::class, 'store'])->name('asset.store');
Route::post('/type/store', [AssetManagementController::class, 'show'])->name('type.store');
Route::get('/AddAssetManagement', [AssetManagementController::class, 'index'])->name('asset.index');
Route::delete('/delete-asset-type/{id}', [AssetManagementController::class, 'destroy'])->name('asset.destroy');
Route::get('/AssetManagement', [AssetManagementController::class, 'create'])->name('asset.create');
Route::post('/asset/update', [AssetManagementController::class, 'updateAsset'])->name('asset.update');
Route::post('/book-values', [AssetManagementController::class, 'book_value'])->name('bookValues.store');
Route::post('/asset-management/search', [AssetManagementController::class, 'search'])->name('assetManagement.search');

Route::get('/CashFlow', function () {
    return view('pages.Accounting.CashFlow');
});
Route::get('/get-cashflow-data', [CashFlowController::class, 'getCashFlowData']);


Route::get('/CashFlowMonthly', function () {
    return view('pages.Accounting.CashFlowMonthly');
});
Route::get('/get-cashflow-data-monthly', [CashFlowController::class, 'getCashFlowDataMonthly']);



Route::get('/InnerBankTransfer', [BankController::class, 'show'])->name('asset.show');
Route::post('/bank-transfer', [BankController::class, 'edit'])->name('bankTransfer.store');


Route::get('/ProfitLoss', [BankController::class, 'profitView'])->name('profitreport.profit');
Route::post('/ProfitLoss', [BankController::class, 'profit'])->name('profitreport.profit');




Route::get('/loanStatus', [BankController::class, 'loanStatusView'])->name('profitreport.loanStatus');
Route::post('/loanStatus', [BankController::class, 'loanStatusView_2'])->name('profitreport.loanStatus');




Route::get('/AddManualJournal', function () {
    return view('pages.Accounting.AddManualJournal');
});




Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.handle');
Route::get('/daily_repayment_sheet', [TransactionController::class, 'create'])->name('transaction.handle');
Route::post('/daily_repayment_sheet_filter', [TransactionController::class, 'create'])->name('transaction.daily_repayment_sheet_filter');
Route::get('/get-groups-by-center/{center_id}', [TransactionController::class, 'getGroupsByCenter']);

Route::get('/repaymntseet9', [TransactionController::class, 'repaymntseet9'])->name('transaction.repaymntseet9');
Route::post('/repaymntseet9_filter', [TransactionController::class, 'repaymntseet9'])->name('transaction.repaymntseet9_filter');

Route::get('/daily_repayment_sheet_finwin', [TransactionController::class, 'create_for_finwin'])->name('transaction.finwin');
Route::post('/daily_repayment_sheet_filter_finwin', [TransactionController::class, 'create_for_finwin'])->name('transaction.daily_repayment_sheet_filter_finwin');



Route::get('/daily_repayment_sheet_lasantha', [TransactionController::class, 'create_lasantha'])->name('transaction.handle_lasantha');
Route::post('/daily_repayment_sheet_filter_lasantha', [TransactionController::class, 'create_lasantha'])->name('transaction.daily_repayment_sheet_filter_lasantha');

//noble
Route::get('/daily_repayment_sheet_hm', [TransactionController::class, 'create_hm'])->name('transaction.handle');
Route::post('/daily_repayment_sheet_filter_hm', [TransactionController::class, 'create_hm'])->name('transaction.daily_repayment_sheet_filter_hm');




Route::post('/update-loan-category', [LoanCategoryController::class, 'saving_update'])->name('update.loan.category');

Route::get('/showsettleloan', [TransactionController::class, 'show'])->name('settleLoan.show');
Route::get('/showsettleloan_filter', [TransactionController::class, 'edit'])->name('settleLoan.show');



Route::get('/BankReconciliation', [BankController::class, 'BankReconciliationView'])->name('profitreport.BankReconciliationView');
Route::post('/BankReconciliation', [BankController::class, 'BankReconciliation'])->name('Bank.BankReconciliation');
Route::post('/bank-log/update-status', [BankController::class, 'updateStatus'])->name('bankLog.updateStatus');



Route::get('/search-reconciliation', [BankController::class, 'searchReconciliation'])->name('search.reconciliation');
Route::get('/get-last-reconciliation', [BankController::class, 'getLastReconciliation'])->name('get.last.reconciliation');
Route::get('/BankReconsilationInside', function () {
    return view('pages.Accounting.BankReconsilationInside');
});
Route::post('/bank-reconciliation/store', [BankController::class, 'storeReconciliation'])->name('bankReconciliation.store');
Route::get('/BankReconsilationInside/{id}/{status}', [BankController::class, 'reconciliation'])->name('bankReconciliation.reconciliation');
Route::delete('/bank-reconciliation/delete', [BankController::class, 'Reconciliation_delete'])->name('delete.reconciliation');
Route::post('/reconciliation/store', [BankController::class, 'reconciliation_store'])->name('reconciliation.store');
Route::get('/ReconciliationDetails/{id}', [BankController::class, 'ReconciliationDetails'])->name('ReconciliationDetails.reconciliation');
Route::get('/ReconciliationSummary/{id}', [BankController::class, 'ReconciliationSummary'])->name('ReconciliationSummary.reconciliation');




Route::get('/viewroutes', [RouteController::class, 'index'])->name('routes.index');
Route::post('/route/update', [RouteController::class, 'update'])->name('route.update');
Route::get('/route/delete/{id}', [RouteController::class, 'destroy'])->name('route.delete');
Route::post('/route/store', [RouteController::class, 'store'])->name('route.store');


Route::get('/changeCollector','\App\Http\Controllers\TodayPaymentController@collector')->name('change.collector');
Route::post('/changeCollector_filter','\App\Http\Controllers\TodayPaymentController@collector_filter')->name('load.collector_filtor');


Route::post('/upload-excel-customer', [\App\Http\Controllers\ExcelController::class, 'uploadExcelCenters']);
//Route::post('/upload-excel-customer', [\App\Http\Controllers\ExcelController::class, 'edit']);
Route::post('/upload-excel-product', [\App\Http\Controllers\ExcelController::class, 'uploadExcelProduct']);
Route::post('/upload-excel-loan', [\App\Http\Controllers\ExcelController::class, 'uploadExcelLoan']);
Route::post('/upload-excel-payment', [\App\Http\Controllers\ExcelController::class, 'uploadExcelPayment']);
//Route::post('/upload-excel-payment', [\App\Http\Controllers\ExcelController::class, 'balance_change']);


Route::post('/upload-excel-category', [\App\Http\Controllers\ExcelController::class, 'uploadExcelCate']);
Route::post('/upload-excel-guardian', [\App\Http\Controllers\ExcelController::class, 'uploadExcelWitness']);
//Route::post('/upload-excel-customer-id', [\App\Http\Controllers\ExcelController::class, 'uploadExcelPayment']);
Route::get('/upload', [\App\Http\Controllers\ExcelController::class, 'show']);

Route::get('/LoanChargers','\App\Http\Controllers\ReportController@LoanChargers')->name('LoanChargers');

Route::post('/loan/comment/store', '\App\Http\Controllers\ReportController@storeComment')->name('loan.comment.store');
Route::get('/loan/comment/fetch', '\App\Http\Controllers\ReportController@fetchComments')->name('loan.comment.fetch');


Route::post('/save-files-pending', 'App\Http\Controllers\LoanController@saveFiles_Pending_loan')->name('save.saveFiles_Pending_loan');


Route::get('/showblacklistcustomers','\App\Http\Controllers\CustomerController@blacklist')->name('customers.blacklist');

Route::get('/get-installments/{loan_id}','\App\Http\Controllers\LoanController@getInstallments')->name('loan.getInstallments');
Route::post('/update-installments','\App\Http\Controllers\LoanController@updateInstallments')->name('installments.update');


Route::get('/trialBalance', function () {
    return view('pages.Accounting.TrialBalance');
});
Route::get('/get-trialBalance-data', [CashFlowController::class, 'trialBalance']);


Route::get('/loansummaryreport','\App\Http\Controllers\ReportController@loansummary')->name('report.loansummary');
Route::get('/get-centers-groups', [\App\Http\Controllers\ReportController::class, 'getCentersGroups'])->name('ajax.centers.groups');




Route::get('/dandlreport','\App\Http\Controllers\ReportController@dandlreport')->name('dandlreport');
Route::get('/monthlyprofit','\App\Http\Controllers\ReportController@monthlyprofit')->name('monthlyprofit');

Route::get('/collector_index','\App\Http\Controllers\BankController@collector_index')->name('bank.collector_index');

Route::post('/update-route','\App\Http\Controllers\TodayPaymentController@updateRoute')->name('update.route');


Route::post('/transfer','\App\Http\Controllers\BankController@transfer')->name('bank.transfer');
Route::post('/transfer_to_collector','\App\Http\Controllers\BankController@transfer_to_collector')->name('bank.transfer_to_collector');
Route::post('/topup','\App\Http\Controllers\BankController@topup')->name('bank.topup');


Route::get('/gl_report','\App\Http\Controllers\ReportController@gl_report')->name('report.gl_report');

Route::get('/savings_report','\App\Http\Controllers\ReportController@savings_report')->name('report.savings_report');
Route::post('/get_branch_data', [\App\Http\Controllers\ReportController::class, 'getBranchRelatedData']);
Route::post('/savings_report_filter','\App\Http\Controllers\ReportController@savings_report_filter')->name('report.savings_report');


//Accounting

Route::get('/ChartOfAccount', [ChartOfAccountController::class, 'create'])->name('chart_of_account.create');

Route::post('/chart_of_account/store', [ChartOfAccountController::class, 'store'])->name('chart_of_account.store');
Route::get('/chart_of_account/list/{group?}', [ChartOfAccountController::class, 'index'])->name('chart_of_account.list');
Route::get('/manual_journal/ledger/{account}', [ChartOfAccountController::class, 'fetchLedger'])->name('manual_journal.ledger');


Route::get('/ManualJournal', function () {
    return view('pages.Accounting.ManualJournal');
});

Route::get('/manual_journal/view/{id}', [ChartOfAccountController::class, 'viewDetails'])->name('manual_journal.view');
Route::get('/manual_journal/edit/{id}', [ChartOfAccountController::class, 'edit'])->name('manual_journal.edit');




Route::get('/AddJournal', [ChartOfAccountController::class, 'show'])->name('manual_journal.show');
Route::post('/manual-journal/save', [ChartOfAccountController::class, 'save'])->name('manual_journal.save');
Route::get('/manual-journal/fetch', [ChartOfAccountController::class, 'fetch'])->name('manual_journal.fetch');
Route::post('/manual-journal/change-status', [ChartOfAccountController::class, 'updateStatus'])->name('manual_journal.change_status');


Route::get('/trialBalanceAccounting', function () {
    return view('pages.Accounting.TrialBalanceAccounting');
});
Route::get('/get-account-trialBalance-data', [ChartOfAccountController::class, 'getAccountTrialBalance']);
Route::post('/get-financial-report', [ChartOfAccountController::class, 'getLog']);


Route::get('/BalanceSheet', [ChartOfAccountController::class, 'BalanceSheetView'])->name('BalanceSheetView.profit');
Route::post('/BalanceSheet', [ChartOfAccountController::class, 'BalanceSheet'])->name('BalanceSheetView.profit');
Route::post('/get-financial-full-report', [ChartOfAccountController::class, 'getBalanceSheetLog'])->name('getBalanceSheetLog.profit');



Route::get('/holidays','\App\Http\Controllers\UserController@holidays')->name('holidays.index');
Route::post('/poya-days/save','\App\Http\Controllers\UserController@poya_days_save')->name('poya_days_save.index');
Route::post('/holidays','\App\Http\Controllers\UserController@holidays_save')->name('holidays.store');
Route::delete('/holidays/delete/{id}','\App\Http\Controllers\UserController@deleteHoliday')->name('holidays.delete');
Route::get('/get-holidays','\App\Http\Controllers\UserController@getHolidays')->name('get.holidays');
Route::post('/generate-due-skip','\App\Http\Controllers\UserController@generateDueSkip')->name('save.holidays');
Route::get('/poya-days','\App\Http\Controllers\HolidayController@fetchPoya')->name('get.fetchPoya');




//branches
Route::get('/branch','\App\Http\Controllers\BranchController@index')->name('branch');
Route::post('/save-branch','\App\Http\Controllers\BranchController@create')->name('save.branch');
Route::post('/update-branch','\App\Http\Controllers\BranchController@updateBranch')->name('update.branch');



Route::get('/activate-branch/{id}','\App\Http\Controllers\BranchController@activateBranch')->name('update.activateBranch');



Route::get('/load_checklist/{levelId}/{loan_id}','\App\Http\Controllers\LoanCategoryController@loadChecklist')->name('loadChecklist');
Route::post('/update_checklist/{itemId}','\App\Http\Controllers\LoanCategoryController@updateChecklist')->name('updateChecklist');



Route::get('/user/get-details/{id}', [UserController::class, 'getUserDetails']);
Route::post('/user/update', [UserController::class, 'updateUser']);
Route::post('/user/reset-password/{id}', [UserController::class, 'resetPassword']);



//Reshedule
Route::get('/loan_reschedule','\App\Http\Controllers\PaymentLoanController@reschedule')->name('reschedule.index');
Route::get('/loan/{loan_id}/{balance}','\App\Http\Controllers\LoanController@reschedule_index')->name('reschedule.index');
Route::post('/loan_reschedule','\App\Http\Controllers\LoanController@save_reschedule')->name('loan_reschedule.index');



//Disbursement Loan
Route::get('/loan_disbursement','\App\Http\Controllers\PendingLoanController@index_disbursement')->name('loan_disbursement.index');
Route::get('/loan_disbursementload','\App\Http\Controllers\PendingLoanController@create_disbursement')->name('loan_disbursement.create');
Route::post('/pendingloanissue','\App\Http\Controllers\PendingLoanController@show')->name('pendingloan.show');
Route::get('/pendingloandelete/{id}','\App\Http\Controllers\PendingLoanController@destroy')->name('pendingloan.destroy');
Route::get('/show_loan/{id}/{loan}','\App\Http\Controllers\PendingLoanController@edit')->name('loan.edit');



//Cashier
Route::post('/save-cashier-data', [CashierController::class, 'store'])->name('cashier.save');
Route::get('/get-today-cashier-data', [CashierController::class, 'getTodayData'])->name('cashier.getTodayData');
Route::get('/cashier/day-end-data', [CashierController::class, 'getDayEndData'])->name('cashier.dayEndData');
Route::post('/cashier/save-day-end', [CashierController::class, 'saveDayEnd'])->name('cashier.saveDayEnd');
Route::get('/cashier/get-saved-day-end', [CashierController::class, 'getSavedDayEndData'])->name('cashier.getSavedDayEndData');
Route::get('/cashier/bank-list', [CashierController::class, 'getBankList']);


//center_collection
Route::get('/center_collection','\App\Http\Controllers\CenterController@center_collection')->name('center_collection.index');
Route::get('/center_collection_summary','\App\Http\Controllers\CenterController@CenterWiseCollectionSummary')->name('center_collection_summary.index');

Route::get('/root_wise_collection','\App\Http\Controllers\CenterController@root_wise')->name('root_wise.index');



Route::get('/portfolio_performance','\App\Http\Controllers\PendingLoanController@portfolio_performance')->name('portfolio_performance');
Route::get('/get-routes-centers','\App\Http\Controllers\PendingLoanController@getRoutesCenters')->name('getRoutesCenters');
Route::get('/get-portfolio-performance-excel','\App\Http\Controllers\PendingLoanController@getPortfolioPerformanceExcel')->name('getPortfolioPerformance');


Route::post('/get-loan-interest-details','\App\Http\Controllers\BankController@profitLog')->name('getLoanInterestDetails');



Route::get('/RightWayDailyRepayment', [TransactionController::class, 'rightway'])->name('transaction.rightway');


Route::get('/GreenLankaTrustRepayment', [TransactionController::class, 'GreenLankaTrustRepayment'])->name('transaction.GreenLankaTrustRepayment');
Route::get('/DandDRepayment', [TransactionController::class, 'DandDRepayment'])->name('transaction.DandDRepayment');
Route::get('/dailyreport', [TransactionController::class, 'dailyreport'])->name('transaction.dailyreport');


Route::get('/PaymentFullDetailsReport','\App\Http\Controllers\ReportController@payment_report')->name('payment-detail.index');


Route::get('/loan-report', [PendingLoanController::class, 'report_disbursement'])->name('loan.report');
Route::post('/loan-report/data', [PendingLoanController::class, 'get_report_disbursement'])->name('loan.report.data');
Route::get('/loan-report/filters', [PendingLoanController::class, 'getFilters_disbursement'])->name('loan.report.filters');

//KYC
Route::get('/kyc', [App\Http\Controllers\KYCController::class, 'index'])->name('kyc.index');
Route::get('/kyc/{id}', [App\Http\Controllers\KYCController::class, 'show'])->name('kyc.show');
Route::get('/kyc/{section}/{id}', [App\Http\Controllers\KYCController::class, 'loadSection']);

//Insurance
Route::get('/insurance', [App\Http\Controllers\KYCController::class, 'create'])->name('kyc.create');
Route::post('/insurance/category/save', [App\Http\Controllers\KYCController::class, 'insurance_category'])->name('insurance.category.save');
Route::post('/insurance/request', [App\Http\Controllers\KYCController::class, 'store'])->name('insurance.request');
Route::get('/insurance/history/{id}', [App\Http\Controllers\KYCController::class, 'getHistory'])->name('insurance.history');
Route::get('/insurance/evidence/{id}', [App\Http\Controllers\KYCController::class, 'evidence'])->name('insurance.evidence');
Route::post('/kyc/load-insurances', [App\Http\Controllers\KYCController::class, 'loadInsurances'])->name('kyc.loadInsurances');
Route::get('/insurance/approval-levels/{categoryId}/{insuranceId}', [App\Http\Controllers\KYCController::class, 'getApprovalLevels']);
Route::post('/insurance/approve-level', [App\Http\Controllers\KYCController::class, 'approveLevel']);
Route::post('/insurance/reject', [App\Http\Controllers\KYCController::class, 'rejectInsurance']);
Route::post('/insurance/issue', [App\Http\Controllers\KYCController::class, 'issueInsurance']);
Route::get('/company/bank-accounts', [App\Http\Controllers\KYCController::class, 'getBankAccounts']);







Route::post('/save-cashier-entries', [CashierController::class, 'saveCashierEntries']);


//data
Route::get('/create_mivence_loan', [\App\Http\Controllers\DataMigrateController::class, 'create_loan']);
Route::get('/create_product', [\App\Http\Controllers\DataMigrateController::class, 'create_product']);
Route::get('/create_user', [\App\Http\Controllers\DataMigrateController::class, 'create_user']);
Route::get('/create_customer', [\App\Http\Controllers\DataMigrateController::class, 'create_customer']);
Route::get('/create_payment', [\App\Http\Controllers\DataMigrateController::class, 'create_payment']);




Route::post('/save-extra-charge', [\App\Http\Controllers\TodayPaymentController::class, 'saveExtraCharge']);
Route::get('/get-extra-charges', [\App\Http\Controllers\TodayPaymentController::class, 'getExtraCharges']);
Route::post('/loan-delete', [\App\Http\Controllers\TodayPaymentController::class, 'deleteLoan']);


Route::get('/prediction_report','\App\Http\Controllers\TodayPaymentController@preditction_report');
Route::post('/prediction-report/fetch','\App\Http\Controllers\TodayPaymentController@fetchPredictionReport')->name('fetchPredictionReport');

Route::get('/get-loan-ids', function () {
    $loan_ids = tableWithBranch('customer_loan')
        ->pluck('idCustomer_Loan')
        ->toArray();

    return response()->json(['loan_ids' => $loan_ids]);
});

Route::get('/loan_log/{loan_id}','\App\Http\Controllers\CapitalBalanceController@show')->name('sms.show');

Route::post('/upload-excel-balance', [\App\Http\Controllers\ExcelController::class, 'balance_change']);

Route::post('/get-customer-bank-details', [LoanController::class, 'getCustomerBankDetails']);


Route::get('/settings/all','\App\Http\Controllers\CapitalBalanceController@all')->name('');
Route::post('/settings/upsert','\App\Http\Controllers\CapitalBalanceController@upsert')->name('upsert');



//depletion
Route::get('/depletion','\App\Http\Controllers\ReportController@depletion')->name('report.depletion');
Route::post('/depletion/data', [\App\Http\Controllers\ReportController::class, 'depletionData'])
    ->name('depletion.data');

//approval
Route::get('/pending_approval','\App\Http\Controllers\ApprovalController@pending_approval')->name('approval.pending');
Route::get('/approved_history','\App\Http\Controllers\ApprovalController@approved_history')->name('approval.approved');
Route::get('/rejected_approval','\App\Http\Controllers\ApprovalController@rejected_approval')->name('approval.rejected');
Route::post('/approve_request','\App\Http\Controllers\ApprovalController@approve')->name('approval.approve');
Route::post('/reject_request','\App\Http\Controllers\ApprovalController@reject')->name('approval.reject');
Route::post('/callback_request','\App\Http\Controllers\ApprovalController@callback')->name('approval.callback');


Route::get('/load_customer_route/{id}','\App\Http\Controllers\CustomerController@load_customer_route')->name('customers.load_customer_route');