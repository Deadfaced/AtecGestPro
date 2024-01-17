<?php

namespace App\Http\Controllers;

use App\Course;
use App\Material;
use App\MaterialSize;

use App\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('sizes','courses')->paginate(5);
        return view('materials.index', compact('materials'));
    }

    public function create()
    {

        $sizes = Size::all();
        $courses = Course::all();


        return view('materials.create', compact('sizes', 'courses'));
    }

    public function store(Request $request)
    {

        //DB::connection()->enableQueryLog();
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
                'stocks.*' => ['nullable', 'integer', 'min:0'],
            ]);

            $quantity = $request->input('quantity');
            if ($request->input('isClothing')) {
                $quantity = 0;
            }

            $request->merge(['quantity' => $quantity]);


            $material = Material::create($request->all());


            $material->courses()->attach($request->input('courses'));

            $stocks = $request->input('stocks');
            $sizes = $request->input('sizes');

            $material->sizes()->attach($sizes, ['stock' => $stocks]);
            dd($stocks);


                $material->sizes()->attach($sizeId, ['stock' => $stock]);


            }


            //  dd(DB::getQueryLog());


            return redirect()->route('materials.show', $material->id)->with('success', 'Material inserido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erro ao inserir o material. Por favor, tente novamente.');
        }
    }


    public function show(Material $material)
    {
        $sizesAll = Size::all();
        $coursesAll = Course::all();
        $sizes = $material->sizes;
        $courses = $material->courses;
//        dd($sizes->get());
        return view('materials.show', compact('material' , 'sizes', 'courses'));
    }

    public function edit(Material $material)
    {

        $sizesAll = Size::all();
        $coursesAll = Course::all();
        $sizes = $material->sizes;
        $courses = $material->courses;

        return view('materials.edit', compact('material' , 'sizes', 'courses' , 'sizesAll' , 'coursesAll'));
    }

    public function update(Request $request, Material $material)
    {
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
            'stocks.*' => ['nullable', 'integer', 'min:0'],
            'courses' => ['nullable', 'array'],
            'courses.*' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($request->input('isClothing') == 0) {
            $request->merge([
                'gender' => null,
                'sizes' => [],
                'stocks' => [],
                'courses' => [],
            ]);
        }
        $material->update($request->all());

        // Update the related courses
        $material->courses()->sync($request->input('courses', []));

        // Update the related sizes and stocks
        $sizes = $request->input('sizes', []);
        $stocks = $request->input('stocks', []);
        $syncData = [];
        foreach ($sizes as $index => $sizeId) {
            $stock = $stocks[$index] ?? 0;
            $syncData[$sizeId] = ['stock' => $stock];
        }

        $material->sizes()->sync($syncData);

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
