# Block development page: design preview

`luster-arbor.html` is a working preview of a "dedicated website" style page for a development on block.ke. It uses Luster Arbor Residency's real content, the 15 listing photos (loaded from block.ke), Loyd's agent card and the site's brand system: Montserrat, navy `#0D2440` and brass `#B98A44`.

The layout borrows from two references:

- **Serhant development pages:** a full-bleed hero, a sticky section bar (Overview, Residences, Gallery and so on), an availability table, editorial copy and a closing call to action.
- **Compass building pages:** a key-facts strip, an agent card that stays in view while you scroll, a building details table, "units in this building" and "similar buildings".

Open the file in a browser to see it. It is a static mock-up and nothing on block.ke has changed.

## Where each section's data comes from in WordPress

| Section | Source on block.ke |
|---|---|
| Hero photo, title, tags | Featured image, title, `property_status`, `property_area` |
| Key facts | `property_price` (from), sub-unit price and size range, `property_completion`. Floors would need a new field. |
| Availability table | The development's sub-units (`property_subunits_list`): each unit listing's price, beds, size and link |
| Gallery and lightbox | `wpestate_property_gallery` |
| Amenities | `property_features` terms. The grouping into Leisure, Family and so on would need content or fields. |
| Location | `property_latitude` / `property_longitude` |
| Agent card and form | `property_agent` (estate_agent post) and the WPResidence contact form |
| Explore each residence | `property_subunits_list` |
| Similar developments | Other listings in the same `property_area` and category |

## Two ways to put it live

**A. Per-listing Elementor template.**
- Build the layout as a "Property Page Design" template using WPResidence's Elementor widgets.
- Point chosen listings at it with the listing's "Property Page Design" field (`property_page_desing_local`).
- No custom code is needed, but each development has to be switched over by hand, and the widgets limit how closely it can match this design.

**B. Automatic development template (custom code).**
- A small PHP template, added as a WPCode snippet or child-theme file, renders this layout for every listing that has sub-units (`property_has_subunits = 1`).
- It keeps the site header and footer.
- This matches the design exactly and applies to every development at once. It should be tested on a staging copy first.

## Things to check before going live

- Sizes are stored in square feet (`wp_estate_measure_sys = ft`). For example, 63 m² is saved as 678. The page should show m².
- The site's existing "BLOCK x SERHANT" custom CSS targets every single property page (`body.single-estate_property`). The new layout's styles need their own scope so the two don't fight.
- FAQ, amenity groups and finishes currently live as free text in each listing's description. Either keep them there or move them into fields.
