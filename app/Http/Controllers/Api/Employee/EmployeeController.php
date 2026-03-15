<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Requests\Employee\DestroyEmployeeRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * @group Colaboradores
 *
 * APIs para gerenciamento de colaboradores (funcionários) da clínica.
 * Apenas usuários com perfil de `admin` podem acessar estes endpoints.
 */
class EmployeeController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Listar Colaboradores
     *
     * Retorna uma lista paginada de todos os colaboradores ativos e inativos da clínica do administrador autenticado.
     *
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        $employees = User::withTrashed()
            ->where('clinic_id', $admin->clinic_id)
            ->whereKeyNot($admin->id)
            ->paginate(15);

        return response()->json($employees);
    }

    /**
     * Criar Colaborador
     *
     * Cria um novo colaborador (usuário) para a clínica.
     * Um e-mail de verificação será enviado para o novo colaborador para que ele possa ativar sua conta.
     *
     * @authenticated
     *
     * @bodyParam name string required Nome do colaborador. Example: "Dr. House"
     * @bodyParam email string required E-mail do colaborador. Deve ser único. Example: "house@example.com"
     * @bodyParam password string required Senha do colaborador. Mínimo 8 caracteres. Example: "password123"
     * @bodyParam role string required Cargo do colaborador. Valores permitidos: 'vet', 'recepcionista'. Example: "vet"
     *
     * @response 201 {
     *     "id": 2,
     *     "name": "Dr. House",
     *     "email": "house@example.com",
     *     "role": "vet"
     * }
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $admin = $request->user();
        $employee = $this->authService->createEmployee($validatedData, $admin);

        return response()->json($employee, 201);
    }

    /**
     * Atualizar Colaborador
     *
     * Atualiza os dados de um colaborador existente.
     * O administrador só pode atualizar colaboradores da sua própria clínica.
     *
     * @authenticated
     * @urlParam user int required O ID do colaborador a ser atualizado. Example: 2
     *
     * @bodyParam name string Nome do colaborador. Example: "Dr. Gregory House"
     * @bodyParam email string E-mail do colaborador. Deve ser único. Example: "g.house@example.com"
     * @bodyParam role string Cargo do colaborador. Valores permitidos: 'vet', 'recepcionista'. Example: "vet"
     * @bodyParam password string (Opcional) Nova senha para o colaborador. Mínimo 8 caracteres. Example: "newSecurePassword"
     *
     * @response 200 {
     *     "id": 2,
     *     "name": "Dr. Gregory House",
     *     "email": "g.house@example.com",
     *     "role": "vet"
     * }
     */
    public function update(UpdateEmployeeRequest $request, User $employee): JsonResponse
    {
        $validatedData = $request->validated();

        $employee->update($validatedData);

        return response()->json($employee->fresh());
    }


    /**
     * Inativar Colaborador
     *
     * Inativa um funcionário e revoga seus acessos imediatamente.
     *
     * @authenticated
     */
    public function destroy(DestroyEmployeeRequest $request, User $employee): JsonResponse
    {
        $employee->update(['is_active' => false]);
        $employee->delete();

        $employee->tokens()->delete();

        return response()->json(['message' => 'Colaborador inativado e desconectado com sucesso.']);
    }

    /**
     * Reativar Colaborador
     *
     * @authenticated
     */
    public function restore(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();

        $employee = User::withTrashed()
            ->where('clinic_id', $admin->clinic_id)
            ->findOrFail($id);

        $employee->restore();

        $employee->update(['is_active' => true]);

        return response()->json([
            'message' => 'Colaborador reativado com sucesso.',
            'employee' => $employee->fresh()
        ]);
    }
}
