<?php

namespace App\Http\Controllers;

use App\Models\CateringBooking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CateringInquiryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'event_time' => ['required', 'date_format:H:i'],
            'venue' => ['required', 'string', 'max:255'],
            'event_type' => ['required', Rule::in(['Corporate', 'Office Party', 'Celebration', 'Wedding', 'Birthday', 'Other'])],
            'service_type' => ['required', Rule::in(['Packed Meals', 'Party Trays', 'Full-Service Catering'])],
            'package_preference' => ['nullable', 'string', 'max:255'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:5000'],
            'budget_range' => ['nullable', 'string', 'max:100'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'campaign_source' => ['nullable', 'string', 'max:100'],
        ]);

        $data['campaign_source'] = $data['campaign_source'] ?: 'direct';
        $data['status'] = 'New';
        CateringBooking::create($data);

        return redirect()->to(route('catering').'#catering-inquiry')
            ->with('catering_status', 'Thank you! Your catering inquiry has been received. Our team will be in touch soon.');
    }
}
