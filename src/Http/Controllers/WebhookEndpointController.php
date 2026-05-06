<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use LikePlatform\Webhooks\Models\WebhookEndpoint;
use Illuminate\Support\Str;

class WebhookEndpointController extends Controller
{
    /**
     * Display a listing of webhook endpoints.
     */
    public function index(Request $request): View
    {
        $endpoints = WebhookEndpoint::where('user_id', $request->user()->id)
            ->withCount('deliveries')
            ->latest()
            ->get();

        return view('likeplatform-webhooks::webhooks.index', compact('endpoints'));
    }

    /**
     * Show the form for creating a new webhook endpoint.
     */
    public function create(): View
    {
        return view('likeplatform-webhooks::webhooks.create');
    }

    /**
     * Store a newly created webhook endpoint.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', 'in:user.created,user.updated,api_key.created,api_key.revoked'],
        ]);

        WebhookEndpoint::create([
            'user_id' => $request->user()->id,
            'url' => $validated['url'],
            'secret' => Str::random(32),
            'events' => $validated['events'],
            'is_active' => true,
        ]);

        return redirect()->route('webhooks.index')
            ->with('success', __('likeplatform-webhooks::webhooks.created_successfully'));
    }

    /**
     * Display the specified webhook endpoint.
     */
    public function show(Request $request, int $id): View
    {
        $endpoint = WebhookEndpoint::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $deliveries = $endpoint->deliveries()->latest()->take(20)->get();

        return view('likeplatform-webhooks::webhooks.show', compact('endpoint', 'deliveries'));
    }

    /**
     * Toggle the active state of a webhook endpoint.
     */
    public function toggle(Request $request, int $id): RedirectResponse
    {
        $endpoint = WebhookEndpoint::where('user_id', $request->user()->id)->findOrFail($id);
        $endpoint->update(['is_active' => !$endpoint->is_active]);

        return redirect()->route('webhooks.index')
            ->with('success', $endpoint->is_active
                ? __('likeplatform-webhooks::webhooks.enabled_successfully')
                : __('likeplatform-webhooks::webhooks.disabled_successfully'));
    }

    /**
     * Remove the specified webhook endpoint.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $endpoint = WebhookEndpoint::where('user_id', $request->user()->id)->findOrFail($id);
        $endpoint->delete();

        return redirect()->route('webhooks.index')
            ->with('success', __('likeplatform-webhooks::webhooks.deleted_successfully'));
    }
}
