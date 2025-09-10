# Laravel Settings Manager

A simple and flexible **settings manager** for Laravel that allows you to store, retrieve, and manage application or user-specific settings with **bags**, **dot notation**, and **cache support**.

---

## 📦 Installation

Install via Composer:

```bash
composer require agunbuhori/settings-manager
```

---

## ⚙️ Setup

The package auto-registers a singleton binding of `SettingsManagerInterface`.

You can access settings via:

- The global `settings()` helper
- Dependency injection of `SettingsManagerInterface`

---

## 🚀 Usage

### 1. Basic Set & Get

```php
// Set a value
settings()->set('site_name', 'My Awesome App');

// Get a value
$name = settings()->get('site_name'); // "My Awesome App"

// With default fallback
$theme = settings()->get('theme', 'light'); // returns "light" if not set
```

---

### 2. Using Dot Notation for Arrays

```php
// Save nested value
settings()->set('mail.driver', 'smtp');
settings()->set('mail.host', 'smtp.mailtrap.io');

// Retrieve nested value
$driver = settings()->get('mail.driver'); // "smtp"
```

---

### 3. Using Bags (Group Settings)

A **bag** is like a namespace for your settings, useful for multi-tenant or user-based settings.

```php
// Set bag to 1 (e.g., Tenant ID = 1)
settings()->bag(1)->set('timezone', 'Asia/Jakarta');

// Get from bag
$tz = settings()->bag(1)->get('timezone'); // "Asia/Jakarta"

// General settings (no bag)
settings()->general()->set('app.locale', 'en');
```

---

### 4. Caching

- Settings are automatically cached for **1 day**.
- Bags use **tagged cache** to keep values isolated per bag.

---

## 🔌 REST API Endpoints

This package comes with a controller (`SettingController`) and routes.

### Routes

```php
GET    /settings            // List settings (with optional ?keys=key1,key2&per_page=20)
GET    /settings/{key}      // Show single setting
POST   /settings/{key}      // Update setting
PUT    /settings/{key}      // Update setting
PATCH  /settings/{key}      // Update setting
```

### Examples

#### List settings

```bash
curl http://your-app.test/settings?per_page=20&keys=site_name,theme
```

#### Get single setting

```bash
curl http://your-app.test/settings/site_name
```

#### Update setting

```bash
curl -X POST http://your-app.test/settings/site_name \
     -H "Content-Type: application/json" \
     -d '{"value": "My Updated App"}'
```

---

## 📚 Example: Controller Usage

```php
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function update()
    {
        // Save user-specific preference
        settings()->bag(auth()->id())->set('profile.color', 'blue');

        // Retrieve later
        $color = settings()->bag(auth()->id())->get('profile.color');

        return response()->json(['color' => $color]);
    }
}
```

---

## ✅ Features

```txt
- Simple API for managing application/user settings
- Dot notation for nested array values
- Bag support (multi-tenant / user-specific settings)
- Cached for performance
- REST API endpoints included
- Helper function `settings()` available globally
```

---

## 📝 License

```txt
This package is open-source software licensed under the MIT license.
```
