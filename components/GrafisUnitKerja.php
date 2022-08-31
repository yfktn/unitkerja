<?php namespace Yfktn\UnitKerja\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\DB;

/**
 * GrafisUnitKerja Component
 */
class GrafisUnitKerja extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'GrafisUnitKerja Component',
            'description' => 'Menampilkan grafis berapa banyak publikasi unit kerja yang melakukan posting.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $sql = <<<SQL
SELECT uk.nama, count(*) as jumlah_posting FROM yfktn_unitkerja_ uk 
INNER JOIN yfktn_tulisan_tulis t on t.unit_kerja_id = uk.id
GROUP BY t.unit_kerja_id, uk.nama
SQL;
        $record = DB::select($sql);
        
    }
}
