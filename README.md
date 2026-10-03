# Gramhane API: Canlı Altın ve Döviz Fiyatları

Türkiye'de altın ve döviz fiyatları için ücretsiz, anahtarsız bir JSON API.
Gram altın, ayar ve ziynet altınları, ons, gümüş ile USD, EUR, GBP ve CHF kurlarını tek istekte döndürür.

- **Uç nokta:** `https://api.gramhane.com/v1/prices.json`
- **Yöntem:** `GET`
- **Kimlik doğrulama:** Yok
- **Biçim:** JSON (UTF-8)
- **CORS:** Açık (`Access-Control-Allow-Origin: *`); tarayıcıdan doğrudan çağrılabilir.

## Hızlı başlangıç

```bash
curl -A "BenimUygulamam/1.0" https://api.gramhane.com/v1/prices.json
```

> **Önemli:** İsteklerde bir `User-Agent` başlığı gönderin. Bu başlığı göndermeyen istekler sunucu tarafından `403` ile reddedilir.

## Yanıt yapısı

```json
{
  "meta": {
    "status": "success",
    "tarih": "2026-10-03 17:59:00",
    "yayin_sahibi": "Gramhane"
  },
  "kurlar": {
    "ALTIN": { "alis": 6569.12, "satis": 6597.18, "yuzde": 0.09 },
    "USD":   { "alis": 49.12,   "satis": 49.175,  "yuzde": -0.01 },
    "EUR":   { "alis": 55.16,   "satis": 55.30,   "yuzde": -0.02 },
    "GBP":   { "alis": 64.68,   "satis": 64.96,   "yuzde": -0.02 },
    "CHF":   { "alis": 58.71,   "satis": 59.23,   "yuzde": -0.13 },
    "ONS":   { "alis": 4142.06, "satis": 4150.76, "yuzde": 0.05 },
    "GUMUS": { "alis": 95.40,   "satis": 97.06,   "yuzde": 0.12 }
  },
  "ziynet_ve_diger": {
    "22 Ayar Bilezik": { "alis": 5888.75, "satis": 6466.55, "yuzde": 0.09 },
    "Çeyrek (Yeni)":   { "alis": 10650.00, "satis": 10890.00, "yuzde": 0.09 }
  }
}
```

Örnekteki değerler temsilidir.

| Alan | Açıklama |
|---|---|
| `meta.tarih` | Verinin son güncellenme zamanı (Europe/Istanbul, `YYYY-MM-DD HH:MM:SS`) |
| `alis` | Kuyumcunun/bankanın **alış** fiyatı: siz satarken alacağınız tutar |
| `satis` | Kuyumcunun/bankanın **satış** fiyatı: siz alırken ödeyeceğiniz tutar |
| `yuzde` | Son kayda göre değişim yüzdesi (piyasa kapalıyken `0`) |

### `kurlar` anahtarları

| Anahtar | Varlık | Birim |
|---|---|---|
| `ALTIN` | Has altın (gram) | TL |
| `USD`, `EUR`, `GBP`, `CHF` | Döviz | TL |
| `ONS` | Ons altın | USD |
| `GUMUS` | Gümüş (gram) | TL |

### `ziynet_ve_diger` anahtarları

`Has`, `24 Ayar`, `22 Ayar Bilezik`, `18 Ayar`, `14 Ayar`, `Cumhuriyet`,
`Çeyrek (Yeni)`, `Yarım (Yeni)`, `Ziynet Lira (Yeni)`, `2.5'luk (Yeni)`,
`Çeyrek (Eski)`, `Yarım (Eski)`, `Ziynet Lira (Eski)`, `2.5'luk (Eski)`

Anahtarlar Türkçe karakter içerir. JSON'u UTF-8 olarak okuyun.

## Kullanım sınırları

- IP başına **dakikada 60 istek**. Aşılırsa `429 Too Many Requests` döner; `Retry-After` başlığı kaç saniye beklemeniz gerektiğini söyler.
- Veri en sık dakikada bir güncellenir. **30-60 saniyede bir** sorgulamak yeterlidir.
- Çok sayıda kullanıcıya hizmet veriyorsanız yanıtı kendi sunucunuzda önbelleğe alın; her kullanıcı için ayrı istek atmayın.

## Hata kodları

| Kod | Anlamı |
|---|---|
| `200` | Başarılı |
| `403` | `User-Agent` başlığı eksik |
| `429` | Dakikalık istek sınırı aşıldı |
| `500` | Veri geçici olarak kullanılamıyor |

## Örnekler

| Dil | Dosya |
|---|---|
| cURL | [ornekler/curl.sh](ornekler/curl.sh) |
| PHP | [ornekler/php/ornek.php](ornekler/php/ornek.php) |
| Python | [ornekler/python/ornek.py](ornekler/python/ornek.py) |
| Node.js | [ornekler/javascript/ornek-node.mjs](ornekler/javascript/ornek-node.mjs) |
| Tarayıcı (HTML + JS) | [ornekler/javascript/ornek-tarayici.html](ornekler/javascript/ornek-tarayici.html) |
| C# | [ornekler/csharp/Program.cs](ornekler/csharp/Program.cs) |

Her örnek aynı işi yapar: API'yi çağırır, hataları ele alır ve gram altın, dolar ve çeyrek altın fiyatlarını yazdırır.

## Yasal uyarı

Veriler yalnızca bilgilendirme amaçlıdır ve yatırım tavsiyesi değildir.
Fiyatlar kaynaklara ve piyasa koşullarına göre gecikmeli veya farklı olabilir; işlem yapmadan önce kuyumcunuzdan veya bankanızdan teyit edin.

Kaynak belirtirseniz seviniriz: **Veriler: [Gramhane](https://gramhane.com)**

## İletişim

Sorular ve öneriler için: [gramhane.com/iletisim](https://gramhane.com/iletisim)
