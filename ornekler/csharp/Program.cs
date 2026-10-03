// Gramhane API: C# örneği
// Çalıştırma: dotnet new console -n GramhaneOrnek && Program.cs dosyasını kopyalayın && dotnet run
// Gereksinim: .NET 6+

using System.Globalization;
using System.Net;
using System.Text.Json;

const string Api = "https://api.gramhane.com/v1/prices.json";
var tr = new CultureInfo("tr-TR");

using var http = new HttpClient { Timeout = TimeSpan.FromSeconds(10) };
http.DefaultRequestHeaders.UserAgent.ParseAdd("GramhaneOrnek/1.0"); // zorunlu

try
{
    using var yanit = await http.GetAsync(Api);
    if (yanit.StatusCode == (HttpStatusCode)429)
    {
        Console.Error.WriteLine("İstek sınırı aşıldı (dakikada 60). Biraz bekleyin.");
        return 1;
    }
    yanit.EnsureSuccessStatusCode();

    using var belge = JsonDocument.Parse(await yanit.Content.ReadAsStringAsync());
    var kok = belge.RootElement;
    var kurlar = kok.GetProperty("kurlar");
    var ziynet = kok.GetProperty("ziynet_ve_diger");

    decimal Satis(JsonElement e) => e.GetProperty("satis").GetDecimal();
    decimal Yuzde(JsonElement e) => e.GetProperty("yuzde").GetDecimal();

    Console.WriteLine($"Güncelleme : {kok.GetProperty("meta").GetProperty("tarih").GetString()}");
    Console.WriteLine($"Gram Altın : {Satis(kurlar.GetProperty("ALTIN")).ToString("N2", tr)} TL (%{Yuzde(kurlar.GetProperty("ALTIN"))})");
    Console.WriteLine($"Dolar      : {Satis(kurlar.GetProperty("USD")).ToString("N2", tr)} TL (%{Yuzde(kurlar.GetProperty("USD"))})");
    Console.WriteLine($"Çeyrek     : {Satis(ziynet.GetProperty("Çeyrek (Yeni)")).ToString("N2", tr)} TL");
    return 0;
}
catch (Exception ex)
{
    Console.Error.WriteLine($"Hata: {ex.Message}");
    return 1;
}
