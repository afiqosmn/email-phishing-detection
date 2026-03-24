<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('generateBreadcrumbs')) {
    function generateBreadcrumbs()
    {
        $routeName = Route::currentRouteName();
        $breadcrumbs = [];

        switch ($routeName) {
            case 'dashboard':
                $breadcrumbs = [
                    ['label' => 'Dashboard', 'url' => route('dashboard')],
                ];
                break;

            case 'mailbox':
                $breadcrumbs = [
                    ['label' => 'Mailbox', 'url' => route('mailbox')]
                ];
                break;

            case 'userprofile':
                $breadcrumbs = [
                    ['label' => 'User Profile', 'url' => route('userprofile')]
                ];
                break;
            
            case 'history':
                $breadcrumbs = [
                    ['label' => 'History', 'url' => route('history')]
                ];
                break;

            case 'discussion':
                $breadcrumbs = [
                    ['label' => 'Discussion', 'url' => route('discussion')]
                ];
                break;
            
            case 'help':
                $breadcrumbs = [
                    ['label' => 'Help', 'url' => route('help')]
                ];
                break;
                
            case 'layout.collapsed':
                $breadcrumbs = [
                    ['label' => 'Home', 'url' => route('dashboard')],
                    ['label' => 'Layout', 'url' => null],
                    ['label' => 'Collapsed Sidebar', 'url' => null],
                ];
                break;

            default:
                $breadcrumbs = [
                    ['label' => 'Home', 'url' => route('dashboard')],
                ];
                break;
        }

        return $breadcrumbs;
    }
}
