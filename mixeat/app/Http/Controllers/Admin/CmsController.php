<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function editAbout()
    {
        $aboutHeading = SiteSetting::where('key', 'about_heading')->value('value');
        $aboutContent = SiteSetting::where('key', 'about_content')->value('value');

        return view('admin.cms.about', compact('aboutHeading', 'aboutContent'));
    }

    public function updateAbout(Request $request)
    {
        SiteSetting::updateOrCreate(['key' => 'about_heading'], ['value' => $request->about_heading]);
        SiteSetting::updateOrCreate(['key' => 'about_content'], ['value' => $request->about_content]);

        return back()->with('success', 'About page content updated.');
    }

    public function editContact()
    {
        $contactPhone = SiteSetting::where('key', 'contact_phone')->value('value');
        $contactEmail = SiteSetting::where('key', 'contact_email')->value('value');
        $contactAddress = SiteSetting::where('key', 'contact_address')->value('value');

        return view('admin.cms.contact', compact('contactPhone', 'contactEmail', 'contactAddress'));
    }

    public function updateContact(Request $request)
    {
        SiteSetting::updateOrCreate(['key' => 'contact_phone'], ['value' => $request->contact_phone]);
        SiteSetting::updateOrCreate(['key' => 'contact_email'], ['value' => $request->contact_email]);
        SiteSetting::updateOrCreate(['key' => 'contact_address'], ['value' => $request->contact_address]);

        return back()->with('success', 'Contact details updated.');
    }
}