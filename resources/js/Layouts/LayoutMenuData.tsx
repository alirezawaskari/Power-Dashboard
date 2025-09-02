import React from "react";
import { usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

const Navdata = () => {
    // Get current URL to determine which menu to show
    const { props } = usePage();
    const currentUrl = window.location.pathname;
    const { t } = useTranslation();

    // User menu items (for all users)
    const userMenuItems: any = [
        {
            id: "dashboard",
            label: t('Dashboard'),
            icon: "ri-dashboard-2-line",
            link: "/dashboard",
        },
        {
            id: "devices",
            label: t('Devices'),
            icon: "ri-device-line",
            link: "/dashboard/devices",
        },
        {
            id: "alerts",
            label: t('Alerts'),
            icon: "ri-notification-2-line",
            link: "/dashboard/alerts",
        },
        {
            id: "support",
            label: t('Support'),
            icon: "ri-customer-service-2-line",
            link: "/dashboard/support",
            subItems: [
                {
                    id: "support-tickets",
                    label: t('Tickets'),
                    link: "/dashboard/support",
                },
                {
                    id: "support-chat",
                    label: t('Ticket Chat'),
                    link: "/dashboard/support/ticket-chat",
                }
            ]
        },
        {
            id: "documents",
            label: t('Documents'),
            icon: "ri-file-text-line",
            link: "/dashboard/documents",
        },
        {
            id: "billing",
            label: t('Billing'),
            icon: "ri-bill-line",
            link: "/dashboard/billing",
        }
    ];

    // Admin menu items (for admin and owner roles)
    const adminMenuItems: any = [
        {
            id: "admin-dashboard",
            label: t('Admin Dashboard'),
            icon: "ri-settings-3-line",
            link: "/admin",
        },
        {
            id: "users",
            label: t('Users'),
            icon: "ri-user-line",
            link: "/admin/users",
            subItems: [
                {
                    id: "users-list",
                    label: t('User List'),
                    link: "/admin/users",
                },
                {
                    id: "user-management",
                    label: t('User Management'),
                    link: "/admin/users/management",
                }
            ]
        },
        {
            id: "analytics",
            label: t('Analytics'),
            icon: "ri-bar-chart-line",
            link: "/admin/analytics",
            subItems: [
                {
                    id: "analytics-overview",
                    label: t('Analytics Overview'),
                    link: "/admin/analytics",
                },
                {
                    id: "power-consumption",
                    label: t('Power Consumption'),
                    link: "/admin/analytics/power-consumption",
                },
                {
                    id: "energy-efficiency",
                    label: t('Energy Efficiency'),
                    link: "/admin/analytics/energy-efficiency",
                }
            ]
        },
        {
            id: "config",
            label: t('Configuration'),
            icon: "ri-settings-4-line",
            link: "/admin/config",
            subItems: [
                {
                    id: "config-overview",
                    label: t('Configuration Overview'),
                    link: "/admin/config",
                },
                {
                    id: "system-settings",
                    label: t('System Settings'),
                    link: "/admin/config/system-settings",
                }
            ]
        },
        {
            id: "monitoring",
            label: t('Monitoring'),
            icon: "ri-eye-line",
            link: "/admin/monitoring",
            subItems: [
                {
                    id: "monitoring-overview",
                    label: t('Monitoring Overview'),
                    link: "/admin/monitoring",
                },
                {
                    id: "device-status",
                    label: t('Device Status'),
                    link: "/admin/monitoring/device-status",
                },
                {
                    id: "alert-logs",
                    label: t('Alert Logs'),
                    link: "/admin/monitoring/alert-logs",
                }
            ]
        },
        {
            id: "admin-documents",
            label: t('Documents'),
            icon: "ri-file-list-line",
            link: "/admin/documents",
        }
    ];

    // Determine which menu to show based on current URL
    let menuItems: any = [];

    if (currentUrl.startsWith('/admin')) {
        // Show admin menu when on admin pages
        menuItems = [...adminMenuItems];
    } else {
        // Show user menu when on user pages
        menuItems = [...userMenuItems];
    }

    return <React.Fragment>{menuItems}</React.Fragment>;
};

export default Navdata;