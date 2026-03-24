@extends('layouts.dashtemp')

@section('page-header')
    <h2 class="text-xl font-semibold">ML Service Health Check</h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">🤖 ML Service Status</h1>
    
    <div class="mb-6 p-4 rounded-lg {{ $status === 'online' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="flex items-center">
            <div class="w-4 h-4 rounded-full mr-3 {{ $status === 'online' ? 'bg-green-500' : 'bg-red-500' }}"></div>
            <h2 class="text-lg font-bold {{ $status === 'online' ? 'text-green-700' : 'text-red-700' }}">
                Service is {{ strtoupper($status) }}
            </h2>
        </div>
        <p class="text-sm text-gray-600 mt-2 ml-7">Endpoint: {{ $ml_url }}</p>
    </div>
    
    @if(isset($response))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Service Info -->
        <div class="bg-gray-50 rounded-lg p-4 border">
            <h3 class="font-semibold text-gray-700 mb-3">Service Information</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Service:</span>
                    <span class="font-medium">{{ $response['service'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Version:</span>
                    <span class="font-medium">{{ $response['version'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Requests:</span>
                    <span class="font-medium">{{ $response['total_requests'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Timestamp:</span>
                    <span class="font-medium text-sm">{{ $response['timestamp'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        
        <!-- ML Model Info -->
        <div class="bg-gray-50 rounded-lg p-4 border">
            <h3 class="font-semibold text-gray-700 mb-3">ML Model Status</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">ML Available:</span>
                    <span class="font-medium {{ $response['ml_available'] ?? false ? 'text-green-600' : 'text-red-600' }}">
                        {{ $response['ml_available'] ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Current Model:</span>
                    <span class="font-medium">{{ $response['current_model'] ?? 'N/A' }}</span>
                </div>
                @if(isset($response['available_models']) && count($response['available_models']) > 0)
                <div>
                    <span class="text-gray-600">Available Models:</span>
                    <div class="mt-1">
                        @foreach($response['available_models'] as $model)
                        <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs mr-1 mb-1">
                            {{ $model }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($error))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <h3 class="font-semibold text-red-700 mb-2">Error Details</h3>
        <p class="text-red-600">{{ $error }}</p>
    </div>
    @endif
    
    <!-- Raw Response -->
    <div class="mt-6">
        <h3 class="font-semibold text-gray-700 mb-2">Raw Response</h3>
        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto">
            <pre class="text-sm">@json($response ?? ['error' => $error ?? 'No response'], JSON_PRETTY_PRINT)</pre>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('dashboard') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded transition">
           ← Back to Dashboard
        </a>
        
        <a href="http://127.0.0.1:5001/" target="_blank" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
           Open ML Service Dashboard
        </a>
        
        <a href="{{ route('mailbox') }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
           Test with Email Scan →
        </a>
    </div>
</div>
@endsection