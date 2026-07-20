<?php

namespace App\Http\Controllers;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Validation\Rule;
use Inertia\{Inertia, Response};

class TimerSettingsController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Timer', [
            'preferences' => $request->user()->getOrCreatePreferences(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'focus_minutes' => ['required', 'integer', 'between:15,90'],
            'short_break_minutes' => ['required', 'integer', 'between:1,15'],
            'long_break_minutes' => ['required', 'integer', 'between:10,45'],
            'focus_tone' => ['required', Rule::in(['soft-bell', 'deep-gong', 'digital-ping'])],
            'break_tone' => ['required', Rule::in(['upbeat-chime', 'gentle-marimba', 'forest-birds'])],
            'browser_notifications' => ['required', 'boolean'],
            'zen_mode' => ['required', 'boolean'],
        ]);

        $request->user()->getOrCreatePreferences()->update($validated);

        return back();
    }
}
