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
- Dosya adı formatı: `borehole_{secililer|tum_kayitlar}_{Y-m-d_H-i-s}.xlsx`

### Özellikler

1. **Seçili olanları Excel'e Dönüştür**: Sadece seçili kayıtları Excel'e dönüştürür
2. **Tümünü Excel'e Dönüştür**: Tüm kayıtları Excel'e dönüştürür

### Excel Dosyası İçeriği

Excel dosyası aşağıdaki bilgileri içerir:
- Tüm borehole alanları
- Türkçe başlıklar
- Profesyonel formatlanmış tablo
- Otomatik sütun genişliği
- Başlık satırı mavi arka plan
- Tüm hücrelerde border

### Kullanım

1. Borehole listesine gidin
2. İstediğiniz kayıtları seçin (seçili export için)
3. "Seçili olanları Excel'e Dönüştür" veya "Tümünü Excel'e Dönüştür" butonuna tıklayın
4. Excel dosyası otomatik olarak indirilir ve storage'a kaydedilir

### Hata Ayıklama

Eğer Excel export çalışmıyorsa:
1. PhpSpreadsheet kütüphanesinin kurulu olduğundan emin olun
2. `storage/app/media/borehole_exports/` klasörünün yazılabilir olduğundan emin olun
3. October CMS log dosyalarını kontrol edin
