<?php namespace Livstag\BoreholeManager\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Livstag\BoreholeManager\Models\Borehole;
use Flash;
use Lang;
use Request;
use Response;
use Excel;
use Input;
use File;

class Boreholes extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\ImportExportController::class
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public $requiredPermissions = ['livstag.boreholemanager.access_boreholes'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Livstag.BoreholeManager', 'boreholes', 'boreholes');
    }

    /**
     * Index action
     */
    public function index()
    {
        $this->asExtension('ListController')->index();
    }

    /**
     * Create action
     */
    public function create()
    {
        $this->asExtension('FormController')->create();
    }

    /**
     * Update action
     */
    public function update($recordId = null)
    {
        $this->asExtension('FormController')->update($recordId);
    }

    /**
     * Preview action
     */
    public function preview($recordId = null)
    {
        $this->asExtension('FormController')->preview($recordId);
    }

    /**
     * Delete action
     */
    public function delete($recordId = null)
    {
        $this->asExtension('FormController')->delete($recordId);
    }

    /**
     * Import action
     */
    public function import()
    {
        $this->asExtension('ImportExportController')->import();
    }

    /**
     * Export action
     */
    public function export()
    {
        $this->asExtension('ImportExportController')->export();
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $template = $this->createImportTemplate();
        
        return Response::download($template, 'borehole_import_template.xlsx');
    }

    /**
     * Bulk delete action
     */
    public function onBulkDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $boreholeId) {
                if (!$borehole = Borehole::find($boreholeId)) {
                    continue;
                }
                
                $borehole->delete();
            }

            Flash::success(Lang::get('livstag.boreholemanager::lang.boreholes.bulk_delete_success', ['count' => count($checkedIds)]));
        }

        return $this->listRefresh();
    }

    /**
     * Bulk restore action
     */
    public function onBulkRestore()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $boreholeId) {
                if (!$borehole = Borehole::withTrashed()->find($boreholeId)) {
                    continue;
                }
                
                $borehole->restore();
            }

            Flash::success(Lang::get('livstag.boreholemanager::lang.boreholes.bulk_restore_success', ['count' => count($checkedIds)]));
        }

        return $this->listRefresh();
    }

    /**
     * Get statistics
     */
    public function onGetStats()
    {
        $stats = [
            'total' => Borehole::count(),
            'active' => Borehole::active()->count(),
            'deleted' => Borehole::onlyTrashed()->count(),
            'by_province' => Borehole::selectRaw('ili, COUNT(*) as count')
                ->whereNotNull('ili')
                ->where('ili', '!=', '')
                ->groupBy('ili')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'by_purpose' => Borehole::selectRaw('tahsis_amaci, COUNT(*) as count')
                ->whereNotNull('tahsis_amaci')
                ->where('tahsis_amaci', '!=', '')
                ->groupBy('tahsis_amaci')
                ->orderBy('count', 'desc')
                ->get()
        ];

        return Response::json($stats);
    }

    /**
     * Create import template
     */
    private function createImportTemplate()
    {
        $headers = [
            'Belge No',
            'Açıldığı Yıl',
            'Derinlik (m)',
            'Statik Seviye (m)',
            'Dinamik Seviye (m)',
            'Pompa Tecrübesi Debisi (L/sn)',
            'Tahsis Amacı',
            'Tahsis Miktarı (m³/yıl)',
            'Sulama Alanı (dekar)',
            'İşletme Faaliyet Konusu',
            'Belge Sahibi',
            'Arazi Sahibi',
            'Adres',
            'İli',
            'İlçesi',
            'Köy/Mahalle/Mevkii',
            'Pafta/Ada/Parsel',
            'Koordinat UTM',
            'Kotu (m)',
            'Havza/Alt Havza Adı',
            'Formasyon/Litoloji',
            'Kuyu Açan Firma/Sondör Belge No',
            'Kuyu Derinlik (m) Tekrar',
            'Pompa Debisi ve Gücü/Fişkiye Sayısı',
            'Statik Seviye Ölçülebiliyorsa (m)',
            'Dinamik Seviye Pompa Montaj Derinliği',
            'Sulama Alanı (dönüm)',
            'Sulama Sistemi',
            'Yılda Ortalama Kaç Sulama',
            'Bir Sulamada Kaç Saat Çalışıyor',
            'Ekilen Ürün',
            'İçme/Kullanma/Sanayi Günlük Çalışma Süresi (saat)',
            'İçme/Kullanma/Sanayi Yıllık Çalışma Süresi (gün)',
            'Yıllık Çalışmada Enerji Tüketimi (kW)',
            'Tespit Eden',
            'Tespit Tarihi',
            'Açıklama'
        ];

        $data = [$headers];
        
        // Add sample data
        $sampleData = [
            'BH-001',
            '2020',
            '150.50',
            '25.30',
            '30.45',
            '2.5',
            'Sulama',
            '50000',
            '25',
            'Tarım',
            'Ahmet Yılmaz',
            'Mehmet Demir',
            'Merkez Mahallesi',
            'Ankara',
            'Çankaya',
            'Merkez',
            '123/45/67',
            '456789, 1234567',
            '850',
            'Sakarya Havzası',
            'Kumtaşı',
            'ABC-2020-001',
            '150.50',
            '5 HP',
            '25.30',
            '30.45',
            '25',
            'Yağmurlama',
            '15',
            '8',
            'Buğday',
            '12',
            '300',
            '2500.50',
            'Mühendis Ali',
            '2023-01-15',
            'Örnek kuyu açıklaması'
        ];
        
        $data[] = $sampleData;

        $filename = temp_path('borehole_template_' . time() . '.xlsx');
        
        Excel::create('Borehole Import Template', function($excel) use ($data) {
            $excel->sheet('Boreholes', function($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', false, false);
            });
        })->store('xlsx', dirname($filename));

        return $filename;
    }
}
