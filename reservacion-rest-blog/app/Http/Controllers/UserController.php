<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('api.auth',['except'=>['login','store']]);
    }

    public function __invoke()
    {
        
    }
    
    public function index(){
        $data=User::all();
        $response=array(
            'status'=>'success',
            'code'=>200,
            'data'=>$data
        );
        return response()->json($response,200);
    }

    public function show($id){
        $user=User::find($id);
        if(is_object($user)){
            $response=array(
                'status'=>'success',
                'code'=>200,
                'data'=>$user
            );
        }else{
            $response=array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Usuario no encontrado'
            );
        }
        return response()->json($response,$response['code']);
    }

    public function store(Request $request){
        $json=$request->input('json',null);
        $data=json_decode($json,true);
        $data=array_map('trim',$data);
        $rules=[
            'name'=>'required|alpha',
            'apellidos'=>'required|alpha',
            'email'=>'required|email|unique:users',
            'password'=>'required',
            'fecha_n'=>'date',
        ];
        $valid=\validator($data,$rules);
        if($valid->fails()){
            $response=array(
                'status'=>'error',
                'code'=>406,
                'message'=>'Los datos son incorrectos',
                'errors'=>$valid->errors()
            );
        }else{
            $user=new User();
            $user->name=$data['name'];
            $user->apellidos=$data['apellidos'];
            $user->fecha_n=$data['fecha_n'];
            $user->role='user_role';
            $user->email=$data['email'];
            $user->password=hash('sha256',$data['password']);
            $user->save();
            $response=array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Datos almacenados exitosamente'
            );
        }
        return response()->json($response,$response['code']);
    }
    public function update(Request $request){
        $json = $request->input('json',null);
        $data= json_decode($json,true);// el true es para pasar ese json a array
        if(!empty($data)){
            $data=array_map('trim',$data);
            $rules=[
                'name'=>'required|alpha',
                'apellidos'=>'required',
                'email'=>'required|email',
                'role'=>'required'
            ];
            //validamos
            $validate = \validator($data, $rules);
            if($validate->fails()){
                $response=array(
                    'status'    =>'error',
                    'code'      =>406,
                    'message'   =>'Los datos enviados son incorrectos',
                    'errors'    => $validate->errors()
                );
            }
            else{
                $email =$data['email'];
                unset($data['id']);
                unset($data['email']);
                unset($data['password']);
                unset($data['create_at']);
                unset($data['remember_token']);
                $data['updated_at']=Carbon::now();
                //Buscamos y modificamos mediante ORM. Esto devuelve la cantidad de registros modificados. Cero si no se modificó
                $updated=User::where('email',$email)->update($data);
                if($updated>0){
                    $response=array(
                        'status'    =>'success',
                        'code'      =>200,
                        'message'   =>'Actualizado correctamente'
                    );
                }else{
                    $response=array(
                        'status'    =>'error',
                        'code'      =>400,
                        'message'   =>'No se pudo actualizar, puede que el usuario no exita'
                    );
                }
            }
        }else{
            $response=array(
                'status'    =>'error',
                'code'      =>400,
                'message'   =>'Faltan parametros'
            );
        }
        return response()->json($response,$response['code']);
    }

    public function destroy($id){
        if(isset($id)){
            $deleted=User::where('id',$id)->delete();
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
                    'message'=>'No se pudo eliminar'
            );
            }
        }
    }
    
    public function getIdentity(Request $request){
        $jwtAuth=new JwtAuth();
        $token=$request->header('token');
        $response=$jwtAuth->verify($token,true);
        return response()->json($response);   
    }

    public function login(Request $request){
        $jwtAuth= new JwtAuth();
        $json=$request->input('json',null);
        $data=json_decode($json,true);
        $data=array_map('trim',$data);
        $rules=[
            'email'=>'required|email',
            'password'=>'required'
        ];
        $validate=\validator($data,$rules);
        if($validate->fails()){
            $response=array(
                'status'=>'error',
                'code'=>'406',
                'message'=>'Los datos enviados son incorrectos',
                'errors'=>$validate->errors()
            );
        }
        else{
            $response=$jwtAuth->signin($data['email'],$data['password']);
        }
        if(isset($response['code'])){
            return response()->json($response,$response['code']);
        }else{
            return response()->json($response,200);
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
            \Storage::disk('users')->put($image_name,\File::get($image));
            $response=array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Imagen almacenada exitosamente',
                'image'=>$image_name
            );
        }
        return response()->json($response,$response['code']);
    }

    public function getImage($filename){
        $exist=\Storage::disk('users')->exists($filename);
        if($exist){
            $file=\Storage::disk('users')->get($filename);
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
}