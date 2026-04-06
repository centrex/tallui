# Installation

```bash
composer require centrex/tallui
```

Then run the install command to publish the config:

```bash
php artisan tallui:install
```

Publish views too (to customise templates):

```bash
php artisan tallui:install --views
```

Force-overwrite previously published files:

```bash
php artisan tallui:install --config --views --force
```

List all registered component tags for your current prefix:

```bash
php artisan tallui:list
```

The package works with **Tailwind CSS 3 (DaisyUI 4)** and **Tailwind CSS 4 (DaisyUI 5)**. Follow the setup for whichever version your project uses.

---

### Tailwind CSS 4 + DaisyUI 5

Tailwind CSS 4 uses a **CSS-first** configuration — no `tailwind.config.js` needed.

Install packages:

```bash
npm install tailwindcss @tailwindcss/vite daisyui
```

Configure Vite (`vite.config.js`):

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true }),
        tailwindcss(),
    ],
})
```

Configure your CSS (`resources/css/app.css`):

```css
@import "tailwindcss";
@plugin "daisyui";

/* Scan TallUI package views so their classes are not purged */
@source "../../vendor/centrex/tallui/resources/views";
```

---

### Tailwind CSS 3 + DaisyUI 4

Install packages:

```bash
npm install tailwindcss postcss autoprefixer daisyui
```

Configure Tailwind (`tailwind.config.js`) — add the package views to the `content` array so classes in TallUI's templates are not purged:

```js
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        // Include TallUI package views
        './vendor/centrex/tallui/resources/views/**/*.blade.php',
    ],
    plugins: [
        require('daisyui'),
    ],
    daisyui: {
        themes: ['light', 'dark'],
    },
}
```

Configure PostCSS (`postcss.config.js`):

```js
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
}
```

Configure your CSS (`resources/css/app.css`):

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

---

### Layout

Make sure `@livewireStyles` and `@livewireScripts` are present in your layout:

```html
<head>
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
```

---

← [Back to docs](../README.md)
