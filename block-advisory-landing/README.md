# Block Advisory: new development landing pages

One template for every new development. Each development page is a single file holding only that project's details. The design, layout and lead capture are shared, so improving them once updates every page.

```
block-advisory-landing/
├── assets/
│   ├── landing.css      design for all pages (white space, type, layout)
│   ├── landing.js       builds each page from its data; forms, gallery, calculator
│   └── agency.js        Block Advisory settings: logo, phone, WhatsApp, agent, where leads go
├── developments/
│   ├── _template/       blank starting point with every field explained
│   ├── luster-arbor/    example with photos, 10 unit layouts, similar developments
│   └── santorini-residences/  example with a reservation + deposit plan and a calculator that counts months to completion
└── index.html           internal list of all development pages
```

## Add a new development (about 15 minutes)

1. Copy `developments/_template` and rename the folder, for example `developments/riverside-heights`.
2. Open its `index.html` and fill in the `window.DEVELOPMENT = { ... }` block: name, area, price from, completion, photos, units, amenities, payment plan, location and FAQs.
3. Update the `<title>` and `description` at the top. These are what Google and WhatsApp link previews show.
4. Add a line for it in the top-level `index.html`.
5. Upload the folder. The page lives at `/developments/riverside-heights/`.

Anything you leave out simply doesn't appear. With no photos yet, the page shows a branded panel instead. With no unit sizes, the size column is hidden. With no payment months, the calculator is replaced by a "get the schedule" form.

## Update every page at once

| To change… | Edit |
|---|---|
| Phone, WhatsApp, email, office address, agent | `assets/agency.js` |
| Where enquiries are sent (CRM / webhook) | `formEndpoint` in `assets/agency.js` |
| "Why Block Advisory" points and the disclaimer | `assets/agency.js` |
| Colours, spacing, fonts | `assets/landing.css` (brand tokens are at the top) |
| Layout, sections, form behaviour | `assets/landing.js` |

## How visitors leave their details

- **Hero form:** asks for only two things, name and phone. It sits next to the price, completion date and payment plan.
- **"Get price list" button:** always visible in the header. On phones, a bar with the same button and WhatsApp appears once the visitor scrolls past the hero form.
- **Floor plan & price:** on every unit row. It opens a short form that already knows which unit the visitor asked about.
- **Reuse of details:** a visitor who has sent the form once in that visit finds their name and phone already filled in on the next form.
- **Instant delivery:** if you add a `brochure` link, or a `plan` link on a unit, it opens right after the visitor sends their details.
- **Full enquiry form:** at the bottom of the page. It also asks for email (optional), unit type, purpose, preferred contact and a message.

Every enquiry includes the development, the unit, the form it came from, and the ad that brought the visitor (`utm_*`, `gclid`, `gbraid`, `wbraid`, `fbclid`).

Where the enquiry goes depends on `formEndpoint` in `assets/agency.js`:

- **Empty:** WhatsApp opens with the enquiry already typed.
- **Set:** the enquiry is sent as JSON to that address. Use a Zapier or Make webhook, Formspree, Zoho Flow, or a small endpoint that creates a Zoho CRM lead.

## Tracking

Paste your Google Tag Manager and Meta Pixel snippets where each page's `<head>` says so. The page sends these events:

| Event | When |
|---|---|
| `generate_lead` | A form is sent. Includes `development`, `lead_source` and `lead_unit`. |
| `enquiry_open` | The short enquiry form opens. |
| `click_whatsapp` | A WhatsApp link is clicked. |
| `click_call` | A phone link is clicked. |
| `gallery_open` | The photo gallery opens. |
| `video_play` | The video starts. |
| `asset_open` | The brochure or a floor plan is opened. |

If the Meta Pixel is on the page, `fbq('track', 'Lead')` also fires on each enquiry.

## Hosting

The pages are plain files with no build step, so any static host works:

- A folder on block.ke's hosting, for example `block.ke/lp/…`
- Netlify
- Cloudflare Pages
- GitHub Pages

Photos can stay in the WordPress media library and be linked by URL.

## Before publishing a development

- **Facts:** confirm prices, sizes, completion dates and payment terms with the developer.
- **Permission:** make sure you're authorised to market the project and to use its renders.
- **Travel times:** these are approximate. Keep the note under the list.
