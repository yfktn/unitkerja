<?php namespace Yfktn\UnitKerja\Models;

use Backend\Models\User;
use Db;
use Illuminate\Contracts\Container\BindingResolutionException;
use Model;
use Yfktn\UnitKerja\Classes\UserAndUnitUtil;

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

    public function afterSave()
    {
        UserAndUnitUtil::forgetCacheUnitOfUser($this->user_id);
    }

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
        $dU = User::selectRaw("first_name, last_name, login, is_superuser, id")
            ->whereNotIn('id', function($query) use($currentId) {
                $query->from('yfktn_unitkerja_operator')
                    ->selectRaw('user_id')
                    ->where('unit_kerja_id', $currentId);
            })
            ->get();
        $ret = [];
        foreach($dU as $d) {
            if($d->isSuperUser()) {
                continue;
            }
            $nm = implode(" ", [$d->first_name, $d->last_name]) . " ({$d->login})";
            $ret[$d->id] = $nm;
        }
        return $ret;
    }
}
