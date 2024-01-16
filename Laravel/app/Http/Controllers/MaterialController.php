<?php

namespace App\Http\Controllers;

use App\Course;
use App\Material;
use App\Role;
use App\Size;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::paginate(5);
        return view('materials.index', compact('materials'));
    }

    public function create()
    {

        $sizes = Size::all();
        $courses = Course::all();


        return view('materials.create', compact('sizes',  'courses'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'supplier' => 'nullable|string|max:255',
                'acquisition_date' => 'nullable|date',
                'isInternal' => 'required|boolean',
                'isClothing' => 'required|boolean',
                'gender' => 'nullable|boolean',
                'quantity' => 'nullable|integer|min:0',
                'sizes' => ['nullable', 'array'],
                'sizes.*' => ['nullable', 'string', 'max:10'],
                'stocks' => ['nullable', 'array'],
                'stock.*' => ['nullable', 'integer', 'min:0'],
            ]);
            $material = Material::create($request->all());

            $material->courses()->attach($request->input('courses'));

            $stocks = $request->input('stocks');
            $sizes = $request->input('sizes');
dd($stocks);

            $material->sizes()->atach($sizes, ['stock' => $stocks]);
            dd($stocks);




            return redirect()->route('materials.show', $material->id)->with('success', 'Material inserido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erro ao inserir o material. Por favor, tente novamente.');
        }
    }




    public function show(Material $material)
    {
        $sizes = $material->sizes;
        $courses = $material->courses;
//        dd($sizes->get());
        return view('materials.show', compact('material' , 'sizes', 'courses'));
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material', ));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'supplier' => 'nullable|string|max:255',
            'aquisition_date' => 'nullable|date',
            'isInternal' => 'required|boolean',
            'isClothing' => 'required|boolean',
            'gender' => 'nullable|boolean',
            'quantity' => 'nullable|integer|min:0',
            'size' => 'nullable|string|max:10',
            'role' => 'nullable|string|max:255',
        ]);

        if ($request->input('isClothing') == 0) {
            $request->merge([
                'gender' => null,
                'size' => null,
                'role' => null,
            ]);
        }

        $material->update($request->all());

        return redirect()->route('materials.index')->with('success', 'Material atualizado com sucesso!');
    }

    public function destroy(Material $material)
    {
        try {
            $material->delete();
            return redirect()->route('materials.index')->with('success', 'Material excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('materials.index')->with('error', 'Erro ao excluir o material. Por favor, tente novamente.');
        }
    }

    public function massDelete(Request $request)
    {
        $request->validate([
            'material_ids' => 'required|array',
            'material_ids.*' => 'exists:materials,id',//all items inside array must exist
        ]);

        try {

            Material::whereIn('id', $request->input('material_ids'))->delete();
            return redirect()->back()->with('success', 'Materiais selecionados excluídos com sucesso!');
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Erro ao excluir os materiais selecionados. Por favor, tente novamente.');
            return redirect()->route('materials.index')->with('error', 'Erro ao excluir o material. Por favor, tente novamente.');
        }
    }


}
