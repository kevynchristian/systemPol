<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aulas = Aula::latest()->paginate(10);
        return view('pages.admin.aulas.index', compact('aulas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patentes = \Spatie\Permission\Models\Role::whereNotNull('hierarquia')->where('hierarquia', '>=', 0)->orderBy('hierarquia', 'asc')->get();
        $funcoes = \Spatie\Permission\Models\Role::whereNull('hierarquia')->orWhere('hierarquia', 0)->orderBy('name', 'asc')->get();
        $aulas = Aula::all();
        return view('pages.admin.aulas.create', compact('patentes', 'funcoes', 'aulas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:aulas,name',
            'required_permission' => 'nullable|string|max:255',
            'prerequisite_id' => 'nullable|exists:aulas,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $aula = Aula::create($request->all());

        if ($request->has('roles')) {
            $aula->roles()->sync($request->roles);
        }

        return redirect()->route('admin.aulas.index')->with('success', 'Formação cadastrada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aula $aula)
    {
        $patentes = \Spatie\Permission\Models\Role::whereNotNull('hierarquia')->where('hierarquia', '>=', 0)->orderBy('hierarquia', 'asc')->get();
        $funcoes = \Spatie\Permission\Models\Role::whereNull('hierarquia')->orWhere('hierarquia', 0)->orderBy('name', 'asc')->get();
        $aulas = Aula::where('id', '!=', $aula->id)->get(); // Não pode ser pré-requisito dela mesma
        return view('pages.admin.aulas.edit', compact('aula', 'patentes', 'funcoes', 'aulas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aula $aula)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:aulas,name,' . $aula->id,
            'required_permission' => 'nullable|string|max:255',
            'prerequisite_id' => 'nullable|exists:aulas,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $aula->update($request->all());

        if ($request->has('roles')) {
            $aula->roles()->sync($request->roles);
        } else {
            $aula->roles()->detach();
        }

        return redirect()->route('admin.aulas.index')->with('success', 'Formação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aula $aula)
    {
        $aula->delete();
        return redirect()->route('admin.aulas.index')->with('success', 'Formação excluída com sucesso!');
    }
}
