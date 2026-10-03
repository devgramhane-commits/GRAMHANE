"""
Gramhane API: Python örneği
Çalıştırma: python ornek.py
Gereksinim: Python 3.8+ (ek paket gerekmez)
"""
import json
import sys
import time
import urllib.error
import urllib.request

API = "https://api.gramhane.com/v1/prices.json"


def fiyatlari_getir() -> dict:
    # User-Agent zorunludur; urllib'in varsayılanı bazı sunucularda engellenir.
    istek = urllib.request.Request(API, headers={
        "User-Agent": "GramhaneOrnek/1.0 (Python)",
        "Accept": "application/json",
    })
    try:
        with urllib.request.urlopen(istek, timeout=10) as yanit:
            veri = json.loads(yanit.read().decode("utf-8"))
    except urllib.error.HTTPError as e:
        if e.code == 429:
            bekle = e.headers.get("Retry-After", "60")
            raise RuntimeError(f"İstek sınırı aşıldı, {bekle} sn bekleyin.")
        raise RuntimeError(f"HTTP {e.code}")
    except urllib.error.URLError as e:
        raise RuntimeError(f"Bağlantı hatası: {e.reason}")

    if veri.get("meta", {}).get("status") != "success":
        raise RuntimeError("Beklenmeyen yanıt")
    return veri


def tl(sayi: float) -> str:
    return f"{sayi:,.2f} TL".replace(",", "X").replace(".", ",").replace("X", ".")


def yazdir(v: dict) -> None:
    k, z = v["kurlar"], v["ziynet_ve_diger"]
    print(f"Güncelleme : {v['meta']['tarih']}")
    print(f"Gram Altın : {tl(k['ALTIN']['satis'])} (%{k['ALTIN']['yuzde']})")
    print(f"Dolar      : {tl(k['USD']['satis'])} (%{k['USD']['yuzde']})")
    print(f"Çeyrek     : {tl(z['Çeyrek (Yeni)']['satis'])}")


if __name__ == "__main__":
    # --izle: 60 saniyede bir yenile (dakikada 60 istek sınırının çok altında)
    izle = "--izle" in sys.argv
    while True:
        try:
            yazdir(fiyatlari_getir())
        except RuntimeError as hata:
            print(f"Hata: {hata}", file=sys.stderr)
            if not izle:
                sys.exit(1)
        if not izle:
            break
        print("-" * 40)
        time.sleep(60)
