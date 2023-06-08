<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth',['except'=>['index','show','store']]);
    }

    public function __invoke()
    {
        
    }
     //INDEX
     public function index(){
        $data=Reserva::all();
        $response=array(
            'status'=>'success',
            'code'=>200,
            'data'=>$data
        );
        return response()->json($response,200);
    }

    //SHOW
    public function show($id){
        $data=Reserva::find($id);
        if(is_object($data)){
            $data=$data->load('huesped');
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

    //STORE 
    public function store(Request $request){
        $json =$request->input('json',null);
        $data=json_decode($json,true);
        if(!empty($data)){
            $data=array_map('trim',$data);
            $rules=[
                'id_huesped'=>'required',
                'id_habitacion'=>'required',
                'fecha_entrada'=>'required|date',
                'fecha_salida'=>'required|date',
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
                $precio=DB::table('habitaciones')->select('precio')->where('id', '=', $data['id_habitacion'])->get();
                $reserva=new Reserva();
                $reserva->id=$data['id'];
                $reserva->id_huesped=$data['id_huesped'];
                $reserva->id_habitacion=$data['id_habitacion'];
                $reserva->fecha_entrada=$data['fecha_entrada'];
                $reserva->fecha_salida=$data['fecha_salida'];
                $dias=(int) ((strtotime($data['fecha_salida'])-strtotime($data['fecha_entrada']))/86400);
                $reserva->cant_dias=$dias;
                $reserva->precio_f=$precio[0]->precio*$dias;
                $reserva->save();
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
                'fecha_entrada'=>'required|date',
                'fecha_salida'=>'required|date',
                'precio_f'=>'required',
                'id_habitacion'=>'required',
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
                unset($data['id_huesped']);
                //$data['updated_at']=Carbon::now();;
                $data['updated_at']=now();
                $updated=Reserva::where('id',$id)->update($data);
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
    public function destroy($idReserva){
        if(isset($idReserva)){
            $delete=Reserva::where('id',$idReserva)->delete();
            if($delete){
                $response=array(
                    'status'=>'success',
                    'code'=>200,
                    'message'=>'El Registro se ha eliminado exitosamente'
                ); 
            }else{
                $response=array(
                    'status'=>'error',
                    'code'=>400,
                    'message'=>'Ocurrio un error en la eliminación del registro'
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
}