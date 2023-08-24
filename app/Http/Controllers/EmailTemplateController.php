<?php
// app/Http/Controllers/EmailTemplateController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $emailTemplates = EmailTemplate::all();
        return view('email-templates.index', compact('emailTemplates'));
    }

    public function create()
    {
        return view('email-templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        // Create the email template
        $emailTemplate = EmailTemplate::create([
            'template_type' => $data['template_type'],
        ]);
        // Set translations for the email template
        foreach (config('app.available_locales') as $locale) {
            $emailTemplate->setTranslation('body', $locale, $data['body'][$locale]);
            $emailTemplate->setTranslation('subject', $locale, $data['subject'][$locale]);
        }
        // Save the translations
        $emailTemplate->save();
        return redirect()->route('email-templates.index');
        
    }

    public function show(EmailTemplate $emailTemplate)
    {
        // Fetch all translations for the 'body' and 'subject' attributes
        $translations = [];

        foreach (config('app.available_locales') as $locale) {
            $bodyTranslation = $emailTemplate->getTranslation('body', $locale);
            $subjectTranslation = $emailTemplate->getTranslation('subject', $locale);

            // Replace tokens with actual values for this locale
            $bodyTranslation = $this->replaceTokens($bodyTranslation, "krish body");
            $subjectTranslation = $this->replaceTokens($subjectTranslation, "krish subkjhkj");

            $translations[$locale] = [
                'body' => $bodyTranslation,
                'subject' => $subjectTranslation,
                'template_type' => $emailTemplate->template_type
            ];
        }
        return view('email-templates.show', compact('translations'));
    }

    private function replaceTokens($content, $value)
    {
        $tokens = [
            '{body}' => $value,
            '{subject}' => $value,
        ];

        // Replace tokens with their corresponding values
        $content = str_replace(array_keys($tokens), array_values($tokens), $content);
        return $content;
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email-templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->all();

        // Update the email template attributes
        $emailTemplate->update([
            'template_type' => $data['template_type'],
            'status' => $data['status'],
        ]);

        // Update translations for the email template
        foreach (config('app.available_locales') as $locale) {
            $emailTemplate->setTranslation('body', $locale, $data['body'][$locale]);
            $emailTemplate->setTranslation('subject', $locale, $data['subject'][$locale]);
        }

        // Save the translations
        $emailTemplate->save();

        return redirect()->route('email-templates.index');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return redirect()->route('email-templates.index');
    }
}

