<?php namespace Yfktn\UnitKerja\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class UnitKerja extends Controller
{
    public $implement = [        
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\RelationController',
        'Backend\Behaviors\FormController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'yfktn.unitkerja.manajemen' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Yfktn.UnitKerja', 'main-menu-unitkerja');
    }
}
