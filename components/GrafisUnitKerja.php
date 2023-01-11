<?php namespace Yfktn\UnitKerja\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\DB;

/**
 * GrafisUnitKerja Component
 */
class GrafisUnitKerja extends ComponentBase
{
    public $grafisUnitKerja = [];

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
SELECT uk.id, uk.slug, uk.nama, count(t.unit_kerja_id) as jumlah_posting FROM yfktn_unitkerja_ uk
LEFT JOIN yfktn_tulisan_tulis t on t.unit_kerja_id = uk.id
GROUP BY uk.id, uk.slug, uk.nama
SQL;
        $this->grafisUnitKerja['record'] = DB::select($sql);
        $this->grafisUnitKerja['total_posting'] = array_sum(array_column($this->grafisUnitKerja['record'], 'jumlah_posting'));

    }
}
