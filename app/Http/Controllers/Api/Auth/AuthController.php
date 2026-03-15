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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * @group Autenticação
 *
 * APIs para gerenciamento de registro, login e sessões de usuários.
 */
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
     * Cria uma nova clínica, registra um usuário administrador vinculado a ela e dispara o e-mail de verificação.
     *
     * @unauthenticated
     *
     * @response 201 {
     * "message": "Cadastro realizado com sucesso! Verifique seu e-mail.",
     * "user": {
     * "id": 1,
     * "name": "Dr. João Silva",
     * "email": "joao@clinica.com",
     * "role": "admin",
     * "created_at": "2024-02-14T10:00:00.000000Z"
     * },
     * "token": "1|aBcDeFgHijK..."
     * }
     * @response 422 {
     * "message": "O valor do campo e-mail já está em uso.",
     * "errors": {
     * "email": ["O valor do campo e-mail já está em uso."]
     * }
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
     * Login no Sistema
     *
     * Autentica o usuário utilizando e-mail e senha. Retorna um token Sanctum com "abilities" baseadas no cargo (role) do usuário.
     * Também verifica se a clínica do usuário está ativa.
     *
     * @unauthenticated
     *
     * @bodyParam email string required O e-mail do usuário. Example: admin@clinica.com
     * @bodyParam password string required A senha do usuário. Example: senha123
     *
     * @response 200 {
     * "user": {
     * "id": 1,
     * "name": "Dr. João",
     * "role": "vet",
     * "clinic": {
     * "id": 1,
     * "name": "Vet Clinic",
     * "is_active": true
     * }
     * },
     * "token": "2|LikM..."
     * }
     *
     * @response 422 {
     * "message": "As credenciais fornecidas estão incorretas.",
     * "errors": {
     * "email": ["As credenciais fornecidas estão incorretas."]
     * }
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

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

    /**
     * Logout
     *
     * Revoga o token de acesso atual do usuário, encerrando a sessão.
     *
     * @authenticated
     *
     * @response 200 {
     * "message": "Logout realizado com sucesso."
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * Verificar E-mail
     *
     * Valida o link de verificação enviado por e-mail. Se for válido, redireciona o usuário para o sistema.
     *
     * @urlParam id int required O ID do usuário.
     * @urlParam hash string required O hash de verificação.
     *
     * @response 302 Redireciona para http://localhost:4200/login?verified=true
     */
    public function verifyEmail(ApiEmailVerificationRequest $request)
    {
        $request->fulfill();
        $url = env('FRONTEND_URL');
        return redirect("$url/login?verified=true");
    }

    /**
     * Reenviar E-mail de Verificação
     *
     * Envia um novo link de verificação para o e-mail do usuário autenticado, caso ainda não tenha sido verificado.
     *
     * @authenticated
     *
     * @response 200 {
     * "message": "Link de verificação reenviado!"
     * }
     * @response 400 {
     * "message": "E-mail já verificado."
     * }
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'E-mail já verificado.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link de verificação reenviado!']);
    }

    /**
     * Excluir Clínica (Admin)
     *
     * Inativa uma clínica no sistema em vez de excluí-la.
     *
     * @authenticated
     *
     * @urlParam clinic int required O ID da clínica a ser deletada.
     *
     * @response 200 {
     * "message": "Clínica inativada com sucesso."
     * }
     * @response 403 {
     * "message": "Você não tem permissão para deletar clínicas."
     * }
     */
    public function destroy(Clinic $clinic): JsonResponse
    {   
        $clinic->update(['is_active' => false]);
        $clinic->delete();


        $clinic->users()->each(function ($user) {
            $user->tokens()->delete();
        });

        return response()->json(['message' => 'Clínica inativada e usuários desconectados com sucesso.']);
    }

    /**
     * Solicitar Link de Redefinição de Senha
     *
     * Envia um link para o e-mail do usuário para que ele possa criar uma nova senha.
     *
     * @unauthenticated
     *
     * @bodyParam email string required O e-mail do usuário. Example: admin@clinica.com
     *
     * @response 200 {
     *   "message": "Enviamos por e-mail o link para redefinição da sua senha."
     * }
     * @response 422 {
     *   "message": "Não encontramos um usuário com esse endereço de e-mail."
     * }
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        // Envia o link de reset usando a infraestrutura do Laravel
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        // Se o e-mail não for encontrado, lança uma exceção de validação
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Redefinir Senha
     *
     * Cria uma nova senha para o usuário a partir do token de redefinição.
     *
     * @unauthenticated
     *
     * @bodyParam token string required O token recebido por e-mail.
     * @bodyParam email string required O e-mail do usuário.
     * @bodyParam password string required A nova senha. Mínimo 8 caracteres.
     * @bodyParam password_confirmation string required A confirmação da nova senha.
     *
     * @response 200 {
     *   "message": "Sua senha foi redefinida com sucesso."
     * }
     * @response 422 {
     *   "message": "Este token de redefinição de senha é inválido."
     * }
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Tenta resetar a senha
        $status = Password::reset($request->all(), function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 422);
    }
}