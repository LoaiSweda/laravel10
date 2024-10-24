<?php

namespace Modules\Users\Http\Controllers\Api;

use App\Events\VerifyEmailByCode;
use App\Http\Requests\Register;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use function PHPUnit\TextUI\CliArguments\filter;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
public function activeCode(Request $request){
      $request->validate([
        'code_type'=>'required|in:email,mobile',
        'code'=>'required|integer'
        ]);
 if($request->code_type=='mobile')
    {
        if($request->code==authApi()->user()->mobile_code){
            $user=authApi()->user();
            $user->mobile_code=null;
            $user->mobile_verified_at=now();
            $user->save();
            $message=__('main.active_code');

        } else{
            $message=__('main.warn_code');

    }
    return Res_data([],$message);
    }

  elseif($request->code_type=='email'){
     if($request->code==authApi()->user()->email_code){
      $user=authApi()->user();
      $user->email_code=null;
      $user->email_verified_at=now();
      $user->save();
         $message=__('main.active_code');

     } else{
         $message=__('main.warn_code');
     }
 }
    return Res_data([],$message);
}



public function ResendActiveCode(Request $request){
$data=$request->validate(['code_type'=>'required|in:email,mobile']);
if($request->code_type='email'){
  event(new VerifyEmailByCode(User::find(authApi()->id())));
}
elseif($request->code_type='mobile'){
    event(new VerifyEmailByCode(User::find(authApi()->id())));

}
$message=__('main.succfily_sent_code');
    return Res_data([], $message);

}




public function register(Register $request){
$data = $request->validated();
$data['password']= bcrypt($request->password);
$data['mobile']= ltrim($request->mobile,'0');////when enter numeric 0 delete 0 in data
$data['mobile_code']= rand(00000,99999);
$data['email_code']= rand(00000,99999);
User::create($data);

$credentials=['email'=>$request->email,'password'=>$request->password];
return $this->login($credentials);

}
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(array  $creden=null)
    {
        $credentials = [
            'password'=>request('password'),
        ];
        if (filter_var(request('account'),FILTER_VALIDATE_EMAIL)){

            $credentials['email']=request('account');
        }
        elseif(intval(request('account'))){
            $credentials['mobile']=ltrim(request('account'),'0');
        }

        $attempt=!empty($creden)?$creden:$credentials;


        if (! $token = authApi()->attempt($attempt)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $data=[];
        $data['token']=$this->respondWithToken($token)->original;
        $data['need_mobile_verfied']=authApi()->user()->mobile_verified_at==null;
        $data['need_email_verfied']=authApi()->user()->email_verified_at==null;

        return Res_data($data, __('main.loggin_msg'));

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user=auth()->user()->only('id','name','email','password','mobile');
        return Res_data($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        authApi()->logout();

        return Res_data([],__('main.logout_msg'));
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token= $this->respondWithToken(authApi()->refresh());
        return Res_data($token,__('main.refresh_msg'));

    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => authApi()->factory()->getTTL() * 60
        ]);
    }
}
