<?php

namespace App\Http\Controllers;

use App\Models\BoardMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BoardMessageController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
        // O problema pode estar aqui - vamos garantir que as permissões estejam corretas
        $this->middleware('permission:board.view')->only(['index', 'show']);
        $this->middleware('permission:board.create')->only(['create', 'store']);
        $this->middleware('permission:board.edit')->only(['edit', 'update']);
        $this->middleware('permission:board.delete')->only(['destroy']);
        $this->middleware('permission:board.pin')->only(['togglePin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = BoardMessage::with('user')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.board.index', compact('messages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.board.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'is_pinned' => 'boolean',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $attachmentPath = null;
        
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('board-attachments', 'public');
        }

        $message = BoardMessage::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'is_active' => $validated['is_active'] ?? true,
            'is_pinned' => $validated['is_pinned'] ?? false,
            'attachment' => $attachmentPath,
        ]);

        return redirect()
            ->route('admin.board.index')
            ->with('success', 'Recado criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BoardMessage $board)
    {
        return view('admin.board.show', compact('board'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BoardMessage $board)
    {
        return view('admin.board.edit', compact('board'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BoardMessage $board)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'is_pinned' => 'boolean',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Verificar se tem um novo anexo
        if ($request->hasFile('attachment')) {
            // Remover o anexo anterior se existir
            if ($board->attachment) {
                Storage::disk('public')->delete($board->attachment);
            }
            
            $attachmentPath = $request->file('attachment')->store('board-attachments', 'public');
            $board->attachment = $attachmentPath;
        }

        $board->title = $validated['title'];
        $board->content = $validated['content'];
        $board->is_active = $validated['is_active'] ?? false;
        $board->is_pinned = $validated['is_pinned'] ?? false;
        $board->save();

        return redirect()
            ->route('admin.board.show', $board)
            ->with('success', 'Recado atualizado com sucesso!');
    }

    /**
     * Toggle pin status of the message.
     */
    public function togglePin(BoardMessage $board)
    {
        if (!Auth::user()->hasPermission('board.pin')) {
            abort(403, 'Não autorizado a fixar recados.');
        }
        
        $board->is_pinned = !$board->is_pinned;
        $board->save();
        
        return redirect()
            ->back()
            ->with('success', 'Status de fixação alterado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoardMessage $board)
    {
        // Remover o anexo se existir
        if ($board->attachment) {
            Storage::disk('public')->delete($board->attachment);
        }
        
        $board->delete();
        
        return redirect()
            ->route('admin.board.index')
            ->with('success', 'Recado excluído com sucesso!');
    }
}