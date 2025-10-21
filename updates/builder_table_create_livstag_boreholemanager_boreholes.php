<?php namespace Livstag\BoreholeManager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLivstagBoreholemanagerBoreholes extends Migration
{
    public function up()
    {
        Schema::create('livstag_boreholemanager_boreholes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned(); // id (primary key)

            // Gelen veri string/tarih formatında olduğu için string olarak güncellendi.
            $table->string('acildigi_yil', 20)->nullable(); 
            $table->decimal('derinlik_m', 10, 2)->nullable();
            $table->decimal('statik_seviye_m', 10, 2)->nullable();
            $table->decimal('dinamik_seviye_m', 10, 2)->nullable();
            $table->decimal('pompa_tecrubesi_debisi_litre_sn', 10, 2)->nullable();
            $table->string('tahsis_amaci', 50)->nullable();
            $table->integer('tahsis_miktari_m3_yil')->nullable();
            $table->integer('sulama_alani_dekar')->nullable();
            $table->string('isletme_faaliyet_konusu', 150)->nullable(); // Uzunluk artırıldı
            $table->string('belge_no', 100)->nullable();
            $table->string('belge_sahibi', 255)->nullable(); // Uzunluk artırıldı
            $table->string('arazi_sahibi', 255)->nullable(); // Uzunluk artırıldı
            $table->string('adres', 255)->nullable();
            $table->string('ili', 50)->nullable();
            $table->string('ilcesi', 50)->nullable();
            $table->string('koy_mahalle_mevkii', 100)->nullable();
            $table->string('pafta_ada_parsel', 150)->nullable(); // Uzunluk artırıldı
            $table->string('koordinat_utm', 150)->nullable(); // Uzunluk artırıldı
            $table->decimal('kotu_m', 10, 2)->nullable(); // Kot bilgileri ondalık olabileceği için decimal olarak değiştirildi
            $table->string('havza_alt_havza_adi', 100)->nullable();
            $table->string('formasyon_litoloji', 200)->nullable(); // Uzunluk artırıldı
            $table->string('kuyu_acan_firma_sondor_belge_no', 100)->nullable();
            $table->decimal('kuyu_derinlik_m_tekrar', 10, 2)->nullable();
            $table->integer('pompa_debisi_ve_gucu_fiskiye_sayisi')->nullable();
            $table->decimal('statik_seviye_olculebiliyorsa_m', 10, 2)->nullable();
            $table->decimal('dinamik_seviye_pompa_montaj_derinligi', 10, 2)->nullable();
            $table->integer('sulama_alani_donum')->nullable();
            $table->string('sulama_sistemi', 50)->nullable();
            $table->integer('yilda_ortalama_kac_sulama')->nullable();
            $table->integer('bir_sulamada_kac_saat_calisiyor')->nullable();
            $table->string('ekilen_urun', 100)->nullable();
            $table->integer('icme_kullanma_sanayi_gunluk_calisma_suresi_saat')->nullable();
            $table->integer('icme_kullanma_sanayi_yillik_calisma_suresi_gun')->nullable();
            $table->decimal('yillik_calismada_enerji_tuketimi_kw', 10, 2)->nullable();
            $table->string('tespit_eden', 100)->nullable();
            $table->date('tespit_tarihi')->nullable();
            $table->text('aciklama')->nullable();
            
            // Image fields for external file attachments
            $table->string('image1_path')->nullable();
            $table->string('image1_filename')->nullable();
            $table->string('image2_path')->nullable();
            $table->string('image2_filename')->nullable();

            $table->timestamps();
            $table->softDeletes(); // deleted_at sütunu
        });
    }

    public function down()
    {
        Schema::dropIfExists('livstag_boreholemanager_boreholes');
    }
}