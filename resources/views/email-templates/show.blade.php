@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Email Template Details</h1>

        @foreach($translations as $locale => $translation)
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Translation ({{ $locale }})</h3>
                </div>
                <div class="card-body">
                    <p><strong>Template Type:</strong> {{ $translation['template_type'] }}</p>
                    <p><strong>Body:</strong> {!! nl2br(e($translation['body'])) !!}</p>
                    <p><strong>Subject:</strong> {{ $translation['subject'] }}</p>
                </div>
            </div>
        @endforeach

        <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
@endsection
