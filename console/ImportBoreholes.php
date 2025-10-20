<?php namespace Livstag\BoreholeManager\Console;

use Illuminate\Console\Command;
use Livstag\BoreholeManager\Models\Borehole;
use Excel;
use File;

class ImportBoreholes extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'borehole:import';

    /**
     * @var string The console command description.
     */
    protected $description = 'Import boreholes from Excel file';

    /**
     * @var array The command arguments.
     */
    protected $arguments = [
        'file' => 'Excel file path to import'
    ];

    /**
     * @var array The command options.
     */
    protected $options = [
        '--skip-duplicates' => 'Skip duplicate records based on belge_no',
        '--batch-size' => 'Number of records to process in each batch (default: 100)',
        '--dry-run' => 'Show what would be imported without actually importing'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $skipDuplicates = $this->option('skip-duplicates');
        $batchSize = (int) $this->option('batch-size') ?: 100;
        $dryRun = $this->option('dry-run');

        $this->info("Starting borehole import from: {$filePath}");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No data will be imported");
        }

        try {
            $data = Excel::load($filePath)->get();
            $totalRecords = $data->count();
            
            $this->info("Found {$totalRecords} records to process");

            $imported = 0;
            $skipped = 0;
            $errors = 0;

            $progressBar = $this->output->createProgressBar($totalRecords);
            $progressBar->start();

            foreach ($data->chunk($batchSize) as $chunk) {
                foreach ($chunk as $row) {
                    try {
                        if ($skipDuplicates && $this->isDuplicate($row)) {
                            $skipped++;
                            $progressBar->advance();
                            continue;
                        }

                        if (!$dryRun) {
                            $this->createBorehole($row);
                        }
                        
                        $imported++;
                    } catch (\Exception $e) {
                        $errors++;
                        $this->error("Error processing row: " . $e->getMessage());
                    }
                    
                    $progressBar->advance();
                }
            }

            $progressBar->finish();
            $this->line('');

            $this->info("Import completed:");
            $this->info("- Imported: {$imported}");
            $this->info("- Skipped: {$skipped}");
            $this->info("- Errors: {$errors}");

            return 0;

        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if a record is duplicate
     */
    protected function isDuplicate($row)
    {
        if (empty($row->belge_no)) {
            return true; // Skip records without belge_no
        }

        return Borehole::where('belge_no', $row->belge_no)->exists();
    }

    /**
     * Create a borehole record
     */
    protected function createBorehole($row)
    {
        $data = [
            'belge_no' => $row->belge_no ?? null,
            'acildigi_yil' => $row->acildigi_yil ?? null,
            'derinlik_m' => $row->derinlik_m ?? null,
            'statik_seviye_m' => $row->statik_seviye_m ?? null,
            'dinamik_seviye_m' => $row->dinamik_seviye_m ?? null,
            'pompa_tecrubesi_debisi_litre_sn' => $row->pompa_tecrubesi_debisi_litre_sn ?? null,
            'tahsis_amaci' => $row->tahsis_amaci ?? null,
            'tahsis_miktari_m3_yil' => $row->tahsis_miktari_m3_yil ?? null,
            'sulama_alani_dekar' => $row->sulama_alani_dekar ?? null,
            'isletme_faaliyet_konusu' => $row->isletme_faaliyet_konusu ?? null,
            'belge_sahibi' => $row->belge_sahibi ?? null,
            'arazi_sahibi' => $row->arazi_sahibi ?? null,
            'adres' => $row->adres ?? null,
            'ili' => $row->ili ?? null,
            'ilcesi' => $row->ilcesi ?? null,
            'koy_mahalle_mevkii' => $row->koy_mahalle_mevkii ?? null,
            'pafta_ada_parsel' => $row->pafta_ada_parsel ?? null,
            'koordinat_utm' => $row->koordinat_utm ?? null,
            'kotu_m' => $row->kotu_m ?? null,
            'havza_alt_havza_adi' => $row->havza_alt_havza_adi ?? null,
            'formasyon_litoloji' => $row->formasyon_litoloji ?? null,
            'kuyu_acan_firma_sondor_belge_no' => $row->kuyu_acan_firma_sondor_belge_no ?? null,
            'kuyu_derinlik_m_tekrar' => $row->kuyu_derinlik_m_tekrar ?? null,
            'pompa_debisi_ve_gucu_fiskiye_sayisi' => $row->pompa_debisi_ve_gucu_fiskiye_sayisi ?? null,
            'statik_seviye_olculebiliyorsa_m' => $row->statik_seviye_olculebiliyorsa_m ?? null,
            'dinamik_seviye_pompa_montaj_derinligi' => $row->dinamik_seviye_pompa_montaj_derinligi ?? null,
            'sulama_alani_donum' => $row->sulama_alani_donum ?? null,
            'sulama_sistemi' => $row->sulama_sistemi ?? null,
            'yilda_ortalama_kac_sulama' => $row->yilda_ortalama_kac_sulama ?? null,
            'bir_sulamada_kac_saat_calisiyor' => $row->bir_sulamada_kac_saat_calisiyor ?? null,
            'ekilen_urun' => $row->ekilen_urun ?? null,
            'icme_kullanma_sanayi_gunluk_calisma_suresi_saat' => $row->icme_kullanma_sanayi_gunluk_calisma_suresi_saat ?? null,
            'icme_kullanma_sanayi_yillik_calisma_suresi_gun' => $row->icme_kullanma_sanayi_yillik_calisma_suresi_gun ?? null,
            'yillik_calismada_enerji_tuketimi_kw' => $row->yillik_calismada_enerji_tuketimi_kw ?? null,
            'tespit_eden' => $row->tespit_eden ?? null,
            'tespit_tarihi' => $row->tespit_tarihi ?? null,
            'aciklama' => $row->aciklama ?? null,
        ];

        // Remove empty values
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        return Borehole::create($data);
    }
}
