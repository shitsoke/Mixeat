@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Update About Page Content</div>
            <div class="card-body">
                <form action="{{ route('admin.cms.about.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">About Us Heading</label>
                        <input type="text" name="about_heading" class="form-control" value="{{ $aboutHeading ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Main Content</label>
                        <textarea name="about_content" class="form-control" rows="8" required>{{ $aboutContent ?? '' }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publish Updates</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection