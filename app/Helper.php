<?php
if(!function_exists('authApi')){
    function authApi(){
        return auth()->guard('api');
    }
}
if(!function_exists('Res_data')){
    function Res_data($data,$message=null,$status=200){
        $message=$message??__('main.success_quere');
  return response([
     'message'=> $message,
      'data'=> !empty($data)?$data:null,
      'statusCode'=> $status,
     'status'=>in_array($status,[201,202,203,200]),

  ])  ;
    }
}

