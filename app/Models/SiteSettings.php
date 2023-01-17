<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'site_settings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_logo',
        'site_name',
        'site_title',
        'site_keywords',
        'site_description',
        'site_email',
        'inquiry_email',
        'site_phone',
        'site_mobile',
        'site_address',
        'social_profiles',
        'company_registration_number',
        'site_url',
        'vat_id',
        'tax_id',
        'street',
        'zip_code',
        'city',
        'country',
        'bank_name',
        'iban',
        'pinterest',
        'facebook',
        'twitter',
        'linkedin',
        'number_of_days',
        'defualt_vat',
        'payment_relief_days',
        'user_deletion_days',
        'operating_hours',
        'commercial_register_address',
        'code',
        'account_inactivity_first_notification',
        'account_inactivity_second_notification',
        'account_inactivity_third_notification',
        'account_soft_delete_time_limit',
        'account_inactivity_time_limit',
        'low_license_notification_count',
        'low_license_notification_duration',
        'license_count_low_notification_threshold',
        'low_license_email_recipients',
        'registration_email_recipients',
        'orders_bcc_email_recipients',
        'reseller_credit_limit',
        'reseller_invoice_cron_day',
        'reseller_invoice_cron_days_duration',
        'refund_grace_period_days',
    ];
}
