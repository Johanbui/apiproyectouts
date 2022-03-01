<?php

namespace App\Http\Controllers\Auth;

use App\Models\Rol;
use App\Models\UserRol;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $enable = (bool) auth()->user()->enable;
        if(!$enable){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();

        $user_roles = UserRol::where('user_id',$user->id)
                            ->where('enable',"=",1)
                            ->get();;
        $user_roles_obj = [];
        for ($i=0; $i < count($user_roles); $i++) {

            $rol = Rol::find($user_roles[$i]->rol_id);
            if($rol->enable == 1){
                array_push($user_roles_obj, $rol);
            }

        }

        $user->users_rols = $user_roles_obj;

        return response()->json([
            'data' => auth()->user(),
            'code' => 20000
        ]);

    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();


        return response()->json([
            'data'=>[
                'message' => 'Successfully logged out'
            ],
            'code' => 20000
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
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
            'data'=>[
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * (60*24)
            ],
            'code' => 20000
        ]);
    }
}
