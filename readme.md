# HS Elektrostav

Web firmy HS Elektrostav s.r.o. – https://www.hselektrostav.cz

Statický web, který generuje skript `site/build.py` (stačí Python 3, nepotřebuje žádné závislosti).

## Úpravy

- texty stránek: `site/src/pages/*.html` (O nás = `index.html`)
- společná hlavička a patička: `site/src/layout.html`
- vzhled: `site/src/css/style.css`
- fotogalerie: fotky vložte do `site/src/images/foto/`, řadí se podle názvu souboru a náhledy vzniknou automaticky (přes `sips` na macOS)

## Build a náhled

```sh
python3 site/build.py           # vygeneruje site/dist/
python3 site/build.py --serve   # vygeneruje a spustí náhled na http://localhost:8000
```

## Nasazení

Obsah `site/dist/` (včetně `.htaccess`) se nahraje přes FTPS do složky `/web` na hostingu Yellowpoint.
Složky `stats/` a `error/` na serveru patří hostingu, nemažte je.
