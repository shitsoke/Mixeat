@extends('layouts.app')

@section('title', 'MixEat Catering | Good Food. Great Moments.')

@section('content')
    <section id="catering" class="scroll-mt-24 bg-[#1A1A1A] py-12 md:py-16">
        <div class="w-full px-4 md:px-7">
            <div class="mb-12 grid grid-cols-1 items-center gap-6 lg:grid-cols-12">
                <div class="lg:col-span-12">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">MixEat Catering</p>
                    <h1 class="catering-hero-title mt-2">Planning an Event or Office Party?</h1>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-[#B8B8B8]">We offer customized tray packages, packed meals, and full-service catering for corporate events, parties, and celebrations.</p>
                </div>
            </div>

            <div class="mb-12 grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach ($cateringContent['highlights'] as $index => $highlight)
                    @php($package = $cateringContent['packages'][$index] ?? null)
                    <button
                        type="button"
                        class="catering-highlight-card flex min-h-[108px] items-center justify-center gap-4 rounded-[20px] border border-[#333333] bg-[#201D19] px-6 py-6 text-left shadow-sm transition hover:-translate-y-1 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#FBAB18]"
                        data-catering-package
                        data-package-name="{{ $package['name'] ?? $highlight }}"
                        data-package-description="{{ $package['description'] ?? 'Contact us to learn more about this catering service.' }}"
                        data-package-price="{{ is_numeric($package['price'] ?? null) ? number_format((float) $package['price'], 2, '.', '') : '' }}"
                        @if (str_contains(strtolower($highlight), 'party trays'))
                            data-party-trays-toggle
                            aria-controls="party-trays-menu"
                            aria-expanded="{{ request('service') === 'Party Trays' ? 'true' : 'false' }}"
                        @endif
                    >
                        @if ($index === 2)
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FBAB18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><path d="M3 14h18"/><path d="M5 14v-2a7 7 0 0 1 14 0v2"/><path d="M12 4v-2"/><path d="M8 14v3a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-3"/></svg>
                            <span class="font-bold text-[#FFFFFF]">Catering</span>
                        @else
                            @if ($index === 0)
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FBAB18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><path d="M16.5 9.4 7.55 4.24"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FBAB18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><path d="M3 11h18"/><path d="M5 11l1 9h12l1-9"/><path d="M12 4a3 3 0 0 1 3 3H9a3 3 0 0 1 3-3z"/></svg>
                            @endif
                            <span class="font-bold text-[#FFFFFF]">{{ $highlight }}</span>
                        @endif
                    </button>
                @endforeach
            </div>

            @if ($partyTrays->isNotEmpty())
                <section id="party-trays-menu" class="mb-12 {{ request('service') === 'Party Trays' ? '' : 'hidden' }}" aria-labelledby="party-trays-heading">
                    <div class="mb-6">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#FBAB18]">Catering menu</p>
                        <h2 id="party-trays-heading" class="mt-2 text-3xl font-bold text-[#FFFFFF]">Party Trays &amp; Buffet Setups</h2>
                        <p class="mt-3 max-w-2xl text-base leading-7 text-[#B8B8B8]">Choose from MixEat favorites for your gathering. Select a tray to start a catering inquiry and our team will help with portions and pricing.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($partyTrays as $partyTray)
                            <article class="overflow-hidden rounded-[20px] border border-[#333333] bg-[#201D19] shadow-sm">
                                <img src="{{ $partyTray->imageUrl() }}" alt="{{ $partyTray->name }}" class="aspect-[4/3] w-full object-cover">
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-[#FFFFFF]">{{ $partyTray->name }}</h3>
                                    <p class="mt-2 min-h-12 text-sm leading-6 text-[#B8B8B8]">{{ $partyTray->description }}</p>
                                    <a href="{{ route('catering', ['service' => 'Party Trays', 'package' => $partyTray->name]) }}#catering-inquiry" class="mt-4 inline-flex rounded-full bg-[#FBAB18] px-5 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#FBAB18]">
                                        Request this tray
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <div class="mb-12 rounded-[20px] border border-[#333333] bg-[#201D19] px-6 py-12 md:px-10">
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
                <div class="overflow-hidden rounded-[24px] border border-[#333333] bg-[#201D19] shadow-sm">
                    <div class="p-6 md:p-10">
                        <div class="mb-6">
                            <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#FBAB18]">Tell us about your event</p>
                            <h2 class="text-3xl font-bold text-[#FFFFFF]">Book Catering</h2>
                            <p class="mb-0 text-[#B8B8B8]">Share a few details and our catering team will contact you to plan your event.</p>
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
                                <label for="catering-client-name" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Client Name</label>
                                <input id="catering-client-name" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="client_name" value="{{ old('client_name') }}" autocomplete="name" maxlength="120" required>
                            </div>
                            <div>
                                <label for="catering-email" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Email</label>
                                <input id="catering-email" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
                            </div>
                            <div>
                                <label for="catering-phone" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Phone</label>
                                <input id="catering-phone" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" maxlength="30" required>
                            </div>
                            <div>
                                <label for="catering-company" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Company / Organization <span class="font-normal text-[#777777]">(optional)</span></label>
                                <input id="catering-company" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="company_name" value="{{ old('company_name') }}" maxlength="160">
                            </div>
                            <div>
                                <label for="catering-date" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Event Date</label>
                                <input id="catering-date" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="date" name="event_date" value="{{ old('event_date') }}" min="{{ now()->toDateString() }}" required>
                            </div>
                            <div>
                                <label for="catering-time" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Event Time</label>
                                <input id="catering-time" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="time" name="event_time" value="{{ old('event_time') }}" required>
                            </div>
                            <div>
                                <label for="catering-venue" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Venue / Location</label>
                                <input id="catering-venue" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="venue" value="{{ old('venue') }}" maxlength="255" required>
                            </div>
                            <div>
                                <label for="catering-event-type" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Event Type</label>
                                <select id="catering-event-type" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" name="event_type" required>
                                    <option value="">Choose an event type</option>
                                    @foreach (['Corporate', 'Office Party', 'Celebration'] as $eventType)
                                        <option value="{{ $eventType }}" @selected(old('event_type') === $eventType)>{{ $eventType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="catering-service" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Service Type</label>
                                <select id="catering-service" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" name="service_type" required>
                                    <option value="">Choose a service</option>
                                    @foreach (['Packed Meals', 'Party Trays', 'Full-Service Catering'] as $service)
                                        <option value="{{ $service }}" @selected(old('service_type', request('service')) === $service)>{{ $service }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="catering-guests" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Guest Count</label>
                                <input id="catering-guests" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="number" name="guest_count" value="{{ old('guest_count') }}" min="1" max="5000" required>
                            </div>
                            <div>
                                <label for="catering-budget" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Budget Range <span class="font-normal text-[#777777]">(optional)</span></label>
                                <input id="catering-budget" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="budget_range" value="{{ old('budget_range') }}" maxlength="100" placeholder="e.g. ₱10,000 - ₱20,000">
                            </div>
                            <div class="md:col-span-2">
                                <label for="catering-package" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Package Preference <span class="font-normal text-[#777777]">(optional)</span></label>
                                <select id="catering-package" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" name="package_preference">
                                    <option value="">No preference yet</option>
                                    @foreach ($cateringContent['packages'] as $package)
                                        <option value="{{ $package['name'] }}" @selected(old('package_preference', request('package')) === $package['name'])>{{ $package['name'] }}</option>
                                    @endforeach
                                    @if ($partyTrays->isNotEmpty())
                                        <optgroup label="Party Trays &amp; Buffet Setups">
                                            @foreach ($partyTrays as $partyTray)
                                                <option value="{{ $partyTray->name }}" @selected(old('package_preference', request('package')) === $partyTray->name)>{{ $partyTray->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="catering-requests" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Special Requests</label>
                                <textarea id="catering-requests" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" name="special_requests" rows="4" maxlength="2000">{{ old('special_requests') }}</textarea>
                            </div>
                            <div class="pt-2 md:col-span-2">
                                <button type="submit" class="w-full rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#FFFFFF] shadow-sm transition hover:bg-[#FBAB18]">Send Catering Inquiry</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <dialog id="catering-package-dialog" class="catering-package-dialog w-[calc(100%-2rem)] max-w-lg rounded-[24px] border border-[#333333] bg-[#201D19] p-0 text-[#FFFFFF] shadow-2xl backdrop:bg-[#FFFFFF]/50" aria-labelledby="catering-package-title">
        <div class="p-6 md:p-8">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#FBAB18]">Catering package</p>
                    <h2 id="catering-package-title" class="text-2xl font-bold"></h2>
                </div>
                <button type="button" class="rounded-full border border-[#333333] px-3 py-1 text-xl leading-none text-[#B8B8B8] hover:bg-[#1A1A1A]" data-close-catering-package aria-label="Close package details">&times;</button>
            </div>
            <p id="catering-package-description" class="text-base leading-7 text-[#B8B8B8]"></p>
            <p id="catering-package-price" class="hidden mb-6 font-bold text-[#FBAB18]"></p>
            <button type="button" class="mt-4 w-full rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#FFFFFF] shadow-sm transition hover:bg-[#FBAB18]" data-book-catering-package>Choose Package &amp; Book</button>
        </div>
    </dialog>

@endsection




