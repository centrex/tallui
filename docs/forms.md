# Form Components

All form components:

- Are pure Blade components — no Livewire overhead per field
- Work with `wire:model`, `wire:model.live`, `x-model`, and plain HTML `name` attributes
- Accept arbitrary HTML attributes via attribute merging
- Use [DaisyUI 5](https://daisyui.com) component classes

### FormGroup

Wraps any input with a label and error/helper message — useful for consistent form layouts without repetition.

```blade
<x-tallui-form-group label="Full Name" for="name" :error="$errors->first('name')" required>
    <x-tallui-input name="name" wire:model="form.name" />
</x-tallui-form-group>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `label` | `?string` | `null` | Label text |
| `for` | `?string` | `null` | `<label for>` value |
| `helper` | `?string` | `null` | Helper text below the input |
| `error` | `?string` | `null` | Error message (replaces helper) |
| `required` | `bool` | `false` | Shows a red asterisk |

---

### Input

```blade
{{-- Basic --}}
<x-tallui-input name="email" label="Email" type="email" wire:model="email" />

{{-- With left icon and validation error --}}
<x-tallui-input
    name="email"
    label="Email address"
    type="email"
    placeholder="you@example.com"
    icon="o-envelope"
    :error="$errors->first('email')"
    required
    wire:model.live="email"
/>

{{-- Password with right icon --}}
<x-tallui-input name="password" label="Password" type="password" icon-right="o-lock-closed" />

{{-- Large size --}}
<x-tallui-input name="search" placeholder="Search..." size="lg" icon="o-magnifying-glass" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `''` | Input `name` and `id` |
| `type` | `string` | `'text'` | HTML input type |
| `label` | `?string` | `null` | Field label |
| `placeholder` | `?string` | `null` | Placeholder text |
| `helper` | `?string` | `null` | Helper text |
| `error` | `?string` | `null` | Error message |
| `icon` | `?string` | `null` | Heroicon name for left icon |
| `iconRight` | `?string` | `null` | Heroicon name for right icon |
| `required` | `bool` | `false` | Required attribute |
| `disabled` | `bool` | `false` | Disabled state |
| `size` | `string` | config `'md'` | `'xs'` \| `'sm'` \| `'md'` \| `'lg'` |

---

### Textarea

```blade
<x-tallui-textarea
    name="bio"
    label="Biography"
    placeholder="Tell us about yourself..."
    :rows="5"
    :error="$errors->first('bio')"
    wire:model="form.bio"
/>
```

Same props as `Input` plus `rows` (default `4`).

---

### Select

```blade
{{-- Static options --}}
<x-tallui-select
    name="role"
    label="Role"
    placeholder="Select a role..."
    :options="['admin' => 'Administrator', 'editor' => 'Editor', 'viewer' => 'Viewer']"
    :error="$errors->first('role')"
    wire:model="form.role"
/>

{{-- Options from a collection --}}
<x-tallui-select
    name="country_id"
    label="Country"
    :options="$countries->pluck('name', 'id')"
    wire:model="form.country_id"
/>

{{-- Inline <option> slots --}}
<x-tallui-select name="status" label="Status" wire:model="status">
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
</x-tallui-select>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `options` | `array` | `[]` | Associative `[value => label]` array |
| `placeholder` | `?string` | `null` | Disabled first option as placeholder |
| `searchable` | `bool` | `false` | Enable async search mode |
| `searchUrl` | `?string` | auto | Override the search endpoint URL |

---

### Async Searchable Select

For large datasets, enable `searchable` mode. The component renders an Alpine.js combobox that queries the package's allowlisted search endpoint.

**Step 1 — Register the model in config:**

```php
// config/tallui.php
'forms' => [
    'searchable_models' => [
        'country' => [
            'model'  => \App\Models\Country::class,
            'label'  => 'name',    // column shown in the dropdown
            'value'  => 'id',      // value stored in the hidden input
            'scope'  => 'active',  // optional Eloquent scope method
        ],
        'user' => [
            'model' => \App\Models\User::class,
            'label' => 'name',
            'value' => 'id',
        ],
    ],
],
```

**Step 2 — Use the component:**

```blade
<x-tallui-select
    name="country"
    label="Country"
    placeholder="Search countries..."
    :searchable="true"
    wire:model="form.country_id"
/>
```

> Only models listed in `searchable_models` can be queried. Any other name returns `403`.

---

### Checkbox

```blade
{{-- Basic --}}
<x-tallui-checkbox name="agree" label="I agree to the terms" wire:model="agree" />

{{-- With color and error --}}
<x-tallui-checkbox
    name="newsletter"
    label="Subscribe to newsletter"
    color="primary"
    :error="$errors->first('newsletter')"
    wire:model="newsletter"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `color` | `string` | `''` | DaisyUI color: `primary`, `secondary`, `accent`, `success`, `warning`, `error` |
| `checked` | `bool` | `false` | Default checked state |

---

### Radio

```blade
<div class="flex flex-col gap-2">
    <x-tallui-radio name="plan" value="free"       label="Free"       wire:model="plan" />
    <x-tallui-radio name="plan" value="pro"        label="Pro"        wire:model="plan" />
    <x-tallui-radio name="plan" value="enterprise" label="Enterprise" wire:model="plan" />
</div>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `value` | `string` | `''` | Value submitted when this radio is selected |
| `color` | `string` | `''` | DaisyUI color |

---

### Toggle

```blade
<x-tallui-toggle name="notifications" label="Email notifications" color="success" wire:model="settings.notifications" />
<x-tallui-toggle name="dark_mode" label="Dark mode" color="primary" :checked="true" wire:model="settings.dark_mode" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `color` | `string` | `'primary'` | DaisyUI color |
| `checked` | `bool` | `false` | Default state |

---

### DatePicker

```blade
{{-- Date only --}}
<x-tallui-date-picker name="dob" label="Date of Birth" wire:model="form.dob" />

{{-- Date + time --}}
<x-tallui-date-picker
    name="scheduled_at"
    label="Schedule At"
    :with-time="true"
    min="{{ now()->toDateString() }}"
    wire:model="form.scheduled_at"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `withTime` | `bool` | `false` | Renders `datetime-local` instead of `date` |
| `min` | `?string` | `null` | Minimum date (`YYYY-MM-DD`) |
| `max` | `?string` | `null` | Maximum date |

---

← [Back to docs](../README.md)
