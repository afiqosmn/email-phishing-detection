@extends('layouts.landingpage')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-10">

        <!-- Page Header -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">
                About HybridPhish
            </h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                HybridPhish is a phishing detection system designed to strengthen email security through structured analysis, classification techniques, and automated threat identification.
            </p>
        </div>

        <!-- Problem & Solution -->
        <div class="grid md:grid-cols-2 gap-12 mb-20">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    The Problem
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    Phishing attacks remain one of the most common and dangerous cybersecurity threats. 
                    Malicious emails often imitate legitimate organizations, making it difficult for users 
                    to distinguish between authentic and fraudulent messages. Traditional filtering methods 
                    are often insufficient to detect evolving attack patterns.
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    The Solution
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    HybridPhish integrates Machine Learning (Random Forest) with rule-based detection techniques 
                    to analyze email content, headers, and embedded URLs. By leveraging Gmail API for email retrieval 
                    and Google Safe Browsing API for URL reputation checking, the system provides automated and 
                    reliable phishing detection.
                </p>
            </div>
        </div>

        <!-- Objectives -->
        <div class="mb-20">
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-10">
                Project Objectives
            </h2>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white shadow rounded-lg p-6 text-center">
                    <h3 class="font-semibold text-lg mb-3 text-blue-700">
                        Intelligent Detection
                    </h3>
                    <p class="text-gray-600 text-sm">
                        Develop a hybrid phishing detection model combining machine learning 
                        and rule-based analysis.
                    </p>
                </div>

                <div class="bg-white shadow rounded-lg p-6 text-center">
                    <h3 class="font-semibold text-lg mb-3 text-blue-700">
                        Automation
                    </h3>
                    <p class="text-gray-600 text-sm">
                        Automate email scanning and threat identification using integrated APIs 
                        and background processing.
                    </p>
                </div>

                <div class="bg-white shadow rounded-lg p-6 text-center">
                    <h3 class="font-semibold text-lg mb-3 text-blue-700">
                        User Awareness
                    </h3>
                    <p class="text-gray-600 text-sm">
                        Provide clear detection results and reporting tools to improve user 
                        understanding of phishing threats.
                    </p>
                </div>
            </div>
        </div>

        <!-- Technology Stack -->
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-10">
                Technology Stack
            </h2>

            <div class="grid md:grid-cols-4 gap-6 text-center">
                <div class="bg-white shadow rounded-lg p-6">
                    <p class="font-semibold text-blue-700">Laravel</p>
                    <p class="text-sm text-gray-600 mt-2">Web Application Framework</p>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <p class="font-semibold text-blue-700">Flask</p>
                    <p class="text-sm text-gray-600 mt-2">Machine Learning API</p>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <p class="font-semibold text-blue-700">Random Forest</p>
                    <p class="text-sm text-gray-600 mt-2">Phishing Classification Model</p>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <p class="font-semibold text-blue-700">MySQL & Queue</p>
                    <p class="text-sm text-gray-600 mt-2">Data & Background Processing</p>
                </div>
            </div>
        </div>

    </div>
@endsection