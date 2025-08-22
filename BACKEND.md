# Power Dashboard Backend Documentation

## Overview

Power Dashboard is a real-time IoT power monitoring and management platform designed for RCPSS-SUTech. It provides comprehensive monitoring, alerting, and analytics for electrical devices and power meters through a robust Laravel-based backend architecture.

## Table of Contents

- [System Architecture](#system-architecture)
- [Core Features](#core-features)
- [Data Models](#data-models)
- [API Endpoints](#api-endpoints)
- [Authentication & Security](#authentication--security)
- [Real-Time Communication](#real-time-communication)
- [Background Processing](#background-processing)
- [Data Storage Strategy](#data-storage-strategy)
- [Monitoring & Operations](#monitoring--operations)
- [Development Setup](#development-setup)

## System Architecture

### Technology Stack

**Backend Framework:**
- **Laravel 10** with PHP 8.1+
- **Laravel Passport** for OAuth2 authentication
- **Laravel Sanctum** for API tokens
- **Inertia.js** for SPA-like experience

**Data & Caching:**
- **PostgreSQL** for primary data storage
- **Redis** for caching and session storage
- **RabbitMQ** for message queuing

**Real-Time Communication:**
- **Pusher** for WebSocket connections
- **RabbitMQ** for reliable message delivery

**Security:**
- **Firebase JWT** for token verification
- **Laravel Passport** for OAuth2 implementation
- **Device-level authentication** with secret hashes

### Architecture Patterns

- **Event Sourcing** for complete audit trails
- **State Machine** for device lifecycle management
- **Repository Pattern** with service layers
- **Observer Pattern** for device state changes
- **Factory Pattern** for data generation

## Core Features

### 1. Multi-Tenant IoT Device Management

**Device Registration & Authentication:**
- Unique UUID for each device
- Secret hash-based authentication
- Device metadata (firmware, model, location, tags)
- Automatic secret rotation capabilities

**Device States:**
```php
enum DeviceStatus: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Maintenance = 'maintenance';
    case Decommissioned = 'decommissioned';
}
```

**Heartbeat Monitoring:**
- Automatic detection of device connectivity
- Configurable heartbeat intervals
- Scheduled offline detection via artisan commands

### 2. Real-Time Power Data Ingestion

**Data Flow:**
```
IoT Device → POST /api/ingest → Validation → Storage → Alert Check → Event Log
```

**Ingestion Process:**
1. **Device Authentication** via secret hash verification
2. **Data Validation** (current/voltage must be non-negative)
3. **Power Calculation** (P = I × V) with automatic computation
4. **Device Status Update** (marks device as online)
5. **Event Logging** for audit trail
6. **Alert Checking** for threshold violations

**API Endpoint:**
```http
POST /api/ingest
Headers: 
  X-Device-ID: {device_uuid}
  X-Device-Secret: {device_secret}
Body:
{
  "ts": "2024-01-01T12:00:00Z",
  "current": 5.2,
  "voltage": 230.0,
  "attributes": {
    "phase": "L1",
    "sample_ms": 1000
  }
}
```

### 3. Advanced Alerting System

**Alert Types:**
```php
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
```

**Alert Lifecycle:**
- **Triggered** → **Acknowledged** → **Resolved**
- **Escalation levels** for critical alerts
- **Notification delivery** via multiple channels

**Alert Management:**
- Real-time threshold monitoring
- Automatic alert creation and escalation
- Manual acknowledgment and resolution
- Historical alert tracking

### 4. Support System

**Ticket Management:**
- Issue tracking and resolution
- Priority-based ticket handling
- Assignment to support staff
- Status tracking (Open, In Progress, Resolved, Closed)

**Live Chat:**
- Real-time support communication
- Message persistence and history
- File attachment support
- Chat session management

**Device Snapshots:**
- Context preservation for troubleshooting
- JSON-based snapshot storage
- Version control for snapshots
- Size limits for performance

## Data Models

### Core Entities

**User Model:**
```php
class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role', 'last_login_at'
    ];
    
    protected $casts = [
        'role' => UserRole::class,
        'last_login_at' => 'immutable_datetime'
    ];
}
```

**Device Model:**
```php
class Device extends Model
{
    protected $fillable = [
        'uuid', 'user_id', 'name', 'slug', 'status',
        'secret_hash', 'firmware', 'model', 'location',
        'tags', 'metadata', 'last_seen_at'
    ];
    
    protected $casts = [
        'status' => DeviceStatus::class,
        'tags' => 'array',
        'metadata' => 'array'
    ];
}
```

**PowerRecord Model:**
```php
class PowerRecord extends Model
{
    protected $fillable = [
        'device_id', 'user_id', 'ts', 'current', 
        'voltage', 'power', 'sampling_ms', 'phase', 'flags'
    ];
    
    protected $casts = [
        'ts' => 'immutable_datetime',
        'current' => 'float',
        'voltage' => 'float',
        'power' => 'float'
    ];
}
```

**Alert Model:**
```php
class Alert extends Model
{
    protected $fillable = [
        'user_id', 'device_id', 'type', 'title', 'message',
        'threshold_value', 'current_value', 'status',
        'triggered_at', 'acknowledged_at', 'resolved_at',
        'escalation_level', 'notification_sent'
    ];
}
```

### Relationships

```php
// User relationships
User -> hasMany(Device)
User -> hasMany(Ticket, 'creator_id')
User -> hasMany(Ticket, 'assignee_id')

// Device relationships
Device -> belongsTo(User)
Device -> hasMany(PowerRecord)
Device -> hasOne(PowerRecord, 'latestRecord')
Device -> hasMany(Ticket)

// PowerRecord relationships
PowerRecord -> belongsTo(Device)
PowerRecord -> belongsTo(User)
```

## API Endpoints

### Device Management

**List Devices:**
```http
GET /api/devices
Authorization: Bearer {token}
Query Parameters:
  - status: online|offline|maintenance|decommissioned
  - tags: comma-separated tag list
  - user_id: filter by user
```

**Get Device Details:**
```http
GET /api/devices/{id}
Authorization: Bearer {token}
Query Parameters:
  - from: start timestamp
  - to: end timestamp
  - limit: max records (default 1000, max 5000)
```

### Data Ingestion

**Submit Telemetry:**
```http
POST /api/ingest
Headers:
  X-Device-ID: {device_uuid}
  X-Device-Secret: {device_secret}
Body:
{
  "ts": "2024-01-01T12:00:00Z",
  "current": 5.2,
  "voltage": 230.0,
  "attributes": {
    "phase": "L1",
    "sample_ms": 1000
  }
}
```

### Ticket Management

**List Tickets:**
```http
GET /api/tickets
Authorization: Bearer {token}
```

**Create Ticket:**
```http
POST /api/tickets
Authorization: Bearer {token}
```

**Update Ticket:**
```http
PUT /api/tickets/{id}
Authorization: Bearer {token}
```

**Send Chat Message:**
```http
POST /api/tickets/{id}/messages
Authorization: Bearer {token}
```

### Notifications

**List Notifications:**
```http
GET /api/notifications
Authorization: Bearer {token}
```

**Mark as Read:**
```http
PATCH /api/notifications/{id}/read
Authorization: Bearer {token}
```

## Authentication & Security

### Multi-Layer Authentication

**1. OAuth2 with Laravel Passport:**
- Web user authentication
- Scope-based access control
- Token management and refresh

**2. Device Authentication:**
```php
// Device authentication middleware
class DeviceAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $devId = $request->header('X-Device-ID');
        $secret = $request->header('X-Device-Secret');
        
        // Verify device exists and secret matches
        $device = Device::where('uuid', $devId)->first();
        if (!Hash::check($secret, $device->secret_hash)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }
        
        $request->attributes->set('device', $device);
        return $next($request);
    }
}
```

**3. JWT Verification:**
```php
class JwtVerifier
{
    public function verify(string $token): array
    {
        // Verify JWT signature using JWKS
        // Validate issuer and audience
        // Return decoded payload
    }
}
```

### Role-Based Access Control

**User Roles:**
```php
enum UserRole: string
{
    case Owner = 'owner';
    case Operator = 'operator';
    case Viewer = 'viewer';
    case Support = 'support';
}
```

**OAuth Scopes:**
```php
'role_scopes' => [
    'owner' => ['devices:read', 'devices:write', 'tickets:read', 'tickets:write', 'settings:read', 'settings:write'],
    'operator' => ['devices:read', 'devices:write', 'tickets:read', 'tickets:write', 'settings:read'],
    'viewer' => ['devices:read', 'tickets:read', 'settings:read'],
    'support' => ['tickets:read', 'tickets:write', 'devices:read'],
]
```

## Real-Time Communication

### Message Queuing (RabbitMQ)

**Exchange Configuration:**
```php
private const EXCHANGE_NOTIFICATIONS = 'notifications';
private const EXCHANGE_CHAT = 'chat';
private const QUEUE_NOTIFICATIONS = 'notifications_queue';
private const QUEUE_CHAT = 'chat_queue';
```

**Message Publishing:**
```php
public function publishNotification(array $data, string $routingKey): void
{
    $message = new AMQPMessage(
        json_encode($data),
        [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'content_type' => 'application/json',
            'timestamp' => time(),
        ]
    );
    
    $this->channel->basic_publish($message, self::EXCHANGE_NOTIFICATIONS, $routingKey);
}
```

### WebSocket Integration

**Pusher Configuration:**
- Real-time device status updates
- Live power consumption monitoring
- Instant alert notifications
- Support chat functionality

**Event Broadcasting:**
```php
// Broadcast device status changes
broadcast(new DeviceStatusChanged($device));

// Broadcast power alerts
broadcast(new PowerAlertTriggered($alert));

// Broadcast chat messages
broadcast(new ChatMessageSent($message));
```

## Background Processing

### Scheduled Commands

**Device Heartbeat Check:**
```bash
php artisan devices:check-heartbeats
```
- Checks device connectivity
- Marks offline devices
- Triggers offline alerts

**Telemetry Rollup:**
```bash
php artisan telemetry:rollup-5m
```
- Aggregates power records into 5-minute buckets
- Improves query performance
- Maintains historical data

**Chat Message Processing:**
```bash
php artisan chat:process
```
- Processes messages from RabbitMQ queue
- Handles WebSocket broadcasting
- Manages message delivery

**Log Pruning:**
```bash
php artisan logs:prune-archive
```
- Removes old event logs
- Maintains database performance
- Configurable retention policies

### Queue Jobs

**Offline Sweeper Job:**
```php
class OfflineSweeper
{
    public function __invoke(): void
    {
        $seconds = (int) env('OFFLINE_SLA_SECONDS', 120);
        
        // Mark devices as offline based on heartbeat
        Device::query()
            ->where('status', '!=', 'decommissioned')
            ->where('last_seen_at', '<', now()->subSeconds($seconds))
            ->update(['status' => 'offline']);
    }
}
```

## Data Storage Strategy

### Primary Data Storage

**Power Records Table:**
```sql
CREATE TABLE power_records (
    id BIGSERIAL PRIMARY KEY,
    device_id BIGINT REFERENCES devices(id),
    user_id BIGINT REFERENCES users(id),
    ts TIMESTAMPTZ NOT NULL,
    current DOUBLE PRECISION NOT NULL,
    voltage DOUBLE PRECISION NOT NULL,
    power DOUBLE PRECISION NOT NULL,
    sampling_ms INTEGER DEFAULT 1000,
    phase VARCHAR(10),
    flags JSONB,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Indexes for performance
CREATE INDEX idx_power_records_device_ts ON power_records(device_id, ts);
CREATE INDEX idx_power_records_user_ts ON power_records(user_id, ts);
```

**Telemetry Rollups Table:**
```sql
CREATE TABLE telemetry_rollups_5m (
    device_id BIGINT REFERENCES devices(id),
    bucket_start_ts TIMESTAMPTZ,
    min_power DOUBLE PRECISION,
    avg_power DOUBLE PRECISION,
    max_power DOUBLE PRECISION,
    count INTEGER,
    min_current DOUBLE PRECISION,
    avg_current DOUBLE PRECISION,
    max_current DOUBLE PRECISION,
    min_voltage DOUBLE PRECISION,
    avg_voltage DOUBLE PRECISION,
    max_voltage DOUBLE PRECISION,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    PRIMARY KEY (device_id, bucket_start_ts)
);
```

### Performance Optimizations

**Data Retention:**
- Raw telemetry: Configurable retention (default 7 days)
- Rollup data: Extended retention for historical analysis
- Event logs: Configurable retention based on compliance needs

**Query Optimization:**
- Time-range bounded queries for telemetry data
- Eager loading for related data
- Pagination for large result sets
- Indexed queries for fast retrieval

## Monitoring & Operations

### System Health Monitoring

**Device Status Tracking:**
- Real-time online/offline status
- Heartbeat monitoring
- Automatic offline detection
- Status change logging

**Performance Metrics:**
- API response times
- Database query performance
- Queue processing rates
- Memory and CPU usage

**Alert Management:**
- Threshold-based alerts
- Escalation procedures
- Notification delivery tracking
- Alert resolution workflows

### Operational Tools

**Artisan Commands:**
```bash
# Device management
php artisan devices:check-heartbeats
php artisan devices:list-offline

# Data management
php artisan telemetry:rollup-5m
php artisan logs:prune-archive

# System maintenance
php artisan queue:work
php artisan cache:clear
```

**Monitoring Dashboards:**
- Device status overview
- System performance metrics
- Alert summary and trends
- User activity monitoring

### Logging & Audit

**Event Logging:**
```php
enum EventType: string
{
    // Ingest events
    case IngestAccepted = 'ingest.accepted';
    case IngestRejectedAuth = 'ingest.rejected_auth';
    case IngestRejectedSchema = 'ingest.rejected_schema';
    
    // Device events
    case DeviceOnline = 'device.online';
    case DeviceOffline = 'device.offline';
    case DeviceMaintenanceOn = 'device.maintenance_on';
    
    // Security events
    case AuthFailedDevice = 'auth.failed_device';
    case PermissionDenied = 'permission.denied';
    
    // Alert events
    case AlertTriggered = 'alert.triggered';
    case AlertAcknowledged = 'alert.acknowledged';
    case AlertResolved = 'alert.resolved';
}
```

**Audit Trail:**
- Complete system event logging
- User action tracking
- Device interaction history
- Security event monitoring

## Development Setup

### Prerequisites

- PHP 8.1+
- Composer
- PostgreSQL 12+
- Redis 6+
- RabbitMQ 3.8+
- Node.js 16+ (for frontend)

### Installation

1. **Clone Repository:**
```bash
git clone <repository-url>
cd powerDashboard
```

2. **Install Dependencies:**
```bash
composer install
npm install
```

3. **Environment Configuration:**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Setup:**
```bash
php artisan migrate
php artisan db:seed
```

5. **Queue Configuration:**
```bash
# Start queue worker
php artisan queue:work

# Start scheduled tasks
php artisan schedule:work
```

### Configuration

**Environment Variables:**
```env
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=power_dashboard
DB_USERNAME=postgres
DB_PASSWORD=password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# RabbitMQ
RABBITMQ_HOST=localhost
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/

# Pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# OAuth
OAUTH_ISSUER_URL=https://your-oauth-provider.com
OAUTH_AUDIENCE=power-dashboard
OAUTH_JWKS_URI=https://your-oauth-provider.com/.well-known/jwks.json
```

### Testing

**Run Tests:**
```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature

# All tests
php artisan test
```

**Test Coverage:**
```bash
# Generate coverage report
php artisan test --coverage
```

## Business Value

This backend system provides:

1. **Real-time power monitoring** for electrical infrastructure
2. **Predictive maintenance** through device health tracking
3. **Energy efficiency optimization** via consumption analytics
4. **Operational intelligence** for power management decisions
5. **Compliance and audit** capabilities through comprehensive logging
6. **Scalable IoT management** for multiple devices and locations

## Technical Excellence

**Enterprise-Grade Features:**
- **Event Sourcing** for complete audit trails
- **State Machine** for device lifecycle management
- **Message Queuing** for reliable communication
- **Multi-tenant architecture** for scalability
- **Comprehensive security** with multiple auth layers
- **Real-time capabilities** for immediate response

This is a **production-ready, enterprise-grade IoT power monitoring platform** that can handle real-world electrical infrastructure monitoring with robust security, scalability, and operational intelligence capabilities.
