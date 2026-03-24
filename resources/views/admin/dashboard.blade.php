@extends('layouts.dashtemp')

@section('page-header')
<h1 class="text-3xl font-bold">🛡️ Admin Dashboard</h1>
<p class="text-gray-500 mt-1">System Administration & Monitoring</p>
@endsection

@section('content')
<div class="w-full">
    <!-- Tab Navigation -->
    <div class="bg-white border-b border-gray-200 rounded-t-lg shadow-sm">
        <nav class="flex space-x-8 px-6 overflow-x-auto" role="tablist">
            <button class="tab-btn active py-4 px-1 border-b-2 border-blue-600 font-medium text-blue-600 whitespace-nowrap"
                    data-tab="overview" role="tab">
                📊 Overview
            </button>
            <button class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 whitespace-nowrap"
                    data-tab="users" role="tab">
                👥 Users Management
            </button>
            <button class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 whitespace-nowrap"
                    data-tab="reports" role="tab">
                📋 Reports
            </button>
            <button class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 whitespace-nowrap"
                    data-tab="analytics" role="tab">
                📈 Analytics
            </button>
            <button class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 whitespace-nowrap"
                    data-tab="health" role="tab">
                🏥 System Health
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="bg-gray-50">
        <!-- OVERVIEW TAB -->
        <div id="overview" class="tab-content active p-6">
            @include('admin.tabs.overview')
        </div>

        <!-- USERS TAB -->
        <div id="users" class="tab-content hidden p-6">
            @include('admin.tabs.users')
        </div>

        <!-- REPORTS TAB -->
        <div id="reports" class="tab-content hidden p-6">
            @include('admin.tabs.reports')
        </div>

        <!-- ANALYTICS TAB -->
        <div id="analytics" class="tab-content hidden p-6">
            @include('admin.tabs.analytics')
        </div>

        <!-- HEALTH TAB -->
        <div id="health" class="tab-content hidden p-6">
            @include('admin.tabs.health')
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabName = btn.dataset.tab;

            // Deactivate all tabs
            tabBtns.forEach(b => {
                b.classList.remove('active', 'border-blue-600', 'text-blue-600');
                b.classList.add('border-transparent', 'text-gray-600');
            });

            // Deactivate all contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });

            // Activate selected tab
            btn.classList.add('active', 'border-blue-600', 'text-blue-600');
            btn.classList.remove('border-transparent', 'text-gray-600');

            // Activate selected content
            document.getElementById(tabName).classList.remove('hidden');
            document.getElementById(tabName).classList.add('active');
        });
    });
</script>
@endsection
