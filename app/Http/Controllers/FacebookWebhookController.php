<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessFacebookLead;

class FacebookWebhookController extends Controller
{
    /**
     * Handle Facebook webhook GET and POST
     */
    public function handle(Request $request)
    {
        // Step 1: Verification (GET)
        if ($request->isMethod('get')) {
            return $this->verifyWebhook($request);
        }

        // Step 2: Incoming lead event (POST)
        if ($request->isMethod('post')) {
            return $this->handleWebhookEvent($request);
        }

        return response('Method Not Allowed', 405);
    }

    /**
     * Verify webhook (GET)
     */
    protected function verifyWebhook(Request $request)
    {
        $verifyToken = env('FB_VERIFY_TOKEN'); // your .env token

        if ($request->hub_mode === 'subscribe' &&
            $request->hub_verify_token === $verifyToken) {
            return response($request->hub_challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming lead event (POST)
     */
    protected function handleWebhookEvent(Request $request)
    {
        $entries = $request->input('entry', []);

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $value = $change['value'] ?? [];
                $leadgenId = $value['leadgen_id'] ?? null;
                $formId = $value['form_id'] ?? null;
                $pageId = $value['page_id'] ?? null;

                if ($leadgenId && $formId && $pageId) {
                    // Dispatch a job to fetch full lead and save
                    ProcessFacebookLead::dispatch($leadgenId, $formId, $pageId);
                }
            }
        }

        // Facebook expects 200 OK quickly
        return response('EVENT_RECEIVED', 200);
    }
}