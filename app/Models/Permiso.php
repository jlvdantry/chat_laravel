<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Permiso extends Model
{

    /**
     * Constantes.
     * Verificar que correspondan al ID de la tabla Permisos
     */
    public const CIUDADANO      = 1;
    public const DESARROLLADOR  = 2;
    
    public const NB_CIUDADANO      = 'Ciudadano';
    public const NB_DESARROLLADOR  = 'Desarrollador';

    
    /**
     * Relación con usuarios
     *  
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class,'permiso_user', 'id_permiso','id_usuario');
    }
}
