<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brands::paginate(10);
        return response()->json([$brands], 200);
    }

    public function show($id)
    {
        $brand = Brands::find($id);
        if (is_null($brand)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json([$brand], 200);
    }

    public function store(Request $request)
    {
        try {
            $validate = $request->validate([
                'name' => 'required|unique:brands|max:255',
            ]);

            $brand = new Brands();
            $brand->name = $request->name;
            $brand->save();

            return response()->json(['message' => 'Record successfully created'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validate = $request->validate([
                'name' => 'required|unique:brands|max:255',
            ]);

            $brand = Brands::find($id);
            if (is_null($brand)) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            $brand->name = $request->name;
            $brand->save();

            return response()->json(['message' => 'Record successfully updated'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brands::find($id);
            if (is_null($brand)) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            $brand->delete();
            return response()->json(['message' => 'Record successfully deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
