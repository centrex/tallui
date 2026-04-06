# UI Components

---

### Alert

Dismissible alert banner with automatic icon selection per type.

```blade
<x-tallui-alert type="success" title="Saved!" dismissible>
    Your changes have been saved successfully.
</x-tallui-alert>

<x-tallui-alert type="error" title="Failed">
    Could not process your request. Please try again.
</x-tallui-alert>

<x-tallui-alert type="warning">
    Your subscription expires in 3 days.
</x-tallui-alert>

<x-tallui-alert type="info" icon="o-bell" :dismissible="false">
    New version available.
</x-tallui-alert>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `type` | `string` | `'info'` | `info` \| `success` \| `warning` \| `error` |
| `title` | `?string` | `null` | Bold heading line |
| `icon` | `?string` | auto | Override the icon (Heroicon name) |
| `dismissible` | `bool` | `false` | Show a close button |

---

### Avatar

User avatar with image, initials fallback, and presence indicator.

```blade
{{-- Image --}}
<x-tallui-avatar src="/storage/avatars/alice.jpg" alt="Alice" size="lg" :online="true" />

{{-- Initials with colour --}}
<x-tallui-avatar initials="BM" color="bg-primary text-primary-content" size="md" />

{{-- Placeholder icon --}}
<x-tallui-avatar size="sm" shape="square" />

{{-- With notification badge --}}
<x-tallui-avatar src="/img/user.jpg" badge="3" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `src` | `?string` | `null` | Image URL |
| `alt` | `string` | `''` | Image alt text |
| `initials` | `?string` | `null` | Up to 2 characters shown when no image |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `shape` | `string` | `'circle'` | `circle` \| `square` \| `rounded` |
| `color` | `string` | `'bg-neutral text-neutral-content'` | Background + text colour when using initials |
| `online` | `bool` | `false` | Green online ring |
| `offline` | `bool` | `false` | Grey offline ring |
| `badge` | `?string` | `null` | Notification count badge |

---

### Breadcrumb

```blade
<x-tallui-breadcrumb :items="[
    ['label' => 'Home',     'url' => '/',          'icon' => 'o-home'],
    ['label' => 'Users',    'url' => '/users'],
    ['label' => 'Edit User'],
]" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `items` | `array` | `[]` | Each item: `label`, optional `url`, optional `icon` |

---

### Loading

```blade
{{-- Inline spinner --}}
<x-tallui-loading />

{{-- Sized and coloured --}}
<x-tallui-loading variant="dots" size="lg" color="text-primary" />

{{-- Inside a button --}}
<button class="btn btn-primary" wire:loading.attr="disabled">
    <x-tallui-loading variant="spinner" size="xs" wire:loading />
    Save
</button>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `variant` | `string` | `'spinner'` | `spinner` \| `dots` \| `ring` \| `ball` \| `bars` \| `infinity` |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `color` | `?string` | `null` | Tailwind text colour, e.g. `text-primary` |

---

### Progress

```blade
<x-tallui-progress :value="65" color="primary" :show-label="true" label="Upload" />

<x-tallui-progress :value="3" :max="10" color="success" size="xs" />

<x-tallui-progress :value="0" color="warning" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `value` | `int\|float` | `0` | Current value |
| `max` | `int\|float` | `100` | Maximum value |
| `color` | `string` | `'primary'` | DaisyUI color |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `showLabel` | `bool` | `false` | Show percentage on the right |
| `label` | `?string` | `null` | Text label on the left |

---

### Rating

```blade
{{-- Interactive --}}
<x-tallui-rating name="review_score" :value="3" :max="5" color="warning" />

{{-- Read-only display --}}
<x-tallui-rating name="avg" :value="4" :readonly="true" size="sm" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `'rating'` | Input name for form submission |
| `max` | `int` | `5` | Number of stars |
| `value` | `int\|float` | `0` | Current value |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `color` | `string` | `'warning'` | DaisyUI color |
| `readonly` | `bool` | `false` | Disable interaction |

