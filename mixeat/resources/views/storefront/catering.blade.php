@extends('layouts.app')

@section('title', 'MixEat Catering | Good Food. Great Moments.')

@section('content')
    <section id="catering" class="scroll-mt-24 bg-[#FFF9E6] py-12 md:py-16">
        <div class="w-full px-4 md:px-7">
            <div class="mb-12 grid grid-cols-1 items-center gap-6 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#E5A900]">MixEat Catering</p>
                    <h1 class="mt-2 text-3xl font-bold leading-tight text-[#090909] md:text-4xl">Planning an Event or Office Party?</h1>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-[#555555]">We offer customized tray packages, packed meals, and full-service catering for corporate events, parties, and celebrations.</p>
                </div>
                <div class="lg:col-span-5">
                    <div class="rounded-[20px] border border-[#E8DFAF] bg-white px-6 py-5 shadow-sm">
                        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-[#E5A900]">{{ $cateringContent['banner_title'] }}</p>
                        <p class="mb-0 text-sm text-[#555555]">{{ $cateringContent['banner_copy'] }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-12 grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach ($cateringContent['highlights'] as $index => $highlight)
                    @php($package = $cateringContent['packages'][$index] ?? null)
                    <button
                        type="button"
                        class="catering-highlight-card flex min-h-[108px] items-center gap-5 rounded-[20px] border border-[#E8DFAF] bg-white px-8 py-6 text-left shadow-sm transition hover:-translate-y-1 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E5A900]"
                        data-catering-package
                        data-package-name="{{ $package['name'] ?? $highlight }}"
                        data-package-description="{{ $package['description'] ?? 'Contact us to learn more about this catering service.' }}"
                        data-package-price="{{ is_numeric($package['price'] ?? null) ? number_format((float) $package['price'], 2, '.', '') : '' }}"
                    >
                        <span class="text-3xl" aria-hidden="true">{{ ['🍱', '🥘', '🚚'][$index] ?? '✨' }}</span>
                        <span class="font-bold text-[#090909]">{{ $highlight }}</span>
                    </button>
                @endforeach
            </div>

            <div class="mb-12 rounded-[20px] border border-[#E8DFAF] bg-white px-6 py-12 md:px-10">
                <p class="mb-6 text-center text-xs font-bold tracking-[0.16em] text-[#777777]">OUR CATERING CLIENTS &amp; PARTNERS</p>
                <div class="catering-partner-marquee" role="region" aria-label="Catering clients and partners">
                    <div class="catering-partner-track">
                        @foreach ([false, true] as $duplicate)
                            <div class="catering-partner-group" @if ($duplicate) aria-hidden="true" inert @endif>
                                @foreach ($cateringContent['partners'] as $partner)
                                    <div class="catering-partner-card">
                                        @if ($partner['website_url'] && ! $duplicate)
                                            <a class="catering-partner-link" href="{{ $partner['website_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $partner['name'] }} website">
                                        @endif
                                            @if ($partner['logo_url'])
                                                <img src="{{ $partner['logo_url'] }}" alt="{{ $duplicate ? '' : $partner['name'].' logo' }}" class="catering-partner-logo">
                                            @else
                                                <span class="catering-partner-name">{{ $partner['name'] }}</span>
                                            @endif
                                        @if ($partner['website_url'] && ! $duplicate)
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="catering-inquiry" class="w-full scroll-mt-24">
                <div class="overflow-hidden rounded-[24px] border border-[#E8DFAF] bg-white shadow-sm">
                    <div class="p-6 md:p-10">
                        <div class="mb-6">
                            <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#E5A900]">Tell us about your event</p>
                            <h2 class="text-3xl font-bold text-[#090909]">Book Catering</h2>
                            <p class="mb-0 text-[#555555]">Share a few details and our catering team will contact you to plan your event.</p>
                        </div>

                        @if (session('catering_status'))
                            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800" role="status">{{ session('catering_status') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800" role="alert">
                                <p class="mb-1 font-bold">Please check the details below.</p>
                                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form action="{{ route('catering.inquiries.store') }}" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            @csrf
                            <input type="hidden" name="campaign_source" value="{{ $campaignSource }}">
                            <div>
                                <label for="catering-client-name" class="mb-1 block text-sm font-semibold text-[#090909]">Client Name</label>
                                <input id="catering-client-name" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="text" name="client_name" value="{{ old('client_name') }}" autocomplete="name" maxlength="120" required>
                            </div>
                            <div>
                                <label for="catering-email" class="mb-1 block text-sm font-semibold text-[#090909]">Email</label>
                                <input id="catering-email" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
                            </div>
                            <div>
                                <label for="catering-phone" class="mb-1 block text-sm font-semibold text-[#090909]">Phone</label>
                                <input id="catering-phone" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" maxlength="30" required>
                            </div>
                            <div>
                                <label for="catering-company" class="mb-1 block text-sm font-semibold text-[#090909]">Company / Organization <span class="font-normal text-[#777777]">(optional)</span></label>
                                <input id="catering-company" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="text" name="company_name" value="{{ old('company_name') }}" maxlength="160">
                            </div>
                            <div>
                                <label for="catering-date" class="mb-1 block text-sm font-semibold text-[#090909]">Event Date</label>
                                <input id="catering-date" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="date" name="event_date" value="{{ old('event_date') }}" min="{{ now()->toDateString() }}" required>
                            </div>
                            <div>
                                <label for="catering-time" class="mb-1 block text-sm font-semibold text-[#090909]">Event Time</label>
                                <input id="catering-time" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="time" name="event_time" value="{{ old('event_time') }}" required>
                            </div>
                            <div>
                                <label for="catering-venue" class="mb-1 block text-sm font-semibold text-[#090909]">Venue / Location</label>
                                <input id="catering-venue" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="text" name="venue" value="{{ old('venue') }}" maxlength="255" required>
                            </div>
                            <div>
                                <label for="catering-event-type" class="mb-1 block text-sm font-semibold text-[#090909]">Event Type</label>
                                <select id="catering-event-type" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" name="event_type" required>
                                    <option value="">Choose an event type</option>
                                    @foreach (['Corporate', 'Office Party', 'Celebration'] as $eventType)
                                        <option value="{{ $eventType }}" @selected(old('event_type') === $eventType)>{{ $eventType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="catering-service" class="mb-1 block text-sm font-semibold text-[#090909]">Service Type</label>
                                <select id="catering-service" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" name="service_type" required>
                                    <option value="">Choose a service</option>
                                    @foreach (['Packed Meals', 'Party Trays', 'Full-Service Catering'] as $service)
                                        <option value="{{ $service }}" @selected(old('service_type') === $service)>{{ $service }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="catering-guests" class="mb-1 block text-sm font-semibold text-[#090909]">Guest Count</label>
                                <input id="catering-guests" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="number" name="guest_count" value="{{ old('guest_count') }}" min="1" max="5000" required>
                            </div>
                            <div>
                                <label for="catering-budget" class="mb-1 block text-sm font-semibold text-[#090909]">Budget Range <span class="font-normal text-[#777777]">(optional)</span></label>
                                <input id="catering-budget" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" type="text" name="budget_range" value="{{ old('budget_range') }}" maxlength="100" placeholder="e.g. ₱20,000–₱30,000">
                            </div>
                            <div class="md:col-span-2">
                                <label for="catering-package" class="mb-1 block text-sm font-semibold text-[#090909]">Package Preference <span class="font-normal text-[#777777]">(optional)</span></label>
                                <select id="catering-package" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" name="package_preference">
                                    <option value="">No preference yet</option>
                                    @foreach ($cateringContent['packages'] as $package)
                                        <option value="{{ $package['name'] }}" @selected(old('package_preference') === $package['name'])>{{ $package['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="catering-requests" class="mb-1 block text-sm font-semibold text-[#090909]">Special Requests</label>
                                <textarea id="catering-requests" class="w-full rounded-lg border border-[#D6D6D6] bg-white px-4 py-3 text-base text-[#090909] focus:border-[#E5A900] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30" name="special_requests" rows="4" maxlength="2000">{{ old('special_requests') }}</textarea>
                            </div>
                            <div class="pt-2 md:col-span-2">
                                <button type="submit" class="w-full rounded-full bg-[#FFC60A] px-6 py-3 font-bold text-[#090909] shadow-sm transition hover:bg-[#E5A900]">Send Catering Inquiry</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <dialog id="catering-package-dialog" class="catering-package-dialog w-[calc(100%-2rem)] max-w-lg rounded-[24px] border border-[#E8DFAF] bg-white p-0 text-[#090909] shadow-2xl backdrop:bg-[#090909]/50" aria-labelledby="catering-package-title">
        <div class="p-6 md:p-8">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#E5A900]">Catering package</p>
                    <h2 id="catering-package-title" class="text-2xl font-bold"></h2>
                </div>
                <button type="button" class="rounded-full border border-[#E8DFAF] px-3 py-1 text-xl leading-none text-[#555555] hover:bg-[#FFF9E6]" data-close-catering-package aria-label="Close package details">&times;</button>
            </div>
            <p id="catering-package-description" class="text-base leading-7 text-[#555555]"></p>
            <p id="catering-package-price" class="hidden mb-6 font-bold text-[#E5A900]"></p>
            <button type="button" class="mt-4 w-full rounded-full bg-[#FFC60A] px-6 py-3 font-bold text-[#090909] shadow-sm transition hover:bg-[#E5A900]" data-book-catering-package>Choose Package &amp; Book</button>
        </div>
    </dialog>

@endsection
