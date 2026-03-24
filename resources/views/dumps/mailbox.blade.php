@extends('layouts.dashtemp')

@section('page-header')
  <h2 class="text-xl font-semibold">Mailbox</h2>
@endsection

@section('content')
  <!-- Navigation (Previous / Next) -->
  <div class="flex justify-end mb-3">
    <div class="inline-flex gap-2">
      @if ($hasPrev)
        <a href="{{ route('mailbox', ['page' => $page - 1]) }}"
          class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-md text-sm flex items-center transition">
          ← Prev
        </a>
      @endif
      @if ($hasNext)
        <a href="{{ route('mailbox', ['page' => $page + 1]) }}"
          class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-md text-sm flex items-center transition">
          Next →
        </a>
      @endif
    </div>
  </div>

  <!-- Email Table -->
  <div class="overflow-x-auto">
    <table class="min-w-full border-collapse border border-gray-200 rounded-lg overflow-hidden shadow-sm">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="border px-3 py-2 w-1/5">From</th>
          <th class="border px-3 py-2 w-2/5">Subject</th>
          <th class="border px-3 py-2 w-1/5 text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($emails as $email)
          <tr class="hover:bg-gray-50 transition">
            <td class="border px-3 py-2 truncate max-w-40" title="{{ $email['from'] }}">
              {{ $email['from'] }}
            </td>
            <td class="border px-3 py-2 truncate max-w-[250px]" title="{{ $email['subject'] }}">
              {{ $email['subject'] }}
            </td>
            <td class="border px-3 py-2 text-center">
              <div class="flex justify-center gap-2">
                <a href="{{ route('emails.view', ['messageId' => $email['id']]) }}"
                  class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded-md transition">
                  View
                </a>


                <form action="{{ route('emails.scan', ['messageId' => $email['id']]) }}" method="POST">
                  @csrf
                  <button type="submit"
                    class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-md transition">
                    Scan
                  </button>
                </form>
                
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center py-4 text-gray-500">No emails found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
