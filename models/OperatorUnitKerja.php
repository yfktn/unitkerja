<?php namespace Yfktn\UnitKerja\Models;

use Backend\Models\User;
use Db;
use Illuminate\Contracts\Container\BindingResolutionException;
use Model;

/**
 * Model
 */
class OperatorUnitKerja extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'yfktn_unitkerja_operator';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'unit_kerja_id' => 'required',
        'user_id' => 'required'
    ];

    public $belongsTo = [
        'unitKerja' => [
            'Yfktn\UnitKerja\Models\UnitKerja',
            'key' => 'unit_kerja_id'
        ],
        'user' => [
            'Backend\Models\User',
            'key' => 'user_id'
        ]
    ];

    /**
     * Tampilkan user yang bisa dipilih, untuk ini jangan tampilkan lagi yang sudah
     * ditambahkan sebelumnya.
     * @param mixed $fieldName 
     * @param mixed $value 
     * @return mixed 
     * @throws BindingResolutionException 
     */
    public function getUserOptions($fieldName, $value)
    {
        $currentId = request()->segment(6);
        return User::selectRaw("concat(first_name, ' ', last_name) as opname, id")
            ->whereNotIn('id', function($query) use($currentId) {
                $query->from('yfktn_unitkerja_operator')
                    ->selectRaw('user_id')
                    ->where('unit_kerja_id', $currentId);
            })
            ->pluck('opname', 'id');
    }
}
