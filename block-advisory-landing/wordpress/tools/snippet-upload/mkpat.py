#!/usr/bin/env python3
"""mkpat.py FILE [--parts N] [--out DIR]
Write PCRE patterns that match FILE's text exactly where it sits in a post, for wp_alter_post with
regex: true, flags: "u", replace: "$0" (a no-op: "1 replacement applied" proves the post holds exactly this text there).
- One part (default): \\A<the whole text>\\z, for a small post such as the safety net.
- --parts N: N patterns, each anchored at its character offset (\\A(?:[\\s\\S]{40000}){k}[\\s\\S]{r}<segment>), the last one ending
  with \\z; together they prove the whole post. Use 3-4 parts for a 30 KB post.
Escaping: PCRE metacharacters get a backslash; every backslash in the text is written \\x5C and every non-ASCII character
\\x{HHHH}, so no pattern contains a backslash followed by u (the connector turns \\uXXXX into a character).
Writes DIR/pat01.txt ... (default DIR: next to FILE, FILE.pat/) and prints their sizes."""
import os, sys


def esc(s):
    out = []
    for ch in s:
        o = ord(ch)
        if ch == '\\':
            out.append('\\x5C')
        elif ch in '^$.|?*+()[]{}':
            out.append('\\' + ch)
        elif o > 0x7E:
            out.append('\\x{%04X}' % o)
        elif o < 0x20 and ch not in '\n\t':
            out.append('\\x{%02X}' % o)
        else:
            out.append(ch)
    return ''.join(out)


def anchor(chars):
    k, r = divmod(chars, 40000)
    return '\\A' + ('(?:[\\s\\S]{40000}){%d}' % k if k else '') + ('[\\s\\S]{%d}' % r if r else '')


args = sys.argv[1:]
src = args[0]
n = int(args[args.index('--parts') + 1]) if '--parts' in args else 1
out = args[args.index('--out') + 1] if '--out' in args else src + '.pat'
text = open(src, encoding='utf-8').read()
os.makedirs(out, exist_ok=True)
lines = text.split('\n')
# split on line boundaries into n nearly equal parts (by characters)
target = len(text) / n
segs, cur = [], ''
for i, l in enumerate(lines):
    piece = l + ('\n' if i < len(lines) - 1 else '')
    if len(segs) < n - 1 and cur and len(cur) + len(piece) > target:
        segs.append(cur)
        cur = ''
    cur += piece
segs.append(cur)
assert ''.join(segs) == text
off = 0
for i, s in enumerate(segs, 1):
    pat = ('\\A' if off == 0 else anchor(off)) + esc(s) + ('\\z' if i == len(segs) else '')
    assert '\\u' not in pat.replace('\\\\', '')
    open(os.path.join(out, 'pat%02d.txt' % i), 'w', encoding='utf-8').write(pat)
    print('pat%02d.txt' % i, 'chars', len(s), 'offset', off, 'pattern bytes', len(pat.encode('utf-8')))
    off += len(s)
