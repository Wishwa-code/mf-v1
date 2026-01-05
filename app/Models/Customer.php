<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Route;
use App\Models\Center;
use App\Models\Group;

class Customer extends Model
{
    use HasFactory;

    protected $table = "customer";

    public $timestamps = false;

    protected $primaryKey = 'idCustomer';

    protected $fillable = [
        'Title',
        'Customer_Group_idCustomer_Group',
        'cus_number',
        'First_Name',
        'Last_Name',
        'Email',
        'Contact_No',
        'contact_number_2',
        'business_registration',
        'Nic',
        'Gender',
        'Dob',
        'Address',
        'Address_02',
        'Address_03',
        'Per_Address_01',
        'Per_Address_02',
        'Per_Address_03',
        'City',
        'State',
        'Landline',
        'Note',
        'Longitude',
        'Latitude',
        'Gua_title',
        'Gua_name',
        'Guardian_gender',
        'Gua_relation',
        'Gua_occu',
        'Gua_contact',
        'Gua_address',
        'Gua_nic',
        'Customer_Risk_Level',
        'civil_status',
        'occu_job_position',
        'occu_monthly_salary',
        'occu_address_01',
        'occu_address_02',
        'occu_address_03',
        'occu_contact_no',
        'occu_longitude',
        'occu_latitude',
        'route_id',
        'branch_id',
        'Cus_phto',
        'center_id' // Added based on typical structure, verifying if used
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'idCompany');
        // Note: Check actual FK if different
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'idBranch');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'idRoute');
    }

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id', 'idCenter');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'Customer_Group_idCustomer_Group', 'idGroup');
    }
}
