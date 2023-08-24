@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Email Templates</h1>

    <a href="{{ route('email-templates.create') }}" class="btn btn-primary mb-3">Create New Template</a>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Template Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($emailTemplates as $template)
                    <tr>
                        <td>{{ $template->id }}</td>
                        <td>{{ $template->template_type }}</td>
                        <td>{{ $template->status ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('email-templates.show', $template->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('email-templates.edit', $template->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('email-templates.destroy', $template->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this template?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
