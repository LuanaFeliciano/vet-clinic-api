<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ApiEmailVerificationRequest;
use App\Http\Requests\Auth\RegisterClinicRequest;
use App\Models\Clinic;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Registrar Nova Clínica
     *
     * Cria uma nova clínica, um usuário admin e envia o e-mail de verificação.
     *
     * @group Autenticação
     *
     * @response 201 {
     * "message": "Cadastro realizado com sucesso! Verifique seu e-mail.",
     * "token": "1|aBcDeFg...",
     * "user": { "id": 1, "name": "Dr. João", "role": "admin" }
     * }
     */
    public function register(RegisterClinicRequest $request)
    {
        $result = $this->authService->registerTenant($request->validated());

        return response()->json([
            'message' => 'Cadastro realizado com sucesso! Verifique seu e-mail.',
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    /**
     * Logar no Sistema
     *
     * Loga o Usuário com email e senha
     *
     * @group Autenticação
     *
     * @response 200 {
     * "token": "1|aBcDeFg...",
     * "user": { "id": 1, "name": "Dr. João", "role": "admin" }
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $user = Auth::user();

        $user->load('clinic');

        if (! $user->clinic || ! $user->clinic->is_active) {
            throw ValidationException::withMessages([
                'email' => ['O acesso desta clínica está suspenso. Entre em contato com o suporte.'],
            ]);
        }

        $abilities = match ($user->role) {
            UserRole::ADMIN => ['*'],
            UserRole::VET => ['pacientes:create', 'pacientes:read', 'prontuarios:write'],
            UserRole::RECEPCIONISTA => ['agenda:read', 'agenda:write', 'clientes:create'],
            default => ['server:read']
        };

        $token = $user->createToken('login_token', $abilities)->plainTextToken;

        return response()->json([
            'user' => $user->load('clinic'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * Verifica o e-mail do usuário (Link clicado no e-mail)
     */
    public function verifyEmail(ApiEmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect('http://localhost:4200/login?verified=true');
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'E-mail já verificado.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link de verificação reenviado!']);
    }

    public function destroy(Clinic $clinic)
    {
        if (! request()->user()->tokenCan('*')) {// somente admin exclui a clinica
            abort(403, 'Seu token não tem permissão para deletar clínicas.');
        }

        $clinic->delete();

        return response()->json(['message' => 'Clínica Deletada']);
    }
}
