# Altura Upper Hill: landing page

A fast, mobile-first landing page for Altura in Upper Hill, Nairobi, built for Google Ads traffic. It is one self-contained file, `index.html`, with no build step and no framework. The only outside requests are Google Fonts and the Google Maps embed.

## Open it

Double-click `index.html`, or serve the folder:

```bash
cd altura && python3 -m http.server 8000   # then open http://localhost:8000
```

## Before it goes live

Every value is set in the `CONFIG` block near the bottom of `index.html`. The page reads phone numbers, prices and status from there, so you only change them once.

| Setting | What to put there |
|---|---|
| `phone`, `phoneDisplay` | The sales line. The current value is a placeholder. |
| `whatsapp` | The WhatsApp number, digits only (for example `2547…`). |
| `email` | The sales email. The current value is a placeholder. |
| `formEndpoint` | Where leads are sent. If it is empty, each lead opens WhatsApp with the enquiry already typed in. |
| `heroImage` | Optional. Add a real photo (for example `assets/hero.jpg`) to use it behind the hero. Otherwise the drawn skyline shows. |
| `units` | Price, size and default rent for each unit type. |
| `status` | The badge at the top of the hero. |

**Check these facts with the sales team.** They came from public listings, not from the sales team:

- Prices: Studio KSh 5,577,500, 1 bed KSh 11,201,000, 2 bed KSh 13,961,000. These are launch-era listing prices and may be out of date.
- Sizes: about 31, 46 and 81 m².
- Status: "Completed · Viewings open". The FAQ answer "Is Altura complete?" says the same thing.
- Travel times in the Location section are approximate.
- The default rents in the returns calculator are illustrative.

## What's built in

- **Ad attribution.** `utm_*`, `gclid`, `gbraid`, `wbraid` and `gad_campaignid` are read from the URL, kept in `localStorage`, and sent with every lead as hidden fields.
- **Conversion events** are pushed to `window.dataLayer` for Google Tag Manager or Google Ads:
  - `generate_lead`, which includes `lead_unit`, `lead_purpose` and `lead_contact`
  - `click_call`
  - `click_whatsapp`
  - `cta_click`

  Paste your GTM snippet where the comment in `<head>` marks it.
- **Lead form.** It checks name, phone, optional email and consent. If `formEndpoint` is set, leads are POSTed there as JSON. Formspree, a Zapier or Make webhook, or a small proxy into Zoho CRM all work. If the POST fails, the visitor is offered WhatsApp instead.
- **Contact options.** Phones get a sticky Call / WhatsApp / Get prices bar. Desktop gets a floating WhatsApp button.
- **Unit tabs** with drawn floor-plan sketches, a **rental returns calculator**, a map and travel times, an FAQ, and schema.org `ApartmentComplex` data for search engines.
- Works with a keyboard and screen readers, and respects the reduced-motion setting.

## Deploy

The page is static, so any of these work:

- Drag the `altura/` folder onto Netlify Drop.
- Use Cloudflare Pages.
- Use GitHub Pages.
- Upload `index.html` to the existing site, for example at `/lp/altura/`.
