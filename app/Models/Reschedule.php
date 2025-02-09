<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reschedule extends Model
{
    use HasFactory;

    protected $table = 'reshedule'; // The table name
    public $timestamps = false; // Disable timestamps if your table doesn't have them
    protected $primaryKey = 'idCustomer_Loan'; // Assuming the primary key for `reschedule` table is `idReschedule`

    // Specify which fields can be mass-assigned
    protected $fillable = [
        'Loan_No',
        'Loan_Category_idLoan_Category',
        'Customer_idCustomer',
        'Leasing_type',
        'Vehicle_No',
        'Date_Time',
        'Amount',
        'Interest_Rate',
        'Panalty_Rate',
        'Installment_Count',
        'Interest_Amount',
        'Total_Other_Amount',
        'Other_Amount_Balance',
        'Total_Loan_Amount',
        'Installment_Amount',
        'Collection_Type',
        'Collection_Date',
        'Panalty_Date',
        'Balance_Amount',
        'Status',
        'reason',
        'User_idUser',
        'capital_balance',
        'installment_balance',
        'type',
        'Interest_period',
        'cus_bank_account',
        'company_bank_account',
        'lending_officer_id',
        'collector_id',
    ];
}

