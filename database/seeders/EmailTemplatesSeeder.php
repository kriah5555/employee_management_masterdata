<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Email\EmailTemplate;

class EmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (config('constants.EMAIL_TEMPLATES') as $template_type => $data) {
            // Check if the template type already exists
            $existingTemplate = EmailTemplate::where('template_type', $template_type)->first();

            if (!$existingTemplate) {
                // If the template type doesn't exist, create a new EmailTemplate
                $emailTemplate = EmailTemplate::create([
                    'template_type' => $template_type,
                ]);

                // Set empty string translations for body and subject for each language
                foreach (config('app.available_locales') as $locale) {
                    $emailTemplate->setTranslation('body', $locale, $data);
                    $emailTemplate->setTranslation('subject', $locale, $data);
                }

                // Save the translations
                $emailTemplate->save();
            }
        }
    }
}
