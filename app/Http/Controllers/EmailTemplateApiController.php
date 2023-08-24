<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailTemplateApiController extends Controller
{
    public function index()
    {
        $emailTemplates = EmailTemplate::all();
        return response()->json($emailTemplates);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $emailTemplate = EmailTemplate::create([
            'template_type' => $data['template_type'],
        ]);

        foreach (config('app.available_locales') as $locale) {
            $emailTemplate->setTranslation('body', $locale, $data['body'][$locale]);
            $emailTemplate->setTranslation('subject', $locale, $data['subject'][$locale]);
        }

        $emailTemplate->save();

        return response()->json($emailTemplate, 201);
    }

    public function show($id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);

        return response()->json($emailTemplate);
    }

    public function update(Request $request, $id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);
        $data = $request->all();

        $emailTemplate->update([
            'template_type' => $data['template_type'],
            'status' => $data['status'],
        ]);

        foreach (config('app.available_locales') as $locale) {
            $emailTemplate->setTranslation('body', $locale, $data['body'][$locale]);
            $emailTemplate->setTranslation('subject', $locale, $data['subject'][$locale]);
        }

        $emailTemplate->save();

        return response()->json($emailTemplate);
    }

    public function destroy($id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);
        $emailTemplate->delete();

        return response()->json(['message' => 'Email template deleted']);
    }
}
