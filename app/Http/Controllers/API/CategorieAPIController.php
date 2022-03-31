<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategorieAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::where(function ($query) use ($request) {
            return $query->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('name', 'LIKE', $request->search . "%")
                    ->orWhere('code', 'LIKE', $request->search . "%");
            });
        })->paginate($request->limit ?? 10);
        return $this->sendResponse($categories, 'Categories retrieved successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $categorie = Category::create($input);
            DB::commit();
            return $this->sendResponse($categorie, 'Categorie saved successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (empty($category)) {
            return $this->sendError('Category not found', 404);
        }
        return $this->sendResponse($category, 'Category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $input = $request->all();
        $category = Category::find($id);
        if (empty($category)) {
            return $this->sendError('Category not found', 404);
        }

        $category->update($input);
        return $this->sendResponse($category, 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (empty($category)) {
            return $this->sendError('Categorie not found', 404);
        }
        $category->delete();
        return $this->sendResponse($category, 'Categorie deleted successfully');
    }
}
