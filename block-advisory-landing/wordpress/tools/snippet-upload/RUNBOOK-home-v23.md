# Home v2.3 go-live runbook (block.ke, 5 Nov 2026)

D = this folder (`home2/v23deploy`), V = `home2/v23` (both under the scratchpad). The connector's tools are
`mcp__Block_Website__*`; load each schema with ToolSearch (`select:mcp__Block_Website__wp_create_post,...`) before the first call.
This method put v2.2.1 (snippet 45262, 255 KB, 19 parts) live on 4 Nov with an identical md5. Follow it exactly.
Regexes below are written as the values themselves (one backslash), not as JSON. Never send a backslash directly followed by the letter u (the connector turns it into a character).

## Hard rules
- Write only what your step lists. Never `wp_update_post` page 44129. Never edit, switch off or delete an existing snippet
  (45262, 42706, any other); never touch listings, media, terms (no new terms), menus, site CSS or other pages.
- One writer per post: never run two calls on the same post at the same time.
- A snippet is uploaded as a **draft** and only published (library, safety net) after its read-back is identical. If any check
  fails and you cannot repair it with one exact call, trash that draft (`wp_delete_post` → trash) and start that snippet again.
  Never leave a half-uploaded snippet published.
- The upload parts are in `D/lib/`, `D/safety/`, `D/widgets/` (`pNN.txt`, `plan.json` from `split.py`). Upload them as they are:
  `cat` a part, then send exactly that text. Never retype, reflow or "fix" code; never add or drop a newline.
- No model or vendor names in titles or notes. ASCII in notes.

## Expected files
| snippet | local file | md5 | bytes | parts | token |
|---|---|---|---|---|---|
| library (L) | `V/library.php` | `40b14d70b8aa287f13642177ed9de637` | 335,955 (335,969 with the 2 `@@BKBS@@`) | 25 | `BKH23LNEXT` |
| safety net (S) | `V/safety-net.php` | `b2a7ac5f1111a6babb4c97ddb2f95f99` | 4,090 | 1 | none |
| widgets (W) | `V/widgets.php` | `acb2fe40760b3dd0f5e7c2eff37ee0b0` | 33,347 | 3 | `BKH23WNEXT` |
Check `md5sum V/library.php V/safety-net.php V/widgets.php` first; stop if any differs.

## 1. Create the draft
`wp_create_post` with
- `post_type` `"wpcode"`, `post_status` `"draft"`, `post_title` (below), `post_content` = part 1 + `/*@@TOKEN@@*/` when there are
  more parts (part 1 already ends with a newline; the placeholder follows it directly; no newline after the placeholder),
- `meta_input`:
  - library and safety net (shortcode-only): `{"_wpcode_auto_insert": "0", "_wpcode_code_type": "php", "_wpcode_priority": "10", "_wpcode_note": NOTE}`
  - widgets (everywhere): `{"_wpcode_auto_insert": "1", "_wpcode_auto_insert_number": "1", "_wpcode_code_type": "php", "_wpcode_priority": "10", "_wpcode_note": NOTE}`

Titles: `BLOCK — Homepage library v2.3`, `BLOCK — Homepage safety net v2.3`, `BLOCK — Homepage Elementor widgets v2.3`.
The result says `Post created ID N`.

## 2. Append the parts
For part k = 2..n: `wp_alter_post` `{"ID": N, "field": "post_content", "search": "/*@@TOKEN@@*/", "replace": <part k> + "/*@@TOKEN@@*/"}`
(plain search, not regex; the last part without the placeholder). Each must answer `1 replacement applied`.

## 3. Byte checks (after every 2 or 3 parts, and before the last part)
With `cum` = the part's `cum_bytes` in `plan.json` and `(N40, R) = cum_40k`:
`wp_alter_post` with `regex: true`, `field: "post_content"`, and (the values themselves, not JSON-escaped)
search `\A((?:[\s\S]{40000}){N40}[\s\S]{R})/\*@@TOKEN@@\*/\z` and replace `${1}/*@@TOKEN@@*/`
(no flags: without `u` each `[\s\S]` is one byte). `1 replacement applied` = the post holds exactly `cum` bytes before the
placeholder. `0` = something went wrong in the parts since the last good check: compare and repair, or trash and restart.
For a part with `nonascii: true`, you may add a no-op check of one line with its special characters (`regex: true`,
`flags: "u"`, characters as `\x{HHHH}`, `replace: "$0"`); the final read-back proves everything anyway.

## 4. Put the backslashes back (library only: `placeholders` 2, in part 17)
After the last part: `wp_alter_post` with `regex: true`, search `@@BKBS@@(?=[\s\S]*?(\\))` and replace `${1}` (values themselves;
in the JSON of the call the search is written "@@BKBS@@(?=[\\s\\S]*?(\\\\))")
→ `2 replacements applied` (each placeholder becomes the next real backslash after it, i.e. a backslash).

