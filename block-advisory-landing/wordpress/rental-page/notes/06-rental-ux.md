# 06. Rental page UX: what makes a rental listing convert and feel trustworthy

Research note for the Block rental page ("BLOCK — Rental page v1"). Written 11 Nov 2026 by the rental-UX research
agent. Scope: what a tenant (or a business occupier) needs from a single listing page, drawn from leading portals and
agencies, published UX research, Kenyan renting norms, WhatsApp practice and commercial lettings pages. It ends with
the seven deliverables the brief asked for (sections 7 to 13), the open questions (14) and every source with its URL (15).

How to read the evidence labels:

- **[F:key]** = I fetched the page myself today and read its HTML or text. Strongest evidence.
- **[S:key]** = the page could not be fetched from this session; the finding comes from a web-search result summary
  of that URL. Treat wording and figures as second-hand until someone opens the page.
- **[L:path:line]** = a local project file (line numbers as of today).
- "Not established" = I looked and could not confirm it. Findings about other sites and about research are never
  filled in from memory. Recommendations are my judgement; where one rests on general web practice and not on a
  cited source, it has no source key.

Contents: 0 summary · 1 method and limits · 2 listing pages · 3 published research · 4 Kenyan norms · 5 WhatsApp ·
6 commercial · **7 first screen (A) · 8 page anatomy (B) · 9 patterns (C) · 10 forms (D) · 11 trust copy (E) ·
12 accessibility and performance (F) · 13 ten ideas (G)** · 14 open questions · 15 sources.

Rule reminder (PROJECT.md section 4, rule 11): every figure on a listing page must come from that listing's own fields
or text. The Kenyan norms in section 4 explain which QUESTIONS the page must answer and how generic copy should be
worded. They are never a source of figures for a page.

---

## 0. One-screen summary

**The single most important finding.** On a rental page the reader's first questions are the rent, what it covers,
whether the home is still available and how to see it; and Block's listings can answer some of these far more
often than others. Renter surveys rank price first, then photos, then availability [S:apt-unit]; 94% of US renters
want every fee disclosed [S:zillow-2026]. The data audit of the 129 rentals finds a rent on 120, availability stated
concretely on 89, the service charge on 50 and furnishing on 55, but a deposit figure on only 6 and a lease length
on only 6 [L:notes/03-data-audit.json] (`summary.coverage`, `summary.terms`); about 81% of descriptions use
"confirmed at enquiry" wording somewhere (section 4.3). So the page should lead with one honest rent sentence, what
the listing says it includes and whether it is available; make "Arrange a viewing" and WhatsApp the two ways
forward; and turn the unknowns into a short "To confirm when you enquire" checklist instead of empty rows, a
calculator or a gated price-list form.

What the next team most needs:

1. Order of importance to renters: price, photos, availability, then plans and details [S:apt-unit], [S:apt-q4-2025].
   Renters research about ten homes and seriously consider three [S:apt-q4-2025], so the page must answer fast and
   offer similar homes.
2. Kenya's own portal contact pattern is Call plus WhatsApp, with a prefilled message that carries the listing URL;
   the Kenya Property Centre listing page I fetched has no enquiry form at all [F:kpc-house].
3. About 19% of rental enquiries on that portal came from outside Kenya in Q3 2026 (18.3% for rentals in Nairobi)
   [F:kpc-diaspora]. Phone fields must accept international numbers, and WhatsApp matters twice over.
4. "Is it still available?" is a real question: the same portal reports a median 74 days from listing to let in Q3
   2026 [F:kpc-ttl], and tenants interviewed by Business Daily describe homes that "suddenly became occupied" after a
   viewing fee was paid [F:bd-2025].
5. Viewing fees, "appearance" and "inconvenience" fees (KSh 1,500 to 3,650 in the cases reported) are the best-known
   grievance against Nairobi agents [F:bd-2025]. A plain statement of Block's own practice would be the strongest
   trust line on the page, but only the owner can confirm what is true (open question 1).
6. Service charge is quoted in Kenyan listings as either "plus service charge" or "inclusive" [F:kpc-sc]. That one
   phrase belongs next to the rent whenever the listing states it.
7. Gallery and form basics are settled research: thumbnails rather than dots, no auto-rotation, slides in the HTML
   rather than injected by script, labels above fields, explain why the phone number is asked for, validate on leaving
   a field (sections 3.2 and 3.4).
8. A sticky bar must not hide the element that has keyboard focus (WCAG 2.2, 2.4.11) [S:wcag-2411] and should be
   small, opaque and high-contrast [S:nng-sticky].
9. For commercial space the occupier's checklist is: size, rent per sq ft a month, service charge per sq ft, parking
   ratio and price per bay, backup power, lifts or loading, condition, lease length. A Nairobi office listing I
   fetched carries exactly these [F:kpc-office], and Block's own office text lists the lease terms still to confirm
   [L:ref/pages-final/36809.html:5033].

---

## 1. Method and limits

**What I could reach.** This session's network allows only a short list of hosts. That shaped the evidence:

| Source group | How obtained | Limit |
|---|---|---|
| Kenya Property Centre (results page, three listing pages, FAQ, two glossary entries, two market indices) | Fetched with curl, one request at a time | One portal; pages as served to a script-less client |
| Business Daily (two articles), Airbnb help and resource pages, the Data Protection Act PDF on odpc.go.ke | Fetched | Business Daily and Airbnb through the fetch tool, which summarises: quotes are as it reported them. The Act's PDF was downloaded and read directly |
| BuyRentKenya | **Blocked.** The host is allowed, but it answers HTTP 403 with a Cloudflare "Just a moment..." challenge. I did not try to get round it | Search results only |
| Kenya Law (new.kenyalaw.org, kenyalaw.org) | **Blocked** (HTTP 202 with an empty body; HTTP 403) | Search results only |
| Zillow, Apartments.com, Rightmove, Zoopla, StreetEasy, Savills, Knight Frank, Property24, LoopNet, Pam Golding, Baymard, Nielsen Norman Group, W3C, web.dev and all news sites other than Business Daily | **Not reachable** from this session (hosts outside the allowed list) | Search-result summaries only, marked [S:...] |
| block.ke | Not fetched (PROJECT.md rule 10). Local copies in `ref/` only | — |

**Not established** (I searched and could not confirm; see section 14 for how to find out):

- The on-page layout, button labels and sticky behaviour of Zillow, Apartments.com, Zoopla and StreetEasy listing
  pages. I have their stated features and research, not a first-hand reading of the pages.
- Savills' and Knight Frank's own lettings listing pages. Search returned only their listings as mirrored on
  Rightmove and OnTheMarket, plus guides and brochures.
- Property24 **Kenya** listing pages. The field names in section 2.2 are from Property24 South Africa listings.
- BuyRentKenya's listing-page interface (buttons, badges, forms).
- Kenyan agencies' own letting pages other than Pam Golding's. Search returned no listing pages for HassConsult,
  Villa Care, Ryden or Knight Frank Kenya.
- Sticky actions on phones on any portal. The one page I could read has a desktop-only bar (2.1).
- Any Nielsen Norman Group study specifically about property listing pages. I found none; their general guidance on
  prices, forms, carousels, sticky elements and FAQs is used instead.

**Local data used.** `ref/listings.json` (the 129 rent-only rows), the 129 saved pages in `ref/pages-final/`,
`ref/dev-v1.9.1.php`, `ref/README-wordpress.md` and `ref/plan.md` for wording the owner has already approved, and
the data audit `notes/03-data-audit.json`, which appeared while I was writing and whose figures I use wherever it has
one. My own counts from the saved pages are a keyword scan: defer to the audit where they differ.

**Working files** (temporary, outside the project folder): raw copies of the fetched pages are in
`/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp-ux-fetch/`, and the scan behind
section 4.3 is `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp-ux-work/scan-descriptions.py`
(run it with the project folder as its argument).

---

## 2. What listing pages do (study 1)

### 2.1 Kenya Property Centre: the one Kenyan portal I could read first-hand

Pages fetched: a 5-bedroom house to rent in Karen [F:kpc-house], a warehouse in Embakasi [F:kpc-warehouse], an office
in Westlands [F:kpc-office], the "houses for rent in Nairobi" results page [F:kpc-list] and the help centre
[F:kpc-faq]. The site says its property page was redesigned on 19 June 2026 [F:kpc-diaspora], so this is a current
design.

**Order of information on the listing page (document order in the HTML):**

