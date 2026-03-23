<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\Security\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function __construct(private readonly TotpService $totpService)
    {
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $user->two_factor_enabled || empty($user->two_factor_confirmed_at) || empty($user->two_factor_secret)) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $user || empty($user->two_factor_secret)) {
            throw ValidationException::withMessages([
                'code' => 'Session invalide. Veuillez vous reconnecter.',
            ]);
        }

        if (! $this->totpService->verify((string) $user->two_factor_secret, (string) $request->input('code'))) {
            throw ValidationException::withMessages([
                'code' => 'Code de vérification invalide.',
            ]);
        }

        $request->session()->put('two_factor_passed_at', now()->timestamp);

        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
