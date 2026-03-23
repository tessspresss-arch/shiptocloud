<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Services\Security\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(private readonly TotpService $totpService)
    {
    }

    public function show(Request $request): View
    {
        $user = $request->user();

        $pendingSecret = (string) $request->session()->get('two_factor_pending_secret', '');
        if ($pendingSecret === '' && empty($user->two_factor_confirmed_at)) {
            $pendingSecret = $this->totpService->generateSecret();
            $request->session()->put('two_factor_pending_secret', $pendingSecret);
        }

        return view('profile.two-factor', [
            'user' => $user,
            'pendingSecret' => $pendingSecret,
            'provisioningUri' => $pendingSecret !== '' ? $this->totpService->provisioningUri($user, $pendingSecret) : null,
            'recoveryCodes' => (array) ($user->two_factor_recovery_codes ?? []),
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();
        $secret = (string) $request->session()->get('two_factor_pending_secret', '');

        if ($secret === '') {
            throw ValidationException::withMessages([
                'code' => 'Configuration 2FA expirée. Veuillez recharger la page et réessayer.',
            ]);
        }

        if (! $this->totpService->verify($secret, (string) $request->input('code'))) {
            throw ValidationException::withMessages([
                'code' => 'Code 2FA invalide. Vérifiez votre application d’authentification.',
            ]);
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
+            'force_password_change' => false,
        ])->save();

        $request->session()->forget('two_factor_pending_secret');
        $request->session()->put('two_factor_passed_at', now()->timestamp);

        return back()->with('success', 'Authentification à deux facteurs activée avec succès.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        $request->session()->forget(['two_factor_pending_secret', 'two_factor_passed_at']);

        return back()->with('success', 'Authentification à deux facteurs désactivée.');
    }

    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->two_factor_enabled || empty($user->two_factor_confirmed_at)) {
            return back()->withErrors(['error' => 'La 2FA doit être active pour générer des codes de secours.']);
        }

        $user->forceFill([
            'two_factor_recovery_codes' => $this->generateRecoveryCodes(),
        ])->save();

        return back()->with('success', 'Nouveaux codes de secours générés.');
    }

    private function generateRecoveryCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn () => strtoupper(Str::random(4) . '-' . Str::random(4)))
            ->values()
            ->all();
    }
}
