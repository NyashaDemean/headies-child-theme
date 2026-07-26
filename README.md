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
