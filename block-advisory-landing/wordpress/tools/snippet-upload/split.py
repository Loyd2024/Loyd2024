#!/usr/bin/env python3
"""Split a WPCode snippet body into upload parts for the site connection.
Usage: split.py SRC OUTDIR [LIMIT]
- Every backslash directly before u+4 hex digits becomes @@BKBS@@ (the connector turns \\uXXXX into the character);
  after the last part, one regex call puts them back from the next real backslash.
- Parts end on line boundaries, at most LIMIT bytes of text each (default 14000).
- Writes OUTDIR/pNN.txt, OUTDIR/upload.txt and OUTDIR/plan.json (part sizes, cumulative byte counts, md5s, placeholder count)."""
import hashlib, json, os, re, sys
src, out = sys.argv[1], sys.argv[2]
limit = int(sys.argv[3]) if len(sys.argv) > 3 else 14000
text = open(src, encoding='utf-8').read()
assert '\r' not in text, 'CR found'
assert '@@BKBS@@' not in text and '/*@@NEXT@@*/' not in text
up = re.sub(r'\\(?=u[0-9a-fA-F]{4})', '@@BKBS@@', text)
assert up.replace('@@BKBS@@', '\\') == text
os.makedirs(out, exist_ok=True)
for f in os.listdir(out):
    if re.match(r'p\d+\.txt$', f): os.remove(os.path.join(out, f))
lines = up.split('\n'); parts = []; cur = ''
for i, l in enumerate(lines):
    piece = l + ('\n' if i < len(lines) - 1 else '')
    if len(cur) + len(piece) > limit and cur:
        parts.append(cur); cur = ''
    cur += piece
parts.append(cur)
assert ''.join(parts) == up
cum = 0; plan = []
for i, p in enumerate(parts, 1):
    open(os.path.join(out, 'p%02d.txt' % i), 'w', encoding='utf-8').write(p)
    b = len(p.encode('utf-8')); cum += b
    plan.append({'part': i, 'chars': len(p), 'bytes': b, 'cum_bytes': cum, 'cum_40k': divmod(cum, 40000), 'nonascii': bool(re.search(r'[^\x00-\x7f]', p)), 'placeholders': p.count('@@BKBS@@')})
open(os.path.join(out, 'upload.txt'), 'w', encoding='utf-8').write(up)
info = {'src': src, 'md5': hashlib.md5(text.encode('utf-8')).hexdigest(), 'bytes': len(text.encode('utf-8')), 'ends_with_newline': text.endswith('\n'),
        'placeholders': up.count('@@BKBS@@'), 'backslashes_after_last_placeholder': (text[up.rfind('@@BKBS@@'):].count('\\') if '@@BKBS@@' in up else None), 'parts': plan}
json.dump(info, open(os.path.join(out, 'plan.json'), 'w'), indent=1)
print(json.dumps({k: v for k, v in info.items() if k != 'parts'}), len(parts), 'parts')
