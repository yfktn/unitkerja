<?php namespace Yfktn\UnitKerja;

use Backend\Models\User as BackendUserModel;
use BackendAuth;
use Event;
use System\Classes\PluginBase;
use Yfktn\Tulisan\Controllers\Tulisan as TulisanController;
use Yfktn\Tulisan\Models\Tulisan as TulisanModel;
use Yfktn\UnitKerja\Classes\UserAndUnitUtil;
use Yfktn\UnitKerja\Models\UnitKerja;

class Plugin extends PluginBase
{

    public $require = ['Yfktn.Tulisan'];

    public function registerComponents()
    {
        return [
            'Yfktn\UnitKerja\Components\HalamanUnitTulisan' => 'halamanUnitTulisan',
            'Yfktn\UnitKerja\Components\GrafisUnitKerja' => 'grafisUnitKerja',
        ];
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
                    'comment' => 'Pilih unit kerja asal tulisan ini.',
                    'emptyOption' => 'Unit Kerja tidak dipilih',
                    'default' => null,
                    'options' => 'Yfktn\UnitKerja\Classes\UserAndUnitUtil::loadPilihanUnitKerjaSampaiSubUnitnya'
                ],
            ]);

            $model->bindEvent('model.beforeSave', function() use ($model) {
                if(empty($model->attributes['unit_kerja_id'])) {
                    // kalau tidak dipilih masukkan jadi null saja!
                    $model->attributes['unit_kerja_id'] = null;
                }
            });
        });

        // pastikan bahwa yang muncul di list sesuai dengan unit kerja bersangkutan.
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
                        $query->whereIn('unit_kerja_id', array_map(function($item) {
                            return $item->id;
                        }, UserAndUnitUtil::getUnitOfUser($currentLoggedUser->id)));
                    }
                }
            }
        });

    }
}
