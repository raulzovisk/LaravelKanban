<?php
// app/Http/Controllers/TagController.php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Lista todas as tags
     */
    public function index()
    {
        $tags = Tag::withCount('tasks')->get();

        return response()->json([
            'success' => true,
            'tags' => $tags
        ]);
    }

    /**
     * Cria nova tag
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
            'color' => 'nullable|string|max:7',
        ]);

        $tag = Tag::create([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#10B981',
        ]);

        return response()->json([
            'success' => true,
            'tag' => $tag,
            'message' => 'Tag criada com sucesso!'
        ]);
    }

    /**
     * Atualiza tag
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
            'color' => 'nullable|string|max:7',
        ]);

        $tag->update($validated);

        return response()->json([
            'success' => true,
            'tag' => $tag,
            'message' => 'Tag atualizada com sucesso!'
        ]);
    }

    /**
     * Remove tag
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tag deletada com sucesso!'
        ]);
    }
}