---

### Stat

```blade
<div class="stats shadow">
    <x-tallui-stat
        title="Total Users"
        value="14,290"
        desc="↗ 12% from last month"
        icon="o-users"
        icon-color="text-primary"
        change="+12%"
        change-type="up"
    />

    <x-tallui-stat title="Revenue" value="$84k" desc="Jan–Dec 2024" change="-3%" change-type="down" />
</div>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | `string` | `''` | Stat label |
| `value` | `string` | `''` | Main stat number/text |
| `desc` | `?string` | `null` | Description line |
| `icon` | `?string` | `null` | Heroicon name shown as figure |
| `iconColor` | `string` | `'text-primary'` | Icon colour class |
| `change` | `?string` | `null` | Change text e.g. `+12%` |
| `changeType` | `string` | `'neutral'` | `up` (green) \| `down` (red) \| `neutral` |

---

### Steps

```blade
<x-tallui-steps
    :steps="[
        ['label' => 'Cart',     'color' => 'primary'],
        ['label' => 'Shipping', 'color' => 'primary'],
        ['label' => 'Payment'],
        ['label' => 'Confirm'],
    ]"
    :current="2"
/>

{{-- Vertical --}}
<x-tallui-steps :steps="[...]" :current="1" :vertical="true" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `steps` | `array` | `[]` | Each step: `label`, optional `color` |
| `current` | `int` | `1` | 1-based index of the active step (steps ≤ current are filled) |
| `vertical` | `bool` | `false` | Vertical layout |

---

### Timeline

```blade
<x-tallui-timeline :items="[
    [
        'time'        => 'Jan 2024',
        'title'       => 'Project Kickoff',
        'description' => 'Team assembled and roadmap finalized.',
        'color'       => 'primary',
        'icon'        => 'o-flag',
    ],
    [
        'time'        => 'Mar 2024',
        'title'       => 'Beta Launch',
        'description' => 'Public beta released to 500 testers.',
        'color'       => 'success',
    ],
    [
        'time'        => 'Jun 2024',
        'title'       => 'v1.0 Released',
        'color'       => 'accent',
    ],
]" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `items` | `array` | `[]` | Each item: `time`, `title`, optional `description`, `color`, `icon` |
| `compact` | `bool` | `false` | Compact layout (no start column) |

---

### Error

Renders the error message for a field from the `$errors` bag, or a custom message.

```blade
{{-- Auto-reads from $errors bag --}}
<x-tallui-error field="email" />

{{-- Custom message --}}
<x-tallui-error message="This value is not allowed." />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `field` | `string` | `''` | Field name to look up in `$errors` |
| `message` | `?string` | `null` | Override message (takes priority over field) |

---

### File Upload

```blade
<x-tallui-file
    name="avatar"
    label="Profile Photo"
    accept="image/*"
    helper="JPG, PNG or GIF up to 2MB"
    :error="$errors->first('avatar')"
/>

{{-- Multiple files --}}
<x-tallui-file
    name="documents"
    label="Attachments"
    :multiple="true"
    accept=".pdf,.docx"
    size="sm"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `''` | Input name |
| `label` | `?string` | `null` | Field label |
| `accept` | `?string` | `null` | MIME type or extension filter |
| `multiple` | `bool` | `false` | Allow selecting multiple files |
| `bordered` | `bool` | `true` | Show border |
| `size` | `string` | config `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `error` | `?string` | `null` | Validation error |

---

### Range Slider

