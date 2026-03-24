@extends('layouts.dashtemp')

@section('page-header')
    <h1 class="font-bold text-3xl">User Profile</h1>
@endsection

@section('content')
@php
    $user = Auth::user();
@endphp

<div class="max-w-4xl mx-auto">
    <div class="flex items-center space-x-6">
        <div class="w-32 h-32 rounded-full overflow-hidden border">
            @if($user && $user->avatar)
                <img src="{{ $user->avatar_url}}" alt="avatar" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                    <i class="fas fa-user fa-2x"></i>
                </div>
            @endif
        </div>

        <div>
            <h2 class="text-2xl font-semibold">{{ $user->name ?? 'No Name' }}</h2>
            <p class="text-sm text-gray-600">{{ $user->email ?? 'No Email' }}</p>
            <p class="text-sm text-gray-600">
                Gmail Connected: {{ $user && $user->google_id ? 'Yes' : 'No' }}
            </p>
        </div>
    </div>

    <div class="mt-8 bg-gray-50 p-4 rounded">
        <h3 class="font-medium">Account Information</h3>
        <ul class="mt-2 space-y-1 text-sm text-gray-700">
            <li><strong>User ID:</strong> {{ $user->id ?? '-' }}</li>
            <li><strong>Created At:</strong> {{ $user->created_at ?? '-' }}</li>
            <li><strong>Last Login:</strong> {{ $user->updated_at ?? '-' }}</li>
        </ul>
    </div>
</div>
@endsection
