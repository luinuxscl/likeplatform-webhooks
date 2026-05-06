{{-- Webhooks — Index --}}
@extends('layouts.app')

@section('title', __('likeplatform-webhooks::webhooks.title_index'))

@section('breadcrumbs')
    <li class="flex items-center gap-1">
        <span class="text-gray-900 dark:text-gray-100">{{ __('likeplatform-webhooks::webhooks.title_index') }}</span>
    </li>
@endsection

@section('topbar-actions')
    <a href="{{ route('webhooks.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
        <x-ui.icon name="plus" class="size-4" />
        {{ __('likeplatform-webhooks::webhooks.create_new') }}
    </a>
@endsection

@section('content')
    <div class="mx-auto max-w-5xl">
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center gap-3">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('likeplatform-webhooks::webhooks.title_index') }}
                    </h2>
                    @if($endpoints->isNotEmpty())
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            {{ $endpoints->count() }}
                        </span>
                    @endif
                </div>
            </x-slot:header>

            @if($endpoints->isEmpty())
                <div class="py-12 text-center">
                    <x-ui.icon name="globe" class="mx-auto size-10 text-gray-300 dark:text-gray-600" />
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('likeplatform-webhooks::webhooks.no_endpoints') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                        {{ __('likeplatform-webhooks::webhooks.no_endpoints_description') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('webhooks.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                            <x-ui.icon name="plus" class="size-4" />
                            {{ __('likeplatform-webhooks::webhooks.create_new') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="-mx-6 -mb-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.url') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.events') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.deliveries') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('likeplatform-webhooks::webhooks.status') }}</th>
                                <th scope="col" class="relative px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            @foreach($endpoints as $endpoint)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="whitespace-nowrap px-6 py-3.5">
                                        <div class="max-w-xs truncate font-mono text-sm text-gray-700 dark:text-gray-300" title="{{ $endpoint->url }}">
                                            {{ $endpoint->url }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($endpoint->events as $event)
                                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                                    {{ $event }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $endpoint->deliveries_count }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5">
                                        @if($endpoint->is_active)
                                            <x-ui.badge variant="success">{{ __('likeplatform-webhooks::webhooks.active') }}</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="danger">{{ __('likeplatform-webhooks::webhooks.disabled') }}</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-right text-sm">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('webhooks.show', $endpoint->id) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                                {{ __('likeplatform-webhooks::webhooks.view') }}
                                            </a>
                                            <form method="POST" action="{{ route('webhooks.toggle', $endpoint->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                    {{ $endpoint->is_active ? __('likeplatform-webhooks::webhooks.disable') : __('likeplatform-webhooks::webhooks.enable') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('webhooks.destroy', $endpoint->id) }}" onsubmit="return confirm('{{ __('likeplatform-webhooks::webhooks.confirm_delete') }}')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                    {{ __('common.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
