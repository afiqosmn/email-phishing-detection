@extends('layouts.template')

@section('content')
<div class="flex flex-col lg:flex-row gap-8 justify-between">

    <!-- LEFT: Email Input Forms -->
    <div class="w-full lg:w-2/3 max-w-2xl">
        @if(Auth::check())
            <h1 class="text-3xl font-bold mb-6">Welcome to PhishDetector, {{ Auth::user()->name }}!</h1>
        @else
            <h1 class="text-3xl font-bold mb-6">Welcome to PhishDetector</h1>
        @endif
        <p class="text-gray-600 mb-6">Detect phishing emails using traditional rules and machine learning.</p>

        <!-- Manual Input Form -->
        <form action="" method="POST" class="space-y-4 mb-10">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Sender</label>
                <input type="text" name="sender" class="w-full px-4 py-2 border rounded-md" placeholder="sender@example.com" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" class="w-full px-4 py-2 border rounded-md" placeholder="Email subject" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" rows="6" class="w-full px-4 py-2 border rounded-md" placeholder="Email body content..." required></textarea>
            </div>

            <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-800">
                Analyze Email
            </button>
        </form>

        <!-- Upload .eml -->
        <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row sm:items-center gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Upload .eml File:</label>
                <input type="file" name="eml_file" accept=".eml" class="border px-4 py-2 rounded-md" required>
            </div>
            <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 mt-2 sm:mt-6">
                Upload & Analyze
            </button>
        </form>

        <!--@if (session('scan_result'))
            @php $result = session('scan_result'); @endphp

            <div id="result" class="mt-10 p-6 bg-gray-100 rounded shadow-md">
                <h2 class="text-xl font-bold mb-4 text-blue-700">Scan Result</h2>

                <p class="mb-2"><strong>Sender:</strong> {{ $result['sender'] }}</p>
                <p class="mb-2"><strong>Subject:</strong> "{{ $result['subject'] }}"</p>
                <p class="mb-4"><strong>Confidence Score:</strong> 
                    <span class="text-red-600 font-bold">{{ $result['confidence'] }}</span>
                </p>

                <div class="mb-4">
                    <h3 class="font-semibold text-gray-700">Detected Indicators:</h3>
                    <ul class="list-disc list-inside text-sm text-gray-800">
                        @foreach ($result['indicators'] as $indicator)
                            <li>{{ $indicator }}</li>
                        @endforeach
                    </ul>
                </div>

                @if (!empty($result['html_indicators']))
                    <div class="mt-6 p-4 bg-white border-l-4 border-yellow-500">
                        <h3 class="font-semibold text-gray-700">HTML Body Indicators:</h3>
                        <ul class="list-disc list-inside text-sm text-gray-800">
                            @forelse ($result['html_indicators'] as $htmlIndicator)
                                <li>{{ $htmlIndicator }}</li>
                            @empty
                                <li>No indicators found in HTML body.</li>
                            @endforelse
                        </ul>
                    </div>
                @endif

                <a href="{{ route('scan.downloadResult') }}" class="text-sm text-blue-600 hover:underline">Download Full Report (PDF)</a>
                <br>
                <!- New Scan New Email button 
                <a href="{{ route('clear.scan') }}" class="text-sm text-blue-600 hover:underline mt-2">
                    <i class="fas fa-envelope"></i> Scan New Email
                </a>
            </div>
        @endif-->
    </div>

    <!-- RIGHT: User Guide Placeholder -->
    <div x-data="{ activeSlide: 1 }" class="w-full lg:w-1/2 border-2 border-dashed border-gray-300 rounded-md h-[800px] text-gray-500 mt-6 p-6 flex flex-col items-center justify-center">

        <h1 class="font-bold text-xl mb-4">USER MANUAL GUIDE</h1>

        <!-- Slider Container -->
        <div class="relative w-full flex items-center justify-center">
            <!-- Slide 1 -->
            <div x-show="activeSlide === 1" class="transition duration-300 ease-in-out">
                <img src="{{ asset('img/Guide.jpg') }}" alt="Guide 1" class="w-[494px] h-auto rounded shadow" />
            </div>

            <!-- Slide 2 -->
            <div x-show="activeSlide === 2" class="transition duration-300 ease-in-out">
                <img src="{{ asset('img/Guide2.jpg') }}" alt="Guide 2" class="w-[494px] h-auto rounded shadow" />
            </div>

            <!-- Prev Button -->
            <button 
                @click="activeSlide = activeSlide === 1 ? 2 : activeSlide - 1" 
                class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-600 hover:text-blue-600 bg-transparent p-2 rounded-full transition duration-200"
                aria-label="Previous Slide">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Next Button -->
            <button 
                @click="activeSlide = activeSlide === 2 ? 1 : activeSlide + 1" 
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-600 hover:text-blue-600 bg-transparent p-2 rounded-full transition duration-200"
                aria-label="Next Slide">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Indicators -->
        <div class="flex space-x-2 mt-4">
            <template x-for="i in 2" :key="i">
                <div @click="activeSlide = i"
                    class="w-3 h-3 rounded-full"
                    :class="activeSlide === i ? 'bg-blue-600' : 'bg-gray-400'">
                </div>
            </template>
        </div>
    </div>


</div>
@endsection
