<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\User;
use App\Log;
// for reset password
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
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
     * Register new user
     *
     * @param $request Request
     */
    public function register(Request $request)
    {
        # Validasi
        $this->validate($request, [
            'identity_id'   => 'required|string|unique:users', // data harus unik, tidak boleh sama
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:6',
            'name'          => 'required|string|max:50',
            'gender'        => 'required|in:L,P',
            'address'       => 'required|string',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png',
            'phone_number'  => 'required|string',
        ]);

        # Atribut foto
        $filename = null;
        if ($request->hasFile('photo')) {
            $filename = Str::random(32) . '.jpg';
            $file = $request->file('photo');
            $file->move(base_path('public/images/users'), $filename); //
        }

        # Post data
        $register = User::create([
            'identity_id'   => $request->identity_id,
            'email'         => $request->email,
            'password'      => app('hash')->make($request->password),
            'name'          => $request->name,
            'gender'        => $request->gender,
            'address'       => $request->address,
            'photo'         => $filename,
            'phone_number'  => $request->phone_number
        ]);

        if ($register) {
            return respondWithMessage("Successful register !", true, 200);
        }else{
            return respondWithMessage("Failed to register...", false, 409);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        $system_version = $request->get('system_version');

        try {
            if (! $token = $this->jwt->attempt([
                'email'    => $request->get('email'),
                'password' => $request->get('password'),
                'active'   => '1'
               ])) 
            {
                return respondWithMessage("Email and password combination doesn't match !", false, 404);
            }
        } catch (TokenExpiredException $e) {
            return respondWithMessage('Token Expired', false, $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return respondWithMessage('Token Invalid', false, $e->getStatusCode());
        } catch (JWTException $e) {
            return respondWithMessage('Token Absent', false, $e->getStatusCode());
        }
        
        $this->jwt->setToken($token);

        // insert log devices
        if($system_version!=''){
            $log = Log::create([
                'user_id'          => $this->jwt->user()->id,
                'system_version'   => $request->get('system_version'),
                'sdk'              => $request->get('sdk'),
                'manufacturer'     => $request->get('manufacturer'),
                'brand'            => $request->get('brand'),
                'model'            => $request->get('model'),
                'codename'         => $request->get('codename'),
                'app_version'      => $request->get('app_version'),
                'log_date'         => date('Y-m-d H:i:s')
            ]);
        }
        
        return $this->respondWithToken($token);
    }


    public function sendResetToken(Request $request)
    {
        // Cek apakah email terdaftar
        $this->validate($request, [
            'email' => 'required|email|exists:users'
        ]);

        $user = User::where('email', $request->email)->first();
        // Generate token
        $user->update(['reset_password' => generatePin(5)]);

        //kirim token via email sebagai otentikasi kepemilikan
         Mail::to($user->email)->send(new ResetPasswordMail($user));

        return response()->json([
            'success' => true,
            'status'  => 200,
            'message' => 'Token has been sent to email',
            'token'   => [
                'reset_password' => $user->reset_password
            ]
        ]);
    }

    public function verifyResetPassword(Request $request, $token)
    {
        //  minimal panjang password 6 karakter
        $this->validate($request, [
            'password' => 'required|string|min:6'
        ]);

        $user = User::where('reset_password', $token)->first();
        if ($user) {
            $user->update(['password' => app('hash')->make($request->password), 'reset_password' => NULL]);
            return respondWithMessage("Password changed successfully !", true, 200);
        }
        return respondWithMessage("Token reset password is expired !", false, 403);
    }
    
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return respondWithData(true, 200, 'User session', $this->jwt->user());
    }

    /**
     * Logout JWT
     * @param Request $request
     * @return array
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function logout(Request $request)
    {
        $this->jwt->invalidate($this->jwt->getToken());
        return response()->json(
            [
                'success' => true,
                "status"    => 200,
                "message" => "Logout berhasil !",
            ], 200
        );
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json(
            [
                'success' => true,
                "status"    => 200,
                "message" => "Token refresh",
                'token'    => [
                    'access_token' => $this->jwt->refresh(),
                    'token_type'   => 'Bearer',
                    'expires_in'   =>  $this->jwt->factory()->getTTL() * 60
                ]
            ], 200
        );
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
            'success' => true,
            'status'  => 200,
            'message' => 'Login berhasil...',
            'token'   => [
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'expires_in'   =>  $this->jwt->factory()->getTTL() * 60
            ]
        ]);
    }

   
}