## 5. Read back and prove it identical
- Whole length: `regex: true`, search `\A(?:[\s\S]{40000}){N40}[\s\S]{R}\z`, replace `$0` with `(N40, R) = divmod(bytes, 40000)` of the
  local file (library `(8, 15955)`, widgets `(0, 33347)`, safety net `(0, 4090)`) → `1 replacement applied`.
- Library and widgets: `wp_get_post {"ID": N}`. When the result is saved to a file (it says "Output too large ... saved to"), run
  `python3 -I D/readback.py <that file> V/<file>` → must print `OK identical` with the expected md5.
- Safety net (small, shown inline) and widgets if its result is inline: the exact-match patterns in `D/safety/pat/pat01.txt` and
  `D/widgets/pat/pat01..04.txt` (`mkpat.py`): for each, `wp_alter_post` `{"regex": true, "flags": "u", "search": <the file's text exactly>, "replace": "$0"}`
  → `1 replacement applied` each (tested locally: each matches once and fails on a changed copy).

## 6. Terms, status and the run list
- `wp_add_post_terms {"ID": N, "taxonomy": "wpcode_type", "terms": [425]}` (php) for all three.
- Widgets only: `wp_add_post_terms {"ID": W, "taxonomy": "wpcode_location", "terms": [426]}` (everywhere).
- Check `wp_get_post_terms` for both taxonomies: L and S have **no** wpcode_location term; W has exactly 426.
- Library and safety net: `wp_update_post {"ID": N, "fields": {"post_status": "publish"}}`. The widgets snippet stays **draft**:
  the owner switches it on in WPCode → Code Snippets (that toggle publishes it and adds it to WPCode's run list).
- `wp_get_option {"key": "wpcode_snippets", "raw": true}` (saved to a file: parse it with python): none of L, S, W in the run list.

## 7. Option and Elementor data
- `wp_update_option {"key": "blockke_home_widgets", "value": {"lib_snippet": L}}`; read back with `raw: true`.
- `cd V && python3 -I rig/fill-safety-id.py S -o out/deploy/elementor-data.json && python3 -I rig/fill-safety-id.py S elementor-data-preview.json -o out/deploy/elementor-data-preview.json && python3 -I rig/fill-safety-id.py --check out/deploy/elementor-data.json && python3 -I rig/fill-safety-id.py --check out/deploy/elementor-data-preview.json`
  Use only these filled files, never the files with `SAFETY_NET_ID`.

## 8. Preview page (draft)
Read page 44129's meta (`wp_get_post_meta {"ID": 44129}`): `_wp_page_template`, `_elementor_version`. Then
`wp_create_post {"post_type": "page", "post_status": "draft", "post_title": "Homepage v2.3 preview (temporary, delete after switch)", "post_content": "[wpcode id=S]", "meta_input": {"_elementor_edit_mode": "builder", "_elementor_template_type": "wp-page", "_wp_page_template": <44129's>, "_elementor_version": <44129's>, "_elementor_data": <the filled preview file's text>}}`
→ P. Read back `_elementor_data` of P and compare with the file byte for byte. `wp_get_rendered {"ID": P}` (a draft renders its
content only, without the theme) and `wp_get_diagnostics {"only": "errors"}`.

## 9. The switch (page 44129)
1. `wp_get_post_meta {"ID": 44129, "key": "_elementor_data"}` must equal, exactly, the v2.2.1 JSON in V/NOTES.md "The live state"
   (the container `bkhome01` with the Shortcode widget `bkhome02` `[wpcode id=45262]`). Keep it: it is the rollback. If it differs,
   stop (someone changed the homepage).
2. `python3 -I V/rig/fill-safety-id.py --check V/out/deploy/elementor-data.json` → ok.
3. `wp_update_post_meta {"ID": 44129, "key": "_elementor_data", "value": <the filled file's text>}`; read it back; identical.
4. `wp_delete_post_meta {"ID": 44129, "key": "_elementor_element_cache"}` and the same for `_elementor_css`.
5. Verify (below). On a broken homepage, roll back at once.

## Rollback
`wp_update_post_meta {"ID": 44129, "key": "_elementor_data", "value": <the v2.2.1 JSON, exactly>}`, delete `_elementor_element_cache`
and `_elementor_css`, read back, `wp_get_rendered {"url": "https://block.ke/"}`. The new snippets can stay (shortcode-only or off).

## Verifying the live page
- `wp_get_rendered {"url": "https://block.ke/"}` and `{"url": "https://block.ke/?bkcheck=N"}` (a new address each time, so no cache
  answers): HTTP 200, the title, the headings, links, visible text. A `?bkdev=Q&bkfor=MODE` address (MODE `buy`, `rent` or
  `offplan`) renders the search's results panel: compare its development links with the same address before the switch.
- `wp_get_diagnostics {"only": "errors"}`: nothing new from `blockke_hm23*`, the snippets or Elementor.
- The 4 Nov way (v2.2.1): `mcp__Block_Website__wp_get_option {"key": "_transient_blockke_home_v23_devs", "raw": true}` exists after a
  front-page render (the v2.3 library ran).
