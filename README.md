<p align="center">
  <img src="images/logos/logo-3d-blue.png" alt="Headies" width="360">
</p>

<p align="center"><i>"Good caps, always. Great ones, sometimes."</i></p>

# Headies

Custom WordPress + WooCommerce child theme for **Headies** — a headwear brand
built on two lanes: reliable everyday caps, and occasional limited
customization **Drops**. Built on the [Storefront](https://woocommerce.com/storefront/)
parent theme.

<p align="center">
  <img src="images/drops/The Ivory League/Ivory main.png" alt="The Ivory League drop" width="31%">
  <img src="images/drops/Heartbloom/Heartbloom main.png" alt="Heartbloom drop" width="31%">
  <img src="images/drops/Under The Green/Green main.png" alt="Under The Green drop" width="31%">
</p>

## Tech stack

- WordPress + WooCommerce
- [Storefront](https://woocommerce.com/storefront/) (parent theme)
- Custom child theme (this repo)
- Payments: Paynow (EcoCash, ZimSwitch, Visa/Mastercard, OneMoney)
- Fonts: [Fredoka](https://fonts.google.com/specimen/Fredoka) (drop names, hero, major headings), [Inter](https://fonts.google.com/specimen/Inter) (everything else) — both free (SIL OFL), self-hosted as `.woff2` in `assets/fonts/`, no Google Fonts CDN request at runtime
- Brand colors: `#12203A` (primary navy), `#41AAF5` (accent blue)

## Pages

| Page | Template | Notes |
|---|---|---|
| Home | `front-page.php` | Hero video, featured drop banner, Drops row, Trending Now |
| Hats | `page-hats.php` | Full catalog grid — badges auto-switch between New / Coming Soon / Exclusive |
| Accessories | `page-accessories.php` | Same grid design as Hats, its own product category |
| Drops | `page-drops.php` | Full-bleed hero + Upcoming/Past drop grids (matches Hat Club's drops-archive layout) |
| Single drop | `taxonomy-product_drop.php` | `/drop/{slug}/` — hero photo, black write-up bar, product grid |
| Wishlist | `page-wishlist.php` | Cookie/account-backed, AJAX add/remove |
| Search | `search.php` | Reuses the Hats grid design instead of Storefront's default blog search |
| Cart / Checkout / My Account | `woocommerce/**` | Fully re-skinned to match the brand, including a stripped-down checkout nav |

## Drops system

Drops are a custom taxonomy, `product_drop`, registered on the `product` post
type — same mechanism as the built-in product categories. Manage them under
**Products → Drops** in wp-admin.

**Fields per drop** (term meta):
- Drop Start / End Date/Time (`YYYY-MM-DD HH:MM:SS`, 24hr — leave End blank to stay live indefinitely once it starts)
- Banner Image — the Drops page hero background for the nearest upcoming drop
- Card Image — homepage Drops row + Past Drops thumbnail
- Detail Page Hero — the photo at the top of the drop's own page
- Tagline, and a Full Description (the taxonomy Description field itself is the short blurb)

**Status is derived automatically**, never set by hand:
- `upcoming` — now < Start
- `live` — between Start and End (or no End set)
- `past` — now > End

**What status drives:**
- Product card badge: `New` (no drop) → `Coming Soon` (upcoming) → `Exclusive` (live/past)
- On the drop's own page, the product grid hides name/price entirely while `upcoming` (a "reveal" grid — you can still wishlist), and shows normally once live/past
- On a product's single page, Add to Cart is replaced with a "wishlist it now" notice while its drop is `upcoming`

`headies_get_drops()` / `headies_build_drop_array()` / `headies_get_drop_status()`
in `functions.php` are the source of truth — everything above reads from those.

## Local development

Built and tested locally via [Local](https://localwp.com/) at `headies-dev.local`.

## Known gaps

- [ ] Real Paynow account credentials (gateway is wired up, running in test mode)
- [ ] Domain + hosting
- [ ] Real social links in the footer

## Author

Nyasha Demean Muzerengi
