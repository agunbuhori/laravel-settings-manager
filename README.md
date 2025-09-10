# Settings Manager for Laravel — GROUPS Explained (Full README)

This README focuses on **how `bag` and `group` work** and gives clear, copy/paste examples so you can use groups correctly.

> **Short summary**
>
> - **Bag** = scope (tenant, user, organization).  
> - **Group** = sub-scope inside a bag to logically separate settings (e.g. `profile`, `preferences`, `billing`).  
> - Groups are always used *together with a bag* in the package controller (API). Programmatically you can call `settings()->bag($bag, $group)`.

---

## ✅ Key concepts

- **Bag** (integer or `null`): primary scope. Examples: tenant id, user id, account id.  
- **Group** (string or `null`): secondary scope inside a bag. Examples: `profile`, `preferences`, `payment`.  
- **Key**: the setting name stored in DB (`key` column). Can use dot notation for nested values (`preferences.color` -> stores as `key = "preferences"` and nested array `["color" => "..."]`).  
- **Storage uniqueness**: rows are unique per `(bag, group, key)` (recommended migration uses a unique index on these columns).

---

## Migration (recommended)

Make sure your `settings` table includes `bag` and `group`:

```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('bag')->nullable()->index();
    $table->string('group')->nullable()->index();
    $table->string('key')->index();
    $table->string('type', 20)->default('string');
    $table->text('value')->nullable();
    $table->timestamps();

    $table->unique(['bag', 'group', 'key']);
});
```

---

## Programmatic examples (clear)

### 1) Save a group-scoped setting (per-user profile)

```php
// Store 'language' under bag = 123 and group = "profile"
settings()->bag(123, 'profile')->set('language', 'id');

// Retrieve the same:
$lang = settings()->bag(123, 'profile')->get('language'); // 'id'
```

### 2) Multiple groups inside the same bag

```php
// Bag 123 has two groups: 'profile' and 'preferences'
settings()->bag(123, 'profile')->set('language', 'id');
settings()->bag(123, 'preferences')->set('notifications.email', true);

// They do NOT conflict:
settings()->bag(123, 'profile')->get('language');           // 'id'
settings()->bag(123, 'preferences')->get('notifications.email'); // true
```

### 3) Dot notation inside groups

```php
// This saves key = "preferences" (DB), with nested value: { "theme": { "color": "blue" } }
settings()->bag(123, 'preferences')->set('preferences.theme.color', 'blue');

// Read nested value:
$color = settings()->bag(123, 'preferences')->get('preferences.theme.color'); // 'blue'
```

### 4) Deleting a group setting

```php
// Delete the 'language' setting inside bag=123, group='profile'
settings()->bag(123, 'profile')->set('language', null);
```

### 5) getMany with bag/group

```php
// Save some values
settings()->bag(123, 'profile')->set('language', 'id');
settings()->bag(123, 'profile')->set('timezone', 'Asia/Jakarta');

// Bulk fetch (still needs same bag/group in context)
$values = settings()->bag(123, 'profile')->getMany(['language', 'timezone']);
// $values -> ['language' => 'id', 'timezone' => 'Asia/Jakarta']
```

---

## REST API usage (group via query params)

Your package controller looks for `bag` (and `group`) on the request. **Important** — the controller sets bag only if `bag` is present in the request. If you want to use `group`, pass both `bag` and `group`.

### Examples

#### List settings (filtered by keys, bag & group)

```
GET /api/settings?per_page=20&keys=language,timezone&bag=123&group=profile
```

- The controller will set the request context to `bag = 123` and `group = profile`.
- `Setting::` queries will be scoped to that bag/group (via the package's bag manager/global scope).

#### Get single setting

```
GET /api/settings/language?bag=123&group=profile
```

Response:

```json
{ "value": "id" }
```

#### Update a setting

```
POST /api/settings/language?bag=123&group=profile
Content-Type: application/json
{ "value": "en" }
```

Response:

```json
{ "message": "Setting updated successfully", "data": "en" }
```

#### Delete a setting

```
DELETE /api/settings/language?bag=123&group=profile
```

Response:

```json
{ "message": "Setting deleted successfully", "data": null }
```

---

## Important details & gotchas

- **Group is meaningful only with a bag in the controller**: the controller constructor sets bag & group only when the `bag` parameter exists. If you call the API with `?group=profile` but no `bag`, the controller **won't** set the group — so include `bag` in the query.  
- **Programmatic usage is fully flexible**: `settings()->bag($bag, $group)` works even if called from code (not via HTTP).  
- **Dot-notation behavior**: `settings()->set('preferences.color', 'blue')` will actually store a DB row with `key = 'preferences'` and a nested `value` containing `['color' => 'blue']`. This is true inside groups as well.  
- **Unique index**: the recommended migration has `unique(['bag','group','key'])` to avoid duplicate rows.  
- **Cache isolation**: the package uses cache tags (per bag) and keys that include bag/group so settings from different bags/groups do not conflict.  
- **Deleting a setting**: calling `set($key, null)` removes the DB row and clears its cache.

---

## Troubleshooting

- **Group not applied in API**: ensure you pass `bag` in query params (example: `?bag=123&group=profile`). The controller only sets group if `bag` is present.  
- **I see wrong values**: confirm you're using the same `(bag, group, key)` when writing and reading.  
- **Arrays not merging**: when using dot notation, ensure the DB value is stored as an array type (`type = 'array'`), otherwise merging could fail. The package normally handles this automatically, but if you manually edited DB rows, check `type` and `value` JSON.  
- **Cache issues**: clear cache or ensure `enable_cache` is true/false in `config/settings-manager.php` as you expect.

---

## Quick checklist

- ✅ Add `group` column in migration  
- ✅ Use `settings()->bag($bag, $group)` in code to set/read group-scoped values  
- ✅ For API calls include `?bag=...&group=...` so controller sets both correctly  
- ✅ Use dot notation for nested values: `key = "preferences"` + nested JSON for `"value"`  
- ✅ `set($key, null)` deletes the row and clears cache

---

## Example: Real-world scenario

You run a multi-tenant app. Tenants have user preferences and billing settings:

```php
// Tenant 50: user profile settings
settings()->bag(50, 'profile')->set('language', 'id');
settings()->bag(50, 'profile')->set('theme.color', 'blue');

// Tenant 50: billing settings (same bag, different group)
settings()->bag(50, 'billing')->set('currency', 'USD');
settings()->bag(50, 'billing')->set('tax.rate', 10);

// Reading:
settings()->bag(50, 'profile')->get('language');       // 'id'
settings()->bag(50, 'billing')->get('currency');       // 'USD'
settings()->bag(50, 'profile')->get('theme.color');    // 'blue'
```

---

If you'd like, I can:
- Add a short **code snippet** for the middleware (how to accept `bag` and `group` from headers or other sources), or  
- Provide a **unit-test example** showing saving/reading across bags and groups, or  
- Generate a nearly complete `README.md` (one copy box) including all of the above plus migration + example controller usage.

Which one should I produce next?
