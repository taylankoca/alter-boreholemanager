<?php namespace Livstag\BoreholeManager\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Livstag\BoreholeManager\Models\Borehole;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Storage;
use Response;

class Boreholes extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'borehole_manager' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Livstag.BoreholeManager', 'boreholes', 'boreholes');
    }

    /**
     * Seçili kayıtları Excel'e dönüştür
     */
    public function onExcel()
    {
        $checkedIds = post('checked', []);
        
        if (empty($checkedIds)) {
            \Flash::error('Lütfen en az bir kayıt seçin.');
            return;
        }

        $boreholes = Borehole::whereIn('id', $checkedIds)->get();
        
        if ($boreholes->isEmpty()) {
            \Flash::error('Seçili kayıtlar bulunamadı.');
            return;
        }

        $this->generateIndividualExcelFiles($boreholes);
    }

    /**
     * Tüm kayıtları Excel'e dönüştür
     */
    public function onExcelAll()
    {
        $boreholes = Borehole::all();
        
        if ($boreholes->isEmpty()) {
            \Flash::error('Dönüştürülecek kayıt bulunamadı.');
            return;
        }

        $this->generateIndividualExcelFiles($boreholes);
    }

    /**
     * Her borehole kaydı için ayrı Excel dosyası oluştur
     */
    private function generateIndividualExcelFiles($boreholes)
    {
        $createdFiles = [];
        $errors = [];

        foreach ($boreholes as $borehole) {
            try {
                $filename = $this->generateSingleBoreholeExcel($borehole);
                if ($filename) {
                    $createdFiles[] = $filename;
                }
            } catch (\Exception $e) {
                $errors[] = "ID {$borehole->id} için hata: " . $e->getMessage();
                \Log::error("Borehole Excel Export Error for ID {$borehole->id}: " . $e->getMessage());
            }
        }

        // Sonuç mesajı
        if (!empty($createdFiles)) {
            $count = count($createdFiles);
            \Flash::success("{$count} adet Excel dosyası başarıyla oluşturuldu: " . implode(', ', $createdFiles));
        }

        if (!empty($errors)) {
            \Flash::error("Bazı dosyalar oluşturulamadı: " . implode('; ', $errors));
        }
    }

    /**
     * Tek bir borehole kaydı için Excel dosyası oluştur
     */
    private function generateSingleBoreholeExcel($borehole)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Dosya adını oluştur (5 haneli ID formatı)
        $formattedId = str_pad($borehole->id, 5, '0', STR_PAD_LEFT);
        $filename = "ALT-{$formattedId}.xlsx";
        
        // Alan adları ve değerleri
        $fields = [
            'ID' => $borehole->id,
            'Açıldığı Yıl' => $borehole->acildigi_yil,
            'Derinlik (m)' => $borehole->derinlik_m,
            'Statik Seviye (m)' => $borehole->statik_seviye_m,
            'Dinamik Seviye (m)' => $borehole->dinamik_seviye_m,
            'Pompa Tecrübesi Debisi (lt/sn)' => $borehole->pompa_tecrubesi_debisi_litre_sn,
            'Tahsis Amacı' => $borehole->tahsis_amaci,
            'Tahsis Miktarı (m³/yıl)' => $borehole->tahsis_miktari_m3_yil,
            'Sulama Alanı (dekar)' => $borehole->sulama_alani_dekar,
            'İşletme Faaliyet Konusu' => $borehole->isletme_faaliyet_konusu,
            'Belge No' => $borehole->belge_no,
            'Belge Sahibi' => $borehole->belge_sahibi,
            'Arazi Sahibi' => $borehole->arazi_sahibi,
            'Adres' => $borehole->adres,
            'İli' => $borehole->ili,
            'İlçesi' => $borehole->ilcesi,
            'Köy/Mahalle/Mevkii' => $borehole->koy_mahalle_mevkii,
            'Pafta/Ada/Parsel' => $borehole->pafta_ada_parsel,
            'Koordinat UTM' => $borehole->koordinat_utm,
            'Kotu (m)' => $borehole->kotu_m,
            'Havza/Alt Havza Adı' => $borehole->havza_alt_havza_adi,
            'Formasyon/Litoloji' => $borehole->formasyon_litoloji,
            'Kuyu Açan Firma/Sondör Belge No' => $borehole->kuyu_acan_firma_sondor_belge_no,
            'Kuyu Derinlik (m) Tekrar' => $borehole->kuyu_derinlik_m_tekrar,
            'Pompa Debisi ve Gücü/Fişkiye Sayısı' => $borehole->pompa_debisi_ve_gucu_fiskiye_sayisi,
            'Statik Seviye Ölçülebiliyorsa (m)' => $borehole->statik_seviye_olculebiliyorsa_m,
            'Dinamik Seviye/Pompa Montaj Derinliği' => $borehole->dinamik_seviye_pompa_montaj_derinligi,
            'Sulama Alanı (dönüm)' => $borehole->sulama_alani_donum,
            'Sulama Sistemi' => $borehole->sulama_sistemi,
            'Yılda Ortalama Kaç Sulama' => $borehole->yilda_ortalama_kac_sulama,
            'Bir Sulamada Kaç Saat Çalışıyor' => $borehole->bir_sulamada_kac_saat_calisiyor,
            'Ekilen Ürün' => $borehole->ekilen_urun,
            'İçme/Kullanma/Sanayi Günlük Çalışma Süresi (saat)' => $borehole->icme_kullanma_sanayi_gunluk_calisma_suresi_saat,
            'İçme/Kullanma/Sanayi Yıllık Çalışma Süresi (gün)' => $borehole->icme_kullanma_sanayi_yillik_calisma_suresi_gun,
            'Yıllık Çalışmada Enerji Tüketimi (kW)' => $borehole->yillik_calismada_enerji_tuketimi_kw,
            'Tespit Eden' => $borehole->tespit_eden,
            'Tespit Tarihi' => $borehole->tespit_tarihi ? $borehole->tespit_tarihi->format('d.m.Y') : '',
            'Açıklama' => $borehole->aciklama,
            'Oluşturulma Tarihi' => $borehole->created_at ? $borehole->created_at->format('d.m.Y H:i') : '',
            'Güncellenme Tarihi' => $borehole->updated_at ? $borehole->updated_at->format('d.m.Y H:i') : ''
        ];

        // Başlık satırı
        $sheet->setCellValue('A1', 'Alan Adı');
        $sheet->setCellValue('B1', 'Değer');

        // Başlık stilini ayarla
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Veri satırlarını yaz
        $row = 2;
        foreach ($fields as $fieldName => $value) {
            $sheet->setCellValue("A{$row}", $fieldName);
            $sheet->setCellValue("B{$row}", $value);
            $row++;
        }

        // Sütun genişliklerini ayarla
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);

        // Veri aralığına border ekle
        $dataRange = 'A1:B' . ($row - 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // A sütununu kalın yap (alan adları için)
        $sheet->getStyle('A2:A' . ($row - 1))->applyFromArray([
            'font' => ['bold' => true]
        ]);

        // Geçici dosya oluştur
        $tempFile = tempnam(sys_get_temp_dir(), 'borehole_excel_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        // Storage'a kaydet
        $storagePath = "media/borehole_exports/{$filename}";
        Storage::put($storagePath, file_get_contents($tempFile));
        
        // Geçici dosyayı sil
        unlink($tempFile);

        return $filename;
    }
}
