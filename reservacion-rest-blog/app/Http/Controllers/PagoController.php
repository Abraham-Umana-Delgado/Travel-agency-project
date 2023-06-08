<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use Carbon\Carbon;

class PagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth',['except'=>['index','show']]);
    }

    public function __invoke()
    {
        
    }
    
   public function index(){
        $data=Pago::all();
        $response=array(
            'status'=>'success',
            'code'=>200,
            'data'=>$data
        );
        return response()->json($response,200);
    } 
    //show--> devuelve un elemento por su id GET
    public function show($id){
        $data=Pago::find($id);
        if(is_object($data)){
            $response=array(
                'status'=>'success',
                'code'=>200,
                'data'=>$data
            );
        }else{
            $response=array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Recurso no encontrado'
            );
        }
        return response()->json($response,$response['code']);
    }
    //store --> agrega o guarda un elemnto  POST
    public function store(Request $request){
        $json =$request->input('json',null);
        $data=json_decode($json,true);
        if(!empty($data)){
            $data=array_map('trim',$data);
            $rules=[
                'titular_t'=>'required|alpha',
                'n_tarjeta'=>'required',
                't_tarjeta'=>'required|alpha',
                'id_reserva'=>'required',
                'cvc'=>'required',
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
                $pago=new Pago();
                $pago->titular_t=$data['titular_t'];
                $pago->n_tarjeta=$data['n_tarjeta'];
                $pago->t_tarjeta=$data['t_tarjeta'];
                $pago->id_reserva=$data['id_reserva'];
                $pago->cvc=$data['cvc'];
                $pago->cvc=$data['cvc'];
                $pago->save();
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
    //update --> modifica un elemento    PUT
    public function update(Request $request){
        $json=$request->input('json',null);
        $data=json_decode($json,true);
        if(!empty($data)){
            $data=array_map('trim',$data);
            $rules=[
                'titular_t'=>'required|alpha',
                'n_tarjeta'=>'required',
                't_tarjeta'=>'required|alpha',
                'id_reserva'=>'required',
                'cvc'=>'required'
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
                $updated=Pago::where('id',$id)->update($data);
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
                        'message'=>'No se pudo actualizar los datos de los pagos'
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
    //destroy --> Elimina un elemento   DELETE
    public function destroy($id){
        if(isset($id)){
            $deleted=Pago::where('id',$id)->delete();
            if($deleted){
                $response=array(
                    'status'=>'success',
                    'code'=>200,
                    'message'=>'Eliminado correctamente'
                );
            }else{
                $response=array(
                    'status'=>'error',
                    'code'=>400,
                    'message'=>'Error al eliminar el registro, verifique que exista el id'
                );
            }
        }else{
            $response=array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Falta el identificador del recurso' 
            );
        }
        return response()->json($response,$response['code']);
    }
}