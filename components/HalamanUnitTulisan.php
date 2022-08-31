<?php namespace Yfktn\UnitKerja\Components;

use Cms\Classes\ComponentBase;
use Yfktn\Tulisan\Models\Tulisan;
use Yfktn\UnitKerja\Models\UnitKerja;

/**
 * HalamanUnit Component
 */
class HalamanUnitTulisan extends ComponentBase
{
    public $dataHalamanUnit = [];

    public function componentDetails()
    {
        return [
            'name' => 'Komponen Halaman Unit',
            'description' => 'Menampilkan komponen halaman unit.'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'Slug',
                'description' => 'Parameter slug',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ],
            'halaman' => [
                'title' => 'Parameter Halaman',
                'description' => 'Parameter menunjukkan halaman aktif yang di load',
                'type' => 'string',
                'default' => '{{ :page }}'
            ],
            'recordPerHalaman' => [
                'title' => 'Jumlah Record per Halaman',
                'description' => 'Jumlah record ditampilkan per halaman!',
                'type' => 'string',
                'default' => '10'
            ],
            'denganSubUnitnya' => [
                'title' => 'Tampilkan Juga Sub Unitnya',
                'description' => 'Tampilkan juga publikasi sub unitnya!',
                'type' => 'checkbox',
                'default' => '0'
            ],
        ];
    }

    protected function siapkanVariable()
    {

        $this->dataHalamanUnit['slug'] = $this->property('slug');
        $this->dataHalamanUnit['halaman'] = $this->property('halaman', 1);
        $this->dataHalamanUnit['paramHalaman'] = $this->paramName('halaman', 1);
        $this->dataHalamanUnit['recordPerHalaman'] = $this->property('recordPerHalaman', 1);
        $this->dataHalamanUnit['denganSubUnitnya'] = (bool)$this->property('denganSubUnitnya', 1);
        $this->dataHalamanUnit['data'] = UnitKerja::where('slug', $this->dataHalamanUnit['slug'])->first();
        if($this->dataHalamanUnit['denganSubUnitnya']) {
            $unitDicari =  $this->dataHalamanUnit['data']
                ->allChildren(true)->lists('id');
        } else {
            $unitDicari = [$this->dataHalamanUnit['data']->id];
        }
        
        if($this->dataHalamanUnit['data']  !== null) {
            // check tulisan yang dimuat
            $this->dataHalamanUnit['tulisan'] = Tulisan::whereIn('unit_kerja_id', $unitDicari)
                ->yangSudahDitampilkan() // hanya tampilkan yang sudah di approve!
                ->listDiFrontEnd([
                    'jumlahItemPerHalaman' => $this->dataHalamanUnit['recordPerHalaman'], 
                    'page' => $this->dataHalamanUnit['halaman'],
                    'order' => [
                        'created_at' => 'desc'
                    ]
                ]);
        } else {
            $this->dataHalamanUnit['tulisan'] = [];
        }
    }

    public function onRun()
    {
        $this->siapkanVariable();
        
    }
}
