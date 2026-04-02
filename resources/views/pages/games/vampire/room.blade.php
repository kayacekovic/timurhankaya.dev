@php
    $seo = [
        'title' => __('vampire.title').' · '.__('vampire.roomTitle', ['code' => $roomCode]),
        'description' => __('vampire.roomMetaDescription'),
        'canonical' => \App\Support\Seo::route('games.vampire.room', ['roomCode' => $roomCode]),
        'image' => 'og-vampire.png',
        'noindex' => true,
    ];
@endphp

@extends('layouts.app')

@section('title', __('vampire.title').' · '.__('vampire.roomTitle', ['code' => $roomCode]))

@section('content')
    <section class="min-h-[calc(100svh-4rem)] pt-20">
        <div class="max-w-3xl reveal" data-reveal>
            <div class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/70 px-4 py-2 text-xs font-semibold text-zinc-700 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">
                <span class="inline-flex h-2 w-2 rounded-full bg-red-400 shadow-[0_0_0_4px_rgba(248,113,113,0.16)]"></span>
                {{ __('vampire.roomTitle', ['code' => $roomCode]) }}
            </div>
            <h1 class="cyber-title mt-6 text-4xl font-extrabold tracking-tight text-zinc-950 sm:text-5xl dark:text-white">{{ __('vampire.title') }}</h1>
            <p class="mt-4 text-base leading-relaxed text-zinc-600 dark:text-zinc-300">
                {{ __('vampire.roomLead') }}
            </p>
        </div>

        <div class="mt-10">
            <livewire:games.vampire.room :room-code="$roomCode" />
        </div>
    </section>
@endsection
