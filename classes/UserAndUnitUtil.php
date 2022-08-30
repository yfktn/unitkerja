<?php namespace Yfktn\UnitKerja\Classes;

use Backend\Facades\BackendAuth;
use Cache;
use Event;
use Illuminate\Support\Facades\DB;

class UserAndUnitUtil
{

    
    /**
     * Get all units where $user_id is a member. Including all the sub units in it.
     * @param mixed $user_id 
     * @return array 
     */
    public static function getUnitOfUser($user_id)
    {
        return Cache::rememberForever("unitofuser{$user_id}", function() use($user_id) {
            $sql = <<<SQL
            select u.nama, u.id, u.nest_depth from 
            (
              select u.id, u.nest_left, u.nest_right
              from yfktn_unitkerja_ u
              inner join yfktn_unitkerja_operator uo on
                uo.unit_kerja_id = u.id and uo.user_id = ?
            ) as userunit, yfktn_unitkerja_ u
            where u.nest_left >= userunit.nest_left and u.nest_right <= userunit.nest_right
            order by u.nest_left
            SQL;
            return DB::select($sql, [$user_id]);
        });
    }

    /**
     * When operator unit updated, we need to forget previous cache. 
     * Call it within OperatorUnitKerja::afterSave!
     * @param mixed $user_id 
     * @return void 
     */
    public static function forgetCacheUnitOfUser($user_id)
    {
        Cache::forget("unitofuser{$user_id}");
    }

    /**
     * We use this with dropdown backend form, to load current user unit, including all the sub units in it.
     * @param mixed $model 
     * @param mixed $formField 
     * @return array 
     */
    public static function loadPilihanUnitKerjaSampaiSubUnitnya($model, $formField)
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
        $hasilQuery = [];
        if($batasiUnitKerja) {
            $hasilQuery = self::getUnitOfUser($currentLoggedUser->id);
        } else {
            $hasilQuery = \Yfktn\UnitKerja\Models\UnitKerja::orderBy('nest_left', 'asc')
                ->get(['nama', 'id', 'nest_depth']);
        }
        $hasil = [];
        foreach($hasilQuery as $q) {
            $c = "";
            if((int)$q->nest_depth > 0) {
                $c = str_repeat("-", ((int)$q->nest_depth * 2)) . "&gt; ";
            }
            $hasil[$q->id] = $c . $q->nama;
        }
        return $hasil;
    }
}