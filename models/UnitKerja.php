<?php namespace Yfktn\UnitKerja\Models;

use BackendAuth;
use Illuminate\Support\Facades\DB;
use Model;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\Sluggable;
use Yfktn\UnitKerja\Classes\TraitGetUnitOfUser;
use Yfktn\UnitKerja\Classes\UserAndUnitUtil;

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

    /**
     * Lakukan load terhadap pilihan unit kerja yang user ini diasosiasikan padanya. Loading
     * ini dilakukan pada form dropdown pada waktu melakukan operasi CRUD di form Tulisan.
     * @param mixed $model 
     * @param mixed $formField 
     * @return mixed 
     */
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
        // $model masuk ke sini pasti tidak null! satu-satunya cara adalah check pada unit kerjanya
        // bila user ini dipindahtugaskan, maka kemungkinan unit kerja akan berubah. Untuk itu,
        // lakukan pengecekan lagi pada unit kerja miliknya, dan tambahkan unit kerja sebelumnya di mana
        // user ini menjadi anggotanya. Karena bug maka pastikan yang diperiksa hanyalah yang telah 
        // mendapatkan pembatasan terhadap Unit Kerja nya!
        if(empty($model->unit_kerja_id) === false && $batasiUnitKerja) { 
            $unitKerjaQuery = $unitKerjaQuery->orWhere('id', $model->unit_kerja_id);
        }
        return $unitKerjaQuery->lists('nama', 'id');
    }
}
