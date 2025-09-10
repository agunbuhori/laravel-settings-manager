# Settings Manager for Laravel

A simple yet powerful Laravel package to manage application settings with support for:

- ✅ Multiple bags (per tenant/user/project scope)  
- ✅ Groups (sub-scope inside a bag, e.g. `profile`, `preferences`, `billing`)  
- ✅ Dot-notation keys for nested array values  
- ✅ Automatic type casting (`string`, `integer`, `float`, `boolean`, `array`)  
- ✅ Cache support for fast retrieval  
- ✅ REST API endpoints out of the box  

---

## Installation

```bash
composer require agunbuhori/settings-manager
```

Publish config and migration:

```bash
php artisan vendor:publish --tag=settings-manager
php artisan migrate
```

---

## Usage

### 1. Basic usage (general bag)

```php
// Save a setting
settings()->set('site_name', 'My App');

// Retrieve it
$name = settings()->get('site_name'); // "My App"
```

### 2. Using bags (per tenant/user)

```php
// Save a setting for bag 10 (e.g. tenant_id = 10)
settings()->bag(10)->set('timezone', 'Asia/Jakarta');

// Retrieve it
$tz = settings()->bag(10)->get('timezone'); // "Asia/Jakarta"
```

### 3. Using groups (sub-scope inside a bag)

```php
// Store profile settings for user 50
settings()->bag(50, 'profile')->set('language', 'id');

// Store preferences for the same user
settings()->bag(50, 'preferences')->set('theme', 'dark');

// Read them back
$lang  = settings()->bag(50, 'profile')->get('language');    // "id"
$theme = settings()->bag(50, 'preferences')->get('theme');   // "dark"
```

### 4. Dot-notation keys (nested values)

```php
// Save nested data
settings()->set('notifications.email.enabled', true);

// Get nested data
$enabled = settings()->get('notifications.email.enabled'); // true
```

### 5. Bulk retrieval

```php
// Save
settings()->set('app.name', 'MyApp');
settings()->set('app.version', '1.0');

// Fetch many at once
$values = settings()->getMany(['app.name', 'app.version']);
// [
//     "app.name"    => "MyApp",
//     "app.version" => "1.0"
// ]
```

### 6. Deleting a setting

```php
// This removes the record
settings()->set('app.name', null);
```

---

## REST API Endpoints

The package auto-registers routes:

```
GET    /api/settings           → list settings
GET    /api/settings/{key}     → get a single setting
POST   /api/settings/{key}     → create/update a setting
PUT    /api/settings/{key}     → update a setting
PATCH  /api/settings/{key}     → update a setting
DELETE /api/settings/{key}     → delete a setting
```

### Examples

#### Create/Update

```
POST /api/settings/site_name
{ "value": "My App" }
```

#### Read

```
GET /api/settings/site_name
```

#### With bag & group

```
POST /api/settings/language?bag=50&group=profile
{ "value": "id" }
```

---

## Config Options

In `config/settings-manager.php`:

```php
return [
    'enable_cache'      => true,
    'cache_expiration'  => 86400, // 1 day
    'enable_api'        => true,
];
```

---

## Summary

- Use `settings()->set($key, $value)` and `settings()->get($key)` for general/global settings.  
- Use `settings()->bag($bag)->set($key, $value)` for tenant/user-specific settings.  
- Use `settings()->bag($bag, $group)->set($key, $value)` for more granular grouping.  
- Dot notation allows nested array values.  
- API endpoints are available if you enable them in the config.  

---
