#!/usr/bin/env python3
"""Build statického webu HS Elektrostav.

Použití:  python3 build.py            -> vygeneruje dist/
          python3 build.py --serve    -> vygeneruje dist/ a spustí náhled na http://localhost:8000

Fotky do galerie stačí nahrát do src/images/foto/ (jpg/png), řadí se podle názvu souboru.
Náhledy se generují nástrojem `sips` (macOS); jinde se použije originál.
"""
import html
import re
import shutil
import subprocess
import sys
import time
from pathlib import Path

ROOT = Path(__file__).resolve().parent
SRC = ROOT / 'src'
DIST = ROOT / 'dist'
SITE_URL = 'https://www.hselektrostav.cz'
THUMB_SIZE = 640
TEASER_COUNT = 9
TEASER_PICKS = ['027', '004', '025', '034', '010', '041', '016', '043', '038']

NAV_KEYS = ('home', 'foto', 'contact')


def parse_page(path):
	text = path.read_text(encoding='utf-8')
	m = re.match(r'^---\n(.*?)\n---\n', text, re.S)
	meta = {}
	for line in m.group(1).splitlines():
		key, _, value = line.partition(':')
		meta[key.strip()] = value.strip()
	return meta, text[m.end():]


def photos():
	folder = SRC / 'images' / 'foto'
	return sorted(p for p in folder.iterdir() if p.suffix.lower() in ('.jpg', '.jpeg', '.png'))


def make_thumbs(files):
	out = DIST / 'images' / 'foto' / 'nahled'
	out.mkdir(parents=True, exist_ok=True)
	has_sips = shutil.which('sips') is not None
	for f in files:
		target = out / f.name
		if target.exists() and target.stat().st_mtime >= f.stat().st_mtime:
			continue
		if has_sips:
			subprocess.run(['sips', '-Z', str(THUMB_SIZE), '-s', 'formatOptions', '78', str(f), '--out', str(target)],
						   check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
		else:
			shutil.copy2(f, target)


def gallery_item(f, i, eager=False):
	alt = 'Ukázka realizace č. %d' % (i + 1)
	loading = 'eager' if eager else 'lazy'
	return ('\t\t\t<a class="g-item" href="/images/foto/{name}">'
			'<img src="/images/foto/nahled/{name}" alt="{alt}" loading="{loading}" decoding="async"></a>').format(
		name=html.escape(f.name), alt=alt, loading=loading)


def build():
	if DIST.exists():
		shutil.rmtree(DIST)
	shutil.copytree(SRC, DIST, ignore=shutil.ignore_patterns('pages', 'layout.html', '.DS_Store'))

	files = photos()
	make_thumbs(files)
	shutil.copy2(SRC / 'images' / 'foto' / '027.jpg', DIST / 'images' / 'og.jpg')

	by_stem = {f.stem: (i, f) for i, f in enumerate(files)}
	teaser = [by_stem[s] for s in TEASER_PICKS if s in by_stem][:TEASER_COUNT]
	blocks = {
		'gallery_all': '\n'.join(gallery_item(f, i, eager=i < 8) for i, f in enumerate(files)),
		'gallery_teaser': '\n'.join(
			gallery_item(f, i).replace('href="/images/foto/%s"' % f.name, 'href="/fotogalerie/"') for i, f in teaser),
	}

	layout = (SRC / 'layout.html').read_text(encoding='utf-8')
	version = str(int(time.time()))
	urls = []

	for page in sorted((SRC / 'pages').glob('*.html')):
		meta, body = parse_page(page)
		for key, value in blocks.items():
			body = body.replace('{{%s}}' % key, value)

		out = layout
		replacements = {
			'title': html.escape(meta['title']),
			'description': html.escape(meta['description']),
			'page': page.stem,
			'version': version,
			'content': body.rstrip(),
		}
		for key in NAV_KEYS:
			replacements['nav_' + key] = 'aria-current="page"' if meta.get('nav') == key else ''
		for key, value in replacements.items():
			out = out.replace('{{%s}}' % key, value)

		if 'path' in meta:
			target = DIST / meta['path']
		elif page.stem == 'index':
			target = DIST / 'index.html'
			urls.append('/')
		else:
			target = DIST / page.stem / 'index.html'
			urls.append('/%s/' % page.stem)
		target.parent.mkdir(parents=True, exist_ok=True)
		target.write_text(out, encoding='utf-8')

	sitemap = ['<?xml version="1.0" encoding="UTF-8"?>',
			   '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">']
	sitemap += ['\t<url><loc>%s%s</loc></url>' % (SITE_URL, u) for u in urls]
	sitemap.append('</urlset>')
	(DIST / 'sitemap.xml').write_text('\n'.join(sitemap) + '\n', encoding='utf-8')
	(DIST / 'robots.txt').write_text('User-agent: *\nAllow: /\n\nSitemap: %s/sitemap.xml\n' % SITE_URL, encoding='utf-8')

	print('Hotovo: %s – stránek: %d, fotek: %d' % (DIST, len(urls) + 1, len(files)))


def serve(port=8000):
	import functools
	import http.server

	class Handler(http.server.SimpleHTTPRequestHandler):
		def send_error(self, code, message=None, explain=None):
			if code == 404:
				body = (DIST / '404.html').read_bytes()
				self.send_response(404)
				self.send_header('Content-Type', 'text/html; charset=utf-8')
				self.send_header('Content-Length', str(len(body)))
				self.end_headers()
				self.wfile.write(body)
			else:
				super().send_error(code, message, explain)

	handler = functools.partial(Handler, directory=str(DIST))
	with http.server.ThreadingHTTPServer(('127.0.0.1', port), handler) as httpd:
		print('Náhled: http://localhost:%d  (Ctrl+C ukončí)' % port)
		httpd.serve_forever()


if __name__ == '__main__':
	build()
	if '--serve' in sys.argv:
		serve()
