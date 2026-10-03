<?php
/**
 * Gramhane API: PHP örneği
 * Çalıştırma: php ornek.php
 * Gereksinim: PHP 7.4+ ve curl eklentisi
 */

const GRAMHANE_API = 'https://api.gramhane.com/v1/prices.json';

/**
 * Fiyatları çeker. Hata durumunda istisna fırlatır.
 */
function gramhaneFiyatlar(): array
{
    $ch = curl_init(GRAMHANE_API);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'GramhaneOrnek/1.0 (PHP)', // zorunlu
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $govde = curl_exec($ch);
    $kod = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hata = curl_error($ch);
    curl_close($ch);

    if ($govde === false) {
        throw new RuntimeException('Bağlantı hatası: ' . $hata);
    }
    if ($kod === 429) {
        throw new RuntimeException('İstek sınırı aşıldı (dakikada 60). Biraz bekleyin.');
    }
    if ($kod !== 200) {
        throw new RuntimeException('HTTP ' . $kod);
    }

    $veri = json_decode($govde, true);
    if (!is_array($veri) || ($veri['meta']['status'] ?? '') !== 'success') {
        throw new RuntimeException('Beklenmeyen yanıt');
    }
    return $veri;
}

function tl(float $sayi): string
{
    return number_format($sayi, 2, ',', '.') . ' TL';
}

try {
    $v = gramhaneFiyatlar();

    echo 'Güncelleme : ' . $v['meta']['tarih'] . PHP_EOL;
    echo 'Gram Altın : ' . tl($v['kurlar']['ALTIN']['satis']) . ' (%' . $v['kurlar']['ALTIN']['yuzde'] . ')' . PHP_EOL;
    echo 'Dolar      : ' . tl($v['kurlar']['USD']['satis']) . ' (%' . $v['kurlar']['USD']['yuzde'] . ')' . PHP_EOL;
    echo 'Çeyrek     : ' . tl($v['ziynet_ve_diger']['Çeyrek (Yeni)']['satis']) . PHP_EOL;
    echo PHP_EOL . 'Tüm ziynet altınları:' . PHP_EOL;
    foreach ($v['ziynet_ve_diger'] as $ad => $f) {
        printf("  %-20s alış %12s   satış %12s\n", $ad, tl($f['alis']), tl($f['satis']));
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Hata: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
