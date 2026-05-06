{{-- Webhooks — Create --}}
@extends('layouts.app')

@section('title', __('likeplatform-webhooks::webhooks.title_create'))

@section('breadcrumbs')
    <li class="flex items-center gap-1">
        <a href="{{ route('webhooks.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            {{ __('likeplatform-webhooks::webhooks.title_index') }}
        </a>
        <x-ui.icon name="chevron-right" class="size-3 text-gray-400" />
    </li>
    <li class="flex items-center gap-1">
        <span class="text-gray-900 dark:text-gray-100">{{ __('likeplatform-webhooks::webhooks.title_create') }}</span>
    </li>
@endsection

@section('content')
    <div class="mx-auto max-w-2xl">
        <x-ui.card :header="__('likeplatform-webhooks::webhooks.create_new')">
            <form method="POST" action="{{ route('webhooks.store') }}">
                @csrf

                <div class="space-y-5">
                    <x-ui.input
                        name="url"
                        type="url"
                        :label="__('likeplatform-webhooks::webhooks.target_url')"
                        :placeholder="__('likeplatform-webhooks::webhooks.url_placeholder')"
                        :value="old('url')"
                        required
                        maxlength="2048"
                        autofocus
                    />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('likeplatform-webhooks::webhooks.events') }}
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('likeplatform-webhooks::webhooks.events_help') }}
                        </p>
                        <div class="mt-3 space-y-2.5">
                            @foreach(['user.created', 'user.updated', 'api_key.created', 'api_key.revoked'] as $event)
                                <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:border-gray-700 dark:has-[:checked]:border-primary-600 dark:has-[:checked]:bg-primary-950/30">
                                    <input
                                        type="checkbox"
                                        name="events[]"
                                        value="{{ $event }}"
                                        class="mt-0.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800"
                                        @checked(is_array(old('events')) && in_array($event, old('events')))
                                    >
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono">
                                            {{ $event }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                    <a href="{{ route('webhooks.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        {{ __('common.cancel') }}
                    </a>
                    <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        {{ __('common.create') }}
                    </button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
