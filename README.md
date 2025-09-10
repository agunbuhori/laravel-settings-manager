# Laravel Settings Manager

A simple yet powerful way to store and manage application settings in Laravel.  
Supports multiple bags (like tenants or users), nested keys, typed values, caching, and even a ready-to-use REST API.

---

## ✨ Features

- 🗄️ Store and retrieve settings directly from the database  
- 🏷️ Support for **bags** and **groups** (e.g. per tenant or per user)  
- 🧩 Use **dot notation** for nested settings (`mail.driver`, `mail.host`)  
- 🔢 Automatic type handling: `string`, `integer`, `float`, `boolean`, `array`  
- ⚡ Built-in **caching** for faster access  
- 🌐 Optional **REST API endpoints** to manage settings via HTTP  
- 🛠️ Easy integration with Laravel service container and helper functions  

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require agunbuhori/settings-manager
```

Run migrations to create the `settings` table:

```bash
php artisan migrate
```

---

## ⚙️ Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=settings-manager
```

`config/settings-manager.php`:

```php
return [
    'enable_cache' => true,
    'cache_expiration' => 86400, // 1 day
    'enable_api' => true,
];
```

---

## 🔑 Basic Usage

### Set and get settings

```php
// Save a setting
settings()->set('site_name', 'My Awesome App');

// Get a setting
$name = settings()->get('site_name', 'Default Name');
```

### Delete a setting

```php
settings()->set('site_name', null);
```

### Nested (dot notation)

```php
settings()->set('mail.driver', 'smtp');
settings()->set('mail.host', 'smtp.example.com');

$driver = settings()->get('mail.driver'); // smtp
```

### Bags & groups

Useful for **multi-tenant** or **user-based** settings.

```php
// Bag 1, group "profile"
settings()->bag(1, 'profile')->set('language', 'id');

// Switch to general (no bag)
settings()->general()->set('timezone', 'UTC');
```

---

## 🌐 REST API

When `enable_api` is `true`, the package registers API routes:

```
GET    /settings           # List settings
GET    /settings/{key}     # Get single setting
PUT    /settings/{key}     # Update setting
PATCH  /settings/{key}     # Update setting
POST   /settings/{key}     # Update setting
DELETE /settings/{key}     # Delete setting
```

### Examples

#### List settings
```
GET /api/settings?per_page=20&keys=site_name,theme&bag=1&group=profile
```

#### Get setting
```
GET /api/settings/site_name
```

#### Update setting
```
PUT /api/settings/site_name
{
  "value": "My New App"
}
```

#### Delete setting
```
DELETE /api/settings/site_name
```

---

## 🧩 Value Types

Values are automatically cast based on type:

- `"string"`
- `"integer"`
- `"float"`
- `"boolean"`
- `"array"` (stored as JSON)

Example:

```php
settings()->set('maintenance_mode', true);
settings()->set('max_users', 100);
settings()->set('features', ['chat' => true, 'billing' => false]);
```

---

## ⚡ Caching

- Settings are cached for faster access
- Configurable via `settings-manager.php`:
  - `enable_cache` → true/false
  - `cache_expiration` → in seconds (default 1 day)

---

## 🎯 Why use this package?

- Replace `.env` configs that need runtime updates  
- Manage tenant or user-specific settings easily  
- Access settings with simple `settings()->get()` calls  
- Expose settings management over a secure API if needed  

---

## 📄 License

MIT
