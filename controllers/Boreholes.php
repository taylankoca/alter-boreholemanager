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

        $this->generateExcelFile($boreholes, 'secililer');
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

        $this->generateExcelFile($boreholes, 'tum_kayitlar');
    }

    /**
     * Excel dosyası oluştur ve storage'a kaydet
     */
    private function generateExcelFile($boreholes, $filenamePrefix)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Başlık satırı
            $headers = [
                'ID', 'Açıldığı Yıl', 'Derinlik (m)', 'Statik Seviye (m)', 'Dinamik Seviye (m)',
                'Pompa Tecrübesi Debisi (lt/sn)', 'Tahsis Amacı', 'Tahsis Miktarı (m³/yıl)',
                'Sulama Alanı (dekar)', 'İşletme Faaliyet Konusu', 'Belge No', 'Belge Sahibi',
                'Arazi Sahibi', 'Adres', 'İli', 'İlçesi', 'Köy/Mahalle/Mevkii',
                'Pafta/Ada/Parsel', 'Koordinat UTM', 'Kotu (m)', 'Havza/Alt Havza Adı',
                'Formasyon/Litoloji', 'Kuyu Açan Firma/Sondör Belge No', 'Kuyu Derinlik (m) Tekrar',
                'Pompa Debisi ve Gücü/Fişkiye Sayısı', 'Statik Seviye Ölçülebiliyorsa (m)',
                'Dinamik Seviye/Pompa Montaj Derinliği', 'Sulama Alanı (dönüm)', 'Sulama Sistemi',
                'Yılda Ortalama Kaç Sulama', 'Bir Sulamada Kaç Saat Çalışıyor', 'Ekilen Ürün',
                'İçme/Kullanma/Sanayi Günlük Çalışma Süresi (saat)', 'İçme/Kullanma/Sanayi Yıllık Çalışma Süresi (gün)',
                'Yıllık Çalışmada Enerji Tüketimi (kW)', 'Tespit Eden', 'Tespit Tarihi', 'Açıklama',
                'Oluşturulma Tarihi', 'Güncellenme Tarihi'
            ];

            // Başlıkları yaz
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, 1, $header);
                $col++;
            }

            // Başlık stilini ayarla
            $headerRange = 'A1:' . $sheet->getCellByColumnAndRow(count($headers), 1)->getCoordinate();
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Veri satırlarını yaz
            $row = 2;
            foreach ($boreholes as $borehole) {
                $col = 1;
                $data = [
                    $borehole->id,
                    $borehole->acildigi_yil,
                    $borehole->derinlik_m,
                    $borehole->statik_seviye_m,
                    $borehole->dinamik_seviye_m,
                    $borehole->pompa_tecrubesi_debisi_litre_sn,
                    $borehole->tahsis_amaci,
                    $borehole->tahsis_miktari_m3_yil,
                    $borehole->sulama_alani_dekar,
                    $borehole->isletme_faaliyet_konusu,
                    $borehole->belge_no,
                    $borehole->belge_sahibi,
                    $borehole->arazi_sahibi,
                    $borehole->adres,
                    $borehole->ili,
                    $borehole->ilcesi,
                    $borehole->koy_mahalle_mevkii,
                    $borehole->pafta_ada_parsel,
                    $borehole->koordinat_utm,
                    $borehole->kotu_m,
                    $borehole->havza_alt_havza_adi,
                    $borehole->formasyon_litoloji,
                    $borehole->kuyu_acan_firma_sondor_belge_no,
                    $borehole->kuyu_derinlik_m_tekrar,
                    $borehole->pompa_debisi_ve_gucu_fiskiye_sayisi,
                    $borehole->statik_seviye_olculebiliyorsa_m,
                    $borehole->dinamik_seviye_pompa_montaj_derinligi,
                    $borehole->sulama_alani_donum,
                    $borehole->sulama_sistemi,
                    $borehole->yilda_ortalama_kac_sulama,
                    $borehole->bir_sulamada_kac_saat_calisiyor,
                    $borehole->ekilen_urun,
                    $borehole->icme_kullanma_sanayi_gunluk_calisma_suresi_saat,
                    $borehole->icme_kullanma_sanayi_yillik_calisma_suresi_gun,
                    $borehole->yillik_calismada_enerji_tuketimi_kw,
                    $borehole->tespit_eden,
                    $borehole->tespit_tarihi ? $borehole->tespit_tarihi->format('d.m.Y') : '',
                    $borehole->aciklama,
                    $borehole->created_at ? $borehole->created_at->format('d.m.Y H:i') : '',
                    $borehole->updated_at ? $borehole->updated_at->format('d.m.Y H:i') : ''
                ];

                foreach ($data as $value) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $value);
                    $col++;
                }
                $row++;
            }

            // Sütun genişliklerini ayarla
            foreach (range('A', $sheet->getCellByColumnAndRow(count($headers), 1)->getColumn()) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Veri aralığına border ekle
            $dataRange = 'A1:' . $sheet->getCellByColumnAndRow(count($headers), $row - 1)->getCoordinate();
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Dosya adını oluştur
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "borehole_{$filenamePrefix}_{$timestamp}.xlsx";
            
            // Geçici dosya oluştur
            $tempFile = tempnam(sys_get_temp_dir(), 'borehole_excel_');
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);

            // Storage'a kaydet
            $storagePath = "media/borehole_exports/{$filename}";
            Storage::put($storagePath, file_get_contents($tempFile));
            
            // Geçici dosyayı sil
            unlink($tempFile);

            \Flash::success("Excel dosyası başarıyla oluşturuldu: {$filename}");
            
            // Dosyayı indir
            return Response::download(storage_path("app/{$storagePath}"), $filename);

        } catch (\Exception $e) {
            \Flash::error('Excel dosyası oluşturulurken hata oluştu: ' . $e->getMessage());
            \Log::error('Borehole Excel Export Error: ' . $e->getMessage());
        }
    }
}
