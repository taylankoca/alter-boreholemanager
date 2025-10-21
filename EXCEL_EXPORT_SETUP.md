# Excel Export Özelliği Kurulum Rehberi

## Gereksinimler

Bu plugin Excel export özelliği için PhpSpreadsheet kütüphanesine ihtiyaç duyar.

### PhpSpreadsheet Kurulumu

Ana October CMS projenizin kök dizininde (plugin klasörü değil) aşağıdaki komutu çalıştırın:

```bash
composer require phpoffice/phpspreadsheet
```

### Klasör Yapısı

Excel dosyaları şu konuma kaydedilir:
- `storage/app/media/borehole_exports/` klasörüne kaydedilir
- Dosya adı formatı: `ALT-{5_haneli_ID}.xlsx` (örn: ALT-00001.xlsx, ALT-00015.xlsx)

### Özellikler

1. **Seçili olanları Excel'e Dönüştür**: Seçili her kayıt için ayrı Excel dosyası oluşturur
2. **Tümünü Excel'e Dönüştür**: Tüm kayıtlar için ayrı ayrı Excel dosyaları oluşturur

### Excel Dosyası Formatı

Her Excel dosyası:
- **Tek bir borehole kaydının** bilgilerini içerir
- **İki sütun formatında** düzenlenmiştir:
  - A sütunu: Alan adları (kalın yazı)
  - B sütunu: Değerler
- **Türkçe alan adları** kullanır
- **Profesyonel formatlanmış** tablo
- **Mavi başlık satırı** (Alan Adı - Değer)
- **Tüm hücrelerde border**
- **Sabit sütun genişlikleri** (A: 40, B: 30)

### Dosya Adlandırma

- ID 1 → ALT-00001.xlsx
- ID 15 → ALT-00015.xlsx
- ID 123 → ALT-00123.xlsx
- ID 9999 → ALT-09999.xlsx

### Kullanım

1. Borehole listesine gidin
2. İstediğiniz kayıtları seçin (seçili export için)
3. "Seçili olanları Excel'e Dönüştür" veya "Tümünü Excel'e Dönüştür" butonuna tıklayın
4. Her kayıt için ayrı Excel dosyası oluşturulur ve storage'a kaydedilir
5. Başarı mesajında oluşturulan dosya adları listelenir

### Örnek Excel İçeriği

```
| Alan Adı                    | Değer                    |
|----------------------------|--------------------------|
| ID                         | 1                        |
| Açıldığı Yıl               | 2020                     |
| Derinlik (m)               | 150                      |
| Statik Seviye (m)          | 25                       |
| ...                        | ...                      |
```

### Hata Ayıklama

Eğer Excel export çalışmıyorsa:
1. PhpSpreadsheet kütüphanesinin kurulu olduğundan emin olun
2. `storage/app/media/borehole_exports/` klasörünün yazılabilir olduğundan emin olun
3. October CMS log dosyalarını kontrol edin
4. Hata mesajları hangi ID'ler için sorun olduğunu gösterecektir
