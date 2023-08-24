@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Email Template</h1>
    <form action="{{ route('email-templates.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="template_type">Template Type:</label>
            <input type="text" class="form-control" name="template_type" id="template_type" required>
        </div>

        @foreach(config('app.available_locales') as $locale)
            <div class="form-group">
                <h3>Translation ({{ $locale }})</h3>
                <label for="body[{{ $locale }}]">Body:</label>
                <textarea class="form-control" name="body[{{ $locale }}]" id="body[{{ $locale }}]" required></textarea>
                
                <label for="subject[{{ $locale }}]">Subject:</label>
                <input type="text" class="form-control" name="subject[{{ $locale }}]" id="subject[{{ $locale }}]" required>
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
