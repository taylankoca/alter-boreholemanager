<?php namespace Livstag\BoreholeManager\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

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
}
