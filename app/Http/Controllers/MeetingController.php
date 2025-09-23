<?php

namespace App\Http\Controllers;

use App\Services\CalendlyService;

class MeetingController extends Controller
{
    public function getMeetingLink(CalendlyService $calendly)
    {
        // Step 1: Get current user
        $user = $calendly->getUser();
        $userUri = $user['resource']['uri'];

        // Step 2: Get event types
        $eventTypes = $calendly->getEventTypes($userUri);
        $eventTypeUri = $eventTypes['collection'][0]['uri']; // pick first event type

        // Step 3: Create scheduling link
        $link = $calendly->createSchedulingLink(
            $eventTypeUri,
            'sarwanverma469@gmail.com',
            'Hello Testing'
        );
        dd($link);
        return response()->json([
            'scheduling_link' => $link['resource']['booking_url'] ?? null
        ]);
    }

    public function createMeeting(CalendlyService $calendly)
    {
        // Step 1: Get user and event type
        $user = $calendly->getUser();
        $userUri = $user['resource']['uri'];

        $eventTypes = $calendly->getEventTypes($userUri);
        $eventTypeUri = $eventTypes['collection'][0]['uri'];

        // Step 2: Create meeting directly
        $meeting = $calendly->createEvent(
            $eventTypeUri,
            'sarwanverma469@gmail.com',
            'Hello Testing',
            '2025-09-20T14:00:00Z',   // UTC start time
            '2025-09-20T14:30:00Z'    // UTC end time
        );
        
        return response()->json($meeting);
    }
}
