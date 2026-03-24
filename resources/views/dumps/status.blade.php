@extends('layouts.dashtemp')

@section('page-header')
  <h2 class="text-xl font-semibold">Mailbox</h2>
@endsection

@section('content')
  <!-- Navigation (Previous / Next) -->
  <div class="flex justify-end mb-3">
    <div class="inline-flex gap-2">
      
    </div>
  </div>

  <!-- Email Table -->
  <div class="overflow-x-auto">
    <table class="min-w-full border-collapse border border-gray-200 rounded-lg overflow-hidden shadow-sm">
      <thead class="bg-gray-100 text-left">
        <tr>
            <th class="border px-3 py-2">Date</th>
            <th class="border px-3 py-2 w-1/5">From</th>
            <th class="border px-3 py-2 w-2/5">Subject</th>
            <th class="border px-3 py-2">Result</th>
            <th class="border px-3 py-2 text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($emails as $email)
          <tr class="hover:bg-gray-50 transition">
            <td class="border px-3 py-2 truncate max-w-40" title="{{ $email['date'] }}">
              {{ $email['date'] }}
            </td>
            <td class="border px-3 py-2 truncate max-w-[250px]" title="{{ $email['from'] }}">
              {{ $email['from'] }}
            </td>
            <td class="border px-3 py-2 text-center" title="{{ $email['subject'] }}">
                {{ $email['subject'] }}
            </td>
            <td class="border px-3 py-2 text-center">
                <h1>90</h1>
            </td>
            <td class="border px-3 py-2 text-center">
              <a href="{{ route('emails.view', ['messageId' => $email['id']]) }}"
                  class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded-md transition">
                  View Details
                </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4 text-gray-500">No emails found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