```blade
<x-tallui-range
    name="volume"
    label="Volume"
    :min="0"
    :max="100"
    :step="5"
    :value="40"
    color="primary"
    :show-value="true"
    :show-steps="true"
/>

{{-- Wired to Livewire --}}
<x-tallui-range name="budget" label="Budget" :min="0" :max="10000" :step="500"
    :value="$budget" :show-value="true" wire:model.live="budget" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `min` | `int` | `0` | Minimum value |
| `max` | `int` | `100` | Maximum value |
| `step` | `int` | `1` | Step increment |
| `value` | `?int` | `null` | Initial value |
| `color` | `string` | `'primary'` | DaisyUI color |
| `showValue` | `bool` | `false` | Display current value in the label |
| `showSteps` | `bool` | `false` | Show min/max tick marks below |

---

### Text Editor

A rich text editor using `contenteditable` — no external dependencies. Outputs raw HTML stored in a hidden input.

```blade
<x-tallui-text-editor
    name="body"
    label="Article Body"
    placeholder="Start writing..."
    :value="old('body', $article->body ?? '')"
    :error="$errors->first('body')"
    :rows="10"
/>
```

Toolbar provides: **Bold**, *Italic*, <u>Underline</u>, ~~Strikethrough~~, ordered/unordered lists, heading format selector (H2–H4, Paragraph, Quote), Undo/Redo.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `''` | Hidden input name for form submission |
| `value` | `?string` | `null` | Initial HTML content |
| `placeholder` | `?string` | `null` | Placeholder shown when empty |
| `rows` | `int` | `8` | Approximate height in lines |
| `error` | `?string` | `null` | Validation error |

---

### Tags Input

```blade
<x-tallui-tags
    name="skills"
    label="Skills"
    :value="old('skills', ['PHP', 'Laravel'])"
    placeholder="Add skill…"
    color="primary"
    :error="$errors->first('skills')"
/>
```

Press **Enter** or **,** to add a tag. Click **×** to remove.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `'tags'` | Submitted as `name[]` array |
| `value` | `array` | `[]` | Initial tags |
| `placeholder` | `?string` | `'Add tag…'` | Input placeholder |
| `color` | `string` | `'primary'` | Badge colour |

---

### Accordion

```blade
<x-tallui-accordion title="What is TallUI?" variant="arrow">
    TallUI is a Laravel UI component library built on Livewire, Tailwind CSS, and DaisyUI.
</x-tallui-accordion>

<x-tallui-accordion title="Pricing" variant="plus" :open="true" color="bg-base-200">
    All plans include unlimited components.
</x-tallui-accordion>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `'accordion'` | Radio group name (use the same name to make them exclusive) |
| `title` | `string` | `''` | Panel header text |
| `open` | `bool` | `false` | Start expanded |
| `variant` | `string` | `'arrow'` | `arrow` \| `plus` |
| `color` | `string` | `''` | Background class e.g. `bg-base-200` |

---

### Calendar

```blade
{{-- Standalone --}}
<x-tallui-calendar
    :selected="today()->toDateString()"
    :events="[
        ['date' => '2024-06-10', 'label' => 'Team meeting',  'color' => 'primary'],
        ['date' => '2024-06-15', 'label' => 'Product launch', 'color' => 'success'],
    ]"
/>

{{-- Wired to Livewire --}}
<x-tallui-calendar wire="selectedDate" :selected="$selectedDate" />
```

Listens for `calendar-select` browser event: `{ date: 'YYYY-MM-DD' }`.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `selected` | `?string` | `null` | Pre-selected date (YYYY-MM-DD) |
| `month` | `?string` | current | Starting month (YYYY-MM) |
| `events` | `array` | `[]` | Each event: `date`, `label`, optional `color` |
| `selectable` | `bool` | `true` | Enable date selection |
| `wire` | `?string` | `null` | Livewire property to sync selected date into |

---

### Carousel

