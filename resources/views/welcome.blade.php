@extends('layouts.landingpage')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-4">Welcome to HybridPhish</h1>
        <p class="text-lg text-gray-600 mb-6">A hybrid email security platform leveraging machine learning and rule-based analysis <br>to detect and mitigate phishing threats.</p>
        <a href="{{ route('login-page') }}" class="inline-block bg-blue-500 text-xl font-semibold text-white px-4 py-2 rounded hover:bg-blue-700 transition">Get Started</a>
    </div>  
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-gray-300 rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4 text-center">Advanced Phishing Detection Engine</h3>
            <p class="text-black text-justify">Powered by a hybrid detection approach that combines Random Forest and several rule-based analysis. Integrated with <strong>Google Safe Browsing API</strong> to identify malicious URLs with high precision and reliability.</p>
        </div>
        <div class="bg-gray-300 rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4 text-center">User-Friendly Interface with User Guide</h3>
            <p class="text-black text-justify">Designed with simplicity and clarity in mind, the system provides an intuitive interface supported by a structured user manual. Step-by-step guidance ensures users can navigate features, interpret detection results, and utilize reporting tools with confidence.</p>
        </div>
        <div class="bg-gray-300 rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4 text-center">Real-Time Automated Protection</h3>
            <p class="text-black text-justify">Emails are automatically analyzed upon retrieval, ensuring immediate identification of potential phishing threats. Stay protected with instant detection results and proactive threat prevention mechanisms.</p>
        </div>
    </div>
</div>
@endsection