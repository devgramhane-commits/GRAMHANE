#!/usr/bin/env bash
# Gramhane API: cURL örneği
# Gereksinim: curl (jq varsa çıktı biçimlendirilir)

URL="https://api.gramhane.com/v1/prices.json"

# User-Agent zorunludur; göndermeyen istekler 403 alır.
YANIT=$(curl -s -w "\n%{http_code}" -A "GramhaneOrnek/1.0" "$URL")
KOD=$(echo "$YANIT" | tail -n1)
GOVDE=$(echo "$YANIT" | sed '$d')

if [ "$KOD" != "200" ]; then
  echo "Hata: HTTP $KOD"
  exit 1
fi

if command -v jq >/dev/null 2>&1; then
  echo "$GOVDE" | jq -r '
    "Güncelleme : \(.meta.tarih)",
    "Gram Altın : \(.kurlar.ALTIN.satis) TL (\(.kurlar.ALTIN.yuzde)%)",
    "Dolar      : \(.kurlar.USD.satis) TL (\(.kurlar.USD.yuzde)%)",
    "Çeyrek     : \(.ziynet_ve_diger["Çeyrek (Yeni)"].satis) TL"
  '
else
  echo "$GOVDE"
fi
