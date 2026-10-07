# Sensei, Nyali: landing page

`index.html` is a single-file, mobile-first landing page for Sensei, the beachfront development on Greenwood Drive, Nyali. Block Real Estate is shown as the marketing agent. Open the file in a browser, or upload it anywhere that serves static files (Netlify Drop, Cloudflare Pages, or a block.ke page).

## Project facts used

| Fact | Detail |
|---|---|
| Location | Greenwood Drive, Nyali, Mombasa |
| Site | 1.89 acres with 67 m of direct beachfront |
| Residences | 111 in total: 1 to 3 bedroom apartments and 1 to 4 bedroom duplexes |
| Price | From KES 24M |
| Payment | Over 30 months |
| Completion | Q3 2029 |
| Developer | Amouage Development Limited with HassConsult (from public listings) |

Amenities:

- Infinity lagoon, courtyards and a seaside lookout
- Gym
- Film lounge and Ocean Pavilion
- Work studio, beach cabanas and lounge islands
- Sand play park and kids' tide pool
- Resident events

## Before it goes live

- **Permission:** confirm Block is authorised by the developer to market Sensei.
- **Settings:** contact details, agency, agent and the "from" price are in the `CONFIG` block near the bottom of the file.
- **Lead delivery:** set `formEndpoint` to send leads to a CRM or webhook (Zoho, WPForms, Formspree). Left empty, each lead opens WhatsApp with the enquiry already typed in.
- **Images:** the hero and diagram are original illustrations, not renders. If the developer supplies official renders and permission, they can be swapped in.
- **Travel times:** these are approximate.
- **Calculators:** the payment-plan and holiday-let calculators are illustrative and clearly labelled.

## Built in

- **Ad tracking:** UTM, gclid, gbraid, wbraid and fbclid are captured from the ad link and sent with every lead.
- **Conversion events:** `generate_lead`, `click_call`, `click_whatsapp`, `floorplan_request`, `cta_click` and `calculator_tab` are pushed to `dataLayer` for Google Tag Manager and Google Ads.
- **Contact options:** a sticky Call / WhatsApp / Launch prices bar on phones, and a floating WhatsApp button on desktop.
- **Quality:** works with a keyboard, respects the reduced-motion setting, and has no sideways scrolling down to 320px wide.
