<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CalendlyService
{
    protected $baseUrl = 'https://api.calendly.com';

    public function __construct(protected string $apiKey = '')
    {
        $this->apiKey = 'eyJraWQiOiIxY2UxZTEzNjE3ZGNmNzY2YjNjZWJjY2Y4ZGM1YmFmYThhNjVlNjg0MDIzZjdjMzJiZTgzNDliMjM4MDEzNWI0IiwidHlwIjoiUEFUIiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiJodHRwczovL2F1dGguY2FsZW5kbHkuY29tIiwiaWF0IjoxNzU4MTIyODczLCJqdGkiOiJmYWRjYzE3Ni0zZGU3LTQzNmQtODUwMi02NmEwZmVmZGIxNDUiLCJ1c2VyX3V1aWQiOiI0MDlhZjZjMy0wOTkwLTRhY2YtYWY4Yy03MzMyMDc0YmM0YzEifQ.U023qKtfisP3fSDgDNBkbzHfdcTohpszT4cBejCA46vXDGSoEexInhdsaqYJ-hbojjawGAqB2VAB17tQrvQxEA';
    }

    private function headers()
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ];
    }

    // 1. Get authenticated user
    public function getUser()
    {
        return Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/users/me')
            ->json();
    }

    // 2. Get event types
    public function getEventTypes($userUri)
    {
        return Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/event_types', [
                'user' => $userUri
            ])
            ->json();
    }

    // 3. Create a scheduling link
    public function createSchedulingLink($eventTypeUri, $inviteeEmail, $name = null)
    {
        return Http::withHeaders($this->headers())
        ->post($this->baseUrl . '/scheduling_links', [
            'max_event_count' => 1,
            'owner' => $eventTypeUri,
            'owner_type' => 'EventType'
        ])->json();
    }

    public function createEvent($eventTypeUri, $inviteeEmail, $inviteeName, $startTime, $endTime)
    {
        return Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/some_event_invitees_endpoint', [
                'event_type' => $eventTypeUri,
                'start_time' => $startTime,   // ISO8601: 2025-09-17T14:00:00Z
                'end_time'   => $endTime,     // ISO8601
                'location'   => [
                    'type' => 'zoom', // or 'physical', 'google_conference', etc.
                ],
                'invitees'   => [[
                    'email' => $inviteeEmail,
                    'name'  => $inviteeName,
                ]]
            ])->json();
    }

}