```blade
<x-tallui-carousel
    :items="[
        ['src' => '/img/slide1.jpg', 'alt' => 'Mountains', 'caption' => 'View from the top'],
        ['src' => '/img/slide2.jpg', 'alt' => 'Ocean'],
        ['src' => '/img/slide3.jpg', 'alt' => 'Forest'],
    ]"
    :arrows="true"
    :indicators="true"
    :autoplay="true"
    :interval="4000"
    height="h-80"
    fit="cover"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `items` | `array` | `[]` | Each item: `src`, optional `alt`, `caption` |
| `arrows` | `bool` | `true` | Show prev/next arrows |
| `indicators` | `bool` | `true` | Show dot indicators |
| `autoplay` | `bool` | `false` | Auto-advance slides |
| `interval` | `int` | `3000` | Auto-advance delay in ms |
| `height` | `string` | `'h-64'` | Tailwind height class |
| `fit` | `string` | `'cover'` | `cover` \| `contain` |

---

### Drawer

```blade
<x-tallui-drawer id="settings-drawer" width="w-96">
    {{-- Main page content --}}
    <button onclick="document.getElementById('settings-drawer').checked = true" class="btn">
        Open Settings
    </button>

    {{-- Sidebar content --}}
    <x-slot:sidebar>
        <div class="p-6">
            <h2 class="text-lg font-bold mb-4">Settings</h2>
            {{-- settings form --}}
        </div>
    </x-slot:sidebar>
</x-tallui-drawer>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `id` | `string` | `'drawer'` | Checkbox input ID (use to open: `document.getElementById(id).checked = true`) |
| `side` | `string` | `'left'` | `left` \| `right` |
| `width` | `string` | `'w-80'` | Tailwind width class |
| `open` | `bool` | `false` | Start open |

---

### Group

Wraps children in a DaisyUI `join` so they share borders and appear as a single unit.

```blade
{{-- Button group --}}
<x-tallui-group>
    <button class="btn join-item">Left</button>
    <button class="btn btn-active join-item">Center</button>
    <button class="btn join-item">Right</button>
</x-tallui-group>

{{-- Vertical input + button --}}
<x-tallui-group :vertical="true">
    <input class="input input-bordered join-item" placeholder="Email" />
    <button class="btn btn-primary join-item">Subscribe</button>
</x-tallui-group>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `vertical` | `bool` | `false` | Stack children vertically |

---

### Image Gallery

```blade
<x-tallui-image-gallery
    :images="[
        ['src' => '/img/a.jpg', 'alt' => 'Sunset',   'caption' => 'Golden hour'],
        ['src' => '/img/b.jpg', 'alt' => 'Mountains'],
        ['src' => '/img/c.jpg', 'alt' => 'City',     'caption' => 'Downtown at night'],
    ]"
    :columns="3"
    :lightbox="true"
    height="h-48"
    fit="cover"
/>
```

Arrow keys navigate in the lightbox; Escape closes it.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `images` | `array` | `[]` | Each image: `src`, optional `alt`, `caption` |
| `columns` | `int` | `3` | Grid columns |
| `lightbox` | `bool` | `true` | Enable click-to-zoom lightbox |
| `height` | `string` | `'h-48'` | Grid thumbnail height |
| `fit` | `string` | `'cover'` | `cover` \| `contain` |

---

### Menu

```blade
<x-tallui-menu :items="[
    ['label' => 'Dashboard', 'url' => '/',        'icon' => 'o-home'],
    ['label' => 'Users',     'url' => '/users',   'icon' => 'o-users', 'active' => true, 'badge' => '12'],
    ['label' => 'Settings',  'icon' => 'o-cog-6-tooth', 'children' => [
        ['label' => 'Profile',   'url' => '/settings/profile'],
        ['label' => 'Security',  'url' => '/settings/security'],
    ]],
]" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `items` | `array` | `[]` | Each item: `label`, `url`?, `icon`?, `active`?, `badge`?, `children`? |
| `horizontal` | `bool` | `false` | Horizontal layout |
| `size` | `string` | `''` | `xs` \| `sm` \| `lg` |
| `color` | `string` | `''` | Background class |

---

### Popover

