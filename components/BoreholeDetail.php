<?php namespace Livstag\BoreholeManager\Components;

use Cms\Classes\ComponentBase;
use Livstag\BoreholeManager\Models\Borehole;
use Request;

class BoreholeDetail extends ComponentBase
{
    public $borehole;

    public function componentDetails()
    {
        return [
            'name'        => 'livstag.boreholemanager::lang.components.borehole_detail.name',
            'description' => 'livstag.boreholemanager::lang.components.borehole_detail.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_detail.id',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_detail.id_description',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'livstag.boreholemanager::lang.components.borehole_detail.id_validation',
                'default'           => '{{ :id }}',
            ],
            'belgeNo' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_detail.belge_no',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_detail.belge_no_description',
                'type'              => 'string',
                'default'           => '{{ :belge_no }}',
            ],
            'showDeleted' => [
                'title'             => 'livstag.boreholemanager::lang.components.borehole_detail.show_deleted',
                'description'       => 'livstag.boreholemanager::lang.components.borehole_detail.show_deleted_description',
                'type'              => 'checkbox',
                'default'           => false
            ]
        ];
    }

    public function onRun()
    {
        $this->borehole = $this->loadBorehole();
        
        if (!$this->borehole) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }
    }

    protected function loadBorehole()
    {
        $query = Borehole::query();

        if ($this->property('showDeleted')) {
            $query->withTrashed();
        } else {
            $query->active();
        }

        // Try to find by ID first
        if ($id = $this->property('id')) {
            return $query->find($id);
        }

        // Try to find by belge_no
        if ($belgeNo = $this->property('belgeNo')) {
            return $query->where('belge_no', $belgeNo)->first();
        }

        return null;
    }

    public function getBorehole()
    {
        return $this->borehole;
    }

    public function getRelatedBoreholes()
    {
        if (!$this->borehole) {
            return collect();
        }

        return Borehole::active()
            ->where('id', '!=', $this->borehole->id)
            ->where(function($query) {
                $query->where('ili', $this->borehole->ili)
                      ->orWhere('ilcesi', $this->borehole->ilcesi)
                      ->orWhere('tahsis_amaci', $this->borehole->tahsis_amaci);
            })
            ->limit(5)
            ->get();
    }

    public function getBoreholeImages()
    {
        if (!$this->borehole) {
            return collect();
        }

        return $this->borehole->images;
    }
}
