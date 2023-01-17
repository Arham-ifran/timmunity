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
use Mail;

class TranslateRedeemPageDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $redeem_page_id, $column_array;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($redeem_page_id, $column_array)
    {
        $this->redeem_page_id = $redeem_page_id;
        $this->column_array = $column_array;
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

            $model = ResellerRedeemedPage::where('id',$this->redeem_page_id)->first();
            $languages = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();
            $language_module_id = 31;

            // Saving Translations
            foreach ($languages as $language)
            {
                if( in_array('description', $this->column_array) )
                {
                    $pattern = '/{{.+}}/i';
                    $replacement = '{{voucher_form}}';
                    //  description
                    $language = LanguageTranslation::updateOrCreate(
                        [
                            'language_module_id' => $language_module_id,
                            'language_id'        => $language->id,
                            'item_id'            => $model->id,
                            'column_name'        => 'description',
                        ],
                        [
                            'language_module_id' => $language_module_id,
                            'language_id'        => $language->id,
                            'language_code'      => $language->iso_code,
                            'item_id'            => $model->id,
                            'column_name'        => 'description',
                            'item_value'         => preg_replace(
                                                            $pattern,
                                                            $replacement,
                                                            translationByDeepL($model->description, $language->iso_code)
                                                        ),
                            'custom'             => 0,
                        ]
                    );
                }
                if( in_array('terms_of_use', $this->column_array) )
                {
                    //  terms_of_use
                    LanguageTranslation::updateOrCreate(
                        [
                            'language_module_id' => $language_module_id,
                            'language_id'        => $language->id,
                            'item_id'            => $model->id,
                            'column_name'        => 'terms_of_use',
                        ],
                        [
                            'language_module_id' => $language_module_id,
                            'language_id'        => $language->id,
                            'language_code'      => $language->iso_code,
                            'item_id'            => $model->id,
                            'column_name'        => 'terms_of_use',
                            'item_value'         => translationByDeepL($model->terms_of_use, $language->iso_code),
                            'custom'             => 0,
                        ]
                    );
                }
                if( in_array('privacy_policy', $this->column_array) )
                {
                    //  privacy_policy
                    LanguageTranslation::updateOrCreate(
                        [
                            'language_module_id' => $language_module_id,
                            'language_id'        => $language->id,
                            'item_id'            => $model->id,
                            'column_name'        => 'privacy_policy',
                        ],
                        [
                            'language_module_id' => $language_module_id,
                            'language_id'        => $language->id,
                            'language_code'      => $language->iso_code,
                            'item_id'            => $model->id,
                            'column_name'        => 'privacy_policy',
                            'item_value'         => translationByDeepL($model->privacy_policy, $language->iso_code),
                            'custom'             => 0,
                        ]
                    );
                }
                if( in_array('imprint', $this->column_array) )
                {
                    //  imprint
                    LanguageTranslation::updateOrCreate(
                        [
                            'language_module_id' => $language_module_id,
                            'language_id'        => $language->id,
                            'item_id'            => $model->id,
                            'column_name'        => 'imprint',
                        ],
                        [
                            'language_module_id' => $language_module_id,
                            'language_id'        => $language->id,
                            'language_code'      => $language->iso_code,
                            'item_id'            => $model->id,
                            'column_name'        => 'imprint',
                            'item_value'         => translationByDeepL($model->imprint, $language->iso_code),
                            'custom'             => 0,
                        ]
                    );
                }
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
