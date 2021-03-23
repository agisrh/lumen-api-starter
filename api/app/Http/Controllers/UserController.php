<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTAuth;
use App\User;
use DB;

class UserController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;
    /**
     * AuthController constructor.
     * @param JWTAuth $jwt
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
    * Checkin
    *
    * @param $request Request
    */

    public function profile()
    {
        $user_id = $this->jwt->user()->id;
        $profil = DB::select("SELECT * FROM merchandisers WHERE id='$id'");
        return respondWithData(true, 200, 'Profil Merchandiser', $profil);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFCMToken(Request $request)
    {
        $user_id = $this->jwt->user()->id;
        $user = User::find($user_id);

        $this->validate($request, [
            'fcm_token' => 'required|string',
        ]);

        // update
       $update = $user->update([
        'fcm_token'  => $request->fcm_token
       ]);
    
       if($update){
        return respondWithMessage("Token FCM berhasil di perbaharui !", true, 200);
       }else{
        return respondWithMessage("Token FCM gagal di perbaharui !", true, 403);
       }
    }
}