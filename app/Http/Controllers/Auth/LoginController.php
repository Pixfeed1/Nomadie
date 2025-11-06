<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Redirection selon le rôle de l'utilisateur
        if ($user->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }
        
        // Si l'utilisateur est un vendeur
        if ($user->role === 'vendor' || $user->isVendor()) {
            return redirect()->intended('/vendor/dashboard');
        }
        
        // Si l'utilisateur est un client
        if ($user->role === 'customer') {
            return redirect()->intended('/mon-compte');
        }
        
        // Par défaut, rediriger les clients vers leur dashboard
        return redirect()->intended('/mon-compte');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function loggedOut(Request $request)
    {
        return redirect('/');
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            
            // Recherche de l'utilisateur par google_id
            $finduser = User::where('google_id', $user->id)->first();
            
            if ($finduser) {
                // Connexion si l'utilisateur existe déjà
                Auth::login($finduser);
                return $this->authenticated(request(), $finduser);
            } else {
                // Recherche de l'utilisateur par email
                $existingUser = User::where('email', $user->email)->first();
                
                if ($existingUser) {
                    // Mise à jour de l'utilisateur existant avec l'ID Google
                    $existingUser->google_id = $user->id;
                    $existingUser->save();
                    
                    Auth::login($existingUser);
                    return $this->authenticated(request(), $existingUser);
                } else {
                    // Création d'un nouvel utilisateur
                    $nameParts = explode(' ', $user->name);
                    $firstname = $nameParts[0] ?? '';
                    $lastname = $nameParts[1] ?? '';
                    
                    $newUser = User::create([
                        'name' => $user->name,
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'pseudo' => $firstname,
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'password' => bcrypt(uniqid()),
                        'role' => 'customer', // Les nouveaux utilisateurs Google sont des clients par défaut
                        'email_verified_at' => now()
                    ]);
                    
                    Auth::login($newUser);
                    // Rediriger vers le dashboard client
                    return redirect()->intended('/mon-compte');
                }
            }
        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Une erreur est survenue lors de la connexion avec Google: ' . $e->getMessage());
        }
    }
}