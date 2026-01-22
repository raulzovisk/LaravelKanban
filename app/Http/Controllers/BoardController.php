<?php
// app/Http/Controllers/BoardController.php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Column;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Events\BoardUpdated;

class BoardController extends Controller
{
    /**
     * Lista todos os quadros
     */
    public function index()
    {
        $user = Auth::user();

        $ownedBoards = $user->ownedBoards()->withCount('tasks')->get();
        $sharedBoards = $user->sharedBoards()->withCount('tasks')->get();

        return view('boards.index', compact('ownedBoards', 'sharedBoards'));
    }

    /**
     * Exibe formulário de criação
     */
    public function create()
    {
        return view('boards.create');
    }

    /**
     * Armazena novo quadro
     */
    public function store(StoreBoardRequest $request)
    {
        $board = Auth::user()->ownedBoards()->create($request->validated());

        // Cria colunas padrão
        $defaultColumns = [
            ['name' => 'A Fazer', 'order' => 1, 'color' => '#EF4444'],
            ['name' => 'Em Progresso', 'order' => 2, 'color' => '#F59E0B'],
            ['name' => 'Concluído', 'order' => 3, 'color' => '#10B981'],
        ];

        foreach ($defaultColumns as $column) {
            $board->columns()->create($column);
        }

        // Registra atividade
        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Board::class,
            'subject_id' => $board->id,
            'type' => 'board.created',
            'properties' => ['board_name' => $board->name],
        ]);

        return redirect()->route('boards.show', $board)
            ->with('success', 'Quadro criado com sucesso!');
    }

    /**
     * Exibe quadro específico
     */
    public function show(Board $board)
    {
        // Verifica permissão
        if (!$board->hasAccess(Auth::user())) {
            abort(403, 'Você não tem acesso a este quadro.');
        }

        $board->load([
            'columns.tasks.tags',
            'columns.tasks.user',
            'users',
            'activities.user'
        ]);

        return view('boards.show', compact('board'));
    }

    /**
     * Exibe formulário de edição
     */
    public function edit(Board $board)
    {
        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para editar este quadro.');
        }

        return view('boards.edit', compact('board'));
    }

    /**
     * Atualiza quadro
     */
    public function update(UpdateBoardRequest $request, Board $board)
    {
        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para editar este quadro.');
        }

        $board->update($request->validated());

        // Registra atividade
        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Board::class,
            'subject_id' => $board->id,
            'type' => 'board.updated',
            'properties' => ['board_name' => $board->name],
        ]);

        // Broadcast para atualização em tempo real
        broadcast(new BoardUpdated($board))->toOthers();

        return redirect()->route('boards.show', $board)
            ->with('success', 'Quadro atualizado com sucesso!');
    }

    /**
     * Remove quadro
     */
    public function destroy(Board $board)
    {
        if (!$board->isAdmin(Auth::user())) {
            abort(403, 'Você não tem permissão para deletar este quadro.');
        }

        $board->delete();

        return redirect()->route('boards.index')
            ->with('success', 'Quadro deletado com sucesso!');
    }

    /**
     * Convida usuário para o quadro
     */
    public function inviteUser(Request $request, Board $board)
    {
        // Verifica se o usuário tem permissão de admin
        if (!$board->isAdmin(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para convidar usuários.'
            ], 403);
        }

        // Validação
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:viewer,editor,admin',
        ], [
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.exists' => 'Este email não está cadastrado no sistema.',
            'role.required' => 'A permissão é obrigatória.',
            'role.in' => 'Permissão inválida.',
        ]);

        try {
            // Busca o usuário pelo email
            $user = User::where('email', $validated['email'])->first();

            // Verifica se é o próprio dono
            if ($user->id === $board->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode convidar a si mesmo. Você já é o dono do quadro.'
                ], 422);
            }

            // Verifica se já tem acesso
            if ($board->users()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este usuário já tem acesso a este quadro.'
                ], 422);
            }

            // Adiciona o usuário ao quadro
            $board->users()->attach($user->id, [
                'role' => $validated['role'],
                'invited_at' => now(),
            ]);

            // Registra atividade
            Activity::create([
                'board_id' => $board->id,
                'user_id' => Auth::id(),
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'type' => 'user.invited',
                'properties' => [
                    'invited_user' => $user->name,
                    'invited_email' => $user->email,
                    'role' => $validated['role'],
                ],
            ]);

            // Envia notificação
            try {
                $user->notify(new \App\Notifications\BoardInvitation($board, Auth::user()));
            } catch (\Exception $e) {
                // Se falhar ao enviar notificação, continua mesmo assim
                \Log::warning('Falha ao enviar notificação de convite: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Usuário convidado com sucesso!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $validated['role'],
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao convidar usuário: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao convidar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove acesso de usuário
     */
    public function updateUserRole(Request $request, Board $board, $userId)
    {
        if (!$board->isAdmin(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para alterar permissões.'
            ], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:viewer,editor,admin',
        ]);

        try {
            $user = User::findOrFail($userId);

            // Não pode alterar o dono
            if ($user->id === $board->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode alterar a permissão do dono do quadro.'
                ], 422);
            }

            // Verifica se o usuário tem acesso
            if (!$board->users()->where('user_id', $userId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este usuário não tem acesso ao quadro.'
                ], 422);
            }

            // Atualiza a permissão
            $oldRole = $board->users()->where('user_id', $userId)->first()->pivot->role;
            $board->users()->updateExistingPivot($userId, [
                'role' => $validated['role']
            ]);

            // Registra atividade
            Activity::create([
                'board_id' => $board->id,
                'user_id' => Auth::id(),
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'type' => 'user.role_updated',
                'properties' => [
                    'user_name' => $user->name,
                    'old_role' => $oldRole,
                    'new_role' => $validated['role'],
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permissão atualizada com sucesso!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $validated['role'],
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar permissão: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar permissão.'
            ], 500);
        }
    }

    /**
     * Remove acesso de usuário
     */
    public function removeUser(Board $board, $userId)
    {
        if (!$board->isAdmin(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para remover usuários.'
            ], 403);
        }

        try {
            $user = User::findOrFail($userId);

            // Não pode remover o dono
            if ($user->id === $board->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode remover o dono do quadro.'
                ], 422);
            }

            // Verifica se o usuário tem acesso
            if (!$board->users()->where('user_id', $userId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este usuário não tem acesso ao quadro.'
                ], 422);
            }

            $board->users()->detach($userId);

            // Registra atividade
            Activity::create([
                'board_id' => $board->id,
                'user_id' => Auth::id(),
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'type' => 'user.removed',
                'properties' => [
                    'removed_user' => $user->name,
                    'removed_email' => $user->email,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Acesso removido com sucesso!'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao remover usuário: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover acesso do usuário.'
            ], 500);
        }
    }

    /**
     * Lista membros do quadro
     */
    public function getMembers(Board $board)
    {
        if (!$board->hasAccess(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem acesso a este quadro.'
            ], 403);
        }

        try {
            // Dono do quadro
            $owner = [
                'id' => $board->owner->id,
                'name' => $board->owner->name,
                'email' => $board->owner->email,
                'avatar' => $board->owner->avatar,
                'role' => 'owner',
                'is_owner' => true,
                'invited_at' => $board->created_at->toIso8601String(),
            ];

            // Membros convidados
            $members = $board->users()->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'role' => $user->pivot->role,
                    'is_owner' => false,
                    'invited_at' => $user->pivot->invited_at,
                ];
            });

            // Combina todos
            $allMembers = collect([$owner])->merge($members);

            return response()->json([
                'success' => true,
                'members' => $allMembers,
                'total' => $allMembers->count(),
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar membros: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar membros.'
            ], 500);
        }
    }

    /**
     * Busca usuários para convidar
     */
    public function searchUsers(Request $request, Board $board)
    {
        if (!$board->isAdmin(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão.'
            ], 403);
        }

        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'users' => []
            ]);
        }

        try {
            // Busca usuários que NÃO são o dono e NÃO são membros atuais
            $existingUserIds = $board->users()->pluck('user_id')->toArray();
            $existingUserIds[] = $board->user_id; // Adiciona o dono

            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
                ->whereNotIn('id', $existingUserIds)
                ->limit(10)
                ->get(['id', 'name', 'email', 'avatar']);

            return response()->json([
                'success' => true,
                'users' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar usuários: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuários.'
            ], 500);
        }
    }
}
