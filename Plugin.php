<?php namespace Yfktn\UnitKerja;

use Backend\Models\User as BackendUserModel;
use BackendAuth;
use Event;
use System\Classes\PluginBase;
use Yfktn\Tulisan\Controllers\Tulisan as TulisanController;
use Yfktn\Tulisan\Models\Tulisan as TulisanModel;
use Yfktn\UnitKerja\Models\UnitKerja;

class Plugin extends PluginBase
{

    public $require = ['Yfktn.Tulisan'];

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }

    public function boot()
    {
        // BackendUserModel::extend(function($model) {
        //     $model->hasMany['unit_kerja'] = [
        //         'Yfktn\UnitKerja\Models\UnitKerja',
        //         'table' => 'yfktn_unitkerja_',
        //         'key' => 'unit_kerja_id'
        //     ];
        // });
        
        TulisanModel::extend(function($model) {
            $model->belongsTo['unit_kerja'] = [
                'Yfktn\UnitKerja\Models\UnitKerja',
                'key' => 'unit_kerja_id'
            ];
        });

        TulisanController::extendFormFields(function($form, $model, $context) {
            // tambahkan field untuk memilih unit kerja!
            if(!$model instanceof TulisanModel) {
                return;
            }

            $form->addFields([
                'unit_kerja_id' => [
                    'type'    => 'dropdown',
                    'label'   => 'Unit Kerja',
                    'comment' => 'Pilih unit kerja tulisan ini.',
                    'options' => 'Yfktn\UnitKerja\Models\UnitKerja::loadPilihanUnitKerja'
                ],
            ]);
        });

        Event::listen('backend.list.extendQuery', function($widget, $query) {
            if( $widget->model instanceof TulisanModel ) {
                // cari tulisan dari user dengan unit kerja yang sama dengan user
                // BILA punya hak akses untuk mengakses user lain, maka:
                $currentLoggedUser = BackendAuth::getUser();
                if($currentLoggedUser->hasAnyAccess(['yfktn.tulisan.tulisan_akses_user_lain']) 
                        && !$currentLoggedUser->isSuperUser()) { // dan user ini bukan super admin
                    // check kembali bila user ini punya hak akses untuk mengakses
                    // punya user unit lain
                    if( $currentLoggedUser->hasAnyAccess([
                        'yfktn.unitkerja.akses_unit_lain']) ) {
                        // nothing to do!
                    } else {
                        // dapatkan daftar posting yang dibuat pada unit yang user ini adalah operatornya
                        // Tulisan (unit_kerja_id) -> UnitKerja (id) -> OperatorUnitKerja (unit_kerja_id) (user_id) -> BackendUserModel (id)
                        $query->whereHas('unit_kerja.operator', function($query) use($currentLoggedUser) {
                            $query->where('user_id', $currentLoggedUser->id);
                        });
                    }
                }
            }
        });

    }
}
