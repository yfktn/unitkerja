<?php namespace Yfktn\UnitKerja\Models;

use BackendAuth;
use Model;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\Sluggable;

/**
 * Model
 */
class UnitKerja extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use Sluggable;
    use NestedTree;

    protected $slugs = ['slug'=>'nama'];
    
    /**
     * @var string The database table used by the model.
     */
    public $table = 'yfktn_unitkerja_';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'nama' => 'required'
    ];

    public $hasMany = [
        'operator' => [
            'Yfktn\UnitKerja\Models\OperatorUnitKerja',
            'key' => 'unit_kerja_id',
        ]
    ];

    public static function loadPilihanUnitKerja($model, $formField)
    {
        $currentLoggedUser = BackendAuth::getUser();
        $batasiUnitKerja = false;
        // punya akses mengakses tulisan user lain DAN bukan super user
        if($currentLoggedUser->isSuperUser()) {
            // tidak perlu dilakukan apa-apa
        }
        else if($currentLoggedUser->hasAnyAccess(['yfktn.tulisan.tulisan_akses_user_lain'])) {
            // check juga dia apakah bisa akses unit lain?
            if( !$currentLoggedUser->hasAnyAccess([
                'yfktn.unitkerja.akses_unit_lain']) ) {
                // user ini tidak memiliki akses ke unit kerja lain
                // load pilihan di mana tempat user ini menjadi operatornya saja!
                $batasiUnitKerja = true;
            }
        } else {
            // tidak punya hak akses ke tulisan orang lain? Batasi unit kerjanya
            $batasiUnitKerja = true;
        }
        $unitKerjaQuery = new UnitKerja;
        if($batasiUnitKerja) {
            $unitKerjaQuery = $unitKerjaQuery->whereHas('operator', function($query) use($currentLoggedUser) {
                $query->where('user_id', '=', $currentLoggedUser->id);
            });
        }
        trace_log($model->unit_kerja_id);
        if($model !== null && !empty($model->unit_kerja_id)) {
            $unitKerjaQuery = $unitKerjaQuery->orWhere('id', $model->unit_kerja_id);
        }
        return $unitKerjaQuery->lists('nama', 'id');
    }
}
