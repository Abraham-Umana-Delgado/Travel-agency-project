<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Huesped;
use Validator;
use Carbon\Carbon;

class HuespedController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth',['except'=>['index','show']]);
    }

    public function __invoke()
    {
        
    }

    //INDEX
    public function index(){
        $data=Huesped::all();
        $response=array(
            'status'=>'success',
            'code'=>200,
            'data'=>$data
        );
        return response()->json($response,200);
    }

    //SHOW
    public function show($id_huesped){
        $data=Huesped::find($id_huesped);
        if(is_object($data)){
            //$data=$data->load('reserva');
            $response=array(
                'status'=>'success',
                'code'=>200,
                'data'=>$data 
            );
        }else{
            $response=array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Registro no encontrado'
            ); 
        } 
        return response()->json($response,$response['code']);
    }

    //STORE
    public function store(Request $request){
        $json =$request->input('json',null);
        $data=json_decode($json,true);
        if(!empty($data)){
            $data=array_map('trim',$data);
            $rules=[
                'id'=>'required',
                'nombre'=>'required|alpha',
                'apellidos'=>'required|alpha',
                'telefono'=>'required',
                'nacionalidad'=>'required|alpha',
                'email'=>'required|email',
                'fecha_nacimiento'=>'required|date'
            ];
            $validate=\validator($data,$rules);
            if($validate->fails()){
                $response=array(
                    'status'=>'error',
                    'code'=>406,
                    'message'=>'Los datos enviados son incorrectos',
                    'errors'=>$validate->errors()
                );
            }else{
                $huesped=new Huesped();
                $huesped->id=$data['id'];
                $huesped->nombre=$data['nombre'];
                $huesped->apellidos=$data['apellidos'];
                $huesped->telefono=$data['telefono'];
                $huesped->email=$data['email'];
                $huesped->fecha_nacimiento=$data['fecha_nacimiento'];
                $huesped->nacionalidad=$data['nacionalidad'];
                $huesped->save();
                $response=array(
                    'status'=>'success',
                    'code'=>201,
                    'message'=>'Datos almacenados satisfactoriamente'
                );
            }
        }
        else{
            $response=array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Faltan parametros'
            );
        }
        return response()->json($response,$response['code']);
    }
    
    //update
    public function update(Request $request){ 
        $json=$request->input('json',null);
        $data=json_decode($json,true);
        if(!empty($data)){
            $data=array_map('trim',$data);
            $rules=[
                'id'=>'required',
                'nombre'=>'required|alpha',
                'apellidos'=>'required|alpha',
                'telefono'=>'required|numeric',
                'nacionalidad'=>'required|alpha',
                'email'=>'required|email',
                'fecha_nacimiento'=>'required|date'
            ];
            $validate=\validator($data,$rules);
            if($validate->fails()){
                $response=array(
                    'status'=>'error',
                    'code'=>406,
                    'message'=>'Los datos enviados son incorrectos',
                    'errors'=>$validate->errors()
                );
            }else{
                $id=$data['id'];
                unset($data['id']);        
                unset($data['created_at']);
                $data['updated_at']=now();
                $updated=Huesped::where('id',$id)->update($data);
                if($updated>0){
                    $response=array(
                        'status'=>'success',
                        'code'=>200,
                        'message'=>'Datos actualizados exitosamente'
                    );
                }else{
                    $response=array(
                        'status'=>'error',
                        'code'=>400,
                        'message'=>'No se pudo actualizar los datos'
                    );
                }
            }
        }else{
            $response=array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Faltan parametros'
            );
        }
        return response()->json($response,$response['code']);
    }

    //DESTROY
    public function destroy($id){
        if(isset($id)){
            $delete=Huesped::where('id',$id)->delete();
            if($delete){
                $response=array(
                    'status'=>'success',
                    'code'=>200,
                    'message'=>'El huesped se ha eliminado exitosamente'
                ); 
            }else{
                $response=array(
                    'status'=>'error',
                    'code'=>400,
                    'message'=>'Error al eliminar el registro'
                );
            }
        }else{
            $response=array(
                'status'=>'error',
                'code'=>400,
                'message'=>'No se encontro el id del huesped'
            );   
        }
        return response()->json($response,$response['code']);
    }
}