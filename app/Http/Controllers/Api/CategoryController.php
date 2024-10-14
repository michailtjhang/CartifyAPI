<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return response()->json([$categories], 200);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (is_null($category)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json([$category], 200);
    }

    public function store(Request $request)
    {
        try {
            $validate = $request->validate([
                'name' => 'required|unique:brands|max:255',
                'image' => 'required',
            ]);

            $file = $request->file('image');
            $fileName = uniqid() . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/categories'), $fileName);

            $category = new Category();
            $category->name = $request->name;
            $category->image = $fileName;
            $category->save();

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
                'image' => 'required',
            ]);

            $category = Category::find($id);
            if (is_null($category)) {
                return response()->json(['message' => 'Record not found'], 404);
            }

            if ($request->hasFile('image')) {
                $path = 'assets/images/categories/' . $category->image;
                if (file_exists($path)) {
                    unlink($path);
                }

                $file = $request->file('image');
                $fileName = uniqid() . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/categories'), $fileName);
            }

            $category->image = $fileName;
            $category->name = $request->name;
            $category->save();

            return response()->json(['message' => 'Record successfully updated'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::find($id);
            if (is_null($category)) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            $category->delete();
            return response()->json(['message' => 'Record successfully deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
