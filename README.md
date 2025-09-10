# Laravel Settings Manager

A simple and flexible **settings manager** for Laravel that allows you to store, retrieve, and manage application or user-specific settings with **bags**, **dot notation**, **type casting**, and **cache support**.

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
    $table->string('key')->index();
    $table->string('type', 20)->default('string');
    $table->text('value')->nullable();
    $table->timestamps();

    $table->unique(['bag', 'key']);
});
```

---

## 🚀 Usage

### 1. Basic Set & Get

```php
// Set a string
settings()->set('site_name', 'My Awesome App');

// Get it back
$name = settings()->get('site_name'); // "My Awesome App"

// With default fallback
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
settings()->set('max_users', 100);        // integer
settings()->set('pi', 3.14);              // float
settings()->set('is_active', true);       // boolean
settings()->set('options', ['a' => 1]);   // array
```

---

### 3. Dot Notation for Arrays

```php
// Save nested array keys
settings()->set('mail.driver', 'smtp');
settings()->set('mail.host', 'smtp.mailtrap.io');

// Retrieve
$driver = settings()->get('mail.driver'); // "smtp"
```

---

### 4. Using Bags (Group Settings)

A **bag** is like a namespace for your settings, useful for multi-tenant or user-based settings.

```php
// Tenant-specific settings
settings()->bag(1)->set('timezone', 'Asia/Jakarta');
$tz = settings()->bag(1)->get('timezone'); // "Asia/Jakarta"

// General (global) settings
settings()->general()->set('app.locale', 'en');
```

---

### 5. Caching

- Settings are cached automatically for **1 day**.
- Bags use **tagged cache** for isolation per tenant/user.

---

## 🔌 REST API Endpoints

This package ships with `SettingController` and ready-to-use routes.

### Routes

```php
GET    /settings            // List settings (?keys=site_name,theme&per_page=20)
GET    /settings/{key}      // Show a single setting
POST   /settings/{key}      // Update setting
PUT    /settings/{key}      // Update setting
PATCH  /settings/{key}      // Update setting
```

### Examples

#### List settings

```bash
curl http://your-app.test/settings?keys=site_name,theme&per_page=20
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
        // Save user preference
        settings()->bag(auth()->id())->set('profile.color', 'blue');

        // Later...
        $color = settings()->bag(auth()->id())->get('profile.color');

        return response()->json(['color' => $color]);
    }
}
```

---

## ✅ Features

```txt
- Simple API for managing settings
- Supports multiple data types (string, int, float, bool, array)
- Dot notation for nested arrays
- Bag support (multi-tenant / user-specific)
- Cached for performance
- REST API endpoints included
- Helper function `settings()` globally available
```

---

## 📝 License

```txt
This package is open-source software licensed under the MIT license.
```
