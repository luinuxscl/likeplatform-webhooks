{{-- Webhooks — Show --}}
@extends('layouts.app')

@section('title', __('likeplatform-webhooks::webhooks.title_show'))

@section('breadcrumbs')
    <li class="flex items-center gap-1">
        <a href="{{ route('webhooks.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            {{ __('likeplatform-webhooks::webhooks.title_index') }}
        </a>
        <x-ui.icon name="chevron-right" class="size-3 text-gray-400" />
    </li>
    <li class="flex items-center gap-1">
        <span class="text-gray-900 dark:text-gray-100">{{ __('likeplatform-webhooks::webhooks.title_show') }}</span>
    </li>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('likeplatform-webhooks::webhooks.details') }}</h2>
                    <x-ui.badge :variant="$endpoint->is_active ? 'success' : 'danger'">
                        {{ $endpoint->is_active ? __('likeplatform-webhooks::webhooks.active') : __('likeplatform-webhooks::webhooks.disabled') }}
                    </x-ui.badge>
                </div>
            </x-slot:header>

            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-1 py-3.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.url') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0 font-mono break-all">{{ $endpoint->url }}</dd>
                </div>
                <div class="px-1 py-3.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.secret') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0 font-mono">{{ Str::mask($endpoint->secret, '*', 4, -4) }}</dd>
                </div>
                <div class="px-1 py-3.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.events') }}</dt>
                    <dd class="mt-1 sm:col-span-2 sm:mt-0">
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($endpoint->events as $event)
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400 font-mono">
                                    {{ $event }}
                                </span>
                            @endforeach
                        </div>
                    </dd>
                </div>
                <div class="px-1 py-3.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.last_sent_at') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                        {{ $endpoint->last_sent_at ? $endpoint->last_sent_at->diffForHumans() : '—' }}
                    </dd>
                </div>
                <div class="px-1 py-3.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.created_at') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                        {{ $endpoint->created_at->format('M d, Y H:i') }}
                    </dd>
                </div>
            </dl>
        </x-ui.card>

        {{-- Delivery History --}}
        <x-ui.card :header="__('likeplatform-webhooks::webhooks.delivery_history')">
            @if($deliveries->isEmpty())
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('likeplatform-webhooks::webhooks.no_deliveries') }}
                </p>
            @else
                <div class="-mx-6 -mb-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.event') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.status_code') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.duration') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.attempt') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.sent_at') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            @foreach($deliveries as $delivery)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="whitespace-nowrap px-6 py-3.5 font-mono text-sm text-gray-700 dark:text-gray-300">
                                        {{ $delivery->event }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-sm">
                                        @if($delivery->isSuccessful())
                                            <x-ui.badge variant="success">{{ $delivery->response_status }}</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="danger">{{ $delivery->response_status ?: 'ERR' }}</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $delivery->duration_ms }}ms
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $delivery->attempt }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $delivery->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>

        <div class="flex justify-between">
            <a href="{{ route('webhooks.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                &larr; {{ __('common.back') }}
            </a>

            <div class="flex gap-3">
                <form method="POST" action="{{ route('webhooks.toggle', $endpoint->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-2 text-sm font-medium text-yellow-700 hover:bg-yellow-100 dark:border-yellow-600 dark:bg-yellow-950/20 dark:text-yellow-400 dark:hover:bg-yellow-950/40">
                        {{ $endpoint->is_active ? __('likeplatform-webhooks::webhooks.disable') : __('likeplatform-webhooks::webhooks.enable') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('webhooks.destroy', $endpoint->id) }}" onsubmit="return confirm('{{ __('likeplatform-webhooks::webhooks.confirm_delete') }}')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                        {{ __('common.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
