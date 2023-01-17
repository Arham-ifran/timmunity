<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LanguageTranslation;
use App\Models\ResellerRedeemedPage;
use App\Models\Languages;
use App\Models\LanguageModule;
use App\Models\ProductSale;
use Mail;

class TranslateProductDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $product_sale_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product_sale_id)
    {
        $this->product_sale_id = $product_sale_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            //code...

            $language_module_id = 11;
            $language_module = LanguageModule::find($language_module_id);
            $table = $language_module->table;
            $columns = explode(',', $language_module->columns);
            $languages = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();

            $product_sale = ProductSale::where('id', $this->product_sale_id)->first();
            // Saving Translations
            foreach ($languages as $language)
            {
                foreach ($columns as $column)
                {
                    $language_translation = LanguageTranslation::where(
                        [
                            'language_module_id' => $language_module->id,
                            'language_id'        => $language->id,
                            'item_id'            => $product_sale->id,
                            'column_name'        => $column
                        ])
                        ->first();

                    if( empty($language_translation) || $language_translation->custom == 0 )
                    {
                        $item_value = translationByDeepL($product_sale->$column,$language->iso_code);
                        LanguageTranslation::updateOrCreate(
                            [
                                'language_module_id' => $language_module->id,
                                'language_id'        => $language->id,
                                'item_id'            => $product_sale->id,
                                'column_name'        => $column,
                            ],
                            [
                                'language_module_id' => $language_module->id,
                                'language_id'        => $language->id,
                                'language_code'      => $language->iso_code,
                                'item_id'            => $product_sale->id,
                                'column_name'        => $column,
                                'item_value'         => $item_value,
                                'custom'             => 0,
                            ]
                        );
                    }
                }
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
