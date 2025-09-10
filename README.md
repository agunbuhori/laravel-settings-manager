# Laravel Settings Manager

A powerful and flexible **settings manager** for Laravel that allows you to store, retrieve, and manage application or user-specific settings with **bags**, **groups**, **dot notation**, **type casting**, and **cache support**.

---

## 📦 Installation

Install via Composer:

```bash
composer require agunbuhori/settings-manager
```

---

## ⚙️ Setup

Run the migration to create the `settings` table:

```bash
php artisan migrate
```

### Example Migration

```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('bag')->nullable()->index();
    $table->string('group')->nullable()->index();
    $table->string('key')->index();
    $table->enum('type', ['string', 'integer', 'float', 'boolean', 'array']);
    $table->text('value')->nullable();
    $table->timestamps();
});
```

### Configuration

You can publish and customize the config:

```bash
php artisan vendor:publish --tag=settings-manager
```

Example `config/settings-manager.php`:

```php
return [
    'enable_cache' => true,
];
```

---

## 🚀 Usage

### 1. Basic Set & Get

```php
settings()->set('site_name', 'My Awesome App');

$name = settings()->get('site_name'); // "My Awesome App"
```

With default:

```php
$theme = settings()->get('theme', 'light'); // "light" if not set
```

---

### 2. Supported Types

The manager automatically casts values by type:

| PHP Type   | Stored as DB `type` | Example Value                       |
|------------|---------------------|-------------------------------------|
| `string`   | string              | `"Hello World"`                     |
| `integer`  | integer             | `42`                                |
| `float`    | float               | `3.14`                              |
| `boolean`  | boolean             | `true / false`                      |
| `array`    | array (JSON)        | `['host' => 'smtp.test.com']`       |

```php
settings()->set('max_users', 100);
settings()->set('pi', 3.14);
settings()->set('is_active', true);
settings()->set('options', ['a' => 1]);
```

---

### 3. Dot Notation for Arrays

```php
settings()->set('mail.driver', 'smtp');
settings()->set('mail.host', 'smtp.mailtrap.io');

$driver = settings()->get('mail.driver'); // "smtp"
```

---

### 4. Bags (Tenant / User Isolation)

A **bag** isolates settings by ID, useful for multi-tenant or user-based configurations.

```php
// Tenant 1 settings
settings()->bag(1)->set('timezone', 'Asia/Jakarta');

// Retrieve
$tz = settings()->bag(1)->get('timezone'); // "Asia/Jakarta"
```

### 5. Groups (Logical Grouping within a Bag)

Groups allow you to organize settings under a bag.

```php
// Bag 1, group "notifications"
settings()->bag(1, 'notifications')->set('email.enabled', true);

// Bag 1, group "appearance"
settings()->bag(1, 'appearance')->set('theme', 'dark');

// Retrieve
$enabled = settings()->bag(1, 'notifications')->get('email.enabled'); // true
```

> ⚠️ Groups require a bag. If you set a group without a bag, an exception will be thrown.

---

### 6. General (Global Settings)

```php
settings()->general()->set('app.locale', 'en');

$locale = settings()->general()->get('app.locale');
```

---

### 7. Caching

- Tagged cache is automatically applied with tags:  
  `['settings-manager', {bag}, {group}]`
- Each value is cached for **1 day (86400 seconds)**.  
- Disable caching via config:

```php
'settings-manager.enable_cache' => false,
```

#### Clear cache manually

```php
settings()->clearCache();
```

---

## 🔌 REST API Endpoints

This package includes a controller with ready-to-use routes.

```php
GET    /settings            // List settings (?keys=site_name,theme&per_page=20)
GET    /settings/{key}      // Show a single setting
POST   /settings/{key}      // Update setting
PUT    /settings/{key}      // Update setting
PATCH  /settings/{key}      // Update setting
```

Example:

```bash
curl -X POST http://your-app.test/settings/site_name \
     -H "Content-Type: application/json" \
     -d '{"value": "My Updated App"}'
```

---

## 📚 Example: Multi-Tenant Usage

```php
// Store theme per tenant
settings()->bag(auth()->user()->tenant_id, 'appearance')->set('theme', 'dark');

// Later retrieve
$theme = settings()->bag(auth()->user()->tenant_id, 'appearance')->get('theme');
```

---

## ✅ Features

```txt
- Store and retrieve settings easily
- Supports multiple data types (string, int, float, bool, array)
- Dot notation for nested arrays
- Bags for tenant/user-specific isolation
- Groups for logical categorization within a bag
- Configurable caching (with tags per bag+group)
- REST API endpoints included
- Global `settings()` helper available
```

---

## 📝 License

This package is open-source software licensed under the MIT license.
