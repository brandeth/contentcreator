# Flashcard Design System Export

This folder contains the portable design system pieces for a Laravel, Vue, and Tailwind project.

## What To Copy

Copy `design-system.css` into the Laravel project, for example:

```text
resources/css/design-system.css
```

Then import it from the main CSS entry file:

```css
@import "tailwindcss";
@import "./design-system.css";
```

Keep Tailwind as the first import. `design-system.css` is intentionally only the portable theme, utility, and base layer.

Copy Vue component examples from `vue-components/` into:

```text
resources/js/components/design/
```

## Font Setup

The current app uses Poppins with these weights:

- `400`
- `500`
- `600`
- `700`

In Laravel/Vue, load Poppins in your app layout, then set the CSS variable on the root element:

```html
<html style="--font-poppins: 'Poppins';">
```

If you use a normal stylesheet instead, set it globally:

```css
:root {
  --font-poppins: "Poppins";
}
```

## Color Tokens

Accent colors:

| Token | Class | Hex |
| --- | --- | --- |
| Yellow 500 | `bg-brand-yellow-500` | `#f8cb46` |
| Blue 400 | `bg-brand-blue-400` | `#92adeb` |
| Blue 600 | `bg-brand-blue-600` | `#5072c7` |
| Teal 400 | `bg-brand-teal-400` | `#47d9c9` |
| Pink 400 | `bg-brand-pink-400` | `#fc8ae5` |
| Pink 500 | `bg-brand-pink-500` | `#f073a3` |
| Pink 700 | `bg-brand-pink-700` | `#e11966` |

Neutral colors:

| Token | Class | Hex |
| --- | --- | --- |
| Neutral 0 | `bg-brand-neutral-0` | `#ffffff` |
| Neutral 100 | `bg-brand-neutral-100` | `#f7f3f0` |
| Neutral 600 | `text-brand-neutral-600` | `#6d5b4d` |
| Neutral 900 | `text-brand-neutral-900` | `#2e1401` |

## Typography

Use text presets instead of one-off font utilities.

| Class | Use | Specs |
| --- | --- | --- |
| `text-preset-1` | Main page prompt or major heading | Poppins 700, 24px mobile, 32px tablet, 40px desktop |
| `text-preset-2` | Section heading | Poppins 600, 24px |
| `text-preset-3` | Card title | Poppins 600, 20px |
| `text-preset-4` | Button text and field labels | Poppins 600, 16px |
| `text-preset-4-regular` | Regular 16px copy | Poppins 400, 16px |
| `text-preset-5` | Supporting UI text | Poppins 500, 14px |
| `text-preset-5-regular` | Body/helper text | Poppins 400, 14px |
| `text-preset-6` | Compact labels | Poppins 500, 12px |

## Component Rules

Buttons use full rounded pills, strong borders, and offset shadows. Primary actions use `bg-brand-yellow-500`. Secondary actions use white with the same raised shadow.

Inputs and textareas use:

```text
rounded-md border border-brand-neutral-900 bg-brand-neutral-0
hover:shadow-[2px_2px_0_0_var(--color-brand-neutral-900)]
focus:border-brand-blue-600 focus:shadow-[2px_2px_0_0_var(--color-brand-blue-600)]
```

Validation states use `brand-pink-700` for border, shadow, and message text.

Stat cards use white content panels with a colored accent panel on the right. Keep the dark border and heavier right/bottom edge.

Tabs are built from base button variants inside a rounded tablist container. Active tabs use yellow; inactive tabs use white.

## Assets

The original React components reference:

```text
public/assets/alert.svg
public/assets/check.svg
```

Copy these into the Laravel public assets folder or replace those masks/background images in the Vue components.

## Tailwind Version

This export assumes Tailwind CSS v4 because the tokens are defined with `@theme inline`. For Tailwind v3, convert these values into `tailwind.config.js` theme extensions instead.
