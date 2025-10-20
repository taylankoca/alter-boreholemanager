<?php namespace Livstag\BoreholeManager\Components;

use Cms\Classes\ComponentBase;
use Livstag\BoreholeManager\Models\Borehole;
use Input;

class BoreholeMap extends ComponentBase
{
    public $boreholes;

    public function componentDetails()
    {
        return [
            'name'        => 'livstag.boreholemanager::lang.components.borehole_map.name',
            'description' => 'livstag.boreholemanager::lang.components.borehole_map.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'maxRecords' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_map.max_records',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_map.max_records_description',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'livstag.boreholemanager::lang.components.borehole_map.max_records_validation',
                'default'           => '1000',
            ],
            'province' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_map.province',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_map.province_description',
                'type'              => 'dropdown',
                'default'           => '',
                'options'           => ['' => 'Tümü'] + Borehole::getProvinces()->toArray()
            ],
            'district' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_map.district',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_map.district_description',
                'type'              => 'dropdown',
                'default'           => '',
                'options'           => ['' => 'Tümü']
            ],
            'purpose' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_map.purpose',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_map.purpose_description',
                'type'              => 'dropdown',
                'default'           => '',
                'options'           => ['' => 'Tümü'] + Borehole::getPurposes()->toArray()
            ],
            'mapCenter' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_map.map_center',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_map.map_center_description',
                'type'              => 'string',
                'default'           => '39.9334,32.8597', // Ankara coordinates
            ],
            'mapZoom' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_map.map_zoom',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_map.map_zoom_description',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'livstag.boreholemanager::lang.components.borehole_map.map_zoom_validation',
                'default'           => '6',
            ]
        ];
    }

    public function onRun()
    {
        $this->boreholes = $this->loadBoreholes();
        $this->provinces = Borehole::getProvinces();
        $this->districts = $this->getDistricts();
        $this->purposes = Borehole::getPurposes();
    }

    public function onFilter()
    {
        $this->boreholes = $this->loadBoreholes();
        $this->districts = $this->getDistricts();
    }

    protected function loadBoreholes()
    {
        $query = Borehole::active()
            ->whereNotNull('koordinat_utm')
            ->where('koordinat_utm', '!=', '');

        // Apply filters
        if ($province = $this->property('province')) {
            $query->byProvince($province);
        }

        if ($district = $this->property('district')) {
            $query->byDistrict($district);
        }

        if ($purpose = $this->property('purpose')) {
            $query->byPurpose($purpose);
        }

        // Limit records for performance
        $maxRecords = $this->property('maxRecords', 1000);
        $query->limit($maxRecords);

        return $query->get();
    }

    protected function getDistricts()
    {
        $province = $this->property('province');
        return ['' => 'Tümü'] + Borehole::getDistricts($province)->toArray();
    }

    public function getBoreholes()
    {
        return $this->boreholes;
    }

    public function getProvinces()
    {
        return $this->provinces;
    }

    public function getDistricts()
    {
        return $this->districts;
    }

    public function getPurposes()
    {
        return $this->purposes;
    }

    public function getMapCenter()
    {
        return $this->property('mapCenter', '39.9334,32.8597');
    }

    public function getMapZoom()
    {
        return $this->property('mapZoom', 6);
    }

    public function getBoreholesForMap()
    {
        $boreholes = $this->getBoreholes();
        $mapData = [];

        foreach ($boreholes as $borehole) {
            $coordinates = $this->parseCoordinates($borehole->koordinat_utm);
            
            if ($coordinates) {
                $mapData[] = [
                    'id' => $borehole->id,
                    'belge_no' => $borehole->belge_no,
                    'belge_sahibi' => $borehole->belge_sahibi,
                    'ili' => $borehole->ili,
                    'ilcesi' => $borehole->ilcesi,
                    'derinlik_m' => $borehole->derinlik_m,
                    'tahsis_amaci' => $borehole->tahsis_amaci,
                    'lat' => $coordinates['lat'],
                    'lng' => $coordinates['lng'],
                    'url' => url('/borehole/' . $borehole->id)
                ];
            }
        }

        return $mapData;
    }

    protected function parseCoordinates($utmString)
    {
        if (!$utmString) {
            return null;
        }

        // Try to parse UTM coordinates
        // Expected format: "456789, 1234567" or "456789,1234567"
        $coords = preg_split('/[,\s]+/', trim($utmString));
        
        if (count($coords) >= 2) {
            $easting = floatval($coords[0]);
            $northing = floatval($coords[1]);
            
            // Simple UTM to Lat/Lng conversion (approximate)
            // This is a basic conversion - for production use a proper UTM library
            $lat = $northing / 111320; // Rough conversion
            $lng = $easting / (111320 * cos(deg2rad($lat)));
            
            return [
                'lat' => $lat,
                'lng' => $lng
            ];
        }

        return null;
    }
}
