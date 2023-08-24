@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Email Template</h1>
    <form action="{{ route('email-templates.update', $emailTemplate) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="template_type">Template Type:</label>
            <input type="text" class="form-control" name="template_type" id="template_type" value="{{ $emailTemplate->template_type }}" required>
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <input type="text" class="form-control" name="status" id="status" value="{{ $emailTemplate->status }}" required>
        </div>

        @foreach(config('app.available_locales') as $locale)
            <div class="form-group">
                <h3>Translation ({{ $locale }})</h3>
                <label for="body[{{ $locale }}]">Body:</label>
                <textarea class="form-control" name="body[{{ $locale }}]" id="body[{{ $locale }}]" required>{{ $emailTemplate->getTranslation('body', $locale) }}</textarea>
                
                <label for="subject[{{ $locale }}]">Subject:</label>
                <input type="text" class="form-control" name="subject[{{ $locale }}]" id="subject[{{ $locale }}]" value="{{ $emailTemplate->getTranslation('subject', $locale) }}" required>
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
