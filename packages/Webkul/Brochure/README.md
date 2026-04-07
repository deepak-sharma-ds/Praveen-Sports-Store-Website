# Webkul Brochure Module

A **Digital Brochure System** for Bagisto — lets your team publish product catalogs as interactive flipbook-style viewers that customers can browse directly on the storefront.

---

## Table of Contents

- [Features](#features)
- [Architecture Overview](#architecture-overview)
- [Installation](#installation)
- [Admin Usage](#admin-usage)
  - [Creating a Brochure](#creating-a-brochure)
  - [Editing a Brochure](#editing-a-brochure)
  - [Deleting a Brochure](#deleting-a-brochure)
  - [Brochure Types](#brochure-types)
- [Public URLs](#public-urls)
- [Footer Link Setup](#footer-link-setup)
- [Brochure Viewer Features](#brochure-viewer-features)
  - [Deep Linking](#deep-linking)
  - [Keyboard Shortcuts](#keyboard-shortcuts)
  - [Analytics Hooks](#analytics-hooks)
- [Storage Structure](#storage-structure)
- [Performance Notes](#performance-notes)
- [Module Structure](#module-structure)
- [Extending This Module](#extending-this-module)

---

## Features

| Feature | Details |
|---------|---------|
| **Flipbook viewer** | Realistic page-turn animation powered by [StPageFlip](https://github.com/Nodlik/StPageFlip) (open-source, no jQuery) |
| **PDF mode** | Upload a PDF → pages rendered at runtime via [PDF.js](https://mozilla.github.io/pdf.js/) with lazy loading |
| **Image mode** | Upload pre-rendered WebP images per page → faster load, better UX |
| **Multi-brochure catalog** | Unlimited brochures, each with its own slug and settings |
| **Admin CRUD** | Add / Edit / Delete brochures with file uploads and DataGrid listing |
| **Deep linking** | `/brochure/my-catalog?page=5` opens directly on page 5 |
| **Zoom** | Click any page to see it full-screen |
| **Page flip sound** | Subtle paper-rustle sound generated via Web Audio API |
| **Keyboard navigation** | Arrow keys, Home, End |
| **Analytics hooks** | GA4 event wiring included (`brochure_open`, `brochure_page_view`) |
| **Mobile-friendly** | Touch swipe, portrait mode, responsive controls |
| **WebP conversion** | Uploaded images auto-converted to WebP via PHP GD |
| **SEO fields** | Meta title + meta description per brochure |
| **Sort order** | Control listing display order from admin |

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│  Admin Panel                                             │
│  /admin/brochure  →  DataGrid (list, edit, delete)      │
│  /admin/brochure/create  →  Upload PDF or page images   │
└────────────────────────┬────────────────────────────────┘
                         │ stores files in
                         ▼
              storage/app/public/brochure/
                  ├── pdf/           ← uploaded PDFs
                  └── pages/{slug}/  ← converted WebP images

┌─────────────────────────────────────────────────────────┐
│  Storefront                                              │
│  /brochures        →  Card-grid listing of all active   │
│  /brochure/{slug}  →  Fullscreen flipbook viewer        │
└─────────────────────────────────────────────────────────┘

Libraries loaded via CDN (no npm build step required):
  • PDF.js  3.11  → renders PDF pages to <canvas>
  • StPageFlip 2.0 → animates page flipping
```

**Rendering engines used:**

- **PDF mode** → PDF.js renders each page into a `<canvas>`, lazy-loading 4 pages at a time as the reader flips.
- **Image mode** → Pre-rendered `.webp` files are fed directly into StPageFlip — fastest load time, no runtime rendering cost.

---

## Installation

The module is already registered in `bootstrap/providers.php` and `composer.json`. Run the migration once:

```bash
php artisan migrate
```

Ensure the public storage symlink exists (it should already):

```bash
php artisan storage:link
```

Clear caches:

```bash
php artisan config:clear
php artisan view:clear
```

> **Optional asset build** — The module has its own Vite config for the admin sidebar icon CSS.
> If you want to rebuild it: `cd packages/Webkul/Brochure && npm install && npm run build`
> This is **not required** for the flipbook viewer to work.

---

## Admin Usage

### Creating a Brochure

1. Go to **Admin Panel → Brochure** (left sidebar)
2. Click **Add Brochure**
3. Fill in the form:

| Field | Required | Notes |
|-------|----------|-------|
| **Title** | ✅ | Displayed on listing page and in the viewer header |
| **Brochure Type** | ✅ | `PDF` or `Pre-rendered Images` — see [Brochure Types](#brochure-types) |
| **PDF File** | ✅ (if PDF type) | Max 50 MB. One PDF for all pages. |
| **Page Images** | ✅ (if Images type) | Upload images in page order. Auto-converted to WebP. Max 5 MB each. |
| **Status** | ✅ | `Active` = visible on storefront; `Inactive` = hidden |
| **Sort Order** | — | Lower number = appears first on listing page |
| **Meta Title** | — | SEO title for the viewer page |
| **Meta Description** | — | SEO description for the viewer page |

4. Click **Save Brochure**

---

### Editing a Brochure

1. Go to **Admin Panel → Brochure**
2. Click the **✏️ Edit** icon on any row
3. Update fields as needed
   - To replace the PDF: upload a new file (old one is deleted automatically)
   - To replace all page images: upload new images (replaces ALL existing pages)
   - To keep existing files: leave file inputs empty
4. Click **Update Brochure**

The **Preview** button (top-right of the edit form) opens the live flipbook in a new tab.

---

### Deleting a Brochure

- **Single delete**: Click the 🗑️ Delete icon on the DataGrid row (confirmation prompt will appear)
- **Mass delete**: Check multiple rows → select "Delete Selected" from the mass action dropdown

Both operations delete the brochure record **and** all associated files from storage.

---

### Brochure Types

#### `PDF` — Rendered at runtime
- Upload a single PDF
- PDF.js renders each page to canvas when the viewer loads
- **First 4 pages** are rendered immediately; remaining pages are rendered on demand as the reader flips
- Good for: quick publishing without pre-processing
- Slightly slower initial render compared to image mode

#### `Images` (Pre-rendered WebP) — Recommended for production
- Upload one image per page in correct page order
- Images are automatically converted to `.webp` format (quality 80, max ~300 KB/page)
- StPageFlip loads images directly — no runtime rendering overhead
- **Fastest load time and smoothest experience**
- Good for: high-traffic brochures, mobile-first audiences

---

## Public URLs

| URL | Route Name | Description |
|-----|------------|-------------|
| `/brochures` | `shop.brochure.index` | Listing page — card grid of all active brochures |
| `/brochure/{slug}` | `shop.brochure.view` | Individual flipbook viewer |
| `/brochure/{slug}?page=5` | `shop.brochure.view` | Opens viewer directly on page 5 (deep linking) |

---

## Footer Link Setup

To add a **Brochures** link in your storefront footer, use either of the following methods:

### Method 1 — Theme Customization (Recommended)

1. Go to **Admin Panel → Settings → Theme → (your active theme)**
2. Under **Footer Links** (or custom navigation), add a new link:
   - **Label:** `Brochures`  (or `Catalog`, `Our Brochures`, etc.)
   - **URL:** `/brochures`
3. Save the theme settings

This is the cleanest method — no code changes needed and the link persists through updates.

---

### Method 2 — Direct Blade URL (hardcoded)

If you want to hardcode it in a custom footer blade, use:

```blade
<a href="{{ route('shop.brochure.index') }}">Brochures</a>
```

Or to link directly to a specific brochure:

```blade
<a href="{{ route('shop.brochure.view', 'my-catalog-slug') }}">View Catalog</a>
```

---

### Method 3 — Link to a Specific (Default) Brochure

If you have one primary brochure and want the footer to open it directly:

```blade
@php
    $defaultBrochure = app(\Webkul\Brochure\Repositories\BrochureRepository::class)
        ->getActiveBrochures()
        ->first();
@endphp

@if ($defaultBrochure)
    <a href="{{ route('shop.brochure.view', $defaultBrochure->slug) }}">
        View Our Catalog
    </a>
@endif
```

---

> **URL Summary for footer:**
>
> | Link target | URL to use |
> |-------------|------------|
> | All brochures listing | `/brochures` |
> | Specific brochure by slug | `/brochure/{slug}` |

---

## Brochure Viewer Features

### Deep Linking

Append `?page=N` to any viewer URL to jump to a specific page on load:

```
https://yoursite.com/brochure/summer-catalog?page=8
```

The URL is automatically updated in the browser address bar as pages are turned (without reloading the page), so the current page is always shareable/bookmarkable.

---

### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| `→` or `↓` | Next page |
| `←` or `↑` | Previous page |
| `Home` | First page |
| `End` | Last page |
| `Escape` | Close zoom overlay |

---

### Analytics Hooks

The viewer fires these events automatically if `window.gtag` (Google Analytics 4) is available:

| Event | Trigger | Parameters |
|-------|---------|------------|
| `brochure_open` | Viewer loads | `brochure_slug` |
| `brochure_page_view` | Page is flipped to | `brochure_slug`, `page_number` |

To connect to a different analytics platform, edit the `trackOpen()` and `trackPageView()` functions in `Resources/views/shop/brochure/view.blade.php`.

---

## Storage Structure

```
storage/app/public/
└── brochure/
    ├── pdf/
    │   └── {random-hash}.pdf         ← uploaded PDFs (Laravel-generated name)
    └── pages/
        └── {brochure-slug}/
            ├── page-1.webp
            ├── page-2.webp
            └── page-N.webp           ← auto-converted WebP images
```

All files are accessible via the public symlink at `/storage/brochure/...`

When a brochure is **deleted** from admin, all its associated files and directories are removed automatically.

---

## Performance Notes

| Concern | Implementation |
|---------|----------------|
| **Initial load** | Only 4 pages are rendered/loaded eagerly; rest are lazy |
| **Mobile vs desktop** | PDF render scale adjusts automatically (1.2× mobile, 2.0× desktop) |
| **Image size** | WebP quality 80 keeps pages under ~300 KB each |
| **CDN-compatible** | All brochure assets (PDFs, images) served via `/storage/` which can be fronted by a CDN |
| **No jQuery** | Zero jQuery dependency — vanilla JS + Web Audio API throughout |
| **No build step** | Viewer libraries (PDF.js, StPageFlip) loaded from CDN; works without `npm run build` |

---

## Module Structure

```
packages/Webkul/Brochure/
├── README.md                                   ← this file
├── vite.config.js                              ← admin asset bundler
├── package.json
│
└── src/
    ├── Config/
    │   ├── acl.php                             ← permissions: brochure, brochure.create/.edit/.delete
    │   ├── admin-menu.php                      ← sidebar menu entry
    │   └── system.php                          ← legacy system settings placeholder
    │
    ├── Database/
    │   └── Migrations/
    │       └── ..._create_brochures_table.php
    │
    ├── DataGrids/
    │   └── BrochureDataGrid.php                ← admin listing with sort/filter/mass actions
    │
    ├── Http/
    │   ├── Controllers/
    │   │   ├── Admin/BrochureController.php    ← index, create, store, edit, update, destroy, massDestroy
    │   │   └── Shop/
    │   │       ├── BrochureController.php      ← listing page
    │   │       └── BrochureViewController.php  ← flipbook viewer + deep linking
    │   └── Requests/
    │       └── Admin/BrochureRequest.php       ← validation (PDF/image rules per type)
    │
    ├── Models/
    │   └── Brochure.php                        ← fillable, casts, scopes, pdf_url accessor, page_images accessor
    │
    ├── Providers/
    │   ├── BrochureServiceProvider.php         ← main: routes, views, migrations, config, storage dirs
    │   └── ModuleServiceProvider.php           ← Concord base (framework requirement)
    │
    ├── Repositories/
    │   └── BrochureRepository.php              ← CRUD + file upload + WebP conversion
    │
    ├── Resources/
    │   ├── assets/
    │   │   ├── css/app.css                     ← Tailwind + sidebar icon styles
    │   │   ├── images/
    │   │   │   ├── icon-brochure.svg
    │   │   │   └── icon-brochure-active.svg
    │   │   └── js/app.js                       ← Vite entry (tracks images for publishing)
    │   │
    │   ├── lang/en/app.php                     ← all English strings
    │   │
    │   └── views/
    │       ├── admin/
    │       │   ├── index.blade.php             ← DataGrid listing
    │       │   ├── create.blade.php            ← create form
    │       │   ├── edit.blade.php              ← edit form with preview link
    │       │   └── layouts/style.blade.php     ← injected into admin <head>
    │       └── shop/
    │           └── brochure/
    │               ├── index.blade.php         ← public listing (card grid)
    │               └── view.blade.php          ← standalone fullscreen flipbook viewer
    │
    └── Routes/
        ├── admin-routes.php                    ← /admin/brochure/* (middleware: web, admin)
        └── shop-routes.php                     ← /brochures, /brochure/{slug} (middleware: web, theme, locale, currency)
```

---

## Extending This Module

### Add a cover thumbnail

1. Add a `cover_image` column to the migration
2. Add `cover_image` to `$fillable` in `Brochure.php`
3. Add an image upload field in `create.blade.php` / `edit.blade.php`
4. In `BrochureRepository::createWithUpload()`, handle the new file upload
5. Use `$brochure->cover_image` in `shop/brochure/index.blade.php` to show real thumbnails

### Add a download button in the viewer

In `view.blade.php`, add to the top nav:

```blade
@if ($brochure->pdf_url)
    <a href="{{ $brochure->pdf_url }}" download class="nav-btn">
        <svg ...> <!-- download icon --> </svg>
        <span>Download</span>
    </a>
@endif
```

### Connect to a custom analytics provider

Find the `trackPageView()` function in `view.blade.php` and replace the GA4 call with your provider's API.

### Change the listing page route

Edit `src/Routes/shop-routes.php`. Remember to update any footer links pointing to `/brochures` if you change the path.

---

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.1+ |
| Laravel | 10+ |
| Bagisto | 2.x |
| PHP GD extension | Any (for WebP conversion) |
| Storage symlink | `php artisan storage:link` |

---

*Module maintained as part of the Praveen Sports Store Bagisto project.*
