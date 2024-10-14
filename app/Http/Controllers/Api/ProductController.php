<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Products::paginate(10);
            return response()->json([$products], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:products|max:255',
                'image' => 'required',
                'price' => 'required|numeric',
                'category_id' => 'required|numeric',
                'brand_id' => 'required|numeric',
                'discount' => 'required|numeric',
                'amount' => 'required|numeric',
            ]);

            $product = new Products();
            if ($request->hasFile('image')) {
                $fileName = uniqid() . $request->image->getoriginalExtension();
                $request->image->move(public_path('assets/images/products'), $fileName);
            }

            $product->name = $request->name;
            $product->image = $fileName;
            $product->price = $request->price;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->discount = $request->discount;
            $product->amount = $request->amount;
            $product->save();

            return response()->json(['message' => 'Record successfully created'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Products::find($id);
        if (is_null($product)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json([$product], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validate = $request->validate([
                'name' => 'required|unique:products|max:255',
                'image' => 'required',
                'price' => 'required|numeric',
                'category_id' => 'required|numeric',
                'brand_id' => 'required|numeric',
                'discount' => 'required|numeric',
                'amount' => 'required|numeric',
            ]);

            $product = Products::find($id);
            if (is_null($product)) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            if ($request->hasFile('image')) {
                $path ='assets/images/products/'. $product->image;
                if (file_exists($path)) {
                    unlink($path);
                }

                $fileName = uniqid() . $request->image->getoriginalExtension();
                $request->image->move(public_path('assets/images/products'), $fileName);
            }

            $product->name = $request->name;
            $product->image = $fileName;
            $product->price = $request->price;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->discount = $request->discount;
            $product->amount = $request->amount;
            $product->save();

            return response()->json(['message' => 'Record successfully updated'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Products::find($id);
            if (is_null($product)) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            $product->delete();
            return response()->json(['message' => 'Record successfully deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
