<?php

namespace App\Http\Controllers;

use App\ConsultaIA;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use OpenAI\Laravel\Facades\OpenAI;
use App\Utilerias\Util;
class ConsultaAIController extends Controller
{
    //instalar
    //composer require openai-php/laravel 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            //Filtro por databse_od
            $q= str_replace('','%',strtolower($request->q));
            //Paginacion
            $registros = isset($request->registros)?$request->registros:-1;


            //Filstrar los registros por database_id
            $query = ConsultaIA::where('database_id', $request->database_id);

            //Filtro por modulo que es con tabla_id
            if (isset($request->tabla_id)) {
                $query = $query->where('tabla_id', '=', $request->tabla_id);
            }
            //En caso de que no se agregue en el request
            if(isset($request->estatus)){
               $query = $query-> where('estatus', '=',(bool)$request->estatus);
            }else{
                $query = $query->where('estatus','=',true);
            }
            //scope en el model
            //Llamos al filtro
            $query->filtrar($q);
            //Si no se manda el registro devuelve todos
            if($registros == -1){
                $row = $query->get();
            }else{
                $row = $query->paginate($registros);
            }

            return response()->json($row, 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        try {
            $row = new ConsultaIA();
            $row-> nombre = $request->nombre;
            $row-> contexto = $request->contexto;
            $row-> rol = $request->rol;
            $row-> tabla_id = $request->tabla_id;
            $row-> database_id = $request->database_id;

            $row->save();
            return response()->json(
                $row,200
            );
        } catch (\Exception $e) {
            return response()->json([
                'error'=>$e->getMessage()
            ],400);
        }
    }

    public function show($id)
    {
        //Devolver solo los datos que se encuentran en true
        try{
            $row = ConsultaIA::find($id);

            //Delver en caso de que no sea nulo
            if(!empty($row)){
                  return response()->json(
                $row,
                200);
            }else {
                return response()->json([
                    'error'=> 'La plantilla de consulta ingresado, no se encuentra'
                ],400);
            }
        
        }catch (\Exception $e){
            response()->json([
                "error"=> $e->getMessage()
            ],500);
        } 
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $row = ConsultaIA::find($id);
        if($row){
            $row-> nombre = $request->nombre;
            $row-> contexto = $request->contexto;
            $row-> rol = $request->rol;
            $row-> tabla_id = $request->tabla_id;
            $row-> database_id = $request->database_id;

            $row->save();
            return response()->json(
                $row,200
            );
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $row=ConsultaIA::find($id);
            if (!empty($row)) {
                $row -> estatus = false;
                $row -> save();

                return response()->json(
                    $row, 200
                );
            }else {
                return response()->json([
                    'error' =>'No se encuentra la consulta solicitada'
                ],400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error'=>$e->getMessage()
            ],400);
        }
    }

    public function consultarTexto($id)
    {
        try {
            $row = ConsultaIA::findOrFail($id);

           
            $rol= $row->rol ?? '';
            $contexto= $row->contexto ?? '';
             //Validar que no devualva campos vacios
            if(empty($rol)||empty($contexto)) 
                return response()->json([
                    'rol'=>"El campo rol es obligatorio",
                    'contexto'=> "El campo contexto es obligatorio"
                ],402);

            //mandar a gpt
            $response = OpenAI::chat()->create([
                'model' => 'gpt-5-chat-latest',
                'messages' => [
                    [
                        'role' => $rol, 
                        'content' => $contexto
                    ],
                ],
                'max_completion_tokens' => 40,
                'temperature' => 1.0,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
            ]);

            $respuesta = $response->choices[0]->message->content ?? '';

            return response()->json([
                'registro' => $row,
                'rol' => $rol,
                'contexto' => $contexto,
                'respuestaGPT' => $respuesta,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function consultarImg($id, Request $request)
    {
        try {
            //Buscarl registro existente
            $row = ConsultaIA::findOrFail($id);

           
            $rol= $row->rol ?? '';
            $contexto= $row->contexto ?? '';
            $file = $request->file("img");

             //Validar que mis campos no acepten nulos
            if(empty($rol)||empty($contexto)||!$file) 
                return response()->json([
                    'rol'=>'El campo rol es obligatorio',
                    'contexto'=> 'El campo contexto es obligatorio',
                    'img' => 'Cargue la imágen porfavor'
                ],422);

            //Convertir la img a base64
            $base64Image = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType(); 


            //mandar a gpt
            $response = OpenAI::chat()->create([
                'model' => 'gpt-5-chat-latest',
                'messages' => [
                    [
                        'role' => $rol, 
                        'content' =>[
                            [
                                'type'=>'text',
                                'text'=>$contexto,
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64Image}"],

                            ],
                        ],
                        
                    ],
                ],
                'max_completion_tokens' => 300,
                'temperature' => 0.7,
            ]);

            $respuesta = $response['choices'][0]['message']['content'] ?? '';
            
            return response()->json([
                'registro' => $row,
                'rol' => $rol,
                'contexto' => $contexto,
                'respuestaGPT' => $respuesta,
            ], 200);
        
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}