#!/usr/bin/env python3
"""readback.py RESULT LOCAL
Compare the post_content in a saved wp_get_post tool result (the file the harness wrote when the result was too large to show)
with the local file LOCAL. Prints the post's id and status, both md5s and byte counts, and OK or the first difference.
Exit 0 only when they are identical."""
import hashlib, json, sys


def find(d):
    if isinstance(d, dict):
        if 'post_content' in d:
            return d
        for v in d.values():
            r = find(v)
            if r is not None:
                return r
    elif isinstance(d, list):
        for v in d:
            r = find(v)
            if r is not None:
                return r
    elif isinstance(d, str):
        s = d.strip()
        if s[:1] in ('{', '['):
            try:
                return find(json.loads(s))
            except Exception:
                return None
    return None


raw = open(sys.argv[1], encoding='utf-8').read()
try:
    top = json.loads(raw)
except Exception:
    top = json.loads(raw[raw.find('{'):])
post = find(top)
if post is None:
    print('no post_content found in', sys.argv[1])
    sys.exit(2)
live = post['post_content']
want = open(sys.argv[2], encoding='utf-8').read()
lm, wm = hashlib.md5(live.encode('utf-8')).hexdigest(), hashlib.md5(want.encode('utf-8')).hexdigest()
print('ID', post.get('ID'), 'status', post.get('post_status'), 'modified', post.get('post_modified'))
print('live md5', lm, len(live.encode('utf-8')), 'bytes; want md5', wm, len(want.encode('utf-8')), 'bytes')
if live == want:
    print('OK identical')
    sys.exit(0)
n = min(len(live), len(want))
i = next((k for k in range(n) if live[k] != want[k]), n)
print('DIFF first difference at character', i)
print('  live:', repr(live[max(0, i - 80):i + 80]))
print('  want:', repr(want[max(0, i - 80):i + 80]))
sys.exit(1)
