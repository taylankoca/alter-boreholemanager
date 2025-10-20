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
        $this->registerConsoleCommand('borehole.import', 'Livstag\BoreholeManager\Console\ImportBoreholes');
    }

    public function registerComponents()
    {
        return [
            'Livstag\BoreholeManager\Components\BoreholeList' => 'boreholeList',
            'Livstag\BoreholeManager\Components\BoreholeDetail' => 'boreholeDetail',
            'Livstag\BoreholeManager\Components\BoreholeMap' => 'boreholeMap',
        ];
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
                'label'       => 'livstag.boreholemanager::lang.navigation.boreholes',
                'url'         => \Backend::url('livstag/boreholemanager/boreholes'),
                'icon'        => 'oc-icon-list',
                'permissions' => ['livstag.boreholemanager.access_boreholes'],
                'order'       => 500,
                'sideMenu' => [
                    'boreholes' => [
                        'label'       => 'livstag.boreholemanager::lang.navigation.boreholes',
                        'icon'        => 'oc-icon-list',
                        'url'         => \Backend::url('livstag/boreholemanager/boreholes'),
                        'permissions' => ['livstag.boreholemanager.access_boreholes'],
                    ],
                    'import' => [
                        'label'       => 'livstag.boreholemanager::lang.navigation.import',
                        'icon'        => 'oc-icon-upload',
                        'url'         => \Backend::url('livstag/boreholemanager/boreholes/import'),
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
            ],
            'livstag.boreholemanager.manage_boreholes' => [
                'tab'   => 'livstag.boreholemanager::lang.plugin.name',
                'label' => 'livstag.boreholemanager::lang.permissions.manage_boreholes'
            ]
        ];
    }
}
