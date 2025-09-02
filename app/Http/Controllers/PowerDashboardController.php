<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PowerDashboardController extends Controller
{
    public function userDashboard()
    {
        return Inertia::render('user/Dashboard/index');
    }

    public function userDevices()
    {
        return Inertia::render('user/Devices/index');
    }

    public function userAlerts()
    {
        return Inertia::render('user/Alerts/index');
    }

    public function userSupport()
    {
        return Inertia::render('user/Support/index');
    }

    public function userDocuments()
    {
        return Inertia::render('user/Documents/index');
    }

    public function userBilling()
    {
        return Inertia::render('user/Billing/index');
    }

    // User Support Subpages
    public function userSupportTicketChat()
    {
        return Inertia::render('user/Support/TicketsDetails/TicketChat');
    }

    public function adminDashboard()
    {
        return Inertia::render('admin/Dashboard/index');
    }

    public function adminUsers()
    {
        return Inertia::render('admin/Users/index');
    }

    public function adminAnalytics()
    {
        return Inertia::render('admin/Analytics/index');
    }

    public function adminConfig()
    {
        return Inertia::render('admin/Configuration/index');
    }

    public function adminMonitoring()
    {
        return Inertia::render('admin/Monitoring/index');
    }

    public function adminDocuments()
    {
        return Inertia::render('admin/Documents/index');
    }

    // Admin Monitoring Subpages
    public function adminMonitoringDeviceStatus()
    {
        return Inertia::render('admin/Monitoring/DeviceStatus');
    }

    public function adminMonitoringAlertLogs()
    {
        return Inertia::render('admin/Monitoring/AlertLogs');
    }

    // Admin Analytics Subpages
    public function adminAnalyticsPowerConsumption()
    {
        return Inertia::render('admin/Analytics/PowerConsumption');
    }

    public function adminAnalyticsEnergyEfficiency()
    {
        return Inertia::render('admin/Analytics/EnergyEfficiency');
    }

    // Admin Configuration Subpages
    public function adminConfigSystemSettings()
    {
        return Inertia::render('admin/Configuration/SystemSettings');
    }

    // Admin Users Subpages
    public function adminUsersUserManagement()
    {
        return Inertia::render('admin/Users/UserManagement');
    }
}
