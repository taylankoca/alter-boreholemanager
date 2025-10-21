<?php namespace Livstag\BoreholeManager;

use System\Classes\PluginBase;
use Backend\Classes\Controller;
use Livstag\BoreholeManager\Models\Borehole;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'livstag.boreholemanager::lang.plugin.name',
            'description' => 'livstag.boreholemanager::lang.plugin.description',
            'author'      => 'Livstag',
            'icon'        => 'oc-icon-list',
            'homepage'    => ''
        ];
    }

    public function register()
    {
        // Excel gösterme route'u
        \Route::get('/kuyu/{id}', function($id) {
            $controller = new \Livstag\BoreholeManager\Controllers\Boreholes();
            return $controller->showExcel($id);
        })->where('id', '[0-9]+');
    }

    public function registerComponents()
    {

    }

    public function registerSettings()
    {
        return [
            'boreholes' => [
                'label'       => 'livstag.boreholemanager::lang.settings.boreholes',
                'description' => 'livstag.boreholemanager::lang.settings.boreholes_description',
                'category'    => 'livstag.boreholemanager::lang.plugin.name',
                'icon'        => 'oc-icon-list',
                'url'         => \Backend::url('livstag/boreholemanager/boreholes'),
                'order'       => 500,
                'keywords'    => 'borehole kuyu sondaj'
            ]
        ];
    }

    public function registerNavigation()
    {
        return [
            'boreholes' => [
                'label'       => 'livstag.boreholemanager::lang.boreholemanager.boreholes',
                'url'         => \Backend::url('livstag/boreholemanager/boreholes'),
                'icon'        => 'oc-icon-list',
                'permissions' => ['livstag.boreholemanager.access_boreholes'],
                'order'       => 500,
                'sideMenu' => [
                    'boreholes' => [
                        'label'       => 'livstag.boreholemanager::lang.boreholemanager.boreholes',
                        'icon'        => 'oc-icon-list',
                        'url'         => \Backend::url('livstag/boreholemanager/boreholes'),
                        'permissions' => ['livstag.boreholemanager.access_boreholes'],
                    ]
                ]
            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'livstag.boreholemanager.access_boreholes' => [
                'tab'   => 'livstag.boreholemanager::lang.plugin.name',
                'label' => 'livstag.boreholemanager::lang.permissions.access_boreholes'
            ]
        ];
    }
}
