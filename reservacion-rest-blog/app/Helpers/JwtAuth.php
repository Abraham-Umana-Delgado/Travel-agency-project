<?php
namespace App\Helpers;
use Firebase\JWT\JWT;
use App\Models\User;

class JwtAuth{
    private $key;
    function __construct()
    {
        $this->key='132612346165231353eqwq1';
    }
    public function signin($email,$password){ //GENERA EL TOKEN PARA UN USUARIO AUTENTICADO
        $user=User::where([
            'email'=>$email,
            'password'=>hash('sha256',$password)
        ])->first();
        if(is_object($user)){
            $token=array(
                'sub'=>$user->id,
                'email'=>$user->email,
                'name'=>$user->name,
                'iat'=>time(),
                'exp'=>time()+1200
            );
            $data=JwT::encode($token,$this->key,'HS256');
        }else{
            $data=array(
                'status'=>'error',
                'code'=>401,
                'message'=>'Datos de autenticación incorrectos'
            );
        }
        return $data;
    }
    
    public function verify($token,$getIdentity=false){ //VERFICA Y DEVUELVE LA INFORMACIÓN PUBLICA DEL TOKEN
        $auth=false;
        try{
            $decoded=JwT::decode($token,$this->key,['HS256']);
        }catch(\UnexpectedValueException $ex){
            $auth=false;
        }
        catch(\DomainException $ex){
            $auth=false;
        }
        if(!empty($decoded)&&is_object($decoded)&&isset($decoded->sub)){
            $auth=true;
        }
        if($getIdentity){
            return $decoded;
        }
        return $auth;
    }
}