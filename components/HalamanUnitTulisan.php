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
        ];
    }

    protected function siapkanVariable()
    {
        $this->dataHalamanUnit['slug'] = $this->property('slug');
        $this->dataHalamanUnit['data'] =  UnitKerja::where('slug', $this->dataHalamanUnit['slug'])
            ->first();
        if($this->dataHalamanUnit['data']  !== null) {
            // check tulisan yang dimuat
            $this->dataHalamanUnit['tulisan'] = Tulisan::where('unit_kerja_id', $this->dataHalamanUnit['data']->id)
                ->get();
        } else {
            $this->dataHalamanUnit['tulisan'] = [];
        }
    }

    public function onRun()
    {
        $this->siapkanVariable();
        
    }
}
