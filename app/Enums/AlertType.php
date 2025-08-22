<?php declare(strict_types=1);

namespace App\Enums;

enum AlertType: string
{
    case PowerThreshold = 'power_threshold';
    case VoltageThreshold = 'voltage_threshold';
    case CurrentThreshold = 'current_threshold';
    case DeviceOffline = 'device_offline';
    case DeviceError = 'device_error';
    case TemperatureThreshold = 'temperature_threshold';
    case PowerFactorThreshold = 'power_factor_threshold';
    case FrequencyThreshold = 'frequency_threshold';
    case CustomThreshold = 'custom_threshold';
}
