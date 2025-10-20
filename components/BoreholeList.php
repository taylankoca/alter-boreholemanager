<?php namespace Livstag\BoreholeManager\Components;

use Cms\Classes\ComponentBase;
use Livstag\BoreholeManager\Models\Borehole;
use Request;
use Input;

class BoreholeList extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'livstag.boreholemanager::lang.components.borehole_list.name',
            'description' => 'livstag.boreholemanager::lang.components.borehole_list.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'recordsPerPage' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_list.records_per_page',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_list.records_per_page_description',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'livstag.boreholemanager::lang.components.borehole_list.records_per_page_validation',
                'default'           => '10',
            ],
            'sortColumn' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_list.sort_column',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_list.sort_column_description',
                'type'              => 'dropdown',
                'default'           => 'created_at',
                'options'           => [
                    'created_at' => 'livstag.boreholemanager::lang.boreholes.created_at',
                    'updated_at' => 'livstag.boreholemanager::lang.boreholes.updated_at',
                    'belge_no' => 'livstag.boreholemanager::lang.boreholes.belge_no',
                    'derinlik_m' => 'livstag.boreholemanager::lang.boreholes.derinlik_m',
                    'acildigi_yil' => 'livstag.boreholemanager::lang.boreholes.acildigi_yil',
                    'ili' => 'livstag.boreholemanager::lang.boreholes.ili',
                    'ilcesi' => 'livstag.boreholemanager::lang.boreholes.ilcesi'
                ]
            ],
            'sortDirection' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_list.sort_direction',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_list.sort_direction_description',
                'type'              => 'dropdown',
                'default'           => 'desc',
                'options'           => [
                    'asc' => 'livstag.boreholemanager::lang.components.borehole_list.sort_asc',
                    'desc' => 'livstag.boreholemanager::lang.components.borehole_list.sort_desc'
                ]
            ],
            'showDeleted' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_list.show_deleted',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_list.show_deleted_description',
                'type'              => 'checkbox',
                'default'           => false
            ],
            'province' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_list.province',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_list.province_description',
                'type'              => 'dropdown',
                'default'           => '',
                'options'           => ['' => 'Tümü'] + Borehole::getProvinces()->toArray()
            ],
            'district' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_list.district',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_list.district_description',
                'type'              => 'dropdown',
                'default'           => '',
                'options'           => ['' => 'Tümü']
            ],
            'purpose' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_list.purpose',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_list.purpose_description',
                'type'              => 'dropdown',
                'default'           => '',
                'options'           => ['' => 'Tümü'] + Borehole::getPurposes()->toArray()
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
        $query = Borehole::query();

        // Apply filters
        if ($this->property('showDeleted')) {
            $query->withTrashed();
        } else {
            $query->active();
        }

        if ($province = $this->property('province')) {
            $query->byProvince($province);
        }

        if ($district = $this->property('district')) {
            $query->byDistrict($district);
        }

        if ($purpose = $this->property('purpose')) {
            $query->byPurpose($purpose);
        }

        // Apply search
        if ($search = Input::get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('belge_no', 'like', "%{$search}%")
                  ->orWhere('belge_sahibi', 'like', "%{$search}%")
                  ->orWhere('arazi_sahibi', 'like', "%{$search}%")
                  ->orWhere('adres', 'like', "%{$search}%")
                  ->orWhere('ili', 'like', "%{$search}%")
                  ->orWhere('ilcesi', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortColumn = $this->property('sortColumn', 'created_at');
        $sortDirection = $this->property('sortDirection', 'desc');
        $query->orderBy($sortColumn, $sortDirection);

        // Paginate
        $perPage = $this->property('recordsPerPage', 10);
        return $query->paginate($perPage);
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
}
