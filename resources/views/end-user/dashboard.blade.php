@extends('layouts.dashtemp')


@section('page-header')
    <h1 class="font-bold text-3xl">Welcome to HybridPhish</h1>
@endsection

@section('content')
    <div class="flex flex-col">
        <div class="flex flex-row">
            <div class="w-1/2 p-4">
                <div class="bg-gray-100 p-6 rounded-lg shadow-md">
                    <i class="fas fa-envelope text-green-500 w-6 text-center"></i>
                    <span class="text-xl font-semibold mb-4">Total Email Scanned: 5</span>
                </div>
            </div>

            <div class="w-1/2 p-4">
                <div class="bg-gray-100 p-6 rounded-lg shadow-md">
                    <i class="fas fa-envelope text-red-500 w-6 text-center"></i>
                    <span class="text-xl font-semibold mb-4">Phishing Detected: 0</span>
                </div>
            </div>
        </div>

        <div class="flex flex-row">
            <div class="w-1/2 p-4">
                <div class="bg-blue-100 p-6 rounded-lg shadow-md">
                    <i class="fas fa-shield-alt text-blue-500 w-6 text-center"></i>
                    <span class="text-xl font-semibold mb-4">Threats Reported: 0</span>
                </div>
            </div>

            <div class="w-1/2 p-4">
                <div class="bg-green-100 p-6 rounded-lg shadow-md">
                    <i class="fas fa-chart-line text-green-500 w-6 text-center"></i>
                    <span class="text-xl font-semibold mb-4">Detection Accuracy: 100%</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
@endsection