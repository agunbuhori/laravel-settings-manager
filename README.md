# 📦 Settings Manager for Laravel

A simple and flexible **settings manager package** for Laravel.  
It helps you store, retrieve, and manage application settings in the database — with support for:

- ✅ Key/value settings  
- ✅ Typed values (`string`, `integer`, `float`, `boolean`, `array`)  
- ✅ Dot notation for nested arrays  
- ✅ Cache support for performance  
- ✅ Bag & group support (multi-tenant, multi-context)  
- ✅ Bulk get (`getMany()`)  
- ✅ REST API endpoints for settings management  

---

## 🚀 Installation

Require the package via Composer:

```
composer require agunbuhori/settings-manager
```

Publish the config file:

```
php artisan vendor:publish --tag=settings-manager
```

Run the migrations:

```
php artisan migrate
```

---

## ⚙️ Configuration

File: `config/settings-manager.php`

```php
return [
    'enable_cache'     => true,
    'cache_expiration' => 86400, // 1 day in seconds
    'enable_api'       => true,
];
```

---

## 🛠 Usage

### 1. Basic set & get

```php
settings()->set('site_name', 'My App');
$name = settings()->get('site_name'); // "My App"
```

---

### 2. Arrays & dot notation

```php
settings()->set('app.theme.color', 'blue');
settings()->set('app.theme.layout', 'grid');

$color = settings()->get('app.theme.color'); // "blue"
```

---

### 3. Typed values

```php
settings()->set('max_users', 100);        // integer
settings()->set('pi_value', 3.14);        // float
settings()->set('is_active', true);       // boolean
settings()->set('allowed_ips', ['1.1.1.1', '8.8.8.8']); // array
```

---

### 4. Multiple settings at once

```php
$data = settings()->getMany(['site_name', 'is_active', 'max_users']);

/*
[
    "site_name" => "My App",
    "is_active" => true,
    "max_users" => 100
]
*/
```

---

### 5. Bag-specific settings (multi-tenant)

```php
// Bag #1
settings()->bag(1)->set('currency', 'USD');

// Bag #2
settings()->bag(2)->set('currency', 'EUR');

// Retrieve per bag
settings()->bag(1)->get('currency'); // USD
settings()->bag(2)->get('currency'); // EUR
```

---

### 6. General settings (no bag)

```php
settings()->general()->set('timezone', 'UTC');
settings()->general()->get('timezone'); // UTC
```

---

## 🌐 API Endpoints

If `enable_api` is set to `true`, the following routes are auto-loaded:

```
GET     /settings?per_page=10&keys=site_name,is_active
GET     /settings/{key}
POST    /settings/{key}   (or PUT/PATCH)
DELETE  /settings/{key}
```

### Example: Fetch a setting

```
GET /settings/site_name
→ { "value": "My App" }
```

### Example: Update a setting

```
POST /settings/site_name
{
  "value": "New Name"
}
→ { "message": "Setting updated successfully", "data": "New Name" }
```

### Example: Delete a setting

```
DELETE /settings/site_name
→ { "message": "Setting deleted successfully", "data": null }
```

---

## 🔑 Middleware Support

All API routes are wrapped with `SettingsManagerMiddleware`.  
This allows you to pass **bag** and **group** via query string:

```
GET /settings?bag=1&group=users
```

That way, your API can handle multiple contexts (multi-tenant, multi-organization, etc.).

---

## 🧩 Helper Function

You can call the global helper:

```php
settings()->set('foo', 'bar');
$value = settings()->get('foo'); // "bar"
```

---

## 📂 Project Structure (important files)

```
app/
config/
    settings-manager.php
routes/
    api.php   // Settings API routes
database/
    migrations/
        create_settings_table.php
src/
    Controllers/SettingController.php
    Middlewares/SettingsManagerMiddleware.php
    Models/Setting.php
    SettingsManager.php
    SettingsBagManager.php
```

---

## ✅ Summary

- Store any type of settings (`string`, `int`, `float`, `bool`, `array`)  
- Use dot notation for arrays (`app.theme.color`)  
- Switch between **general**, **bag**, and **group** easily  
- Cache for faster performance  
- Full REST API included out of the box  

---
