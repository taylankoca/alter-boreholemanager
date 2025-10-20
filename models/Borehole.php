<?php namespace Livstag\BoreholeManager\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\SoftDelete;
use System\Models\File;

/**
 * Borehole Model
 */
class Borehole extends Model
{
    use Validation;
    use SoftDelete;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'livstag_boreholemanager_boreholes';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'acildigi_yil',
        'derinlik_m',
        'statik_seviye_m',
        'dinamik_seviye_m',
        'pompa_tecrubesi_debisi_litre_sn',
        'tahsis_amaci',
        'tahsis_miktari_m3_yil',
        'sulama_alani_dekar',
        'isletme_faaliyet_konusu',
        'belge_no',
        'belge_sahibi',
        'arazi_sahibi',
        'adres',
        'ili',
        'ilcesi',
        'koy_mahalle_mevkii',
        'pafta_ada_parsel',
        'koordinat_utm',
        'kotu_m',
        'havza_alt_havza_adi',
        'formasyon_litoloji',
        'kuyu_acan_firma_sondor_belge_no',
        'kuyu_derinlik_m_tekrar',
        'pompa_debisi_ve_gucu_fiskiye_sayisi',
        'statik_seviye_olculebiliyorsa_m',
        'dinamik_seviye_pompa_montaj_derinligi',
        'sulama_alani_donum',
        'sulama_sistemi',
        'yilda_ortalama_kac_sulama',
        'bir_sulamada_kac_saat_calisiyor',
        'ekilen_urun',
        'icme_kullanma_sanayi_gunluk_calisma_suresi_saat',
        'icme_kullanma_sanayi_yillik_calisma_suresi_gun',
        'yillik_calismada_enerji_tuketimi_kw',
        'tespit_eden',
        'tespit_tarihi',
        'aciklama'
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'belge_no' => 'required|unique:livstag_boreholemanager_boreholes',
        'derinlik_m' => 'numeric|min:0',
        'statik_seviye_m' => 'numeric|min:0',
        'dinamik_seviye_m' => 'numeric|min:0',
        'pompa_tecrubesi_debisi_litre_sn' => 'numeric|min:0',
        'tahsis_miktari_m3_yil' => 'integer|min:0',
        'sulama_alani_dekar' => 'integer|min:0',
        'acildigi_yil' => 'integer|min:1900|max:' . (date('Y') + 1),
        'tespit_tarihi' => 'date'
    ];

    /**
     * @var array Custom validation messages
     */
    public $customMessages = [
        'belge_no.required' => 'Belge numarası zorunludur.',
        'belge_no.unique' => 'Bu belge numarası zaten kullanılmaktadır.',
        'derinlik_m.numeric' => 'Derinlik sayısal bir değer olmalıdır.',
        'derinlik_m.min' => 'Derinlik 0\'dan büyük olmalıdır.',
        'acildigi_yil.integer' => 'Açıldığı yıl tam sayı olmalıdır.',
        'acildigi_yil.min' => 'Açıldığı yıl 1900\'den küçük olamaz.',
        'acildigi_yil.max' => 'Açıldığı yıl gelecek yıldan büyük olamaz.',
        'tespit_tarihi.date' => 'Tespit tarihi geçerli bir tarih olmalıdır.'
    ];

    /**
     * @var array Dates
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'tespit_tarihi'
    ];

    /**
     * @var array Casts
     */
    protected $casts = [
        'derinlik_m' => 'decimal:2',
        'statik_seviye_m' => 'decimal:2',
        'dinamik_seviye_m' => 'decimal:2',
        'pompa_tecrubesi_debisi_litre_sn' => 'decimal:2',
        'kuyu_derinlik_m_tekrar' => 'decimal:2',
        'statik_seviye_olculebiliyorsa_m' => 'decimal:2',
        'dinamik_seviye_pompa_montaj_derinligi' => 'decimal:2',
        'yillik_calismada_enerji_tuketimi_kw' => 'decimal:2'
    ];

    /**
     * Attachments
     */
    public $attachMany = [
        'images' => File::class
    ];

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeByProvince($query, $province)
    {
        return $query->where('ili', $province);
    }

    public function scopeByDistrict($query, $district)
    {
        return $query->where('ilcesi', $district);
    }

    public function scopeByPurpose($query, $purpose)
    {
        return $query->where('tahsis_amaci', $purpose);
    }

    /**
     * Accessors & Mutators
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->adres,
            $this->koy_mahalle_mevkii,
            $this->ilcesi,
            $this->ili
        ]);
        
        return implode(', ', $parts);
    }

    public function getStatusAttribute()
    {
        if ($this->deleted_at) {
            return 'deleted';
        }
        
        if ($this->tespit_tarihi && $this->tespit_tarihi->diffInDays() > 365) {
            return 'outdated';
        }
        
        return 'active';
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'deleted':
                return 'Silinmiş';
            case 'outdated':
                return 'Güncel Değil';
            case 'active':
            default:
                return 'Aktif';
        }
    }

    /**
     * Get provinces list for dropdown
     */
    public static function getProvinces()
    {
        return self::select('ili')
            ->whereNotNull('ili')
            ->where('ili', '!=', '')
            ->distinct()
            ->orderBy('ili')
            ->pluck('ili', 'ili');
    }

    /**
     * Get districts list for dropdown
     */
    public static function getDistricts($province = null)
    {
        $query = self::select('ilcesi')
            ->whereNotNull('ilcesi')
            ->where('ilcesi', '!=', '');
            
        if ($province) {
            $query->where('ili', $province);
        }
        
        return $query->distinct()
            ->orderBy('ilcesi')
            ->pluck('ilcesi', 'ilcesi');
    }

    /**
     * Get purposes list for dropdown
     */
    public static function getPurposes()
    {
        return self::select('tahsis_amaci')
            ->whereNotNull('tahsis_amaci')
            ->where('tahsis_amaci', '!=', '')
            ->distinct()
            ->orderBy('tahsis_amaci')
            ->pluck('tahsis_amaci', 'tahsis_amaci');
    }
}