1. Three "similar properties" links (same type in the area, in the city, and the average rent for that size).
2. Badge ("Featured" or "Premium") and the listing kind: "House for rent".
3. H1 ("5 Bedroom House"), then the area ("Karen, Nairobi").
4. The rent: "KSh 380,000" with "per month" beside it.
5. A small meta line: "Ref ID: #706-36", "Posted 8 Nov 2026", "5 views · last 30 days".
6. Five actions: **Call agent**, **WhatsApp**, Save, Share, Report.
7. The gallery ("View all photos", opens a lightbox).
8. Fact tiles: bedrooms, bathrooms, toilets, property type (the warehouse shows plot size and parking instead).
9. "About this property" (the advertiser's free text).
10. "Features and amenities" in four groups: Interior & Finishing, **Power & Utilities**, Security & Safety,
    Outdoor & Communal. Under Power & Utilities the house lists "24/7 power supply", "Prepaid meter", "Solar power",
    "Inverter", "Borehole" and "High-speed internet / WiFi".
11. "Property details" table (reference, listed by, type, status, counts).
12. "Similar properties in Karen": three cards, "See all in Karen", "See all similar properties".
13. A disclaimer, then "Marketed by" (agency name, "Member since March 2026", link to all its listings), then
    "Useful links".

**How contact works.** There is no enquiry form in the served listing HTML. The two primary actions are a call and WhatsApp:

- *Call agent* opens a small dialog, "Choose a number to call", listing each number as text, with the line "Tap a
  number to start a call. Standard call rates may apply." Numbers shown as text also serve desktop visitors.
- *WhatsApp* is a `wa.me` link with a prefilled message that includes the listing's URL:
  "Hello, I'd like to enquire about your property on Kenya Property Centre. https://kenyapropertycentre.com/for-rent/…"
- *Share* offers WhatsApp first, then Facebook, Messenger, X, LinkedIn, Email, SMS and "copy link".
- The help centre still describes a "Request details" button that emails the agent, and advises: "the quickest way
  to reach them is to call the number on the property page rather than waiting on email" [F:kpc-faq]. I did not find
  that button in the listing HTML, so the form may have been dropped in the redesign (not established).
- The site counts "Phone and WhatsApp" as its enquiries when it reports demand [F:kpc-diaspora].

**Trust devices.** Reference ID and posting date under the title; a named advertiser with "Member since"; a
"Verified" badge that the help centre defines carefully ("we have confirmed the agent or developer's identity and
business details … though we always recommend the usual due diligence before making any payment") [F:kpc-faq]; a
standing disclaimer; and a **Report this listing** dialog whose categories are a catalogue of what goes wrong when
renting in Kenya [F:kpc-house]:

- "I've lost money to this advertiser" (payment taken for rent or a deposit; someone posing as a landlord or agency;
  money sent to a different account than agreed).
- "The property doesn't exist, or isn't theirs to advertise."
- "Problem with a viewing or agency fee: You paid a fee and were shown a different property, or no viewing happened,
  or extra fees were demanded afterwards, or a promised refund never came."
- "The listing is wrong, misleading or inappropriate" (price, size, condition or location don't match; photos aren't
  of this property).
- "The property is no longer available: Already let, sold or withdrawn, but still being advertised."

**Sticky behaviour.** A bar fixed to the top appears on desktop only (it is hidden below the large breakpoint). It
repeats the title, the area and a shortened rent ("KSh 380K"). I found no fixed bottom bar for phones in the HTML.

**Other details worth noting.** A header currency switch (KES, USD, GBP, EUR, CAD) and a text-size control; a
glossary pop-over on local terms such as "DSQ"; listing cards on the results page show the rent as "KSh 500,000 /mo"
with the description's first lines, and a "Verification status" sort [F:kpc-list].

**One thing not to copy.** The gallery's photos are bound by script (`<img :src="slide.full">`); the photo addresses
are not in `src` attributes in the served HTML, so a visitor without JavaScript, and any crawler that does not run
it, sees no photos [F:kpc-house]. Our rule 9 requires the opposite.

**What real Nairobi listings say about terms** (free text on the same portal, fetched today):

- "Rent: Usd 3,500 Dollar payments only. Terms: 2 months' deposit + quarterly rent in advance." [F:kpc-list]
- "Rent: Usd 1,700 -- all inclusive Service charge and utility bills included." [F:kpc-list]
- "KSh 450,000 per month (Inclusive of Service Charge)" (a Runda townhouse) [F:kpc-list]
- Warehouse: "Monthly Rent: KSh 280,000 + VAT", "8-metre truss height", "Double dock doors for loading and
  offloading", "Small mezzanine suitable for office setup" [F:kpc-warehouse]
- Office: "Price: Kes110 per SQ FT. Service charge : Kes 22 per SQ foot. Parking: 2 Bays for 1,000 SQ.FT @ kes
  10,000 + VAT per bay", "Space available: From 3,000 - 50,000 SQ.FT.", "Auxiliary power via backup 440 kva
  generators", "2.7 meter clear headroom", "3 High-speed lifts" [F:kpc-office]. The headline shows the monthly total
  for the advertised 8,331 sq ft ("KSh 916,410 per month"), which is 8,331 × 110.

### 2.2 Other portals and agencies (search results only; none of these pages was fetched)

| Site | What the search results show | Source |
|---|---|---|
| **Rightmove** (UK lettings) | A fixed "Letting details" block: *Let available date* ("Now" or a date), *Deposit*, *Min. Tenancy*, *Let type* ("Long term"), *Furnish type*. One listing reads: available "Now", deposit £3,461, minimum tenancy 12 months, long term, unfurnished. Many listings leave values as "Ask agent". Furnishing sits with property type, bedrooms, bathrooms and size | [S:rm-listing] |
| **Rightmove** agent guidance | Tells agents to "include all the vital lettings information" in descriptions; a 2020 guide says lettings listings with video get on average 39% more of a metric the snippet cuts off; "Online Viewings" was retired on 25 July 2024 and video now sits under Virtual Tours | [S:rm-lettings-guide], [S:rm-online-viewings] |
| **Zoopla** (UK) | Its help article says a lettings listing should state when the home is available and whether pets are likely to be accepted, and lists the lettings fields: rent, council tax band, deposit, EPC rating, "letting arrangements" (free text). It adds that lettings listings with a floor plan got a higher search click rate | [S:zoopla-fields] |
| **Zillow** (US) | A "total price" display (base rent plus fixed required monthly fees) with a "Costs & Fees" breakdown that separates optional fees and notes usage-based ones such as utilities; available to landlords from 25 Aug 2025. Listings also show "days listed", "contacts" and "applications" counts (the last three from forum posts, not Zillow documentation) | [S:zillow-price], [S:zillow-group-price], [S:zillow-contacts] |
| **Apartments.com** (US) | "Total Monthly Price" label and badge beside the price on search results and the listing page; "Plus Fees" where a total is not given; a cost calculator link in the pricing section. Its own analysis: listings with transparent pricing get 15 to 20% more leads (19% on average in another post) | [S:apt-price], [S:apt-leads] |
| **StreetEasy** (New York) | After the FARE Act (11 June 2025): every listing must disclose all one-time and recurring fees; listing pages carry the notice "Under NYC law, you can't be charged a broker fee if you didn't hire a broker"; a cost breakdown and a "Report listing" function | [S:se-fare] |
| **Property24** (South Africa listings) | A "Property Overview" block with *Occupation Date*, *Lease Period* ("12 months"), *Deposit Requirements* (an amount, or "Two Month's Rental"), *Pets Allowed*, *Furnished* ("No", "Optional"). Several listings showed occupation dates already in the past | [S:p24-listing] |
| **Airbnb** (gallery pattern) | Fetched help pages: at least five photos; the first five are "the most important" and are shown prominently, the first being the large one used in search; photos are sorted into rooms for a "photo tour" and each room or photo can carry a caption ("Add details the photos don't show"); minimum 1024 × 683 px | [F:airbnb-tour], [F:airbnb-photos] |
| **BuyRentKenya** | Blocked for fetching. Search shows: advertisers can be reached "through WhatsApp, phone, and email"; listing pages are organised as Description, Amenities, Internal Features, External Features, Neighbourhood; an expired listing shows a notice with similar properties. Office listings state terms in full, for example "Lease Term: 5 years and 3 months • Refundable Security Deposit: Equivalent to 3 months' rent • Rent Payment: Payable quarterly in advance" | [S:brk-private], [S:brk-office-1] |
| **Pam Golding Kenya** (agency) | Rental listings show bedrooms, bathrooms and a monthly rent (some in US dollars; the .co.za site converts to rand), a "Call us and schedule a viewing today!" line, and a named agent profile with a WhatsApp option | [S:pg-kenya] |
| **Knight Frank UK** (agency) | Its lettings FAQ says "Knight Frank will organise all the viewings and we will accompany the applicants on each viewing". Brochures give a guide rent per week or per calendar month and whether furnished | [S:kf-faq] |
| **Savills** | Search surfaced only index pages ("Monthly + Fees apply") and Rightmove mirrors. Listing layout not established | — |
| **LoopNet** (commercial) | Header gives the size range and rate ("3,495 - 12,395 SF, $45.00 /SF/YR") with a unit toggle; a **space availability table** (Space, Size, Term, Rental Rate, Space Use, Build-Out or Condition, Available); bulleted Highlights; Property Facts (class, year, size, parking) | [S:loopnet] |

### 2.3 What the pages agree on

- **The rent with its period sits beside the title**, before any description: "KSh 380,000 per month" [F:kpc-house].
- **A short block of letting facts** follows the photos on the mature portals: available date, deposit, minimum term,
  furnishing [S:rm-listing], [S:p24-listing], [S:zoopla-fields]. Where the advertiser has not supplied a value,
  Rightmove prints "Ask agent". Our rule 11 does not allow a "to be confirmed" value to stand as a fact, so the
  adaptation is: show what is stated, and gather the rest into one "To confirm when you enquire" line.
- **What the rent includes is moving to the headline** (Zillow total price, Apartments.com Total Monthly Price,
  StreetEasy fee disclosure) [S:zillow-price], [S:apt-price], [S:se-fare]. The Kenyan equivalent is the service charge
  phrase [F:kpc-sc].
- **Contact is immediate and low-effort**: call and WhatsApp in Kenya [F:kpc-house]; tour or message buttons on the US
  portals (labels not established).
- **Trust is carried by plain facts**: a reference, a date, a named agent, a way to report a problem, and a
  disclaimer that tells the reader to check before paying [F:kpc-house], [F:kpc-faq].
- **Similar homes close the page**, scoped to the same area and type [F:kpc-house].
- **Two things I could not find precedent for.** A fixed action bar on phones (not established for any portal; the
  recommendation in 12.2 rests on research guidance and on the bar the owner approved [L:ref/plan.md:701]), and FAQs
  on the listing page itself (the Kenyan portal has a glossary and a help centre instead; Block's own descriptions
  already carry FAQs, section 4.3).

---

## 3. Published research (study 2)

All of section 3 is from search-result summaries of the cited pages, unless marked [F:...].

### 3.1 What renters want to know first

| Finding | Source |
|---|---|
| Asked which unit-level details matter, renters put **price first, then photos, availability and floor plans**; 99% say unit-specific information is important; 77% want photos of the actual unit, 67% its floor plan, 35% a 3D tour | [S:apt-unit], [S:apt-q4-2025] |
| 53% call it a deal-breaker if a listing does not show exact photos of the home | [S:apt-q4-2025] |
| Renters plan to research about ten properties and seriously consider three; 87% start without a specific property in mind | [S:apt-q4-2025] |
| Top deal-breakers: out of budget 79%, crime or safety concerns 67%, maintenance or condition 62%, **hidden fees 58%** | [S:apt-q1-2025] |
| 94% of renters call staying within budget essential (2024); 94% want all fees disclosed and 76% prefer fees bundled into the rent (2026); 65% say allowing pets is essential | [S:zillow-2024], [S:zillow-2026] |
| 81% of renters search on a phone, 73% use an app, 77% apply online (US, 2025) | [S:zillow-2025] |
| Business customers rank price as the most-needed information on a website, 29% ahead of availability, which came second; people leave sites that hide prices. Where an exact price cannot be given, show a representative one | [S:nng-price] |
| Hidden fees and prices are among the ten most enduring web-design mistakes | [S:nng-top10] |
| Commercial: priced listings get 12% more leads than "price on application" listings; 69% of commercial searchers say comprehensive details, and 66% transparent pricing, make a listing stand out | [S:rm-commercial-guide] |
| Floor plans: over a third of buyers were less likely to enquire without one, and one in five buyers and tenants would ignore such a listing (UK, about 2015 to 2016; mostly about sales) | [S:rm-floorplan] |

Reading for Block: the surveys are American and British, and vendor-run, so the percentages do not transfer. The
ORDER does, and it matches the Kenyan portal's layout: rent, photos, whether it can still be had, then the detail.

### 3.2 Galleries

- **Thumbnails beat dots.** Every desktop site Baymard benchmarked shows thumbnails, but about three-quarters of
  mobile sites use indicator dots; in testing, users mostly swiped the main image and dots were a poor fallback with
  tiny hit areas [S:bay-thumbs].
- **Say how many photos there are.** A truncated row with no cue makes people assume they have seen everything; a
  "+N" tile, arrows or a partly visible last thumbnail fix it [S:bay-truncate]. NN/g adds that low-contrast dots are a
  weak cue and that a cut-off image signals more content [S:nng-carousel-mobile].
- **Let people zoom.** 40% of the top mobile sites did not support pinch or double-tap on product images
  [S:bay-gestures].
- **Images are where people start.** 56% of first actions on a product page went to the images [S:bay-img-types];
  and people read photos literally, so anything shown reads as included unless text says otherwise
  [S:bay-accessories]. For a rental: a furnished show-flat photo on an unfurnished listing needs a caption.
- **Do not auto-rotate.** Carousels and accordions should change only when the user asks; auto-forwarding annoys and
  gets ignored as advertising, and should never be used on mobile [S:nng-auto], [S:nng-carousel-mobile].
- **People stop early.** On mobile most people stop after three or four slides, so the best photos go first
  [S:nng-carousel-mobile]. Airbnb tells hosts the same: the first five photos are "the most important"
  [F:airbnb-tour].
- **Group and caption.** Airbnb sorts photos by room and lets each carry a caption for "details the photos don't
  show" [F:airbnb-tour], [F:airbnb-photos].
- **Build it in HTML.** web.dev calls loading carousel content with JavaScript "probably the single biggest
  performance mistake"; only the first slide of a static carousel can be the page's largest paint, so that image
  must not be lazy-loaded and may take `fetchpriority="high"` (on one or two images at most); images need explicit
  width and height; transitions should use transforms [S:webdev-carousel], [S:webdev-lcp].

### 3.3 Page structure and sticky elements

- **No horizontal tabs for page sections.** Baymard found tabs the worst-performing layout: on one site 21% of users
  missed content in tabs while looking for it. The two safe layouts are one long page with a sticky table of contents,
  or vertically collapsed sections [S:bay-tabs].
- **Accordions suit phones** when people need only a few of the sections, as with FAQs; they suit long narrative
  text badly, and they need standard HTML controls that work without JavaScript [S:nng-accordion], [S:nng-faq].
- **Sticky bars: small, opaque, high-contrast, no gratuitous animation, never covering content.** Tap targets at
  least 1 cm × 1 cm and text about 16 pt, with as little extra height as possible; first ask whether the bar needs to
  be sticky at all [S:nng-sticky]. Baymard: give a sticky primary button generous white space rather than stretching
  it edge to edge, and never let another overlay cover the primary action [S:bay-buttons], [S:bay-app-ads].
- **Accessibility law of sticky bars.** WCAG 2.2 success criterion 2.4.11 (level AA): a component that receives
  keyboard focus must not be entirely hidden by author-created content; sticky headers and footers are the named
  case, failure F110, and `scroll-padding` is the documented fix [S:wcag-2411], [S:wcag-f110].
- **Target size.** WCAG 2.2 criterion 2.5.8 (AA) asks for 24 × 24 CSS pixels or equivalent spacing; the enhanced
  criterion asks for 44 × 44 [S:wcag-258]. Our project rule is 44.

### 3.4 Forms on phones

- **Fewer fields.** Remove any field whose answer can be derived, collected later or left out [S:nng-forms]; a contact
  form should have three to five fields and ask only what is needed to reply, and it should sit beside a phone number,
  not replace it [S:nng-contact]. Testers were intimidated by forms showing ten to fifteen fields [S:bay-fields].
- **One name field.** 42% of test participants typed their full name into "First name" at least once; people treat
  a name as one thing [S:bay-fields]. web.dev gives the same advice [S:webdev-forms].
- **Labels above the field, never inside it.** Placeholder text used as a label disappears on typing, hurts memory
  and error-checking, and caused severe problems in mobile testing [S:nng-placeholder], [S:bay-inline-labels],
  [S:nng-mobile-input].
- **Say why you want a phone number.** 14% of users said they would abandon if a phone number is simply required,
  and some enter false numbers; a short reason next to the field ("In case we need to contact you about your order")
  mostly resolves it [S:bay-phone].
- **Mark both required and optional fields.** Where only optional fields were marked, 32% of participants missed a
  required one [S:bay-required].
- **The right keyboard.** `type="tel"` brings up the telephone keypad; set `autocomplete` on every field and keep
  field names stable so browsers can fill them; switch off autocorrect for names; never use `type="number"` for a
  phone number [S:webdev-forms], [S:bay-keyboard], [S:nng-mobile-input].
- **When to validate.** Tell people about an error when they leave the field or submit, not while they type, which
  "feels like unwarranted scolding"; put the message beside the field, in words, politely and with the fix. A third
  of sites give no inline validation at all [S:nng-errors], [S:bay-validation].
- **After sending.** When a confirmation does not arrive within minutes, people suspect something went wrong (NN/g,
  writing about order emails) [S:nng-confirm]; its mobile checklist suggests a closing confirmation that shows back
  what the person entered [S:nng-register].

### 3.5 Replies

- 76% of US renters expect to hear back about a listing within 24 hours, 27% within a few hours (AppFolio, May
  2024, n = 2,105) [S:appfolio]. Apartments.com reports 85% expecting a reply within 24 hours and 40% not getting
  one [S:apt-speed].
- NN/g: state each channel's hours and reply time, and only state times you meet [S:nng-contact].
- Zillow's leasing "living lab" found that renters expect quick replies, that keen renters want to choose a viewing
  time themselves, and that a phone call follows up better than other methods [S:zillow-lab].

Reading for Block: a reply-time promise is powerful and dangerous. Rule 11 forbids invented timelines, so the page
makes none unless the owner sets one (open question 3).

### 3.6 Trust

- Four things make a site credible: design quality (no typos or broken links), **up-front disclosure** of everything
  that bears on the customer, content that is comprehensive and current, and links to the wider world. Trust builds
  slowly and one breach undoes it [S:nng-trust].
- Rental fraud is common enough to shape behaviour: 43% of US renters had seen a listing they suspected was
  fraudulent and 6.4% had lost money (2018 survey); the US regulator logged nearly 65,000 rental-scam reports and
  about $65 million lost since 2020, half starting from a fake advert on Facebook [S:aptlist-fraud], [S:ftc-rental].
  The Kenyan picture is in section 4.4.

---

## 4. Kenyan renting norms and the questions they create (study 3)

These norms tell us what a tenant in Nairobi will ask and how generic copy should read. **None of the figures below
may appear on a listing page** unless that listing states them (rule 11).

### 4.1 Norms, the question each one creates, and how the page should handle it

| Topic | What the sources say | The tenant's question | On the page |
|---|---|---|---|
| **Deposit** | No amount is fixed by law; practice is one to two months' rent, sometimes three; the first month is usually paid up front and some landlords ask for more rent in advance (up to six months, in one guide) [S:expat-arrivals], [S:expat-exchange]. A Runda listing: "2 months' deposit + quarterly rent in advance" [F:kpc-list]. Refunds are a known source of dispute [S:standard-deposit] | "How much do I need to move in?" | Show the deposit only when the listing gives a figure. Otherwise list "the deposit and any rent in advance" under *To confirm when you enquire*. Never compute a move-in total |
| **Service charge** | Pays for shared services: security, cleaning, garbage, lifts, generators, water pumps, common-area lighting. "In Kenyan listings it is usually quoted separately from rent: monthly for homes, and often by floor area for offices and shops." Listings say "plus service charge" or "inclusive". For a tenant it "is whatever the tenancy agreement says" [F:kpc-sc] | "Is that the whole monthly cost?" | Put "Service charge included" or "plus service charge of KES … a month" directly under the rent when the listing says so. Say nothing when it does not |
| **Utilities** | Electricity is almost always paid by the tenant through a prepaid meter (tokens); mains water is unreliable, so storage tanks, boreholes and water deliveries are normal; some higher-end flats include water and garbage in the rent [S:expat-arrivals]. Portal amenity lists carry "Prepaid meter", "Borehole", "Inverter", "Solar power" [F:kpc-house]. Advice columns tell tenants to ask whether water is county supply, borehole or tanker, and whether power is prepaid or shared billing [S:nation-queries] | "Will there be water and power, and what do I pay for them?" | A "day to day" block built from the listing's own features (borehole, backup generator, solar, fibre, parking, security). No claims about reliability |
| **Lease length and notice** | Most leases are for a year [S:expat-arrivals]. Homes above the Rent Restriction Act's ceiling (standard rent of KSh 2,500 a month) are governed by the tenancy agreement, so notice periods and rent reviews "come from there"; a Landlord and Tenant Bill that would have changed this "did not become law" (checked 13 Oct 2026) [F:kpc-rra] | "How long must I commit, and how do I leave?" | Show a lease term only when stated with a figure. Generic copy: "The lease length and notice period are set out in the tenancy agreement" |
| **Stamp duty and registration** | Guides give 1% of annual rent for leases up to three years and 2% for longer ones, usually paid by the tenant; sources conflict on the registration threshold (two or three years) and on whether short residential tenancies are stamped at all [S:stamp-duty] | "Are there other costs when I sign?" | Do not state. Too uncertain and not listing-specific. A question for the advisor |
| **Agent and viewing fees** | Guides disagree on who pays the letting fee (often the tenant, sometimes "as much as a month's rent"; elsewhere "paid by the landlord") [S:expat-arrivals], [S:expat-exchange]. Viewing fees of roughly KSh 1,000 to 3,000 are widely reported and resented [S:nation-kisumu], [S:standard-brokers], [S:pulse-scams], [F:bd-2025] | "Will this agent charge me to look?" | Say what Block does, in one plain sentence, **only once the owner confirms it** (open question 1) |
| **Rent in US dollars** | Dollar rents are found in prime offices and in affluent suburbs such as Westlands, Karen, Gigiri, Runda and Muthaiga [F:bd-2023], [S:kenyans-dollars]; Knight Frank says only a few landlords require actual dollar payment and that leases can be paid in shillings at the prevailing rate [F:bd-2023]. A Runda listing: "Usd 3,500 Dollar payments only" [F:kpc-list] | "Which currency do I pay in?" | Already decided: rule 12 and the `bke_rent_usd` rules. State the currency the lease is set in |
| **Furnishing, DSQ, pets** | Standard fields on portals elsewhere: furnished or not, pets allowed [S:p24-listing], [S:rm-listing]. "DSQ" (staff quarters) needs a glossary entry even on a Kenyan portal [F:kpc-house] | "Does it come furnished? Is the DSQ included? Can I bring my dog?" | Facts only when stated. Spell out "DSQ (staff quarters)" once |
| **Verification of the agent** | The Estate Agents Act requires registration for anyone who negotiates lettings as a business; the Estate Agents Registration Board publishes registered agents on its site and in the Kenya Gazette, and tells consumers to ask for a registration certificate and annual practising certificate [S:earb], [S:earb-warning] | "Is this agent real?" | Show Block's registration only if the owner supplies it (open question 2) |

### 4.2 What goes wrong (and so what a page can reassure about)

- **Viewing and "inconvenience" fees.** Business Daily (19 Mar 2025) reports tenants paying Sh2,000 as an
  "appearance fee" plus Sh1,500 as an "inconvenience fee", Sh2,500, and Sh3,650 followed by a request for a
  "subscription fee". One tenant: "When I paid the viewing and inconvenience fees, all the houses the agent had
  shared on Whatsapp suddenly became occupied." House hunters have moved to WhatsApp and Telegram groups instead, one
  community reaching about 21,000 members [F:bd-2025].
- **Deposits sent before viewing.** Fraudsters post attractive photos, pose as the owner or agent, and press for a
  deposit or "booking fee" by M-Pesa before the home has been seen, then go silent; reported in Kilimani,
  Kileleshwa, Jamhuri and Lavington in October 2025 [S:citizen-scams], [S:pulse-scams].
- **Listings that are no longer available.** The portal's report form has a category for it [F:kpc-house], and its
  own index shows homes taking a median 74 days from listing to let in Q3 2026 (119 lets; the middle half took 27 to
  397 days) [F:kpc-ttl]. A listing can therefore be weeks old when a tenant finds it.
- **Extra monthly charges discovered late**: service charge, garbage, parking, generator fuel
  [S:kenyans-charges], [S:nation-queries].

### 4.3 What Block's own rental listings state today (data audit, and my own rough scan; 11 Nov 2026)

**The data audit is the authority** [L:notes/03-data-audit.json]. It was written while this note was in progress;
these are its figures for the facts this note's design depends on (129 listings: 102 homes, 27 commercial):

| Fact | Stated concretely | Left open or only mentioned | Absent | Audit key |
|---|---|---|---|---|
| Deposit | 6 listings | 98 (44 open rows, 54 mentions) | 21 (4 more carry a purchase deposit left from sale text) | `summary.terms.deposit` |
| Lease length | 6 | 106 (56 open rows, 50 mentions) | 17 | `summary.terms.lease` |
| Service charge | 50 | 35 | 44 | `summary.terms.service` |
| Furnishing | 55 | 9 | 65 | `summary.terms.furnish` |
| Availability | 89 (57 of them in a table or list row, the rest in prose or an FAQ) | 14 | 26 | `summary.terms.available` |
| Viewing arrangement | 31 | 93 | 5 | `summary.terms.viewing` |
| An FAQ of the listing's own | 127 (median 8 questions) | — | 2 | `summary.description.faq` |
| Photos | median 11 (homes 13, commercial 3) | 25 listings have fewer than five; 10 have one | 2 have none | `summary.photos.buckets`, `.by_kind` |
| First photo is portrait | 19 listings | — | — | `summary.photos.first_photo_orientation` |
| Photo alt text | 608 of 1,496 photos, usually just the building's name | 888 photos empty; 47 listings have none at all | — | `summary.photos.alt_empty`, `.alt_examples` |
| Features in the theme's taxonomy | 105 listings; chapters "Water & Power" (96), "Security & Access" (85), "Work & Connectivity" (47) | — | 24 (17 of them commercial) | `summary.features` |
| Map pin | 121 listings, but only 29 distinct pins: 104 listings sit on a shared neighbourhood point | — | 8 | `summary.map` |
| Video in the description | 0 | — | — | `summary.description.media` |

**My own rough scan**, made before the audit existed, is kept below for the wording patterns it shows. Method: for
each of the 129 rent-only listings I took the description region of its saved page in `ref/pages-final/` (from the
first to the last WordPress block, plus a little) and ran keyword patterns over the text. Where it differs from the
audit, the audit is right (for example, my pattern for availability caught only "available now" phrasing, and my
heading count missed FAQs written as bold paragraphs).

| In the description | Listings (of 129) |
|---|---|
| Mentions a deposit at all | about 109 (84%) |
| States a deposit with a figure ("2 months") | about 7 (5%) |
| States a lease length with a figure | about 8 (6%) |
| Mentions service charge | about 89 (68%) |
| Says service charge is included | about 27 (20%) |
| Uses "confirmed at enquiry", "on request" or similar | about 105 (81%) |
| Says available now or immediately | about 41 (31%) |
| Mentions backup power / borehole or water supply / parking / security | about 84% / 73% / 83% / 92% |
| Mentions fibre or internet | about 45% |
| Mentions a video or virtual tour | about 13% |

Section headings the rewritten descriptions use: *Rental Terms* (about 54 listings), *Arrange a Viewing* (51),
*Frequently Asked Questions* (about 50) or *FAQ* (about 47; the two may overlap), *Why It Works for Tenants*
(48), *Why It Works for Companies Housing Staff* (48), *Why It Works for Business Occupiers* (12). Other common
headings read like a development brochure: *Project Snapshot* (88), *Finishes and Specifications* (97), *Who
This Development Suits Best* (65). The median description is about 7,500 characters.

From the feed [L:ref/listings.json]: of the 129, 97 carry the label "per month", 17 a per-square-foot label, 12 no
label, 2 "rent on application", 1 "per day"; 9 have no price; 76 have a size; features tagged include Lifts (95),
Backup Generator (93), Ample Parking (88), CCTV Surveillance (84), Borehole Water (71), Fibre Internet Ready (47).

Four things follow for the design:

1. **The terms block will usually be short, and unevenly filled.** Rent, availability, service charge and
   furnishing are often there; deposit and lease almost never. Typical text: "Our team will take you through the deposit, advance
   payment, lease length and renewal terms when you enquire" [L:ref/pages-final/45577.html:4842]; or a list where
   every row reads "confirmed at point of enquiry" [L:ref/pages-final/43543.html:4658]. The page needs a designed
   state for "rent known, terms not", and it must look deliberate, not empty.
2. **The practical features are there to be used**: power, water, internet, parking and security are stated on most
   listings, which is exactly what tenants ask about (4.1), and the theme's feature taxonomy already groups them
   ("Water & Power", "Security & Access"). The exception is commercial space: 17 of the 27 commercial listings have
   no features in the taxonomy, so their specification has to come from the text or be left out.
3. **The description's own headings should not become the page's navigation.** Many say "Project" and
   "Development". Keep the description as one collapsible block under a heading the page supplies ("The home").
4. **Descriptions carry their own contact lines**, and they do not always match the page's contact. In listing
   45577 the description says "Call: 0725 937686" [L:ref/pages-final/45577.html:4946] while the page's WhatsApp link
   goes to 254180711259 [L:ref/pages-final/45577.html:4640]; listing 43543's description gives a phone number and
   an email address after pictograms [L:ref/pages-final/43543.html:4843]. The page should have one contact block that
   follows rule 14, and the description's own "Arrange a Viewing" lines should not sit beside it looking like a
   second, different instruction.

### 4.4 Why WhatsApp and a visible phone number are also trust signals here

In a market where the fear is an agent who takes a fee and disappears, a named firm with a phone number that is
answered, a listing reference to quote, and a message thread the tenant keeps are evidence of a real counterparty.
That is the Kenyan portal's whole contact model [F:kpc-house], and NN/g's general rule that easy-to-find contact
details raise trust says the same [S:nng-trust].

---

## 5. WhatsApp as the main enquiry channel (study 4)

### 5.1 Evidence that it is the main channel

- The Kenyan portal's listing page offers Call and WhatsApp and no form, and reports "Phone and WhatsApp" as its
  enquiry count (5,380 in Q3 2026) [F:kpc-house], [F:kpc-diaspora].
- House hunters organise themselves in WhatsApp communities; one grew to about 21,000 members [F:bd-2025].
  Agencies' listing pages carry a WhatsApp option beside the agent's name [S:pg-kenya]; BuyRentKenya sells listings
  on being reachable "through WhatsApp, phone, and email" [S:brk-private].
- Usage: the Communications Authority's figure for WhatsApp was 53.9% in the first quarter of its 2025/26 year (the
  base of that percentage is not stated in the report I saw), and it is one of Kenya's two most-used platforms; a
  2025 GeoPoll survey ranks it first at 90% of respondents [S:allafrica-ca], [S:star-geopoll].
- About a fifth of rental enquiries on the portal come from abroad, the United States first (25% of enquiries from
  abroad) and the United Kingdom second (8%) [F:kpc-diaspora]. For them WhatsApp is the natural way to reach a
  Kenyan number.

### 5.2 Click-to-chat practice

- **Link format:** `https://wa.me/<number>?text=<URL-encoded message>`. The number is the full international number
  with no plus sign, leading zeros, brackets or dashes (Kenya: `2547…`). The text is editable by the sender before it
  is sent. `https://wa.me/?text=…` with no number lets the sender choose the recipient, which is the form to use for
  "share this home" [S:wa-tyntec], [S:wa-bird], [S:wa-faq]. WhatsApp's own Help Centre article "How to use click to
  chat" is the primary reference; I could not open it, so check its wording before relying on edge cases.
- **Link previews:** WhatsApp builds a preview card from the page's `og:title`, `og:description`, `og:url` and
  `og:image`. Meta's documentation says the image should be under 600 KB and an absolute URL, and that the `<head>`
  carrying the tags must fall within the first 300 KB of the HTML; community reports say images over about 300 KB are
  sometimes dropped; previews are cached [S:wa-preview]. Because Block's prefilled message ends with the listing's
  address, the advisor and the tenant both see the home's photo and title in the thread, provided the listing's Open
  Graph image is small enough. Rank Math owns those tags (rule 7), so this is a content check, not a build task
  (open question 9).
- **The message Block already uses** (owner-approved rent wording):
  "Hi Block, I'm interested in renting {name} ({rent}). Is it still available, and when could I arrange a viewing?
  Ref W-{id}. {url}" [L:ref/dev-v1.9.1.php:1905]; and after a form has been sent, "Hi Block, I've just sent an
  enquiry about renting {name} on block.ke. When could I arrange a viewing? Ref W-{id}. {url}"
  [L:ref/dev-v1.9.1.php:1904]. Compare the portal's generic "Hello, I'd like to enquire about your property on Kenya
  Property Centre. {url}" [F:kpc-house]: Block's version already asks the two questions that matter and carries a
  reference. Keep it.

### 5.3 Recommendations

1. One message everywhere (hero, sticky bar, FAQ, viewing panel, thank-you state), built on the server so the link
   works without JavaScript. Order: greeting, home and rent, the two questions, the reference, the address last (so
   the preview attaches).
2. A real link (`<a href="https://wa.me/…">`) with `rel="noopener"` and `data-blkct="1"` (rule 14). Visible label
   "WhatsApp"; accessible name "Message Block on WhatsApp about {name}".
3. Keep the phone number visible as text and as a `tel:` link next to it. Not everyone on a desktop has WhatsApp
   Web open, and a number in plain sight is itself reassurance (4.4).
4. Use the message-only form for sharing: "{name}, {rent sentence}. {url}".
5. No floating WhatsApp bubble on top of the sticky bar, and the site's chat float kept clear of it (12.2). One
   bar, one WhatsApp button.
6. No reply-time promise beside the button unless the owner sets one (open question 3).
7. Write phone numbers so they work from abroad: the `tel:` link in international form (`tel:+254…`), the displayed
   number as Block writes it today.

---

## 6. Commercial lettings: what occupiers look for (study 5)

### 6.1 Evidence

| What | Detail | Source |
|---|---|---|
| A Nairobi office listing in full | "Price: Kes110 per SQ FT. Service charge : Kes 22 per SQ foot. Parking: 2 Bays for 1,000 SQ.FT @ kes 10,000 + VAT per bay"; "Space available: From 3,000 - 50,000 SQ.FT."; "Auxiliary power via backup 440 kva generators"; "2.7 meter clear headroom"; three lifts; fibre backbone; 255,000 litres of water storage; provision for air conditioning; access control | [F:kpc-office] |
| A Nairobi warehouse listing | 5,500 sq ft; "8-metre truss height"; "Double dock doors for loading and offloading"; mezzanine for an office; "KSh 280,000 + VAT"; three parking spaces | [F:kpc-warehouse] |
| Lease terms stated in Nairobi office adverts | Rent "per sq ft + VAT"; service charge per sq ft; "Lease Term: 5 years and 3 months"; "Refundable Security Deposit: Equivalent to 3 months' rent"; "Rent Payment: Payable quarterly in advance"; annual escalation such as 7.5%; a fit-out period of one or two months with rent waived but service charge payable; parking priced per bay | [S:brk-office-1], [S:brk-office-2] |
| Warehouse specifications that adverts give | Clear height to eaves (6 to 10 m), floor slab specification, three-phase power, dock height, loading doors, truck access, yard; rent per sq ft plus VAT, sometimes a separate service charge per sq ft | [S:brk-warehouse], [S:kpc-warehouse-2] |
| How a mature commercial portal lays it out | Size range and rate in the header with a unit switch; a space table (Space, Size, Term, Rental Rate, Space Use, Condition, Available); Highlights; Property Facts | [S:loopnet] |
| What makes a commercial listing work | Priced listings get 12% more leads than "POA"; searchers want comprehensive details (69%) and transparent pricing (66%); key facts include grade and condition | [S:rm-commercial-guide] |
| Tenant-side checklists | Compare total occupancy cost, not headline rent; how the area is measured; what the service charge covers and whether it is capped; parking; backup power; fit-out consent and reinstatement; break, renewal and rent-review terms | [S:forsters], [S:brigade], [S:langham] |
| Why "5 years 3 months" | A written tenancy of a shop, hotel or catering establishment for five years or less is a "controlled tenancy" under Cap 301, with tribunal protection; landlords therefore grant terms a little over five years, and commercial leases generally run five to six years. Check with an advocate before quoting this to anyone | [S:cap301], [S:bd-cap301] |
| VAT | Letting residential premises is exempt; letting commercial premises is taxable at the standard rate (16%) according to law-firm commentary. I did not see KRA's own wording | [S:vat-commercial] |
| Market backdrop | Prime Grade A office rents steady at about US$13 per sq m a month; Grade A occupancy about 85% in June 2026; occupiers consolidating into smaller, more efficient space | [S:kf-h1-2026], [S:bd-office] |
| Block's own office text | A "Rent and Lease Structure" table (Office, Monthly Rent, Service Charge, Parking), then fifteen lease terms "confirmed at the point of enquiry": deposit, payment frequency, duration, escalation, renewal, fit-out period and concessions, legal fees, stamp duty, utility deposits, VAT, signage, reinstatement, extra parking, handover | [L:ref/pages-final/36809.html:5021], [L:ref/pages-final/36809.html:5033] |

### 6.2 What it means for the commercial variant

- **Two headline numbers, not one:** the rate ("KES 130 per sq ft a month", rule 12) and the size available. A
  monthly total appears only where the listing states one.
- **Cost lines in the order an occupier adds them up:** rent, service charge, parking, then "plus VAT" exactly as
  the listing words it. Nothing computed.
- **A space table** when the listing has more than one suite or size (LoopNet's pattern, and Block's own table in
  36809).
- **A specification list** in place of lifestyle amenities: backup power (with kVA if given), lifts or loading doors,
  clear height, floor loading, power supply, condition (fitted, or shell and core), parking ratio, connectivity,
  water storage, access control, fire safety.
- **Lease terms as a checklist.** Almost none are stated, and Block's text already lists them. Show stated terms as
  facts and the rest under "To confirm when you enquire".
- **Occupier wording** (rule 13): "space", "premises", "your team"; no bedrooms; "Arrange a viewing" still works.
- **Questions business occupiers ask**, taken from the headings Block's commercial listings already use: minimum
  lease term, parking, backup power, leasing from abroad.

---

## 7. Deliverable A. The 12 things a renter needs on the first screen, ranked

"First screen" means what is visible before any scroll: about 360 × 560 px of page on a common phone once the
browser and the site header have taken their share, and about 1280 × 560 px on a laptop. The ranking is by how much
the item decides whether the visitor stays; the evidence column says why. Figures in the wording column show the
format only; on a page they come from the listing or they do not appear.

| # | What | Wording and rule | Phone (first screen) | Desktop (first screen) | Why |
|---|---|---|---|---|---|
| 1 | **The rent, with period and currency, in full** | "KES 135,000 a month"; "KES 130 per sq ft a month"; "Rent on request" (rule 12). Never "KES 135K", never "Price" | Largest text after the H1, directly under it; repeated in the bottom bar | Top of the right-hand summary card | Price is ranked first by renters [S:apt-unit]; hidden prices drive people away [S:nng-price]; the portal puts rent beside the title [F:kpc-house] |
| 2 | **A real photo of this home, and how many more** | First photo, eager; "1 / 19" counter; "View all 19 photos" | Edge to edge at the top, about 3:2, capped near 45% of the viewport height; swipeable | Mosaic on the left: one large photo and two to four smaller ones, with a "+N photos" tile | Photos rank second [S:apt-unit]; 53% call missing real photos a deal-breaker [S:apt-q4-2025]; signpost hidden photos [S:bay-truncate] |
| 3 | **What it is and where** | One H1 (name or type); status line "For rent · Kilimani, Nairobi" | Status line then H1, two lines at most | Same, above the rent | Visitors arrive from search, adverts and shared links and must confirm they are in the right place; 87% start without a specific property in mind [S:apt-q4-2025] |
| 4 | **Availability** | "Available now" or "Available from 1 December 2026", only from a stated row [L:ref/README-wordpress.md:181]; otherwise say nothing and let the action ask | One short line under the rent | Line in the summary card | Ranked third by renters [S:apt-unit]; "no longer available" is a named complaint [F:kpc-house]; listings linger for weeks [F:kpc-ttl] |
| 5 | **What the rent includes** | "Service charge included"; "plus service charge of KES 12,000 a month"; only when the listing states it | Same line as the rent or the one below | Under the rent | Fee disclosure wanted by 94% [S:zillow-2026]; hidden fees a deal-breaker for 58% [S:apt-q1-2025]; Kenyan listings say "plus" or "inclusive" [F:kpc-sc] |
| 6 | **One obvious next step: "Arrange a viewing"** | Primary button (rule 13). Variant label "Check availability" where no availability is stated | Right-hand button of the fixed bottom bar, present from the first paint | Primary button in the summary card, with the two-field form directly below it | A viewing is the conversion for a rental; keen renters want to fix a time [S:zillow-lab] |
| 7 | **WhatsApp, with the message ready** | "WhatsApp" + icon; prefilled text [L:ref/dev-v1.9.1.php:1905]; `data-blkct="1"` | Middle of the bottom bar | Secondary button beside the primary | The local contact norm [F:kpc-house]; about a fifth of rental enquiries come from abroad [F:kpc-diaspora] |
| 8 | **Key facts** | Bedrooms, bathrooms, size, furnished or unfurnished (when stated), parking | One row of three or four facts under the availability line | Row in the summary card | The fixed fact block on every portal [F:kpc-house], [S:rm-listing] |
| 9 | **Deposit and lease length, when the listing states them** | "Deposit: 2 months' rent"; "Lease: 12 months". Hidden when not stated (rule 11) | Not on the first screen unless stated; then one line under the facts | In the summary card under the facts | The letting-details block of mature portals [S:rm-listing], [S:p24-listing]; "how much to move in" is the next question after rent (4.1) |
| 10 | **Who is letting it, and a number** | "Block Real Estate" and the advisor's name where the listing has one; phone as text and `tel:` link (rule 14) | Just below the facts; may fall to the first swipe | Foot of the summary card | Contact details up front raise trust [S:nng-trust]; calling is the quickest route [F:kpc-faq] |
| 11 | **Day-to-day essentials** | Up to four from the listing's features: "Backup generator", "Borehole water", "Fibre ready", "Lift" | Chips at the first swipe | Chips under the gallery | What Nairobi tenants ask about (4.1); stated on most Block listings (4.3) |
| 12 | **Reference, share and a way back** | "Ref W-45577"; Share (WhatsApp first); "Homes for rent in Kilimani" | Small line with the breadcrumb or under the H1; share icon on the photo | Breadcrumb row above the H1; share beside it | The reference ties a call or chat to a listing [F:kpc-house]; renters compare about ten homes [S:apt-q4-2025]; a rental is often chosen with someone else |

**How often each conditional item will actually appear** (data audit, 129 listings): availability on 57 to 89
listings depending on whether only table and list rows count; "what the rent includes" on about 50; furnished or
unfurnished on 55; deposit and lease on 6 each [L:notes/03-data-audit.json] (`summary.terms`). The first screen must
look complete without items 5 and 9.

**Phone, first screen, top to bottom:** photo with counter and share · status line · H1 · rent · includes and
availability · facts row · (fixed bottom bar: rent | WhatsApp | Arrange a viewing). Items 9 to 12 arrive with the
first swipe. The bottom bar is there from the start because, on a phone, it is the only place the two actions fit
above the fold.

**Desktop, first screen:** breadcrumb and share across the top; gallery mosaic on the left (about 60%); a summary
card on the right holding status, H1, rent, includes, availability, facts, stated deposit and lease, then the form
(name, phone, button) with WhatsApp and the phone number beneath.

**Commercial variant (same ranking, different facts):** 1 the rate per sq ft a month, with "plus VAT" as stated;
2 the size available (or range); 3 photos; 4 what and where ("Office space to let · Westlands"); 5 availability;
6 service charge and parking as stated; 7 Arrange a viewing; 8 WhatsApp; 9 condition (fitted or shell and core) and
floor; 10 lease term if stated; 11 backup power, lifts or loading, parking ratio; 12 reference, share, other space in
the building.

---

## 8. Deliverable B. Recommended page anatomy

One long page, sections in a fixed order, each a plain heading (no horizontal tabs [S:bay-tabs]). On desktop a slim
"on this page" bar may stick under the site header once the hero has scrolled away; on phones the bottom bar does
that job. Sections with nothing to show are omitted, never printed empty.

### 8.1 Homes (apartments, houses, villas, studios)

| # | Section | What it holds | Source of content | Notes |
|---|---|---|---|---|
| 1 | **Top line** | Breadcrumb (Home / For rent / Area / Name), Share, reference | Taxonomy terms, post ID | On phones: one "‹ Homes for rent in Kilimani" link and the reference |
| 2 | **Hero** | Gallery; status line; H1; rent; what it includes; availability; key facts; Arrange a viewing; WhatsApp; phone | Listing fields and stated rows | Section 7. No form overlaying the photo |
| 3 | **Rent and terms** | The rent sentence again; a short list of **stated** terms (deposit, lease, service charge, furnishing, availability, viewing); then **"To confirm when you enquire"**: the terms the listing does not state, as a checklist of questions | The listing's glance rows and its *Rental Terms* section (the rules ported from [L:ref/README-wordpress.md:157]) | Replaces payment plan and mortgage. No arithmetic. This is where the page is most unlike a sale page |
| 4 | **Arrange a viewing** | Two or three lines on what happens; the form; WhatsApp and call | Generic copy (section 10) | On desktop the form already sits in the hero card; this section then carries the explanation and a second button. On phones this is where the bottom bar's button lands |
| 5 | **The home** | The listing's description: opening paragraph in view, the rest behind "Read the full description" (`<details>`, text in the HTML, rule 7) | Post content, through `wp_kses_post` | Long narrative is poor accordion material [S:nng-accordion], but the median description is about 7,500 characters, so collapse after the opening and keep its own subheadings inside |
| 6 | **Living here day to day** | Grouped features: *Water and power* (borehole, storage, backup generator, solar, inverter), *Internet*, *Parking*, *Security and access*, *Lifts and laundry*; then *Shared amenities* (pool, gym, play area) | The listing's feature terms | Essentials before leisure. The portal's "Power & Utilities" and "Security & Safety" groups are the model [F:kpc-house] |
| 7 | **Homes available in this building** (only when the listing covers several sizes or has sibling listings) | A small table: home, size, rent a month, link | The listing's own table or sub-unit listings (fixtures 36499, 36506, 36502; 45577 and 45538) | Replaces "residences and price list". Never a form to "get" the list |
| 8 | **Around this home** | Map; the places the listing names (schools, shopping, healthcare, transport stages); the listing's own caution about travel times | Listing text and coordinates | A static map image or a map that loads on request. 121 listings have a pin, but 104 of them share a neighbourhood point [L:notes/03-data-audit.json] (`summary.map`), so show the area, not a house-number pin. For living, not for investment |
| 9 | **Questions tenants ask** | The listing's own FAQ (127 of 129 have one, median 8 questions); a generic answer only for a gap (section 11.3) | Listing FAQ block; generic set | `<details>` accordions suit FAQs [S:nng-accordion], [S:nng-faq]. Place above "Renting with Block" so questions are answered before reassurance is offered |
| 10 | **Renting with Block** | Three "why" points (already approved [L:ref/dev-v1.9.1.php:278]); "How renting works" in four steps; "Before you pay" advice (section 11) | Settings and generic copy | Calm, factual. No guarantees |
| 11 | **Similar homes to rent** | Three or four cards: photo, name, area, beds, rent "a month"; "See all homes for rent in {area}" | Same area, similar size and rent | Rent cards never link to sale pages [L:ref/plan.md:724]. Renters compare about ten homes [S:apt-q4-2025] |
| 12 | **Closing action** | "Arrange a viewing of {name}", WhatsApp, call, reference | — | A last, quiet repeat; no new claims |
| 13 | **Disclaimer** | The approved tenant disclaimer [L:ref/dev-v1.9.1.php:283] | Setting | Small type, full width |
| — | **Fixed elements** | Phone: bottom bar (rent, WhatsApp, Arrange a viewing). Desktop: sticky summary card or slim section bar | — | One sticky element per breakpoint (section 12.2) |

Never on this page: mortgage or payment calculators, payment plans, price lists, floor-plan offers, investor view,
"buy" wording, sale or "Ready" badges, completion dates (PROJECT.md section 1), pop-ups that ask for details in
exchange for information, urgency counters.

### 8.2 Commercial space (offices, warehouses, retail)

| # | Section | What it holds | Notes |
|---|---|---|---|
| 1 | **Top line** | Breadcrumb (Home / For rent / Commercial / Area / Name), Share, reference | — |
| 2 | **Hero** | Gallery; status "To let · Westlands, Nairobi"; H1; **rate per sq ft a month**; **size available**; service charge, parking and "plus VAT" as stated; availability; Arrange a viewing; WhatsApp; phone | Two headline numbers (6.2). "Rent on request" where there is no rate (fixture 35986) |
| 3 | **Space and costs** | Table of suites or units: space, size, rent, service charge, parking, condition, availability. Then "To confirm when you enquire": deposit, payment frequency, lease length, escalation, renewal, fit-out period, VAT treatment, legal fees and stamp duty, signage, reinstatement, handover | Stated values only. The checklist is Block's own list [L:ref/pages-final/36809.html:5033] |
| 4 | **Arrange a viewing** | What happens next; form; WhatsApp; call | Same form; wording "your team", "the space" |
| 5 | **The space** | Description: opening paragraph, the rest collapsed | Post content |
| 6 | **Specification** | *Power* (backup, kVA, three-phase), *Access* (lifts, loading doors, dock height, truck access), *Dimensions* (floor plate, clear or eaves height, floor loading), *Condition* (fitted, shell and core), *Parking* (ratio, price per bay), *Connectivity*, *Water*, *Security and fire safety* | From the listing's features and text only; 17 of 27 commercial listings have no feature terms, so this section is often text-derived or omitted. This replaces "Living here day to day" |
| 7 | **Other space in this building** | Sibling listings or the listing's own unit table | — |
| 8 | **Location and access** | Map; roads, public transport, nearby business amenities as the listing names them | — |
| 9 | **Questions business occupiers ask** | Listing FAQ first (minimum lease term, parking, backup power, leasing from abroad) | — |
| 10 | **Leasing with Block** | Why points; "How leasing works": enquiry, viewing, offer and terms, lease, handover; "Before you pay" advice | Generic; no timelines |
| 11 | **Similar premises** | Cards with size and rate per sq ft a month | — |
| 12 | **Closing action and disclaimer** | As for homes | — |

Never on a commercial page: bedrooms, "home" wording, family amenities presented as selling points, any yield or
investment language.

---

## 9. Deliverable C. Patterns to borrow, and patterns to avoid

"Seen" tells you how I know: **F** fetched today, **S** search result only.

### 9.1 Borrow

| # | Pattern | One-line description | Source URL | Seen |
|---|---|---|---|---|
| 1 | Rent beside the title | The rent and "per month" sit directly under the H1 and area, before anything else | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house | F |
| 2 | Call and WhatsApp as the two primary actions | Two buttons under the rent; no form needed to make contact | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house | F |
| 3 | Prefilled WhatsApp message that carries the listing address | The agent sees which home is meant without asking | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house | F |
| 4 | Numbers shown as text in a "choose a number" panel | Works on a desktop, where a `tel:` link often does nothing | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house | F |
| 5 | Reference and date under the title | "Ref ID" and "Posted" give something to quote and a sense of age | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house | F |
| 6 | Amenities grouped by what they do | "Power & Utilities", "Security & Safety" ahead of leisure items | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house | F |
| 7 | Glossary pop-over for local terms | "DSQ" explained in place | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house | F |
| 8 | A carefully worded trust definition | "Verified" is defined, and followed by "we always recommend the usual due diligence before making any payment" | https://kenyapropertycentre.com/frequently-asked-questions | F |
| 9 | Similar homes scoped to area and type | Three cards and "See all in Karen" | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house | F |
| 10 | Letting-details block | A fixed short list: available date, deposit, minimum term, let type, furnishing | https://www.rightmove.co.uk/properties/87464886 | S |
| 11 | Overview fields for a rental | Occupation date, lease period, deposit requirements, pets allowed, furnished | https://www.property24.com/to-rent/bothasrus/despatch/eastern-cape/7213/117707526 | S |
| 12 | "What the rent includes" in the headline | Total monthly price with a fees breakdown separating required, optional and usage-based costs | https://www.zillow.com/rentals-network/pricing-transparency | S |
| 13 | A label that says the price is complete | "Total Monthly Price" badge beside the rent; "Plus Fees" when it is not | https://www.apartments.com/grow/learning-center/price-transparency-nationwide | S |
| 14 | A plain fee notice on the listing | One sentence telling renters what they cannot be charged, plus a report link | https://streeteasy.com/blog/fare-act-nyc | S |
| 15 | Five strong photos first, then all | The first five photos shown prominently; the first is the large one | https://www.airbnb.com/help/article/3509 | F |
| 16 | Photos grouped by room, with captions | A "photo tour" sorted into rooms; captions for "details the photos don't show" | https://www.airbnb.com/resources/hosting-homes/a/selecting-the-best-photos-of-your-space-367 | F |
| 17 | Thumbnails, not dots | Thumbnails show what the other photos are; dots do not | https://baymard.com/blog/always-use-thumbnails-additional-images | S |
| 18 | "+N" tile for hidden photos | Signals that more photos exist than the row shows | https://baymard.com/blog/truncating-product-gallery-thumbnails | S |
| 19 | Pinch and double-tap zoom in the viewer | Expected by mobile users; missing on 40% of sites tested | https://baymard.com/blog/mobile-image-gestures | S |
| 20 | One long page or collapsed sections | The two layouts that tested safely for detail pages | https://baymard.com/blog/avoid-horizontal-tabs | S |
| 21 | Accessible carousel semantics | Region labelled as a carousel; slides labelled "N of M"; no rotation without the user | https://www.w3.org/WAI/ARIA/apg/patterns/carousel/ | S |
| 22 | Carousel content in the HTML | Slides are markup, not script; only the first image is prioritised | https://web.dev/articles/carousel-best-practices | S |
| 23 | Scroll padding under sticky bars | `scroll-padding` keeps focused elements clear of a fixed header or footer | https://www.w3.org/WAI/WCAG22/Understanding/focus-not-obscured-minimum.html | S |
| 24 | A reason beside the phone field | One short line on what the number is for | https://baymard.com/research-articles/explain-phone-number-field | S |
| 25 | A single "Full name" field | One field, one `autocomplete="name"` | https://baymard.com/blog/mobile-form-usability-single-input-fields | S |
| 26 | Form beside, not instead of, a phone number | Three to five fields; stated hours and reply times only if kept | https://www.nngroup.com/articles/contact-us-pages/ | S |
| 27 | FAQ in the visitor's own words | Questions phrased as people ask them, grouped, kept current | https://www.nngroup.com/articles/faqs-deliver-value/ | S |
| 28 | Accompanied viewings stated as policy | "We will accompany the applicants on each viewing" | https://knightfrank.co.uk/residential/faqs/letting | S |
| 29 | Space availability table (commercial) | One row per suite: size, term, rate, use, condition, available | https://www.loopnet.com/Listing/1200-main-st-irvine-ca/4096810/ | S |
| 30 | The occupier's cost block (commercial) | Rate per sq ft, service charge per sq ft, parking ratio and price per bay, generator capacity in one place | https://kenyapropertycentre.com/for-rent/commercial/offices/nairobi/westlands/56492-8331-sq-ft-office-space-in-westlands | F |
| 31 | Always show a price (commercial) | Priced listings draw more leads than "POA" | https://hub.rightmove.co.uk/content/uploads/2026/01/Ultimate-Commercial-Property-Listing-Guide-2.pdf | S |
| 32 | Lease terms written out (commercial) | Term, deposit and payment frequency stated in the advert | https://www.buyrentkenya.com/listings/5078-ft2-office-for-rent-westlands-area-4034290 | S |

How each is adapted to Block's rules: 10 and 11 show only stated values (rule 11); 12 and 13 become the
service-charge phrase; 14 and 28 become trust lines only after the owner confirms them; 5 uses "Ref W-{id}" and no
date unless a truthful one exists (9.2, item 9); 8 means defining any trust word we use, or not using it.

### 9.2 Avoid

| # | Pattern | Why | Evidence |
|---|---|---|---|
| 1 | A gallery whose images exist only after script runs | No photos without JavaScript; breaks rule 9; slower first paint | Seen on [F:kpc-house]; https://web.dev/articles/carousel-best-practices (S) |
| 2 | Auto-rotating photos | Annoys, is ignored as advertising, moves the largest paint | https://www.nngroup.com/articles/auto-forwarding/ (S) |
| 3 | Dots as the only sign of more photos | Tiny targets, no information | https://baymard.com/blog/always-use-thumbnails-additional-images (S) |
| 4 | Horizontal tabs for sections | Content in tabs gets missed | https://baymard.com/blog/avoid-horizontal-tabs (S) |
| 5 | Sale-page furniture on a rental | "Get the price list and floor plans", "When would you like to buy?", a "Mortgage" link and "Ready to move in" all appear today on a rental shown in the development layout | [L:ref/pages-final/45577.html:4640] |
| 6 | Abbreviated rent | "KES 135K" reads as a sale price teaser; the rent must be exact and carry its period | [L:ref/pages-final/45577.html:4640]; rule 12 |
| 7 | "Ask agent" or "to be confirmed" printed as a value | Looks like data, tells nothing; rule 11 forbids it | https://www.rightmove.co.uk/properties/87464886 (S) |
| 8 | "Rent on application" when the rent is known | Fewer enquiries, less trust | https://www.nngroup.com/articles/show-price/ (S) |
| 9 | Dates that go stale | Occupation dates already in the past were visible on live listings; a "posted" or "updated" date that reflects an edit, not a check, misleads | https://www.property24.com/to-rent/bothasrus/despatch/eastern-cape/7213/117707526 (S) |
| 10 | View counts and demand counters | "5 views · last 30 days" shows weak demand, and invented urgency would break rule 11 | [F:kpc-house]; https://biggerpockets.com/forums/21/topics/962974-contacts-meaning-on-zillow-for-a-rental (S) |
| 11 | Placeholder text as the label | Disappears on typing; hurts checking and correction | https://www.nngroup.com/articles/form-design-placeholders/ (S) |
| 12 | A required phone number with no reason given | Abandonment and false numbers | https://baymard.com/research-articles/explain-phone-number-field (S) |
| 13 | A Kenyan-only number hint | "07XX XXX XXX" as the only example tells someone abroad the form is not for them; about a fifth of rental enquiries come from abroad | [L:ref/dev-v1.9.1.php:1983]; [F:kpc-diaspora] |
| 14 | Errors while typing, or errors by colour alone | Feels like scolding; unreadable for some | https://www.nngroup.com/articles/errors-forms-design-guidelines/ (S) |
| 15 | A sticky bar that covers the focused field or the form's own button | Fails WCAG 2.2 criterion 2.4.11 and blocks the task | https://www.w3.org/WAI/WCAG22/Techniques/failures/F110 (S) |
| 16 | Two bars at once | The theme's agent bar plus ours; a chat bubble over the bar | [L:ref/plan.md:701] |
| 17 | Email-only enquiry | "The quickest way to reach them is to call … rather than waiting on email" | https://kenyapropertycentre.com/frequently-asked-questions (F) |
| 18 | A time-slot picker that implies a confirmed booking | Block confirms availability by hand; a slot would be a promise | Rule 11; https://www.zillow.com/rentals-network/how-to-convert-more-leads-to-tours (S) for the appeal of self-scheduling |
| 19 | Undefined trust words | "Verified", "safe", "guaranteed", "no hidden fees" without a definition and the facts to back them | https://kenyapropertycentre.com/frequently-asked-questions (F); rule 11 |
| 20 | A second, different contact instruction inside the description | A phone number in the text that differs from the page's contact block | [L:ref/pages-final/45577.html:4946] |

---

## 10. Deliverable D. Form recommendations

Starting point: the viewing form the owner approved for the classic rental page, with two fields and three move-in
choices [L:ref/dev-v1.9.1.php:1979] to [L:ref/dev-v1.9.1.php:1986]. The research supports keeping it that small.
"Approved" below means the wording exists in v1.9.1; "Proposed" is new and needs the copy owner's eye.

### 10.1 Fields

| Field | Label | Attributes | Required | Status |
|---|---|---|---|---|
| Name | "Full name" | `type="text" name="name" autocomplete="name" autocapitalize="words" spellcheck="false" maxlength="80"` | Yes | Approved. One field, not two [S:bay-fields], [S:webdev-forms] |
| Phone | "Phone / WhatsApp" | `type="tel" name="phone" inputmode="tel" autocomplete="tel" maxlength="40"` | Yes | Approved. Accept spaces, "+", brackets and dashes; 9 to 15 digits, as now [L:ref/dev-v1.9.1.php:9922] |
| Move-in | "When would you like to move in?" with "This month", "1–3 months", "Later" | Radio inputs in a `<fieldset>` with a `<legend>`; each choice at least 44 px tall | No | Approved [L:ref/dev-v1.9.1.php:7830]. In the hero card ask it after sending, to keep the first screen short; in the viewing section ask it before |
| Honeypot | none (hidden) | A field name browsers' AutoFill ignores (rule 8) | — | — |
| Context | hidden | Listing ID, which form, campaign tags | — | — |

Do not add: email (make it optional only if the owner wants it, and ask after sending), a message box, a budget,
a date picker, a CAPTCHA. Each extra field costs completions [S:nng-forms], and the advisor's first call collects the
rest.

Commercial: the same two fields. If the lead format allows it, one optional field after sending: "Company
(optional)". Whether the saved lead row has room for it is unknown to me; see the leads note.

### 10.2 Wording and microcopy

| Place | Text | Status |
|---|---|---|
| Heading | "Arrange a viewing" | Approved (rule 13) |
| Lead-in | "Tell us when you'd like to move in. We'll confirm that {name} is still available and arrange a time for you to see it." | Approved [L:ref/dev-v1.9.1.php:1979] |
| Phone hint, under the label and above the field | "We'll call or WhatsApp you on this number. Outside Kenya? Include your country code." | Proposed. A reason beside the phone field cuts abandonment [S:bay-phone]; the second sentence serves visitors abroad [F:kpc-diaspora]. Replaces the placeholder "07XX XXX XXX" [L:ref/dev-v1.9.1.php:1983], which should go or become part of the hint [S:nng-placeholder] |
| Optional mark | "(optional)" after the move-in legend | Proposed. Mark optional as well as required [S:bay-required]; with only two required fields, marking the one optional group is enough |
| Button | "Arrange a viewing"; where no availability is stated the hero may use "Check availability" | Approved (rule 13). Never "Submit" or "Send" |
| Under the button | "We'll reply by WhatsApp or phone. By sending this you agree to be contacted about {name}." + link "Privacy policy" | Approved [L:ref/dev-v1.9.1.php:7900]. It also meets the duty to say that data is collected and why (Data Protection Act 2019, section 29) [F:dpa]. Do not reuse the number for marketing without express consent (section 37) [F:dpa] |
| Beside the form | "WhatsApp us" and "Call {number}" | Approved [L:ref/dev-v1.9.1.php:1988] |

### 10.3 Behaviour and error handling

1. **Works without JavaScript**: a normal POST to the REST route, answered with a redirect back to the listing and
   a server-drawn result (the pattern in [L:ref/plan.md:782]). With JavaScript, the same route is called in place.
2. **Validate when the visitor leaves a field and again on sending**, never while typing [S:nng-errors],
   [S:bay-validation].
3. **Messages sit under the field they concern**, in words, with an icon as well as colour, linked by
   `aria-describedby`, and the field takes `aria-invalid="true"`. On a failed send, focus moves to the first field in
   error. For the no-JavaScript round trip, print a short summary above the form that links to each field
   [S:nng-errors].
4. **Wording of errors** (the first two exist; the proposals are more specific, as NN/g asks):

   | Case | Now | Proposed |
   |---|---|---|
   | Name missing or one character | "Please enter your name." [L:ref/dev-v1.9.1.php:9921] | Keep |
   | Phone missing or not 9 to 15 digits | "Please enter a valid phone number." [L:ref/dev-v1.9.1.php:9922] | "Please enter a phone number we can reach you on. Include the country code if you are outside Kenya." |
   | Too many attempts | "Too many requests from this connection. Please WhatsApp us directly." [L:ref/dev-v1.9.1.php:8050] | Keep, and make "WhatsApp us" the link |
   | Network or server failure | — | "That didn't send. Please try again, or message us on WhatsApp." with the WhatsApp link |

5. **Never lose what was typed**: keep field values after any error and after a failed send (rule 8 already saves
   the enquiry before email or Zoho, so a slow step cannot lose it).
6. **While sending**: disable the button, change its label to "Sending…", and ignore a second tap.
7. **Keyboard and autofill**: labels are real `<label>` elements above the fields; input text is at least 16 px
   (below that, iOS Safari zooms the page on focus: general practice, not sourced here); `enterkeyhint="send"` on
   the last field; nothing covers the field or the button while the keyboard is open, so hide the bottom bar
   while a form field has focus [S:webdev-forms], [S:wcag-2411].
8. **Abuse**: honeypot and rate limit only. No CAPTCHA.

### 10.4 Success state

| Element | Text or behaviour | Status |
|---|---|---|
| Where | The form is replaced in place by a confirmation with `role="status"`, and focus moves to it | Approved pattern [L:ref/dev-v1.9.1.php:1976] |
| Heading | "Thank you, {first name}." | Approved pattern |
| Body | "We have your request to view {name}. An advisor will call or WhatsApp you on {number as typed} to confirm it is still available and arrange a time." | Proposed. Shows the number back so a typo is caught [S:nng-register] and makes no time promise. The approved line says "in touch shortly" [L:ref/dev-v1.9.1.php:1976]; "shortly" is a soft timeline, so either keep it knowingly or drop it (open question 3) |
| Correction | "Not your number? Send it again." (reopens the form with the values kept) | Proposed |
| Next action | "Continue on WhatsApp" with the after-enquiry message [L:ref/dev-v1.9.1.php:1904] | Approved |
| Follow-up | If move-in was not asked, ask it here as an optional question | Approved pattern (README, *Forms* [L:ref/README-wordpress.md:158]) |
| Still visible | The phone number and the rent | — |
| Without JavaScript | Same content drawn by the server on return; that response is not cached and conversions are counted once | [L:ref/plan.md:782] |

---

## 11. Deliverable E. Trust and safety copy that is true for an agency and promises nothing

### 11.1 Principles

1. **Facts before adjectives.** The strongest trust signals on the pages studied are plain facts: an exact rent, a
   reference, a named firm, a phone number, a way to report a problem [F:kpc-house], [S:nng-trust].
2. **Advice to the reader is always safe to print; a promise about Block is not**, unless the owner has confirmed it
   for every listing (rule 11). So the copy below tells the tenant what to do, and says what Block does only in words
   the owner has already approved.
3. **Say the same thing everywhere.** The rent phrase on the page, in the WhatsApp message and in the lead must match
   [L:ref/README-wordpress.md:162]. One contact block (rule 14); no second number in the body text (4.3).
4. **No trust words without a definition.** The portal defines "Verified" and still adds "we always recommend the
   usual due diligence before making any payment" [F:kpc-faq]. Block has no such programme, so the word stays off.

### 11.2 Copy that can be used now

**Why Block** (owner-approved, unchanged) [L:ref/dev-v1.9.1.php:278]:

- *Advice, not pressure.* "We compare rentals, streets and rents across the area side by side, so you choose with
  clarity."
- *Checks before you commit.* "We help you check the tenancy agreement, the deposit and what the rent covers before
  you pay or sign."
- *One advisor throughout.* "From the first viewing to signing the lease, the same advisor stays with you."

**How renting works** (proposed; four steps, no timelines, each true of any listing):

1. *Ask.* "Send the form or a WhatsApp message. We check that the home is still available."
2. *View.* "We arrange a time for you to see it."
3. *Agree.* "Ask for the rent, the deposit, the lease length and what the rent covers in writing, in a tenancy
   agreement, and read it before you sign or pay."
4. *Move in.* "On the date in your agreement."

**Before you pay** (proposed; advice only):

- "See the home before you pay anything."
- "Read the tenancy agreement before you sign or pay a deposit."
- "Check what the rent covers: service charge, water, parking."
- "Pay only as the agreement sets out, and keep the receipt."
- "Quote Ref W-{id} when you call or message, so we know which home you mean."

Each line answers a documented way that renting goes wrong in Nairobi: deposits sent before a viewing
[S:citizen-scams], charges discovered late [S:kenyans-charges], and homes that turn out to be gone [F:bd-2025].
None says anything about what Block will do.

**Disclaimer** (owner-approved, unchanged) [L:ref/dev-v1.9.1.php:283]: "Listings are published in good faith from
information supplied by owners and landlords, and may change without notice. Please confirm the rent, availability,
the deposit and the lease terms before you commit. Block Real Estate does not give financial or legal advice; we
recommend reading the full tenancy agreement before you sign or pay a deposit."

For commercial pages swap "home" for "space" or "premises", "tenancy agreement" for "lease", and add "letter of
offer" where Block's own text does [L:ref/pages-final/36809.html:5033].

### 11.3 Generic answers for "Questions tenants ask"

Use the listing's own FAQ first: 127 of the 129 listings have one, with a median of eight questions
[L:notes/03-data-audit.json] (`summary.description.faq`), for example [L:ref/pages-final/43543.html:4799]. Add a
generic answer only for a question the listing has not already answered; the two listings with no FAQ (42370, 31842)
get the generic set.

| Question | Answer when the listing states the fact | Answer when it does not |
|---|---|---|
| "Is {name} still available?" | "The listing says it is available {now / from …}. Availability changes, so we confirm it before arranging a viewing." | "Availability changes, so we confirm it before arranging a viewing. Send the form or a WhatsApp message." |
| "What does the rent include?" | "The listing says the rent includes {the service charge}." | "The listing does not say. Ask us for a written list of what the rent covers before you commit." |
| "How much is the deposit, and how long is the lease?" | "The listing states {a deposit of … / a lease of …}." | "The listing does not state them. They are agreed with the landlord and written into the tenancy agreement; ask us to confirm them before you commit." |
| "How do I arrange a viewing?" | — | "Send the form with your name and number, or message us on WhatsApp. We confirm the home is still available and arrange a time." |
| "Is it furnished?" | "The listing says it is {furnished / unfurnished}." | Omit the question |
| "Are pets allowed?" | "The listing says {…}." | Omit the question |

Questions that cannot be answered generically, because the true answer is Block's policy and I do not know it:
"Does Block charge tenants a fee?", "Can I view by video from abroad?", "How quickly will you reply?". They are worth
answering; see 11.4.

### 11.4 Lines that would be strong here, but only if the owner confirms each is true

| Line (wording to adapt) | Why it would matter | Needs |
|---|---|---|
| "Block does not charge a fee to view a home." | Viewing and "inconvenience" fees are the best-documented grievance against Nairobi agents [F:bd-2025], [S:nation-kisumu] | Owner: is it true of every viewing? |
| "We never ask for a deposit before you have seen the home." | Deposits sent before viewing are the classic scam [S:citizen-scams], [S:pulse-scams] | Owner: true without exception, including for tenants abroad? |
| "A Block advisor meets you at the viewing." | Knight Frank states the same as policy [S:kf-faq] | Owner: always, or sometimes the landlord or caretaker? |
| "Block Real Estate is registered with the Estate Agents Registration Board (No. …)." | The Board tells consumers to check registration [S:earb], [S:earb-warning] | Owner: registration number and current practising certificate |
| "Who pays Block's fee: …" | Guides disagree on whether tenant or landlord pays the letting fee [S:expat-exchange] | Owner: the actual arrangement, per listing type |
| "We reply within {one working day}." | Most renters expect a reply within 24 hours [S:appfolio]; state times only if they are met [S:nng-contact] | Owner: a time the team keeps, and hours |
| "Availability checked with the landlord on {date}." | Listings stay up for weeks [F:kpc-ttl] | A real field and a process that fills it; otherwise no date at all |

Suggestion for the build: keep these as an empty list of optional trust lines in the option `blockke_rent_page`, so
that each can be switched on, with its exact wording, when the owner confirms it. Off by default.

### 11.5 Keep off the page

"Verified", "guaranteed", "safe", "secure", "no hidden fees", "best price", "instant", "limited", "last one",
countdowns, view or enquiry counters, unattributed testimonials or star ratings, and any reply time, fee or service
the owner has not confirmed. Also exclamation marks and pictograms (rule 15); some descriptions contain pictograms
today [L:ref/pages-final/43543.html:4843], which is a content matter for the listing, not the layout.

---

## 12. Deliverable F. Accessibility and performance must-haves for galleries and sticky bars

These sit on top of PROJECT.md rule 9. Items with a source key rest on that source; items without one are this
note's engineering recommendations. Context for the budgets: Google's "good" thresholds are a largest paint
within 2.5 s, interaction response within 200 ms and layout shift of 0.1 or less, at the 75th percentile of visits
[S:webdev-vitals]; Kenya's median mobile download speed was 45.37 Mbps in December 2025, but that is a median
[S:ookla-kenya]; and roughly half of Kenyan web traffic is on phones, by a measure that swings from month to month
[S:statcounter-ke].

### 12.1 Gallery

**Markup and loading**

1. Every photo is in the HTML as an `<img>` (or a link to the image file) with `width`, `height` and `alt`. Nothing
   is injected by script [S:webdev-carousel]. Counter-example: [F:kpc-house].
2. The first photo is `loading="eager"` with `fetchpriority="high"`; no other image has high priority; all others
   are `loading="lazy" decoding="async"` [S:webdev-lcp]. The first photo will be the page's largest paint, so it
   must not wait for script or CSS background loading.
3. `srcset` and `sizes` so a phone downloads a phone-sized file. The viewer loads a large size (about 1,024 to
   1,536 px wide), not the 2,048 px originals [L:ref/pages-final/45577.html:4640], unless the visitor zooms.
4. Space is reserved with `aspect-ratio` or the width and height attributes, so nothing moves when photos arrive
   [S:webdev-carousel].
5. No auto-rotation, no autoplaying video; a video is a poster image that loads the player on request
   [S:nng-auto], [S:nng-carousel-mobile].
6. **Design every photo count, not just the ideal.** 2 listings have no photos, 10 have one and 13 have two to
   four; commercial listings have a median of three [L:notes/03-data-audit.json] (`summary.photos`). No photo: no
   gallery and no blank box. One photo: a single image, no counter, no arrows. Two to four: a smaller mosaic with no
   "+N" tile.
7. **Portrait first photos** (19 listings): crop to the hero's shape with `object-fit: cover` and show the whole
   image in the viewer; never stretch.

**Phone carousel**

8. A horizontal scroll-snap strip that works by touch with no script; script only adds the "3 / 19" counter and the
   previous and next buttons. Without script the visitor still swipes and sees "19 photos" [S:webdev-carousel].
9. The container is a labelled region ("Photos of {name}") with `aria-roledescription="carousel"`; each slide is
   labelled "Photo 3 of 19" [S:w3c-carousel].
10. Previous and next are real `<button>` elements with text names, at least 44 × 44 px, and visible focus.
11. Show the count, and let the next photo peek in at the edge so it is obvious there is more
    [S:bay-truncate], [S:nng-carousel-mobile]. Best photos first: people stop after three or four [S:nng-carousel-mobile].

**Desktop mosaic and viewer**

12. Thumbnails, not dots; the last tile reads "+14 photos" [S:bay-thumbs], [S:bay-truncate].
13. The full-screen viewer is a modal dialog: focus moves in and stays in, Escape closes it, and focus returns to
    the photo that opened it [S:w3c-dialog]. In addition (this note's recommendation): the page behind is inert and
    does not scroll, and the Close button is the first control and at least 44 px. The native `<dialog>` element
    with `showModal()` gives most of this.
14. Arrow keys and swipes move between photos; pinch and double-tap zoom work; page zoom is never disabled
    [S:bay-gestures].
15. `alt` text: 888 of the 1,496 photos have none and the rest mostly repeat the building's name
    [L:notes/03-data-audit.json] (`summary.photos.alt_empty`), so generate "{name}: photo 3 of 19" for every photo
    unless its alt says something more. Captions, where the media library has them, are shown, because people read
    photos literally [S:bay-accessories], [F:airbnb-photos].
16. Opening the viewer changes no layout and loads no library; transitions are opacity or transform only, and none
    at all under `prefers-reduced-motion`.
17. Print and "reader" views get the first photo and the text, not a blank block.

### 12.2 Sticky bars

**One bar, sized and placed with care**

1. One sticky element per breakpoint: the bottom bar on phones, the summary card (or a slim section bar) on
   desktop. The theme's own phone agent bar must stand down on these pages [L:ref/plan.md:701],
   [L:notes/05-live-snippets.md:723]. The site also carries a chat float (Zoho SalesIQ): it must sit clear of the
   bar or be hidden on these pages, never on top of the bar's buttons; the live-snippets note has the mechanics
   [L:notes/05-live-snippets.md:1003].
2. Opaque background, strong contrast, no blur-through; as short as the 44 px targets allow (about 56 to 64 px)
   plus the device's safe area: `padding-bottom: max(8px, env(safe-area-inset-bottom))`
   [S:nng-sticky], [S:chrome-safe-area].
3. The bar holds three things only: the rent, WhatsApp, "Arrange a viewing". The primary button is not stretched
   edge to edge; it keeps space around it [S:bay-buttons].
4. It is printed by the server and its two actions are real links (`https://wa.me/…` and `#bkr-…`), so it works
   before and without script. It is a labelled region ("Rent and viewing"), as in v1.9.1's bar
   [L:ref/dev-v1.9.1.php:1993].

**It must never hide what the visitor is doing**

5. Nothing that has keyboard focus may be covered by the bar (WCAG 2.2, 2.4.11; failure F110). Set
   `scroll-padding-bottom` on the page to the bar's height, and `scroll-padding-top` to the height of any sticky
   header, so focused elements and in-page targets land clear of both [S:wcag-2411], [S:wcag-f110].
6. Reserve the bar's height at the end of the page (padding on the page wrapper), so the footer, the disclaimer and
   the last button are reachable.
7. Hide the bar while a form field has focus and while the form section is on screen: the on-screen keyboard and
   the bar together leave no room for the field, and the form's own button is then the action [S:webdev-forms].
8. The bar appears without moving content: `position: fixed`, shown and hidden with `transform` or `visibility`,
   driven by an `IntersectionObserver`, not a scroll handler. No slide animation under `prefers-reduced-motion`
   [S:nng-sticky].
9. At 320 px wide the three items still fit on one line with 44 px targets; the rent may wrap to two short lines
   ("KES 135,000" over "a month") as v1.9.1 does [L:ref/dev-v1.9.1.php:1993], but is never abbreviated.
10. The desktop summary card is sticky only while it is shorter than the viewport; otherwise its lower part can
    never be reached. Give it `max-height` and let it scroll inside, or drop the stickiness on short screens.
11. Bars are excluded from print.

**How to test** (for the test team): tab through the whole page at 360 px and 1,280 px and confirm that the focused
element is always visible; open the form with the on-screen keyboard on a small Android phone; load with JavaScript
off and confirm photos, rent, phone, WhatsApp and the form all work; throttle to a slow connection and confirm the
first photo is the first thing to arrive and nothing shifts.

---

## 13. Deliverable G. Ten ideas that make this page clearly different from a development sales page

The development page leads with a form that trades contact details for a price list and floor plans, then sells
with a payment plan or mortgage calculator and an investor view. On a rental shown in that layout today the hero
reads "Get the price list and floor plans", the facts say "Price: KES 135K" and "Status: Ready", the section bar
links to "Mortgage" and the follow-up asks "When would you like to buy?" [L:ref/pages-final/45577.html:4640]. Each
idea below replaces one of those moves with its rental counterpart.

1. **A rent sentence, not a price teaser.** The largest words in the hero are "KES 135,000 a month", with
   "Service charge included" beneath when the listing says so. Nothing is withheld to be "sent to your WhatsApp".
   Evidence: price first [S:apt-unit]; fee disclosure [S:zillow-2026]; rule 12.

2. **Photos first, form second.** The hero is the home: a real-photo gallery with a counter on phones and a mosaic
   on desktop. The form sits beside or below the summary, never laid over the picture, and it asks for a viewing,
   not a download. Evidence: photos rank second and missing real photos are a deal-breaker [S:apt-q4-2025]; the first
   five photos matter most [F:airbnb-tour].

3. **Availability in place of "Status: Ready" and completion dates.** "Available now" or "Available from …" when
   stated; otherwise the action itself asks: "Check availability". Evidence: availability ranks third [S:apt-unit];
   "no longer available" is a standing complaint [F:kpc-house], [F:kpc-ttl].

4. **A "Rent and terms" card where the payment plan or mortgage calculator would be.** A short definition list of
   what the listing states (deposit, lease, service charge, furnishing), then a ticked-off list headed "To confirm
   when you enquire" for what it does not. No sliders, no sums. Evidence: the letting-details block of mature portals
   [S:rm-listing], [S:p24-listing]; Block's own "confirmed at point of enquiry" lists
   [L:ref/pages-final/43543.html:4658].

5. **"Living here day to day" before leisure amenities.** Water (borehole, storage), power (backup generator,
   solar), internet, parking, security and lifts come first and are grouped by what they do; pool, gym and play area
   follow. A development page leads with lifestyle; a tenant wants to know the taps and lights work. Evidence: the
   portal's "Power & Utilities" group [F:kpc-house]; what tenants are told to ask [S:nation-queries]; 93 of 129
   rentals list a backup generator and 71 a borehole [L:ref/listings.json], and the theme's taxonomy already has the
   chapters "Water & Power" and "Security & Access" [L:notes/03-data-audit.json] (`summary.features.chapters`).

6. **A viewing panel instead of a brochure offer.** "Arrange a viewing" with a two-field form, the WhatsApp message
   that asks "Is it still available, and when could I arrange a viewing?" [L:ref/dev-v1.9.1.php:1905], and a
   thank-you that says what happens next and shows the number back. Evidence: section 3.4; [F:kpc-house].

7. **"Homes available in this building" instead of "Residences and price list".** Where a listing covers several
   sizes, or has sibling listings, a small table of home, size and rent a month, each linking to its own page
   ("See all 4 rentals"). No request form stands between the visitor and the figures. Evidence: fixtures 36499,
   36506 and 36502; unit tables on commercial portals [S:loopnet].

8. **Questions tenants ask, high on the page, and "Renting with Block" instead of the investor view.** The
   listing's own FAQ, then the generic answers of 11.3, then four steps of how renting works and the "Before you
   pay" advice. Where a sale page argues for returns, this page lowers the risk of a mistake. Evidence: scams and fee
   grievances (4.2); FAQs in users' words [S:nng-faq].

9. **A bottom bar that says the rent and opens WhatsApp.** On phones: "KES 135,000 a month", a WhatsApp button and
   "Arrange a viewing", there from the first paint. The development page's section bar says "Get the price list"
   [L:ref/pages-final/45577.html:4640]. Evidence:
   call and WhatsApp are the local contact norm [F:kpc-house]; the approved bar [L:ref/plan.md:701].

10. **"Around this home" written for living, and share-first.** Schools, shops, clinics and transport stages as the
    listing names them, with its own caution that travel times vary, instead of an investment case; and a Share
    action that opens WhatsApp with the home's name, rent and address, because a rental is often chosen with a
    partner, a family or an employer (50 descriptions have a "Why It Works for Companies Housing Staff" section
    [L:notes/03-data-audit.json], `summary.headings.why_audiences`).
    Evidence: share options with WhatsApp first [F:kpc-house]; renters compare about ten homes [S:apt-q4-2025].

Two more, if there is room:

11. **Built for enquiries from abroad.** International numbers accepted and invited in the phone hint; the rent in
    the currency the lease is set in (rule 12); phone links in international form. About a fifth of rental enquiries
    on the Kenyan portal come from outside the country [F:kpc-diaspora].

12. **A commercial page that reads like a schedule of accommodation.** Rate per sq ft a month and size as the two
    headline numbers, a "Space and costs" table, a specification list (power, access, dimensions, condition,
    parking), and "your team" wording throughout; no bedrooms and no lifestyle amenities (section 6).

---

## 14. Open questions, and how to answer each

| # | Unknown | Why it matters | How to find out |
|---|---|---|---|
| 1 | Does Block charge a tenant anything to view, or any agency or administration fee, and who pays Block's fee on a letting? | Decides the strongest trust line (11.4) and an FAQ answer | Ask the owner. Until then the page says nothing about fees |
| 2 | Is Block Real Estate (or its principal) registered with the Estate Agents Registration Board, and under what number? | A registration line is a checkable fact | Ask the owner; check the Board's directory at https://estateagentsboard.or.ke/find-trusted-estate-agency-firms-through-the-earb-firm-directory/ |
| 3 | Is there a reply time the team keeps, and what are the hours? | "Shortly" is in the approved thank-you line; a stated time would help only if true | Ask the owner. If none, remove time words |
| 4 | Who attends viewings (a Block advisor, the landlord, a caretaker), and are video viewings offered to people abroad? | Viewing copy and the "from abroad" FAQ | Ask the owner |
| 5 | What do Zillow, Apartments.com, Zoopla, StreetEasy, Savills, Knight Frank, Property24 Kenya and BuyRentKenya listing pages look like on a phone today (order, buttons, sticky bars, forms)? | Section 2.2 rests on search summaries | Open them in a browser, or allow those hosts in the session's network settings and re-run this study. Start with https://www.buyrentkenya.com/flats-apartments-for-rent/nairobi/westlands and https://www.rightmove.co.uk/properties/87464886 |
| 6 | What share of block.ke's rental-page visits are on phones, and from outside Kenya? | Confirms the phone-first ranking and the international-number hint | GA4: device category and country for `/property/` pages tagged For Rent |
| 7 | Which channel produces rental enquiries on block.ke now: WhatsApp clicks, calls or forms? | Decides whether the bar's primary action should be WhatsApp or the form | The contact-click tracking of snippet 40769 and GA4 events, against rows in `blockke_dev_leads` with `kind` rent |
| 8 | Do the listings' photos have captions or room names? | Decides whether captions and room grouping are possible. Alt text is known: 888 of 1,496 photos have none and the rest mostly repeat the building's name [L:notes/03-data-audit.json] | Captions are not in the saved pages; check the media library's caption field for a sample of listings |
| 9 | Are the listings' Open Graph images under 600 KB? | Decides whether WhatsApp shows the home's photo in the chat. The audit records widths (ten listings have a small one, the narrowest 415 px) but not file sizes [L:notes/03-data-audit.json] (`summary.photos.og_image_small_ids`) | Check each listing's `og:image` file size (Rank Math); for example the hero of 45577 is a 2,048 px JPEG [L:ref/pages-final/45577.html:4640] |
| 10 | What happens to a listing once the home is let, and how is a stated availability kept true? | Item 4 of the first screen: 89 listings state availability [L:notes/03-data-audit.json], and a stale "Available now" damages trust more than silence | Ask the owner what the team does when a home is let |
| 11 | Does the privacy policy name Block, the purpose, the contact and any third parties (landlords, Zoho)? | The form's notice leans on it (Data Protection Act, section 29) | Read https://block.ke/privacy-policy-2/ (not fetched here, rule 10) |
| 12 | Exact wording of WhatsApp's own click-to-chat article | Section 5.2 relies on third-party summaries | Open WhatsApp's Help Centre article "How to use click to chat" (search surfaced https://whatsapp.com/faq/en/general/26000030) |
| 13 | Whether charging a viewing fee is lawful in Kenya | Would sharpen the trust copy | Not established by search; a question for an advocate or the Board |
| 14 | Can the saved lead row carry an optional "Company" field for commercial enquiries? | Section 10.1 | The leads note and `ref/inbox-v1.1.1.php` |
| 15 | My own counts in 4.3 are a keyword scan | They guide design, not copy | Use the audit's figures (the first table in 4.3) where they differ |

---

## 15. Sources

Every key used above, with its address. **F** sources were fetched on 11 Nov 2026. **S** sources were not reachable
from this session; what is reported comes from a search-result summary of the page and should be re-read before any
figure is quoted outside this note. Where a summary did not make clear which of several pages carried a figure, the
entry says so.

### 15.1 Fetched (F)

| Key | What | URL |
|---|---|---|
| F:kpc-house | Kenya Property Centre: 5-bedroom house to rent, Karen (listing page) | https://kenyapropertycentre.com/for-rent/houses/nairobi/karen/70636-5-bedroom-house |
| F:kpc-warehouse | Kenya Property Centre: warehouse to rent, Embakasi | https://kenyapropertycentre.com/for-rent/commercial/warehouses/nairobi/embakasi/70570-warehouse-with-mezzanine-office-and-double-dock-doors |
| F:kpc-office | Kenya Property Centre: office space to rent, Westlands | https://kenyapropertycentre.com/for-rent/commercial/offices/nairobi/westlands/56492-8331-sq-ft-office-space-in-westlands |
| F:kpc-list | Kenya Property Centre: houses for rent in Nairobi (results page) | https://kenyapropertycentre.com/for-rent/houses/nairobi |
| F:kpc-faq | Kenya Property Centre: help centre | https://kenyapropertycentre.com/frequently-asked-questions |
| F:kpc-sc | Kenya Property Centre glossary: service charge (last checked 19 Oct 2026) | https://kenyapropertycentre.com/glossary/service-charge |
| F:kpc-rra | Kenya Property Centre glossary: Rent Restriction Act (last checked 13 Oct 2026) | https://kenyapropertycentre.com/glossary/rent-restriction-act |
| F:kpc-ttl | Kenya Property Centre Time to Let and Sell Index, Q3 2026 | https://kenyapropertycentre.com/market-trends/time-to-let-and-sell |
| F:kpc-diaspora | Kenya Property Centre Diaspora Demand Index, Q3 2026 | https://kenyapropertycentre.com/market-trends/diaspora-demand |
| F:bd-2025 | Business Daily, 19 Mar 2025: "House hunters ditch expensive agents for WhatsApp groups" | https://www.businessdailyafrica.com/bd/lifestyle/design-interiors/house-hunters-ditch-expensive-agents-for-whatsapp-groups-4971700 |
| F:bd-2023 | Business Daily, 8 Feb 2023: "Landlords setting rent in dollars amid weakening shilling" | https://www.businessdailyafrica.com/bd/economy/landlords-setting-rent-in-dollars--4115560 |
| F:airbnb-tour | Airbnb Help Centre: photo tour | https://www.airbnb.com/help/article/3509 |
| F:airbnb-photos | Airbnb Resource Centre: selecting the best photos of your space | https://www.airbnb.com/resources/hosting-homes/a/selecting-the-best-photos-of-your-space-367 |
| F:dpa | Data Protection Act, No. 24 of 2019 (sections 29, 30, 32, 37), copy on the Data Commissioner's site | https://www.odpc.go.ke/wp-content/uploads/2024/02/TheDataProtectionAct__No24of2019.pdf |

### 15.2 Search results only (S): portals and agencies

| Key | What | URL |
|---|---|---|
| S:rm-listing | Rightmove: a rental listing with a full "Letting details" block | https://www.rightmove.co.uk/properties/87464886 |
| S:rm-lettings-guide | Rightmove: "Create the Ultimate Lettings Listing" (2020) | https://hub.rightmove.co.uk/content/uploads/2020/09/Create-the-Ultimate-Lettings-Listing.pdf |
| S:rm-online-viewings | Rightmove customer FAQ: Online Viewings (retired 25 July 2024) | https://customerfaq.rightmove.co.uk/support/solutions/articles/7000056352-how-do-i-add-the-online-viewings-lozenge- |
| S:rm-floorplan | Rightmove: floor plan research | https://www.rightmove.co.uk/news/articles/property-news/maximise-chances-finding-buyer-floorplan/ |
| S:rm-commercial-guide | Rightmove: Ultimate Commercial Property Listing Guide (Jan 2026); the 69% and 66% figures are from Rightmove's commercial team as summarised in the same search | https://hub.rightmove.co.uk/content/uploads/2026/01/Ultimate-Commercial-Property-Listing-Guide-2.pdf |
| S:zoopla-fields | Zoopla support: fields a listing should include | https://support.zoopla.co.uk/hc/en-gb/articles/360018370017 |
| S:zillow-price | Zillow Rentals: total price display | https://www.zillow.com/rentals-network/pricing-transparency |
| S:zillow-group-price | Zillow Group: transparent rental pricing | https://www.zillowgroup.com/news/transparent-rental-pricing-for-renters-and-housing-providers |
| S:zillow-contacts | BiggerPockets forum: what "contacts" means on a Zillow rental | https://biggerpockets.com/forums/21/topics/962974-contacts-meaning-on-zillow-for-a-rental |
| S:zillow-2024 | Zillow Rentals Consumer Housing Trends Report 2024 (guide) | https://www.zillow.com/rentals-network/2024-consumer-housing-trends-report |
| S:zillow-2025 | Zillow: renters' housing trends report 2025 (mobile, app and online-application shares as relayed by Zillow pages in the search) | https://www.zillow.com/research/renters-housing-trends-report-2025-35647 |
| S:zillow-2026 | Zillow Rentals Consumer Housing Trends Report 2026 | https://www.zillow.com/rentals-network/2026-consumer-housing-trends-report |
| S:zillow-lab | Zillow: multifamily "Living Lab" on converting leads to tours (2019) | https://www.zillow.com/rentals-network/how-to-convert-more-leads-to-tours |
| S:apt-price | Apartments.com: transparent pricing for all properties | https://www.apartments.com/grow/learning-center/price-transparency-nationwide |
| S:apt-leads | Apartments.com: listings with transparent pricing receive more leads | https://www.apartments.com/grow/news/transparent-pricing-lead-performance |
| S:apt-unit | Apartments.com: why unit-specific details matter; unit-specific details to optimise listings | https://www.apartments.com/grow/learning-center/why-unit-specific-details-matter and https://www.apartments.com/grow/learning-center/unit-specific-details-to-optimize-listings |
| S:apt-q4-2025 | Apartments.com survey, Nov to Dec 2025: how today's renters choose a place | https://offcampuspartners.com/grow/learning-center/renter-search-survey-q4-2025 |
| S:apt-q1-2025 | Apartments.com survey, Q1 2025: what renters want and what they don't (deal-breaker shares as relayed by a secondary article) | https://apartments.com/grow/learning-center/renter-search-survey-q1-2025 |
| S:apt-speed | Apartments.com: renters' need for speed (the page carrying the 85% and 40% figures was not pinned down) | https://www.offcampuspartners.com/grow/learning-center/time-win-grow-your-business-satisfying-customers-need-speed |
| S:se-fare | StreetEasy: the FARE Act and listings | https://streeteasy.com/blog/fare-act-nyc and https://streeteasy.com/blog/how-fare-act-impacts-landlords |
| S:p24-listing | Property24 (South Africa): a rental listing with the overview fields | https://www.property24.com/to-rent/bothasrus/despatch/eastern-cape/7213/117707526 |
| S:brk-private | BuyRentKenya: listing privately (contact channels); sample listing page | https://www.buyrentkenya.com/list-privately and https://www.buyrentkenya.com/listings/4-bedroom-house-for-sale-karen-hardy-3661831 |
| S:brk-office-1 | BuyRentKenya: 5,078 sq ft office, Westlands (lease terms) | https://www.buyrentkenya.com/listings/5078-ft2-office-for-rent-westlands-area-4034290 |
| S:brk-office-2 | BuyRentKenya: 11,000 sq ft office, Westlands (escalation, deposit), among several Westlands office listings in the same search | https://www.buyrentkenya.com/listings/11000-ft2-office-for-rent-westlands-area-3845834 |
| S:brk-warehouse | BuyRentKenya: warehouse, Eastern Bypass (specification) | https://www.buyrentkenya.com/listings/2500-m2-warehouse-for-rent-eastern-bypass-1-3437951 |
| S:kpc-warehouse-2 | Kenya Property Centre: 76,000 sq ft warehouse, Enterprise Road (seen in search, not fetched) | https://kenyapropertycentre.com/for-rent/commercial/warehouses/nairobi/embakasi/industrial-area/61998-a-well-maintained-warehouse-measuring-76-000-sq-ft |
| S:pg-kenya | Pam Golding Properties: Nairobi rentals (search page and a listing) | https://www.pamgolding.co.za/property-search/apartments-to-rent-nairobi-kenya/5821 and https://www.pamgolding.co.za/property-details/apartment-to-rent-kileleshwa-kenya/3ke1716816 |
| S:kf-faq | Knight Frank UK: lettings FAQs | https://knightfrank.co.uk/residential/faqs/letting |
| S:kf-h1-2026 | Knight Frank: Africa office market dashboard and Kenya market update, H1 2026 | https://content.knightfrank.com/research/2261/documents/en/africa-office-market-dashboard-h1-2026-13007.pdf and https://content.knightfrank.com/research/3135/documents/en/the-kenya-market-update-h1-2026-13009.pdf |
| S:loopnet | LoopNet: an office listing (layout inferred from several listings) | https://www.loopnet.com/Listing/1200-main-st-irvine-ca/4096810/ |

### 15.3 Search results only (S): UX research and standards

| Key | What | URL |
|---|---|---|
| S:bay-thumbs | Baymard: always use thumbnails for additional images | https://baymard.com/blog/always-use-thumbnails-additional-images |
| S:bay-truncate | Baymard: signpost hidden thumbnails | https://baymard.com/blog/truncating-product-gallery-thumbnails |
| S:bay-gestures | Baymard: mobile image gestures | https://baymard.com/blog/mobile-image-gestures |
| S:bay-img-types | Baymard: seven types of product images (the page carrying the 56% figure was not pinned down) | https://baymard.com/blog/ux-product-image-categories |
| S:bay-accessories | Baymard: included accessories image | https://baymard.com/blog/included-accessories-image |
| S:bay-tabs | Baymard: avoid horizontal tabs | https://baymard.com/blog/avoid-horizontal-tabs |
| S:bay-buttons | Baymard: e-commerce best practices (sticky button) and button design | https://baymard.com/blog/ecommerce-ux-best-practices and https://baymard.com/blog/button-design |
| S:bay-app-ads | Baymard: de-emphasise "install app" adverts | https://baymard.com/research-articles/deemphasize-install-app-ads |
| S:bay-fields | Baymard: minimise form fields; single input entities | https://baymard.com/research-articles/checkout-flow-average-form-fields and https://baymard.com/blog/mobile-form-usability-single-input-fields |
| S:bay-inline-labels | Baymard: never use inline labels on mobile forms | https://baymard.com/blog/mobile-forms-avoid-inline-labels |
| S:bay-phone | Baymard: explain why the phone field is required | https://baymard.com/research-articles/explain-phone-number-field |
| S:bay-required | Baymard: mark both required and optional fields | https://baymard.com/blog/required-optional-form-fields |
| S:bay-keyboard | Baymard: touch keyboards; keyboard types cheat sheet | https://baymard.com/blog/mobile-touch-keyboards and https://baymard.com/labs/touch-keyboard-types |
| S:bay-validation | Baymard: inline form validation | https://baymard.com/blog/inline-form-validation |
| S:nng-price | NN/g: pricing information gives B2B sites a competitive advantage | https://www.nngroup.com/articles/show-price/ |
| S:nng-top10 | NN/g: top 10 enduring web-design mistakes | https://www.nngroup.com/articles/top-10-enduring/ |
| S:nng-carousel-mobile | NN/g: carousels on mobile devices; designing effective carousels | https://www.nngroup.com/articles/mobile-carousels/ and https://www.nngroup.com/articles/designing-effective-carousels/ |
| S:nng-auto | NN/g: auto-forwarding carousels and accordions annoy users | https://www.nngroup.com/articles/auto-forwarding/ |
| S:nng-accordion | NN/g: accordions on mobile | https://www.nngroup.com/articles/mobile-accordions/ |
| S:nng-sticky | NN/g: sticky headers, five ways to make them better | https://www.nngroup.com/articles/sticky-headers/ |
| S:nng-forms | NN/g: website forms usability; four principles to reduce cognitive load in forms | https://www.nngroup.com/articles/web-form-design/ and https://www.nngroup.com/articles/4-principles-reduce-cognitive-load/ |
| S:nng-placeholder | NN/g: placeholders in form fields are harmful | https://www.nngroup.com/articles/form-design-placeholders/ |
| S:nng-mobile-input | NN/g: a checklist for designing mobile input fields | https://www.nngroup.com/articles/mobile-input-checklist/ |
| S:nng-errors | NN/g: ten design guidelines for reporting errors in forms | https://www.nngroup.com/articles/errors-forms-design-guidelines/ |
| S:nng-confirm | NN/g: transactional email and confirmation messages | https://www.nngroup.com/articles/transactional-and-confirmation-email/ |
| S:nng-register | NN/g: a checklist for registration and login forms on mobile | https://www.nngroup.com/articles/checklist-registration-login/ |
| S:nng-contact | NN/g: "Contact us" page guidelines | https://www.nngroup.com/articles/contact-us-pages/ |
| S:nng-faq | NN/g: FAQs still deliver great value; Strategic Design for FAQs (report) | https://www.nngroup.com/articles/faqs-deliver-value/ and https://media.nngroup.com/media/reports/free/Strategic_Design_for_Frequently_Asked_Questions.pdf |
| S:nng-trust | NN/g: trustworthiness in web design, four credibility factors | https://www.nngroup.com/articles/trustworthy-design/ |
| S:wcag-2411 | W3C: Understanding SC 2.4.11 Focus Not Obscured (Minimum) | https://www.w3.org/WAI/WCAG22/Understanding/focus-not-obscured-minimum.html |
| S:wcag-f110 | W3C: failure F110, sticky footer or header hiding focused elements | https://www.w3.org/WAI/WCAG22/Techniques/failures/F110 |
| S:wcag-258 | W3C: Understanding SC 2.5.8 Target Size (Minimum) | https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html |
| S:w3c-carousel | W3C: carousel pattern (APG) and carousels tutorial | https://www.w3.org/WAI/ARIA/apg/patterns/carousel/ and https://www.w3.org/WAI/tutorials/carousels/ |
| S:w3c-dialog | W3C: modal dialog pattern (APG) | https://www.w3.org/WAI/ARIA/apg/patterns/dialog-modal/ |
| S:webdev-carousel | web.dev: best practices for carousels | https://web.dev/articles/carousel-best-practices |
| S:webdev-lcp | web.dev: optimise Largest Contentful Paint; Fetch Priority | https://web.dev/articles/optimize-lcp and https://web.dev/articles/fetch-priority |
| S:webdev-vitals | web.dev: Web Vitals and how the thresholds were defined | https://web.dev/articles/vitals and https://web.dev/articles/defining-core-web-vitals-thresholds |
| S:webdev-forms | web.dev: sign-up form best practices; payment and address form best practices | https://web.dev/articles/sign-up-form-best-practices and https://web.dev/articles/payment-and-address-form-best-practices |
| S:chrome-safe-area | Chrome for Developers: edge-to-edge and safe-area insets | https://developer.chrome.com/docs/css-ui/edge-to-edge |
| S:appfolio | AppFolio: 2024 renter preferences survey (press release) | https://appfolio.com/newsroom/new-appfolio-survey-finds-property-managers-must-innovate-to-meet-modern-renter-expectations |
| S:aptlist-fraud | Apartment List: how common is rental fraud (2018) | https://www.apartmentlist.com/research/how-common-is-rental-fraud-scams |
| S:ftc-rental | US Federal Trade Commission: rental scams data spotlight (Dec 2025) | https://ftc.gov/news-events/data-visualizations/data-spotlight/2025/12/rental-scams-hit-home-65-million-reported-losses |

### 15.4 Search results only (S): Kenya, WhatsApp and commercial

| Key | What | URL |
|---|---|---|
| S:expat-arrivals | Expat Arrivals: accommodation in Nairobi | https://www.expatarrivals.com/africa/kenya/nairobi/accommodation-nairobi |
| S:expat-exchange | Expat Exchange: how to rent a home in Kenya | https://www.expatexchange.com/ctryguide/18310/64/Kenya/Renting-in-Kenya-How-to-Rent-a-Home-in-Kenya |
| S:standard-deposit | The Standard: "Rent deposit: every tenant's nightmare" | https://www.standardmedia.co.ke/home-away/article/2000026570/rent-deposit-every-tenants-nightmare |
| S:standard-brokers | The Standard: "Tech, transparency driving brokers out of property market" (the page carrying the Sh1,500 to Sh3,000 viewing-fee figure was not pinned down) | https://thestandard.ke/sports/amp/real-estate/2001523278/tech-transparency-driving-brokers-out-of-property-market |
| S:nation-kisumu | Nation: "Agents fleecing newcomers to Kisumu" | https://nation.africa/kenya/life-and-style/dn2/agents-fleecing-newcomers-to-kisumu--1016158 |
| S:nation-queries | Nation: "Queries to ask your landlord before signing that lease"; the water and power questions came from two other Kenyan checklists in the same search | https://nation.africa/kenya/life-and-style/dn2/queries-to-ask-your-landlord-before-signing-that-lease-96806 and https://kenya.hsmagazine.digital/your-essential-checklist-finding-the-perfect-rental-home-in-kenya-2 and https://peopledaily.digital/lifestyle/ten-questions-to-ask-before-signing-lease-agreement |
| S:kenyans-charges | Kenyans.co.ke: nine extra charges landlords use | https://www.kenyans.co.ke/news/85435-9-extra-charges-landlords-use-raise-nairobi-rent-ksh8000 |
| S:kenyans-dollars | Kenyans.co.ke: Nairobi landlords list houses in dollars | https://www.kenyans.co.ke/news/96967-nairobi-landlords-list-houses-dollars-shilling-weakens |
| S:pulse-scams | Pulse Kenya: tricks used by fraudsters when house hunting | https://www.pulse.co.ke/story/house-hunting-like-a-pro-tricks-used-by-fraudsters-and-how-to-avoid-them-2025070510135961615 |
| S:citizen-scams | Citizen Digital (Oct 2025): housing scams surge in Nairobi's prime estates | https://www.citizen.digital/article/housing-scams-surge-in-nairobis-prime-estates-as-foreign-demand-rises-n371915 |
| S:earb | Estate Agents Registration Board: firm directory; Estate Agents Act on Kenya Law | https://estateagentsboard.or.ke/find-trusted-estate-agency-firms-through-the-earb-firm-directory/ and https://new.kenyalaw.org/akn/ke/act/1984/17/eng@2022-12-31 |
| S:earb-warning | Reports of the Board's warnings about unregistered agents | https://www.kenyans.co.ke/news/110444-govt-warns-kenyans-about-unregistered-real-estate-agents-threatens-legal-action and https://thekenyatimes.com/business/estate-agent-board-issues-fresh-notice-to-all-property-buyers-and-tenants/ |
| S:stamp-duty | Stamp duty on leases: a legal-forms guide, PKF's tax guide, Chambers' real estate guide | https://forms-legal.com/facts/tenancy-stamp-duty and https://www.pkfea.com/media/5quhwe2o/pkf_kenya_2019_2020_quick_tax_guide.pdf and https://gpg-pdf.chambers.com/view/826355218/584 |
| S:cap301 | Landlord and Tenant (Shops, Hotels and Catering Establishments) Act, Cap 301: the Act and section 2 | https://new.kenyalaw.org/akn/ke/act/1965/13/eng@2022-12-31 and https://www.sheriaplex.com/kenya-acts/4290-section-2-interpretation |
| S:bd-cap301 | Business Daily: "Kenya's archaic commercial leases law now ripe for repeal" | https://www.businessdailyafrica.com/analysis/ideas/Kenya-commercial-leases-law/4259414-4609120-ixq6suz/index.html |
| S:vat-commercial | VAT on commercial premises: Bowmans and EY on the Court of Appeal ruling (2025) | https://bowmanslaw.com/insights/kenya-court-of-appeal-clarifies-the-applicability-of-value-added-tax-vat-on-the-sale-of-commercial-premises/ and https://globaltaxnews.ey.com/news/2025-0913 |
| S:bd-office | Business Daily: Nairobi prime office rental yield (Knight Frank, 2026) | https://www.businessdailyafrica.com/bd/markets/real-estate/nairobi-prime-office-rental-yield-stuck-on-higher-supply-5589442 |
| S:forsters | Forsters: choosing your new office space | https://www.forsters.co.uk/feature/choosing-your-new-office-space |
| S:brigade | Brigade Group: office lease checklist | https://www.brigadegroup.com/blog/commercial/office-lease-checklist-for-cfos |
| S:langham | The Langham Estate: essential tenancy documents for commercial tenants | https://langhamestate.com/industry-community/essential-tenancy-documents/ |
| S:allafrica-ca | allAfrica (Feb 2026): WhatsApp usage in Kenya, Communications Authority data | https://allafrica.com/stories/202602030414.html |
| S:star-geopoll | The Star (Sep 2025): GeoPoll social media survey | https://www.the-star.co.ke/business/kenya/2025-09-06-majority-of-kenyans-spend-up-to-six-hours-on-social-media-daily-report |
| S:wa-faq | WhatsApp: "How to use click to chat" (surfaced by search; not opened) | https://whatsapp.com/faq/en/general/26000030 |
| S:wa-tyntec | tyntec help centre: adding a WhatsApp conversation button | https://www.tyntec.com/helpcenter/docs/faqs/whatsapp-business/your-whatsapp-account/how-can-i-add-a-whatsapp-conversation-button-on-my-website/ |
| S:wa-bird | Bird: what is a WhatsApp click-to-chat link | https://bird.com/explained/whatsapp/what-is-a-whatsapp-click-to-chat-link |
| S:wa-preview | Meta for Developers: WhatsApp link previews | https://developers.facebook.com/documentation/business-messaging/whatsapp/link-previews/ |
| S:ookla-kenya | HapaKenya (Feb 2026) reporting Ookla's Speedtest Global Index, Dec 2025 | https://hapakenya.com/2026/02/18/kenya-emerges-as-a-digital-anchor-in-africa-in-2025-speedtest-insights |
| S:statcounter-ke | StatCounter: desktop, mobile and tablet share, Kenya | https://gs.statcounter.com/platform-market-share/desktop-mobile-tablet/kenya |

### 15.5 Also consulted, not relied on

| What | Why not used | URL |
|---|---|---|
| MeasuringU: UX and NPS benchmarks of real estate websites (2022) | The findings sections did not come through in search | https://measuringu.com/real-estate-benchmark-2022/ |
| Online Marketplaces: Portal Standards Report 2023 | Full report is gated; no figures on contact features | https://www.onlinemarketplaces.com/articles/real-estate-portal-standards-2023/ |
| National Trading Standards (UK): Part A material information for listings (rent, deposit, council tax); withdrawn in May 2025 | UK regulation, now withdrawn; useful only as a precedent for "rent and deposit are the minimum" | https://www.nationaltradingstandards.uk/news/national-trading-standards-publishes-guidance-on-part-a-material-information-for-property-listings/ and https://www.propertymark.co.uk/resource/material-information-guidance-withdrawn-as-the-digital-markets-competition-and-consumers-act-takes-over.html |
| DataReportal: Digital 2026 Kenya | Platform ranking not confirmed in search | https://datareportal.com/reports/digital-2026-kenya |
| Kenyans.co.ke: Kenyans find rentals through WhatsApp communities | Same story as Business Daily, which I could fetch | https://www.kenyans.co.ke/news/109379-kenyans-ditch-agents-and-find-rentals-through-whatsapp-communities |
| BuyRentKenya: "10 questions to ask before signing a lease" | Could not be opened | https://www.buyrentkenya.com/discover/signing-lease |

### 15.6 Local files cited

`ref/dev-v1.9.1.php` (lines 278, 283, 1904, 1905, 1976, 1979 to 1988, 1993, 7830, 7900, 8050, 9921, 9922),
`ref/README-wordpress.md` (lines 157, 158, 162, 181), `ref/plan.md` (lines 701, 724, 782), `ref/listings.json`,
`ref/pages-final/45577.html` (lines 4640, 4842, 4946), `ref/pages-final/43543.html` (lines 4658, 4799, 4843),
`ref/pages-final/36809.html` (lines 5021, 5033), and `notes/03-data-audit.json` (cited by key, for example
`summary.terms.deposit`; written by the data-audit agent). Paths are relative to
`/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp`.
