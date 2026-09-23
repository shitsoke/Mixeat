@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Update Contact Information</div>
            <div class="card-body">
                <form action="{{ route('admin.cms.contact.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Contact Phone / Mobile</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ $contactPhone ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $contactEmail ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Main Office Address</label>
                        <textarea name="contact_address" class="form-control" rows="4" required>{{ $contactAddress ?? '' }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Contact Details</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection