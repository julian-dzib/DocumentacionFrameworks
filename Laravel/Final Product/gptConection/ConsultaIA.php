<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultaIA extends Model
{
    use HasFactory;
    protected $table = "consulta_ia";

    protected $fillable =
    [
        'nombre',
        'contexto',
        'rol',
        'tabla_id',
        'database_id'
    ];

    //Definir los campos para filtrar, por nombre, contexto y rol
    public function scopeFiltrar($query, $semilla){
        $query->whereRaw('LOWER(nombre) like ?', "%$semilla%");
        $query->whereRaw('LOWER(contexto) like ?', "%$semilla%");
        $query->whereRaw('LOWER(rol) like ?', "%$semilla%");
    }
    
    
    
    //Establecer la relacion uno a muchos con las tablas modulo y databases
    //Una Consulta ia tiene varios modulos
    public function modulos(){
        return $this->belongsTo('App\Modulo','id','tabla_id');
    }
    
}
