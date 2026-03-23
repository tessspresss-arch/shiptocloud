<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();
        if ($user) {
            $accountStatus = (string) ($user->account_status ?? 'actif');
            $isExpired = $user->account_expires_at && now()->greaterThan($user->account_expires_at->endOfDay());

            if (in_array($accountStatus, ['suspendu', 'desactive', 'en_attente'], true) || $isExpired) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $accountMessage = match (true) {
                    $isExpired => "Votre compte a expire. Contactez l'administrateur pour reactiver l'acces.",
                    $accountStatus === 'desactive' => "Votre compte est desactive. Contactez l'administrateur.",
                    $accountStatus === 'suspendu' => "Votre compte est temporairement suspendu. Contactez l'administrateur.",
                    $accountStatus === 'en_attente' => "Votre compte n'est pas encore autorise. Contactez l'administrateur.",
                    default => "Votre compte n'est pas actif. Contactez l'administrateur.",
                };

                throw ValidationException::withMessages([
                    'email' => $accountMessage,
                ]);
            }
        }

        $request->session()->regenerate();

        $user = $request->user();
        if ($user) {
            $now = Carbon::now();
            $user->forceFill([
                'last_login_at' => $now,
                'last_activity_at' => $now,
            ])->save();
            $request->session()->put('last_activity_at', $now->timestamp);

            $requiresChallenge = (bool) $user->two_factor_enabled
                && !empty($user->two_factor_secret)
                && !empty($user->two_factor_confirmed_at);

            if ($requiresChallenge) {
                $request->session()->forget('two_factor_passed_at');

                return redirect()->route('two-factor.challenge');
            }
        }

        return redirect()->intended($this->resolvePostLoginRoute((string) ($user?->role ?? '')));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $request->session()->forget('two_factor_passed_at');

        return redirect('/');
    }

    private function resolvePostLoginRoute(string $role): string
    {
        $user = auth()->user();
        if (! $user) {
            return RouteServiceProvider::HOME;
        }

        if ($user->hasRole('admin') && Route::has('admin.dashboard')) {
            return route('admin.dashboard');
        }

        $moduleRouteMap = [
            'dashboard' => 'dashboard',
            'patients' => 'patients.index',
            'consultations' => 'consultations.index',
            'planning' => 'agenda.index',
            'medecins' => 'medecins.index',
            'pharmacie' => 'medicaments.index',
            'facturation' => 'factures.index',
            'examens' => 'examens.index',
            'depenses' => 'depenses.index',
            'contacts' => 'contacts.index',
            'sms' => 'sms.index',
            'documents' => 'documents.index',
            'statistiques' => 'statistiques',
            'rapports' => 'rapports.index',
        ];

        $rolePreferredModules = [
            'medecin' => ['consultations', 'planning', 'patients', 'dashboard'],
            'infirmier' => ['patients', 'planning', 'consultations', 'dashboard'],
            'infirmiere' => ['patients', 'planning', 'consultations', 'dashboard'],
            'infirmière' => ['patients', 'planning', 'consultations', 'dashboard'],
            'receptionniste' => ['planning', 'patients', 'facturation', 'sms', 'dashboard'],
            'réceptionniste' => ['planning', 'patients', 'facturation', 'sms', 'dashboard'],
            'secretaire' => ['planning', 'patients', 'facturation', 'sms', 'dashboard'],
            'secrétaire' => ['planning', 'patients', 'facturation', 'sms', 'dashboard'],
        ];

        $normalizedRole = mb_strtolower(trim($role), 'UTF-8');
        $preferredModules = $rolePreferredModules[$normalizedRole] ?? ['dashboard'];

        foreach ($preferredModules as $moduleId) {
            if (! $user->hasModuleAccess($moduleId)) {
                continue;
            }

            $routeName = $moduleRouteMap[$moduleId] ?? null;
            if ($routeName && Route::has($routeName)) {
                return route($routeName);
            }
        }

        foreach (\App\Models\User::managedModules() as $module) {
            $moduleId = (string) ($module['id'] ?? '');
            if ($moduleId === '' || ! $user->hasModuleAccess($moduleId)) {
                continue;
            }

            $routeName = $moduleRouteMap[$moduleId] ?? null;
            if ($routeName && Route::has($routeName)) {
                return route($routeName);
            }
        }

        if (Route::has('dashboard')) {
            return route('dashboard');
        }

        return RouteServiceProvider::HOME;
    }
}
