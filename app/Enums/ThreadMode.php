<?php declare(strict_types=1);
namespace App\Enums;
enum ThreadMode: string
{
    case ClientOnly = 'client_only';
    case SnapshotJson = 'snapshot_json';
}
