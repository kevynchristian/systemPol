<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comunicado;
use Illuminate\Http\Request;

class ComunicadoController extends Controller
{
    public function index()
    {
        $comunicados = Comunicado::with('author')->latest()->paginate(15);
        return view('pages.admin.comunicados.index', compact('comunicados'));
    }

    public function create()
    {
        return view('pages.admin.comunicados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:info,alerta,aula',
            'image_url' => 'nullable|url',
        ]);

        $data = $request->all();
        $data['author_id'] = auth()->id();
        $data['active'] = $request->has('active') ? true : false;

        Comunicado::create($data);

        return redirect()->route('admin.comunicados.index')->with('success', 'Comunicado divulgado com sucesso!');
    }

    public function edit(string $id)
    {
        $comunicado = Comunicado::findOrFail($id);
        return view('pages.admin.comunicados.edit', compact('comunicado'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:info,alerta,aula',
            'image_url' => 'nullable|url',
        ]);

        $comunicado = Comunicado::findOrFail($id);
        
        $data = $request->all();
        $data['active'] = $request->has('active') ? true : false;
        
        $comunicado->update($data);

        return redirect()->route('admin.comunicados.index')->with('success', 'Comunicado atualizado!');
    }

    public function destroy(string $id)
    {
        $comunicado = Comunicado::findOrFail($id);
        $comunicado->delete();
        
        return redirect()->route('admin.comunicados.index')->with('success', 'Comunicado removido permanentemente.');
    }
}
