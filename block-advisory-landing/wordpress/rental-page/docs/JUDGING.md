# Judging the three concepts for the block.ke rental page

`ROOT` = `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp`.

Three designers worked independently on the same brief (`ROOT/design/BRIEF.md`). Each handed in a directory:

| Concept | Directory | Direction they were given |
|---|---|---|
| A | `ROOT/design/concept-a/` | "The viewing desk": quick, tool-like, a rent-and-viewing panel always in reach |
| B | `ROOT/design/concept-b/` | "The home tour": editorial, photographs lead, the listing's words as a short tour |
| C | `ROOT/design/concept-c/` | "The pocket page": phone and WhatsApp first, the rent as the headline |

You are one of three judges. You do not see the other judges' work. One of the three concepts, or a combination,
will be built and shown to real tenants on block.ke, so judge what is on the screen, not what the spec promises.

## Read first

1. `ROOT/PROJECT.md` (what is being built, and the hard rules), `ROOT/DECISIONS.md`, `ROOT/design/BRIEF.md`.
2. `ROOT/design/reference/TOKENS.md` and the pictures in `ROOT/design/reference/`: the development page
   (`dev-sale-santorini_*`) is the page the rental page must NOT look like a relabelled copy of; the classic rental
   page (`classic-rental-kilimani-3bed_*`) is what tenants see today.
3. Each concept's `spec.md` (long: read sections 1 to 3 in full, then the parts your lens needs).

## Look, do not trust

- Every concept has `shots/` (list the directory). First screens are named `...first...png` at 390x844 and 1440x900
  for the three mocks `home` (45577, 19 photos, full data), `sparse` (34171, one photo, rent in US dollars, few terms)
  and `commercial` (36809, offices).
- Full-page pictures are too tall to read in one piece. They are already cut into slices that you can read at full
  size: `ROOT/design/judging/slices/concept-a/`, `.../concept-b/`, `.../concept-c/` (file names end `-01.png`,
  `-02.png` … from the top of the page down). To slice any other tall picture:
  `python3 ROOT/design/tools/slice.py <png> <outdir> [slice height] [overlap]`. Write only under
  `ROOT/design/judging/<your key>/`.
- The mocks are live in the local test site, inside the real site header, footer and CSS:
  `http://127.0.0.1:8089/?rig_mock=concept-a/home` (and `/sparse`, `/commercial`; other state directories exist in
  each concept, list them). Take your own pictures and measurements with
  `ROOT/rig/bin/shot '<url path>' <out.png> [width] [full=1|0] [height]` (it reports sideways scroll, fonts, console
  errors; `ROOT/rig/bin/shot.js` documents more options and can be required from a Playwright script of your own:
  `const { open } = require('ROOT/rig/bin/shot.js')`). If the site does not answer, wait a minute and retry;
  `ROOT/rig/bin/start` starts it. Never touch block.ke itself, never edit a concept's files or the test site.
- Look at pictures with the Read tool. At the least: all first screens (9 phone, 9 desktop), the `home` and `sparse`
  phone full pages of all three concepts slice by slice, one desktop full page per concept, and the state pictures
  that matter to your lens (viewer, form error, thank-you, let, no photo).

## Criteria and weights (score every concept on every criterion, 0 to 10, halves allowed)

| Key | Weight | What a 10 means |
|---|---|---|
| `first_screen` | 20 | On a 390x844 phone and on a 1440x900 desktop, without scrolling, a tenant knows the rent, what it covers, what the place is and looks like, where it is, and how to see it. Nothing wasted, nothing shouting. |
| `rent_terms` | 15 | The rent is one honest sentence in the approved wording; stated terms are plain; what the listing leaves open is one short "To confirm when you enquire" list; no invented fact, no empty cell, no "TBC". |
| `contact` | 15 | Arranging a viewing, WhatsApp and calling are one tap away at every scroll position on phone and desktop, never in the way, never duplicated into noise; the form is short and honest (no booked-slot promise); errors and the thank-you are clear. |
| `brand_craft` | 15 | It looks like Block at its best: type, spacing, alignment, image handling, consistent detail at every width; you would sign it. |
| `difference` | 10 | A visitor who has seen a development page would never say "the same template with the words changed". Different job, different page; same family. |
| `thin_data` | 10 | With one photo, no photo, few stated terms, a rent in dollars, a rent on request or a very long title the page still looks intended, not broken or padded. |
| `commercial` | 5 | The office, shop or warehouse occupier gets their own wording and facts; no bedrooms, no tenant-family language. |
| `accessibility` | 5 | One H1, ordered headings, keyboard and focus, 44px targets, contrast AA, dialogs handled, reduced motion, works without JavaScript, no sideways scroll 320 to 1440, no layout shift. |
| `buildability` | 5 | It can be built as ONE WordPress snippet that reads real listings: every element maps to a field or a parse rule that survives the 129 very uneven descriptions, with a decent fallback when the parse finds nothing; CSS and JS within budget; nothing that fights the theme. |

Weighted total = sum of (score x weight) / 10, out of 100. Score what you see; a concept is not owed a high mark
because its direction was harder. Be exact about faults: name the picture or the URL and say what is wrong.

## What to hand in

Write your report to `ROOT/design/judging/<your key>.md`: for each concept the marks with one or two sentences of
evidence per criterion, the three best things, the three worst things; then a section "What the final page should
take from each" (name the component and the concept, as concretely as a designer needs: "C's rent sentence as the
headline", "A's tickable checklist feeding the WhatsApp text"), a section "Faults the final page must not carry
over", and your recommendation: which concept should be the base of the final design and what must be grafted on
or changed. Then return the structured result you are asked for.
