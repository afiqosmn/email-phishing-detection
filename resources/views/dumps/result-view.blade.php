@extends('layouts.dashtemp')

@section('page-header')
<h2 class="text-xl font-semibold">Detection Result Detail</h2>
@endsection

@section('content')

<div class="space-y-4">
    <div>
        <strong>Message ID:</strong> {{ $result->message_id }}
    </div>

    <div>
        <strong>Decision:</strong>
        <span class="font-bold">
            {{ ucfirst($result->final_decision) }}
        </span>
    </div>

    <div>
        <strong>Scanned At:</strong>
        {{ $result->created_at->format('Y-m-d H:i') }}
    </div>

    <div>
        <strong>Email Subject:</strong><br>
        {{ $result->email->subject ?? '-' }}
    </div>

    <div>
        <strong>Email Snippet:</strong><br>
        {{ $result->email->snippet ?? '-' }}
    </div>

    <a href="{{ route('result') }}"
       class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
        Back
    </a>
</div>

@endsection
