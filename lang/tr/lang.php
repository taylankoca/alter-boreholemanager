<?php return [
    'plugin' => [
        'name' => 'Kuyu Yöneticisi',
        'description' => 'Sondaj kuyularını yönetmek için kapsamlı bir sistem'
    ],

    'navigation' => [
        'boreholes' => 'Kuyular',
        'import' => 'İçe Aktar'
    ],

    'settings' => [
        'boreholes' => 'Kuyu Ayarları',
        'boreholes_description' => 'Kuyu yönetim ayarlarını yapılandırın'
    ],

    'permissions' => [
        'access_boreholes' => 'Kuyulara Erişim',
        'manage_boreholes' => 'Kuyuları Yönet'
    ],

    'boreholes' => [
        'manage_boreholes' => 'Kuyuları Yönet',
        'new_borehole' => 'Yeni Kuyu',
        'create_title' => 'Yeni Kuyu Oluştur',
        'update_title' => 'Kuyu Düzenle',
        'preview_title' => 'Kuyu Önizleme',
        'import' => 'İçe Aktar',
        'export' => 'Dışa Aktar',
        'delete_selected' => 'Seçilenleri Sil',
        'restore_selected' => 'Seçilenleri Geri Yükle',
        'import_boreholes' => 'Kuyuları İçe Aktar',
        'export_boreholes' => 'Kuyuları Dışa Aktar',
        'bulk_delete_success' => ':count kuyu başarıyla silindi.',
        'bulk_restore_success' => ':count kuyu başarıyla geri yüklendi.',

        // Field labels
        'belge_no' => 'Belge No',
        'acildigi_yil' => 'Açıldığı Yıl',
        'derinlik_m' => 'Derinlik (m)',
        'statik_seviye_m' => 'Statik Seviye (m)',
        'dinamik_seviye_m' => 'Dinamik Seviye (m)',
        'pompa_tecrubesi_debisi_litre_sn' => 'Pompa Tecrübesi Debisi (L/sn)',
        'tahsis_amaci' => 'Tahsis Amacı',
        'tahsis_miktari_m3_yil' => 'Tahsis Miktarı (m³/yıl)',
        'sulama_alani_dekar' => 'Sulama Alanı (dekar)',
        'isletme_faaliyet_konusu' => 'İşletme Faaliyet Konusu',
        'belge_sahibi' => 'Belge Sahibi',
        'arazi_sahibi' => 'Arazi Sahibi',
        'adres' => 'Adres',
        'ili' => 'İli',
        'ilcesi' => 'İlçesi',
        'koy_mahalle_mevkii' => 'Köy/Mahalle/Mevkii',
        'pafta_ada_parsel' => 'Pafta/Ada/Parsel',
        'koordinat_utm' => 'Koordinat UTM',
        'kotu_m' => 'Kotu (m)',
        'havza_alt_havza_adi' => 'Havza/Alt Havza Adı',
        'formasyon_litoloji' => 'Formasyon/Litoloji',
        'kuyu_acan_firma_sondor_belge_no' => 'Kuyu Açan Firma/Sondör Belge No',
        'kuyu_derinlik_m_tekrar' => 'Kuyu Derinlik (m) Tekrar',
        'pompa_debisi_ve_gucu_fiskiye_sayisi' => 'Pompa Debisi ve Gücü/Fişkiye Sayısı',
        'statik_seviye_olculebiliyorsa_m' => 'Statik Seviye Ölçülebiliyorsa (m)',
        'dinamik_seviye_pompa_montaj_derinligi' => 'Dinamik Seviye Pompa Montaj Derinliği',
        'sulama_alani_donum' => 'Sulama Alanı (dönüm)',
        'sulama_sistemi' => 'Sulama Sistemi',
        'yilda_ortalama_kac_sulama' => 'Yılda Ortalama Kaç Sulama',
        'bir_sulamada_kac_saat_calisiyor' => 'Bir Sulamada Kaç Saat Çalışıyor',
        'ekilen_urun' => 'Ekilen Ürün',
        'icme_kullanma_sanayi_gunluk_calisma_suresi_saat' => 'İçme/Kullanma/Sanayi Günlük Çalışma Süresi (saat)',
        'icme_kullanma_sanayi_yillik_calisma_suresi_gun' => 'İçme/Kullanma/Sanayi Yıllık Çalışma Süresi (gün)',
        'yillik_calismada_enerji_tuketimi_kw' => 'Yıllık Çalışmada Enerji Tüketimi (kW)',
        'tespit_eden' => 'Tespit Eden',
        'tespit_tarihi' => 'Tespit Tarihi',
        'aciklama' => 'Açıklama',
        'images' => 'Resimler',
        'status' => 'Durum',
        'created_at' => 'Oluşturulma Tarihi',
        'updated_at' => 'Güncellenme Tarihi',

        // Status options
        'status_active' => 'Aktif',
        'status_outdated' => 'Güncel Değil',
        'status_deleted' => 'Silinmiş',

        // Tabs
        'tabs' => [
            'basic_info' => 'Temel Bilgiler',
            'allocation' => 'Tahsis Bilgileri',
            'document' => 'Belge Bilgileri',
            'location' => 'Konum Bilgileri',
            'technical' => 'Teknik Bilgiler',
            'irrigation' => 'Sulama Bilgileri',
            'energy' => 'Enerji Bilgileri',
            'detection' => 'Tespit Bilgileri',
            'images' => 'Resimler'
        ],

        // Validation messages
        'validation' => [
            'belge_no_required' => 'Belge numarası zorunludur.',
            'belge_no_unique' => 'Bu belge numarası zaten kullanılmaktadır.',
            'derinlik_numeric' => 'Derinlik sayısal bir değer olmalıdır.',
            'derinlik_min' => 'Derinlik 0\'dan büyük olmalıdır.',
            'acildigi_yil_integer' => 'Açıldığı yıl tam sayı olmalıdır.',
            'acildigi_yil_min' => 'Açıldığı yıl 1900\'den küçük olamaz.',
            'acildigi_yil_max' => 'Açıldığı yıl gelecek yıldan büyük olamaz.',
            'tespit_tarihi_date' => 'Tespit tarihi geçerli bir tarih olmalıdır.'
        ]
    ],

    'components' => [
        'borehole_list' => [
            'name' => 'Kuyu Listesi',
            'description' => 'Kuyuları listeleyen component',
            'records_per_page' => 'Sayfa Başına Kayıt',
            'records_per_page_description' => 'Bir sayfada gösterilecek kuyu sayısı',
            'records_per_page_validation' => 'Geçerli bir sayı giriniz',
            'sort_column' => 'Sıralama Sütunu',
            'sort_column_description' => 'Hangi sütuna göre sıralanacağı',
            'sort_direction' => 'Sıralama Yönü',
            'sort_direction_description' => 'Sıralama yönü',
            'sort_asc' => 'Artan',
            'sort_desc' => 'Azalan',
            'show_deleted' => 'Silinmişleri Göster',
            'show_deleted_description' => 'Silinmiş kuyuları da listele',
            'province' => 'İl',
            'province_description' => 'Filtreleme için il seçin',
            'district' => 'İlçe',
            'district_description' => 'Filtreleme için ilçe seçin',
            'purpose' => 'Tahsis Amacı',
            'purpose_description' => 'Filtreleme için tahsis amacı seçin'
        ],
        'borehole_detail' => [
            'name' => 'Kuyu Detayı',
            'description' => 'Tek bir kuyunun detaylarını gösteren component',
            'id' => 'Kuyu ID',
            'id_description' => 'Gösterilecek kuyunun ID\'si',
            'id_validation' => 'Geçerli bir ID giriniz',
            'belge_no' => 'Belge No',
            'belge_no_description' => 'Gösterilecek kuyunun belge numarası',
            'show_deleted' => 'Silinmişleri Göster',
            'show_deleted_description' => 'Silinmiş kuyuları da göster'
        ],
        'borehole_map' => [
            'name' => 'Kuyu Haritası',
            'description' => 'Kuyuları harita üzerinde gösteren component',
            'max_records' => 'Maksimum Kayıt',
            'max_records_description' => 'Haritada gösterilecek maksimum kuyu sayısı',
            'max_records_validation' => 'Geçerli bir sayı giriniz',
            'province' => 'İl',
            'province_description' => 'Filtreleme için il seçin',
            'district' => 'İlçe',
            'district_description' => 'Filtreleme için ilçe seçin',
            'purpose' => 'Tahsis Amacı',
            'purpose_description' => 'Filtreleme için tahsis amacı seçin',
            'map_center' => 'Harita Merkezi',
            'map_center_description' => 'Haritanın merkez koordinatları (lat,lng)',
            'map_zoom' => 'Harita Yakınlaştırma',
            'map_zoom_description' => 'Haritanın yakınlaştırma seviyesi',
            'map_zoom_validation' => 'Geçerli bir sayı giriniz'
        ]
    ]
];