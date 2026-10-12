<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FeedbackPageTest extends TestCase
{
    public function test_feedback_page_only_shows_approved_google_sheet_reviews(): void
    {
        config([
            'feedback.sheet_json_url' => 'https://sheets.example.test/reviews.json',
            'feedback.form_url' => 'https://forms.google.com/example',
        ]);
        Http::fake([
            'https://sheets.example.test/reviews.json' => Http::response([
                [
                    'Name' => 'Alex Approved',
                    'Feedback' => 'The food was excellent.',
                    'Rating' => '5',
                    'Status' => 'Yes',
                    'Supporting Picture' => 'https://drive.google.com/file/d/PHOTO_123/view?usp=sharing',
                ],
                [
                    'Name' => 'Pat Approved',
                    'Review' => 'Friendly service.',
                    'Approved' => ' yes ',
                    'Date' => 'October 8, 2026',
                ],
                [
                    'Name' => 'Casey Approved',
                    'Feedback' => 'Would visit again.',
                    'Approva Status' => 'Approved',
                ],
                [
                    'name' => 'Lowercase Sheet Review',
                    'feedback' => 'Good restaurant.',
                    'approva_status' => 'Approved',
                ],
                [
                    'Name' => 'Sam Pending',
                    'Feedback' => 'Not published yet.',
                    'Status' => 'No',
                ],
            ]),
        ]);

        $response = $this->get('/feedback')
            ->assertOk()
            ->assertSee('Alex Approved')
            ->assertSee('The food was excellent.')
            ->assertSee('https://drive.google.com/thumbnail?id=PHOTO_123&amp;sz=w1200', false)
            ->assertSee('Pat Approved')
            ->assertSee('Friendly service.')
            ->assertSee('Casey Approved')
            ->assertSee('Would visit again.')
            ->assertSee('Lowercase Sheet Review')
            ->assertSee('Good restaurant.')
            ->assertDontSee('Sam Pending')
            ->assertDontSee('Not published yet.')
            ->assertSee('https://forms.google.com/example', false)
            ->assertSee(asset(config('feedback.qr_image')), false);
        $response->assertSee('grid gap-5 sm:grid-cols-2 xl:grid-cols-3', false)
            ->assertSee('View details')
            ->assertSee('id="feedback-details-dialog"', false);

        Http::assertSent(fn ($request) => $request->url() === 'https://sheets.example.test/reviews.json');
    }

    public function test_feedback_page_converts_google_sheets_values_rows(): void
    {
        config(['feedback.sheet_json_url' => 'https://sheets.example.test/values.json']);
        Http::fake([
            'https://sheets.example.test/values.json' => Http::response([
                'values' => [
                    ['Name', 'Review', 'Rating', 'Status'],
                    ['Taylor', 'Great meal.', '4', 'Yes'],
                    ['Jordan', 'Hidden review.', '3', 'No'],
                ],
            ]),
        ]);

        $this->get('/feedback')
            ->assertOk()
            ->assertSee('Taylor')
            ->assertSee('Great meal.')
            ->assertDontSee('Jordan')
            ->assertDontSee('Hidden review.');
    }

    public function test_feedback_page_reports_when_sheet_is_not_configured(): void
    {
        config([
            'feedback.sheet_json_url' => null,
            'feedback.form_url' => null,
        ]);

        $this->get('/feedback')
            ->assertOk()
            ->assertSee('Customer feedback is not available right now.')
            ->assertSee('The feedback form link will be available soon.')
            ->assertSee('Feedback');
    }
}