```blade
<x-tallui-popover position="top" trigger="hover">
    <x-slot:trigger_slot>
        <button class="btn btn-sm">Hover me</button>
    </x-slot:trigger_slot>
    <x-slot:content>
        <p class="font-semibold">Quick tip</p>
        <p class="text-base-content/70 text-xs mt-1">This appears on hover.</p>
    </x-slot:content>
</x-tallui-popover>

{{-- Click trigger --}}
<x-tallui-popover position="bottom" trigger="click">
    <x-slot:trigger_slot><button class="btn">Click</button></x-slot:trigger_slot>
    <x-slot:content>Popover content here.</x-slot:content>
</x-tallui-popover>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `position` | `string` | `'top'` | `top` \| `bottom` \| `left` \| `right` |
| `trigger` | `string` | `'hover'` | `hover` \| `click` |

---

### Spotlight

A ⌘K command-palette overlay with fuzzy search.

```blade
{{-- Trigger button (place in navbar) --}}
<x-tallui-spotlight
    placeholder="Search pages, users, actions..."
    :items="[
        ['label' => 'Dashboard',     'url' => '/',        'group' => 'Pages',   'description' => 'Overview & charts'],
        ['label' => 'Users',         'url' => '/users',   'group' => 'Pages'],
        ['label' => 'New User',      'url' => '/users/create', 'group' => 'Actions'],
        ['label' => 'Documentation', 'url' => '/docs',    'group' => 'Links'],
    ]"
>
    Search
</x-tallui-spotlight>
```

Opens with **⌘K** / **Ctrl+K** or the `/` key (when not in an input). Arrow keys navigate, Enter follows the link.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `placeholder` | `string` | `'Search anything...'` | Input placeholder |
| `shortcut` | `string` | `'/'` | Single-key shortcut to open (set `''` to disable) |
| `items` | `array` | `[]` | Each item: `label`, `url`?, `description`?, `group`?, `icon`? |

---

### Swap

Toggle between two states with a rotate or flip animation (DaisyUI `swap`).

```blade
{{-- Sound toggle --}}
<x-tallui-swap effect="rotate">
    <x-slot:on>🔊</x-slot:on>
    <x-slot:off>🔇</x-slot:off>
</x-tallui-swap>

{{-- Hamburger / close --}}
<x-tallui-swap effect="rotate">
    <x-slot:on>
        <svg class="w-6 h-6" ...><!-- X icon --></svg>
    </x-slot:on>
    <x-slot:off>
        <svg class="w-6 h-6" ...><!-- Hamburger icon --></svg>
    </x-slot:off>
</x-tallui-swap>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `active` | `bool` | `false` | Start in the "on" state |
| `effect` | `string` | `'rotate'` | `rotate` \| `flip` |

---

### Tabs

```blade
<x-tallui-tab
    :tabs="[
        ['id' => 'overview',  'label' => 'Overview',  'icon' => 'o-home'],
        ['id' => 'activity',  'label' => 'Activity'],
        ['id' => 'settings',  'label' => 'Settings',  'icon' => 'o-cog-6-tooth'],
    ]"
    active="overview"
    variant="bordered"
>
    <x-slot:overview>
        <p>Overview panel content.</p>
    </x-slot:overview>

    <x-slot:activity>
        <p>Activity feed goes here.</p>
    </x-slot:activity>

    <x-slot:settings>
        <p>Settings form goes here.</p>
    </x-slot:settings>
</x-tallui-tab>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `tabs` | `array` | `[]` | Each tab: `id`, `label`, optional `icon` |
| `active` | `string` | first tab id | Initially active tab id |
| `variant` | `string` | `'bordered'` | `bordered` \| `lifted` \| `boxed` |
| `size` | `string` | `''` | `xs` \| `sm` \| `lg` |

---

### Theme Toggle

```blade
{{-- Icon-only toggle --}}
<x-tallui-theme-toggle />

{{-- With label --}}
<x-tallui-theme-toggle :with-label="true" light="light" dark="dark" />
```

Persists the selected theme to `localStorage` and sets the `data-theme` attribute on `<html>`.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `light` | `string` | `'light'` | DaisyUI light theme name |
| `dark` | `string` | `'dark'` | DaisyUI dark theme name |
| `withLabel` | `bool` | `false` | Show current theme name next to the icon |

---

← [Back to docs](../README.md)
