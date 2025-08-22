<?php declare(strict_types=1);

namespace App\Enums;

enum SettingScope: string
{
    case Global = 'global';
    case User = 'user';
    case Device = 'device';
}
