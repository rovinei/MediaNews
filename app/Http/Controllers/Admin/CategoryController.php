<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Category;
use App\Models\CategoryType;
use App\Http\Requests\UpdateCategory;
use Validator;
use Auth;
use Session;
use Exception;

class CategoryController extends Controller
{
    // Display list of categories
    public function index(Request $request){

        // Query all categories
        $categories = Category::whereNull('deleted_at')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.category.category')->with(['categories' => $categories]);
    }

    // Creation category form
    public function create(Request $request){

        return view('admin.category.create_category');
    }

    // Store new category
    public function store(Request $request){

        // Validate form data
        $this->validate($request,[
            'name' => 'required|min:5|unique:categories',
            'mediatype_id' => 'required'
        ]);

        // Create and Store new category
        try{
            $category = new Category($request->all());
            $category->createdBy()->associate(Auth::user());
            $category->save();
            $category->mediaTypes()->attach(explode(',', $request->mediatype_id));
            $category->save();
            Session::flash('success_message', 'Category '.$category->name.' successfully created.');
            return redirect()->back();

        }catch(Exception $e){
            Session::flash('error_message', 'Oop! Something went wrong, Please try again!');
            return redirect()->back();
        }
    }

    // Edit category
    public function edit(Request $request, $category_id){

        // Determine if category existed
        try{
            $category = Category::findOrFail($category_id);
            $mediatypes = CategoryType::where('category_id', $category_id)->pluck('mediatype_id')->toArray();
            return view('admin.category.update_category')->with([
                'category' => $category,
                'mediatypes' => $mediatypes
            ]);
        }catch(ModelNotFoundException $e){
            Session::flash('error_message', 'Query return 0 result, Category cannot be found with this id : '.$category_id);
            return redirect()->back();
        }
    }

    // Update category
    public function update(UpdateCategory $request, $category_id){

        // Determine if category exists
        try{
            $category = Category::findOrFail($category_id);
            $category->updatedBy()->associate(Auth::user());
            $category->mediaTypes()->sync(explode(',', $request->mediatype_id));
            $category->update($request->all());
        }catch(ModelNotFoundException $e){
            Session::flash('error_message', 'Cannot find category');
            return redirect()->back();
        }
        catch(Exception $e){
            Session::flash('error_message', 'Error while updating category, Please try again!');
            return redirect()->back();
        }

        Session::flash('success_message', 'Successfully updated category');
        return redirect()->back();

    }
}
