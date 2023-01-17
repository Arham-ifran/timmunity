<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LanguageTranslation;
use App\Models\LanguageModule;
use App\Models\Languages;
use Mail;

class TranslationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $language_module_id, $translate_language, $translation_flag;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($language_module_id, $translate_language, $translation_flag)
    {
        $this->language_module_id = $language_module_id;
        $this->translate_language = $translate_language;
        $this->translation_flag = $translation_flag;
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

            $language_module = LanguageModule::find($this->language_module_id);
            $table = $language_module->table;
            $columns = explode(',', $language_module->columns);
            $records = \DB::table($table)->get();
            if($this->translate_language && $this->translate_language <> '')
            {
                $languages = Languages::where('id',$this->translate_language)->get();
            }
            else
            {
                $languages = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();
            }
            foreach ($languages as $language)
            {
                foreach ($records as $record)
                {
                    foreach ($columns as $column)
                    {
                        $language_translation = LanguageTranslation::where(
                            [
                                'language_module_id' => $language_module->id,
                                'language_id'        => $language->id,
                                'item_id'            => $record->id,
                                'column_name'        => $column
                            ])
                            ->first();

                        if($this->translation_flag == 1 || empty($language_translation) || $language_translation->custom == 0)
                        {
                            $item_value = translationByDeepL($record->$column,$language->iso_code);

                            LanguageTranslation::updateOrCreate(
                                [
                                    'language_module_id' => $language_module->id,
                                    'language_id'        => $language->id,
                                    'item_id'            => $record->id,
                                    'column_name'        => $column,
                                ],
                                [
                                    'language_module_id' => $language_module->id,
                                    'language_id'        => $language->id,
                                    'language_code'      => $language->iso_code,
                                    'item_id'            => $record->id,
                                    'column_name'        => $column,
                                    'item_value'         => $item_value,
                                    'custom'             => 0,
                                ]
                            );
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
