<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

//use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user)
    {
        //check if the url is a valid signed url
        if (!URL::hasValidSignature($request)) {
            return response()->json(["errors" => [
                "message" => "Verificación de link invalido"
            ]], 422);
        }

        // check if the user has alredy verified account
        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "Email ya ha sido verificado"
            ]], 422);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json(["errorrs" => [
            "message" => "Email verificado satisfactoriamente"
        ]], 200);
    }

    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        $user = User::where('email', $request->email)->first();

        // check if the user has alredy verified account
        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "Email ya ha sido verificado"
            ]], 422);
        }

        if (!$user) {
            return response()->json(["errors" => [
                "email" => "No se ha encontrado un usuario con este email"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status'  => "Link de verificación reenviada"]);
    }
}
