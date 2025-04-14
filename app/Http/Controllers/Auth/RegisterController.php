<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:14', 'min:8'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'adresse_postale' => ['required', 'string', 'max:255'],
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'date_naissance' => ['required', 'date'],
            'service_id' => ['required', 'exists:services,id'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
{
    return User::create([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'telephone' => $data['telephone'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'adresse_postale' => $data['adresse_postale'],
        'photo' => $data['photo']->store('photos', 'public'),
        'date_naissance' => $data['date_naissance'],
        'service_id' => $data['service_id'],
    ]);
}

}
