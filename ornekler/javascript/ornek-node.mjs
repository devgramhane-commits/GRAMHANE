// Gramhane API: Node.js örneği
// Çalıştırma: node ornek-node.mjs
// Gereksinim: Node.js 18+ (yerleşik fetch)

const API = "https://api.gramhane.com/v1/prices.json";

async function fiyatlariGetir() {
  const yanit = await fetch(API, {
    headers: {
      "User-Agent": "GramhaneOrnek/1.0 (Node.js)", // zorunlu
      Accept: "application/json",
    },
    signal: AbortSignal.timeout(10000),
  });

  if (yanit.status === 429) {
    throw new Error(`İstek sınırı aşıldı, ${yanit.headers.get("Retry-After") ?? 60} sn bekleyin.`);
  }
  if (!yanit.ok) throw new Error(`HTTP ${yanit.status}`);

  const veri = await yanit.json();
  if (veri?.meta?.status !== "success") throw new Error("Beklenmeyen yanıt");
  return veri;
}

const tl = (n) => n.toLocaleString("tr-TR", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " TL";

try {
  const v = await fiyatlariGetir();
  console.log(`Güncelleme : ${v.meta.tarih}`);
  console.log(`Gram Altın : ${tl(v.kurlar.ALTIN.satis)} (%${v.kurlar.ALTIN.yuzde})`);
  console.log(`Dolar      : ${tl(v.kurlar.USD.satis)} (%${v.kurlar.USD.yuzde})`);
  console.log(`Çeyrek     : ${tl(v.ziynet_ve_diger["Çeyrek (Yeni)"].satis)}`);
} catch (hata) {
  console.error(`Hata: ${hata.message}`);
  process.exit(1);
}
