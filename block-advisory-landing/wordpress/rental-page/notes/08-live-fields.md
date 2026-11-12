# Live field check (main session, 12 Nov 2026): what a rental and its agent really hold in the database

Two read-only snapshots through the site connector (listing 45577 and its agent 35366). This settles several
"unknown" items of the earlier notes and of `ROOT/rig/FIXTURES.md` section 11. Where this file and an earlier note
disagree, this file wins.

## Listing 45577 (post type `estate_property`)

Custom fields that matter to the rental page, with the stored values:

| Field | Stored value | Meaning for the page |
|---|---|---|
| `property_price` | `"135000"` | the rent (a string of digits; empty string when there is none) |
| `property_label` | `"per month"` | after-price label |
| `property_label_before` | `""` | before-price label ("From" lives here when a listing has it) |
| `property_second_price`, `property_label_before_second_price`, `property_second_price_label` | `""` | unused on this listing |
| `local_show_hide_price` | `"global"` | theme switch; ignore unless it says to hide |
| `property_size` | `""` | EMPTY on this listing although the text says "about 162 square metres": no size field, no size (DECISIONS 15) |
| `property_lot_size` | `"0"` | zero means none |
| `property_rooms` / `property_bedrooms` / `property_bathrooms` | `"7"` / `"3"` / `"3"` | rooms is not shown |
| `property_agent` | `"35366"` | post ID of the agent (post type `estate_agent`) |
| `wpestate_property_gallery` | array of 19 attachment IDs: 45576, 45575, 45573, 45574, 45572, 45570, 45569, 45568, 45565, 45564, 45563, 45562, 45561, 45558, 45559, 45549, 45551, 45552, 45553 | the gallery, in the order staff arranged it |
| `image_to_attach` | the same IDs as one comma-separated string with a trailing comma | theme's duplicate of the gallery |
| `_thumbnail_id` | `"45549"` | the featured image. NOTE: it is the 16th photo of the gallery, not the first. Follow the development layout's rule for which photo leads (notes/01) and never print the same photo twice |
| `property_address` | `""` | no street address on this listing |
| `hidden_address` | `", , Kilimani, Nairobi, "` | theme's search helper; not for display |
| `property_zip` / `property_country` | `""` / `"Kenya"` | |
| `property_latitude` / `property_longitude` | `"-1.2892786"` / `"36.7869311"` | a neighbourhood point (notes/03), never "exact location" |
| `page_custom_zoom` | `"16"` | |
| `property_hide_map_marker` | `""` | |
| `embed_video_id`, `embed_virtual_tour`, `property_custom_video` | `""` | none |
| `property_has_subunits`, `property_subunits_list_manual` | `""` | none |
| `use_floor_plans` | `"0"` | none |
| `bke_dev_layout` | `"off"` | set by the stopgap of 11 Nov |
| `rank_math_title`, `rank_math_description` | set | the SEO description is here |
| `wpestate_total_views` | `"25"`; `wpestate_detailed_views` an array by date | the theme's view counter |
| `prop_featured` | `"0"` | |
| `structure-type`, `stories-number` | `"Not Available"` | theme placeholders: never print "Not Available" as a fact |

Not present at all on this listing (so `get_post_meta` returns an empty string): `bke_rent_layout`, `bke_rent_usd`,
`bke_rent_usd_date`, `bke_dev_name`, `blockke_crm_status`.

The post's stored excerpt: the connector returned a generated excerpt (the SEO description followed by the start of
the content and an ellipsis), which is what `get_the_excerpt()` builds when the stored excerpt is empty. Treat the
stored excerpt as empty and never print an excerpt. The content's first paragraph repeats `rank_math_description`
word for word (DECISIONS 16 applies: drop it when it only repeats the SEO description).

Terms on 45577:

- `property_action_category`: For Rent (`for-rent`, id 54)
- `property_category`: Apartments (`apartments`, 103), Residential (`residential`, 70). Note: a parent-level
  category ("Residential", "Commercial") is filed next to the real type; show the specific one.
- `property_city`: Nairobi (100); `property_area`: Kilimani (`kilimani`, 114)
- `property_project`: Kings Courtyard (`kings-courtyard`, 1180)
- `property_status`: Complete (`complete`, 841)
- `property_features` (13 terms): 24/7 Manned Security (`manned-security`, 933), Ample Parking (`ample-parking`,
  938), CCTV Surveillance (`cctv`, 934), Fitted Kitchen (`fitted-kitchen`, 90), Gym (`gym`, 986), Kids Play Area
  (`kids-play-area`, 127), Lifts (`lifts`, 52), Swimming pool (`pool`, 69) **and five GROUP terms that are not
  features**: Building &amp; Parking (`building-parking`, 955), Family &amp; Convenience (`family-convenience`,
  957), Lifestyle &amp; Recreation (`lifestyle-recreation`, 956), Security &amp; Access (`security-access`, 950),
  Unit Features (`unit-features`, 958).

Consequences for the build:

1. **Feature group terms.** The feature taxonomy is hierarchical and listings carry the parent ("group") terms as
   well as the features. The page must never list a group as a feature. Rule: a feature term that has child terms
   in the taxonomy (`get_term_children`, or any other term of the listing whose `parent` is this term) is a group
   name; it may be used as a sub-heading for its children or dropped, never printed as a feature. Term names come
   back HTML-encoded (`&amp;`): decode before escaping, or the page prints `&amp;amp;`.
2. **Category parents.** The same holds for `property_category`: "Residential" and "Commercial" are parents.
3. **Empty numbers** are stored as `""` for some fields and `"0"` for others. Treat `''`, `'0'`, `0` and
   non-numeric strings alike: no value.
4. **The gallery** is `wpestate_property_gallery` (array of attachment IDs, staff order). The featured image may
   sit anywhere in it. Missing attachments must be skipped silently.

## Agent 35366 (post type `estate_agent`)

| Field | Stored value |
|---|---|
| post title | Ricky Wekesa |
| stored excerpt | "Every good deal starts with a straight answer." (a personal tagline; not needed) |
| `first_name` / `last_name` | Ricky / Wekesa |
| `agent_position` | Sales Executive |
| `agent_member` | Block Real Estate |
| `agent_mobile` | `+254180711259` |
| `agent_phone` | `+254180711259` (same number) |
| `agent_email` | the agent's own address at block.ke |
| `agent_address` | Upper Hill Gardens, Suite D15, Upper Hill, Nairobi (the office) |
| `_thumbnail_id` | 35357 (portrait; alt text "Ricky Wekesa, Sales Executive at Block Real Estate"; the file is a large "-scaled" original, so always ask WordPress for a small size) |
| `secondary_listings` | a long array of listing IDs with many repeats (the theme's bookkeeping; ignore) |

So: the agent's number is in both `agent_mobile` and `agent_phone`, stored in international form with a plus sign.
Prefer `agent_mobile`, fall back to `agent_phone`, and use the development snippet's `blockke_dev_agent()` behind
`function_exists` when it is there (notes/01), with this as the built-in fallback. Display form "0180 711 259"
(national) with `tel:+254180711259` and `wa.me/254180711259`.
