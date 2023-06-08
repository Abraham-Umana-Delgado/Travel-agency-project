<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion;

class HabitacionController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth',['except'=>['index','show','store']]);
    }

    public function __invoke()
    {
        
    }

    //INDEX
    public function index(){
        $data=Habitacion::all();
        $response=array(
            'status'=>'success',
            'code'=>200,
            'data'=>$data
        );
        return response()->json($response,200);
    }

    //SHOW
    public function show($id){
        $data=Habitacion::find($id);
        if(is_object($data)){
            $data=$data->load('reserva');
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
                'tipo'=>'required|alpha',
                'estado'=>'required|alpha',
                'precio'=>'required',
                'caracteristicas'=>'required|alpha',
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
                $habitacion=new Habitacion();
                $habitacion->tipo=$data['tipo'];
                $habitacion->estado=$data['estado'];
                $habitacion->precio=$data['precio'];
                $habitacion->caracteristicas=$data['caracteristicas']; 
                $habitacion->save();
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
    public function update(Request $request){ ///revisar
        $json=$request->input('json',null);
        $data=json_decode($json,true);
        if(!empty($data)){
            $data=array_map('trim',$data);
            $rules=[
                'tipo'=>'required|alpha',
                'estado'=>'required',
                'precio'=>'required',
                'caracteristicas'=>'required',
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
                $id =$data['id'];
                unset($data['id']);        
                unset($data['created_at']);
                $data['updated_at']=now();
                $updated=Habitacion::where('id',$id)->update($data);
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
            $delete=Habitacion::where('id',$id)->delete();
            if($delete){
                $response=array(
                    'status'=>'success',
                    'code'=>200,
                    'message'=>'La habitacion se ha eliminado exitosamente'
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
                'message'=>'No se encontro el id de la habitacion'
            );   
        }
        return response()->json($response,$response['code']);
    }

    //obtiene la imagen
    public function getImage($filename){
        $exist=\Storage::disk('habitacion')->exists($filename);
        if($exist){
            $file=\Storage::disk('habitacion')->get($filename);
            return new Response($file,200);
        }else{
            $response=array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Recurso/imagen no existe'
            );
            return response()->json($response,$response['code']);
        }
    }
    public function uploadImage(Request $request){
        $image=$request->file('file0');
        $validate=\Validator::make($request->all(),[
            'file0'=>'required|image|mimes:jpg,jpeg,png'
        ]);
        if($validate->fails()){
            $response=array(
                'status'=>'error',
                'code'=>406,
                'message'=>'Error al subir la imagen',
                'errors'=>$validate->errors()
            );
        }else{
            $image_name=time().$image->getClientOriginalName();
            \Storage::disk('habitacion')->put($image_name,\File::get($image));
            $response=array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Imagen almacenada exitosamente',
                'image'=>$image_name
            );
        }
        return response()->json($response,$response['code']);
    }
}