<?php
namespace App\Http\Traits;
use App\Models\User;
use App\Models\Admin;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Jobs\RequestAdminLicenseEmailJob;
use Hashids;
use App\Models\SiteSettings;

trait AdminNotificationTrait {
    public function requestAdmintoUploadMoreVouchers($product_id, $variation_id, $body, $admin_id = null)
    {
        $license_email_from_site_settings   = SiteSettings::first();
        $bcc_email_array  = explode(',',$license_email_from_site_settings->low_license_email_recipients);
        $product = Products::where('id', $product_id)->first();
        $ProductVariation = ProductVariation::where('id', $variation_id)->first();

        $product_name = $product->product_name;
        $product_name .= isset($product->variations[0]) ? ' '.@$ProductVariation->variation_name : '';
        $details = [];
        $details['emails'] = $bcc_email_array;
        $details['subject'] =  'Low Count License for '.$product_name;
        $details['body'] =  $body;
        dispatch(new \App\Jobs\RequestAdminLicenseEmailJob($details));

        return 'true';
    }
}
