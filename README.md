# Headies

Custom WordPress + WooCommerce child theme for **Headies** — a headwear brand built on two lanes: reliable everyday caps, and occasional limited customization drops (bedazzled, floral patches).

> "Good caps, always. Great ones, sometimes."

Built on the [Storefront](https://woocommerce.com/storefront/) parent theme.

## Tech stack

- WordPress + WooCommerce
- Storefront (parent theme)
- Custom child theme (this repo)
- Payments: Paynow (EcoCash, ZimSwitch, Visa/Mastercard, OneMoney)
- Fonts: Cleo Folk (headings), Nunito (body)
- Brand colors: `#2359A9` (primary blue), `#41AAF5` (accent blue)

## Features built

- **Custom nav** — transparent over hero, flips white/black on scroll or hover, logo swaps to match
- **Toggleable drop bar** — promo banner above the nav that auto-shows when a drop's status is `'live'` in `headies_get_drops()`
- **Hero section** — full-bleed image, headline, CTA
- **Recent Collections** — 4-across grid pulling from the drops array
- **Trending Now** — full-bleed feature section with drop name + CTA
- **Custom footer** — socials, copyright, centered logo

## Local development

Built and tested locally via [Local](https://localwp.com/) at `headies-dev.local`.
## Managing drops

Drops live in `headies_get_drops()` inside `functions.php`. Each drop is an array with `name`, `desc`, `status` (`'live'` or `'upcoming'`), and `date`. Setting a drop's status to `'live'` automatically shows the promo bar site-wide with that drop's name.

## Still to do

- [ ] Real product photography (currently using placeholder images)
- [ ] Black logo variant for nav hover state
- [ ] Search icon functionality (currently a placeholder link)
- [ ] Real social media links in footer
- [ ] Drops page styling
- [ ] Real WooCommerce products
- [ ] Paynow integration
- [ ] Domain + hosting
- [ ] Cleo Folk commercial license (currently free personal-use — must upgrade before launch)

## Author

Nyasha Demean Muzerengi

## Recent Updates

- Added 7 new hat products (Chicago White Sox, LA Dodgers x3, NY Pink Monogram, NY Yankees Maroon, Phillies) via WP-CLI, each with front/back images for hover-swap
- Reworked Hats page grid layout: 4-column dense grid with thin dividers (no gaps), full-bleed product images edge-to-edge
- Wishlist and cart icons now overlay directly on top of product images (heart top-left, cart top-right) instead of sitting in a separate row
- Swapped the "bag" icon for a cart icon matching the main nav
- Fixed nav bar and logo visibility bug on non-hero pages (was invisible/blue-on-white)

**Known issue / next session:** icon overlay positioning needs another pass — icons aren't consistently sitting flush over the image on all cards yet.
## Drops System (added Aug 2026)

Drops are a custom WordPress taxonomy called `product_drop`, registered on the
`product` post type — same mechanism as the built-in "Hats" category.

### How to add a new drop
1. Go to **Products → Drops → Add New Drop**
2. Fill in: Name, Description, Drop Start Date/Time, Drop End Date/Time, Drop Image (Attachment ID)
   - Date format: `YYYY-MM-DD HH:MM:SS` (24hr)
   - Leave End Date blank to keep a drop "live" indefinitely
   - Image ID = the Media Library attachment ID (upload photo first, find ID in Media Library URL or via `wp media import`)
3. To assign hats to a drop: edit a hat product, check the relevant Drop in the
   "Drops" panel (same UI pattern as assigning Hat categories)

### How status is calculated (automatic, no manual field)
- `upcoming` — now < Drop Start Date/Time
- `live`     — between Start and End (or no End set)
- `past`     — now > Drop End Date/Time

### Where this data flows
- `headies_get_drops()` in `functions.php` reads the taxonomy + term meta,
  returns an array shaped for display (name, desc, status, date, drop_datetime, image)
- `page-drops.php` groups drops into "upcoming/live" (shown in hero) vs "past"
  (shown in horizontal-scroll cards)
- `front-page.php` (homepage) — Recent Collections and Trending Now sections
  both call `headies_get_drops()` directly, so adding a drop updates the
  homepage automatically, no separate edit needed
- Countdown timer on the hero is pure JS, reads `data-dropdate` attribute,
  ticks live, flips to "OUT NOW" at zero

### Drop images already in Media Library (imported Aug 2026)
| File | Media ID |
|---|---|
| drop-photo-01.jpeg | 57 |
| drop-photo-02.jpg | 58 |
| drop-photo-03.jpg | 59 |
| drop-photo-04.jpg | 60 |
| drop-photo-05.jpg | 61 |
| drop-photo-06.jpg | 62 |
| drop-photo-07.jpg | 63 |
| drop-photo-08.jpg | 64 |
| drop-photo-09.jpg | 65 |
| drop-photo-10.jpg | 66 |

### Known issue being fixed (Aug 2026)
Drops with status `live` were not displaying anywhere — `page-drops.php`
grouping logic only checked for `upcoming`/`past`. Fix: treat `live` same as
`upcoming` for display purposes (shows in hero, countdown auto-shows "OUT NOW").

### Still outstanding
- Wishlist: YITH WooCommerce Wishlist plugin selected, not yet installed
- Cart & My Account pages: using WooCommerce defaults, not yet styled to match brand
- 3 placeholder drops (TBD 1/2/3) need real names + hat assignments
- Single-drop hat listing page (view hats within one specific drop) not yet built

