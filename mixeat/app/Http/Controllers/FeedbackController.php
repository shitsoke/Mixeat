<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $reviews = [];
        $feedbackError = null;
        $sheetUrl = config('feedback.sheet_json_url');

        if (! is_string($sheetUrl) || trim($sheetUrl) === '') {
            $feedbackError = 'Customer feedback is not available right now.';
        } else {
            try {
                $response = Http::acceptJson()
                    ->timeout(10)
                    ->get($sheetUrl);

                if (! $response->successful()) {
                    Log::warning('Unable to fetch published feedback sheet.', [
                        'status' => $response->status(),
                    ]);
                    $feedbackError = 'Customer feedback could not be loaded. Please try again later.';
                } else {
                    $payload = $response->json();
                    $rows = $this->sheetRows($payload);

                    if ($rows === null) {
                        Log::warning('Published feedback sheet returned an unsupported JSON structure.');
                        $feedbackError = 'Customer feedback could not be loaded. Please try again later.';
                    } else {
                        $reviews = collect($rows)
                            ->filter(fn ($row) => is_array($row) && $this->isApproved($row))
                            ->map(fn (array $row) => [
                                'name' => $this->firstValue($row, ['Name', 'Customer Name', 'Full Name']) ?: 'MixEat customer',
                                'review' => $this->firstValue($row, ['Feedback', 'Review', 'Comment', 'Comments', 'Message']) ?: '',
                                'rating' => $this->firstValue($row, ['Rating', 'Stars']),
                                'date' => $this->firstValue($row, ['Date', 'Timestamp', 'Submitted At']),
                                'image' => $this->feedbackImageUrl($this->firstValue($row, ['Supporting Picture', 'Picture', 'Photo', 'Image', 'Uploaded Picture'])),
                            ])
                            ->filter(fn (array $review) => $review['review'] !== '')
                            ->values()
                            ->all();
                    }
                }
            } catch (ConnectionException) {
                Log::warning('Connection failed while fetching the published feedback sheet.');
                $feedbackError = 'Customer feedback could not be loaded. Please try again later.';
            }
        }

        return view('feedback', [
            'reviews' => $reviews,
            'feedbackError' => $feedbackError,
            'formUrl' => config('feedback.form_url'),
        ]);
    }

    private function sheetRows(mixed $payload): ?array
    {
        if (! is_array($payload)) {
            return null;
        }

        if (isset($payload['data']) && is_array($payload['data'])) {
            $payload = $payload['data'];
        } elseif (isset($payload['values']) && is_array($payload['values'])) {
            $values = $payload['values'];
            $headers = array_shift($values);

            if (! is_array($headers)) {
                return null;
            }

            $headers = array_map(
                fn ($header) => is_scalar($header) ? trim((string) $header) : '',
                $headers
            );
            if ($headers === [] || in_array('', $headers, true)) {
                return null;
            }

            $payload = array_map(function ($row) use ($headers) {
                if (! is_array($row)) {
                    return null;
                }

                $row = array_slice(array_pad($row, count($headers), null), 0, count($headers));

                return array_combine($headers, $row);
            }, $values);
        }

        return array_is_list($payload) ? $payload : null;
    }

    private function isApproved(array $row): bool
    {
        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));
            $isApprovalColumn = in_array($normalizedKey, ['status', 'approved', 'approvalstatus', 'approvastatus'], true);

            if ($isApprovalColumn && is_scalar($value)
                && in_array(strtolower(trim((string) $value)), ['yes', 'approved'], true)) {
                return true;
            }
        }

        return false;
    }

    private function firstValue(array $row, array $keys): string
    {
        $normalizedKeys = array_map(
            fn ($key) => strtolower(preg_replace('/[^a-z]/i', '', $key)),
            $keys
        );

        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));

            if (in_array($normalizedKey, $normalizedKeys, true) && is_scalar($value)) {
                $value = trim((string) $value);

                if ($value !== '') {
                    return $value;
                }
            }
        }

        return '';
    }

    private function feedbackImageUrl(string $imageUrl): ?string
    {
        if ($imageUrl === '' || ! filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($imageUrl);
        if (($parts['scheme'] ?? null) !== 'https') {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        if (! in_array($host, ['drive.google.com', 'docs.google.com'], true)) {
            return null;
        }

        parse_str($parts['query'] ?? '', $query);
        $fileId = $query['id'] ?? null;

        if (! $fileId && preg_match('~/file/d/([a-zA-Z0-9_-]+)~', $parts['path'] ?? '', $matches)) {
            $fileId = $matches[1];
        }

        if (! is_string($fileId) || ! preg_match('/^[a-zA-Z0-9_-]+$/', $fileId)) {
            return null;
        }

        return 'https://drive.google.com/thumbnail?id='.rawurlencode($fileId).'&sz=w1200';
    }
}
