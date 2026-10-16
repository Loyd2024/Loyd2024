# Brightwater Dental website

A complete, ready-to-customize website for a family dental practice. It is plain HTML, CSS and a little
JavaScript, so there is nothing to install or build. You can open `index.html` in a browser, or upload the
folder to any static host.

**Brightwater Dental is a placeholder practice.** Every name, phone number, address, price and review is an
example to replace before launch (see [Before you launch](#before-you-launch)).

## Pages

| Page | File | What's on it |
| --- | --- | --- |
| Home | `index.html` | Hero with booking and emergency numbers, comfort options, services, first visit and $99 offer, dentists, costs, sample reviews, hours and map |
| Services | `services.html` | Seven kinds of care, each with where it happens on the tooth chart, visit length, what to expect and cost notes |
| About | `about.html` | Practice story, how we work, the team, office and technology, access and languages, community |
| New patients | `new-patients.html` | First visit step by step, what to bring, forms, insurance, membership plan, financing, policies, FAQ |
| Contact | `contact.html` | Emergency numbers, appointment request form, contact details, hours, map, parking and access |
| Not found | `404.html` | Friendly "This tooth isn't on the chart" page |

Also included: `sitemap.xml`, `robots.txt`, `site.webmanifest`, icons and a social sharing image.

```
dentist-website/
├── index.html, services.html, about.html, new-patients.html, contact.html, 404.html
├── assets/
│   ├── css/styles.css     design tokens (light + dark), layout and every component
│   ├── js/main.js         mobile menu, appointment form, breathing exercise, small enhancements
│   └── img/               logo, favicon, icons, tooth-chart artwork, og-image.png
├── sitemap.xml, robots.txt, site.webmanifest
└── README.md
```

## Preview it

- Double-click `index.html`, or
- from the repository root run `npx serve dentist-website` and open the URL it prints.

The 404 page only displays correctly when served by a web server, because it uses `<base href="/">` so it
can load its styles from any missing URL.

## Before you launch

Work through this list. Everything is plain text in the HTML files, so your editor's "find in files" is
the fastest way to make each change.

1. **Practice details.** Find and replace these across all HTML files, `sitemap.xml` and `robots.txt`:

   | Placeholder | Replace with |
   | --- | --- |
   | `Brightwater Dental` | your practice name |
   | `(555) 014-2368` and `tel:+15550142368` | main phone number (shown text and link) |
   | `(555) 014-2399` and `tel:+15550142399` | after-hours / emergency number |
   | `hello@brightwater-dental.example` | your email |
   | `214 Maple Avenue`, `Suite 200`, `Springfield`, `ST`, `00000` | your address |
   | `https://www.brightwater-dental.example/` | your real domain (canonical links, social tags, sitemap, structured data) |
   | `214+Maple+Avenue+Springfield` | the "Get directions" map search |

2. **Remove the template notice.** Delete the "Template preview" banner (the element with
   `data-template-notice`, inside `<aside aria-label="Template notice">`) at the top of every page.
3. **Hours.** Update the visible hours (home, contact, footer). On the home and contact pages, the bars in
   the hours card take their open/close times from `style="--o:8;--c:17"`. Also update
   `openingHoursSpecification` in the structured data (`<script type="application/ld+json">`) on the home and contact pages.
4. **Team, story and prices.** Search for `OWNER:` comments: they mark text only you can supply, such as
   dental schools, the practice story, community work and the insurance plans you actually accept. Check
   every price ($99 new-patient visit, $29/$22 membership) and the insurance list.
5. **Reviews.** The home page reviews are marked `data-sample="true"`. Replace them with real reviews that
   patients have agreed to let you publish, or remove the section.
6. **Photos.** Search for `PHOTO:` comments. Each one describes the photo that belongs there and its size.
   Swap the placeholder `<div>` for an `<img>` with descriptive `alt` text.
7. **Map coordinates.** In the structured data, replace `"latitude": 0, "longitude": 0` with your real coordinates.
8. **Social image.** `assets/img/og-image.png` (1200×630) is rendered from `og-image.svg`. Re-export it if
   you change the name or colors.

Also check your state dental board's advertising rules before you launch. The copy avoids terms like "specialist",
superlatives and outcome guarantees on purpose.

## Connect the appointment form

The form on `contact.html` works without a server. When it isn't connected, it checks the fields and then
asks the visitor to call. To receive requests:

1. Create a form endpoint with a form service (for example Formspree, Basin or Getform).
2. In `contact.html`, set **both** `data-endpoint="…"` and `action="…"` on the `<form>` to that URL.
3. Submit a test request.

The form only asks for contact details, preferred times and a reason for the visit, and tells people not to
include medical history. If you ever collect health information online, use a HIPAA-compliant form provider
that will sign a Business Associate Agreement.

## Deploy

### GitHub Pages (set up in this repository)

The workflow `.github/workflows/deploy-dentist-website.yml` publishes this folder whenever a change to it
reaches `main`.

1. In the repository, open **Settings → Pages** and set **Source** to **GitHub Actions** (one-time).
2. Merge the site to `main`, or run the workflow by hand from the **Actions** tab.
3. The site appears at `https://<user>.github.io/<repository>/`. The workflow automatically points the 404
   page at that path.

To use your own domain, add it under **Settings → Pages → Custom domain**, then update the canonical domain
(step 1 above).

### Other hosts

Netlify, Cloudflare Pages, Vercel or any web host: publish the `dentist-website` folder as-is, with no build
command. On hosts that serve the site from the root of a domain, `404.html` works as it is.

## Design notes

- The visual idea is a patient's dental chart: the 32-tooth chart with universal numbering (1–32), small
  charts on each service showing where that care happens, an appointment card and a six-month recall ring.
- Fonts: Bricolage Grotesque (headings), Commissioner (text) and Spline Sans Mono (chart labels), loaded
  from Google Fonts with fallbacks.
- Every color is a token at the top of `styles.css`, with a designed dark theme that follows the visitor's
  system setting.
- Built for WCAG 2.2 AA: keyboard and screen-reader friendly, visible focus, 44px touch targets, and reduced
  motion respected. It works without JavaScript.
