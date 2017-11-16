<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use App\Models\PlaylistSerie;
use App\Models\Post;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\FileEntry;
use App\Models\User;
use App\Models\Admin;
use App\Models\Department;
use App\Models\Partner;
use Conner\Tagging\Model\Tag;
use Conner\Tagging\Model\Tagged;
use Auth;
use Session;
use Exception;

class AdminAjaxController extends Controller
{
    // Get categories by media type
    public function getTypeCategories(Request $request){
        if($request->ajax()){
            if($request->has('typeid')){
                $mediaid = $request->input('typeid');
                try {
                    $type = CategoryType::findOrFail($mediaid);
                } catch (ModelNotFoundException $e) {
                    return response()->json([
                        "status" => 404,
                        "error" => [
                            "code" => 202,
                            "message" => "No Query result for media"
                        ]
                    ]);
                }

                // Query categories by type
                $categories = $type->categories;

                // Query all series related to articale type #1
                $series = PlaylistSerie::where('mediatype_id','=', $mediaid)->get();

                return response()->json([
                        'categories' => $categories,
                        'series' => $series
                    ]);
            }
            return response()->json([
                "status" => 202,
                "error" => [
                    "code" => 202,
                    "message" => "Invalid request data"
                ]
            ]);
        }
        return redirect()->back();
    }

    // Add serie
    public function addSerie(Request $request){
        if($request->ajax()){
            if($request->has('title') & $request->has('type')){
                $validType = array(1 ,2 ,3);
                $title = $request->input('title');
                $type = $request->input('type');
                if(!in_array($type, $validType)){
                    return response()->json([
                        "status" => 202,
                        "error" => [
                            "code" => 202,
                            "message" => "Invalid request data, media type must 1, 2 or 3"
                        ]
                    ]);
                }

                try {
                    $serie = new PlaylistSerie();
                    $serie->title = $title;
                    $serie->mediatype_id = $type;
                    $serie->createdBy()->associate(Auth::user());
                    $serie->save();
                    $data = $serie->toArray();
                } catch (Exception $e) {
                    return response()->json([
                        "status" => 500,
                        "error" => [
                            "code" => 500,
                            "message" => "Error while trying to insert into database"
                        ]
                    ]);
                }

                return response()->json([
                    "status" => 200,
                    "data" => $data,
                    "success" => [
                        "code" => 200,
                        "message" => "Successfully Created Serie with title : ".$title
                    ]
                ]);
            }

            return response()->json([
                "status" => 202,
                "error" => [
                    "code" => 202,
                    "message" => "Invalid request data"
                ]
            ]);
        }

        return redirect()->back();
    }

    // Remove post by id
    public function removePost(Request $request){
        if($request->ajax()){

            // Determine if post exist
            if($request->has('id')){
                $postId = $request->input('id');
                try{
                    $post = Post::findOrFail($postId);
                }catch(ModelNotFoundException $e){
                    return response()->json([
                        "status" => 202,
                        "error" => [
                            "code" => 202,
                            "message" => "No Query result found for post ID : ".$postId
                        ]
                    ]);
                }

                // Try to delete post
                try{
                    $post->delete();
                }catch(Exception $e){
                    return response()->json([
                        "status" => 505,
                        "error" => [
                            "code" => 505,
                            "message" => "Error while trying to delete post"
                        ]
                    ]);
                }

                // Response with successful message
                return response()->json([
                    "status" => 200,
                    "success" => [
                        "code" => 200,
                        "message" => "Successfully deleted post with ID : ".$postId
                    ]
                ]);
            }

            // Request must specify post id
            return response()->json([
                "status" => 202,
                "error" => [
                    "code" => 202,
                    "message" => "Invalid request data"
                ]
            ]);
        }

        // Return if rquest not an AJAX
        return redirect()->back();
    }

    // Remove Partner
    public function removePartner(Request $request){
        if($request->ajax()){

            // Determine if post exist
            if($request->has('id')){
                $partnerId = $request->input('id');
                try{
                    $partner = Partner::findOrFail($partnerId);
                }catch(ModelNotFoundException $e){
                    return response()->json([
                        "status" => 202,
                        "error" => [
                            "code" => 202,
                            "message" => "No Query result found for partner ID : ".$partnerId
                        ]
                    ]);
                }

                // Try to delete post
                try{
                    $partner->delete();
                }catch(Exception $e){
                    return response()->json([
                        "status" => 505,
                        "error" => [
                            "code" => 505,
                            "message" => "Error while trying to delete partner"
                        ]
                    ]);
                }

                // Response with successful message
                return response()->json([
                    "status" => 200,
                    "success" => [
                        "code" => 200,
                        "message" => "Successfully deleted partner with ID : ".$partnerId
                    ]
                ]);
            }

            // Request must specify post id
            return response()->json([
                "status" => 202,
                "error" => [
                    "code" => 202,
                    "message" => "Invalid request data"
                ]
            ]);
        }

        // Return if rquest not an AJAX
        return redirect()->back();
    }


    // Remove category
    public function removeCategory(Request $request){
        if($request->ajax()){
            // Determine if category exist
            if($request->has('id')){
                $categoryId = $request->input('id');
                try{
                    $category = Category::findOrFail($categoryId);
                }catch(ModelNotFoundException $e){
                    return response()->json([
                        "status" => 202,
                        "error" => [
                            "code" => 202,
                            "message" => "No Query result found for category ID : ".$categoryId
                        ]
                    ]);
                }

                // Try to delete post
                try{
                    $category->delete();
                }catch(Exception $e){
                    return response()->json([
                        "status" => 505,
                        "error" => [
                            "code" => 505,
                            "message" => $e->getMessage()
                        ]
                    ]);
                }

                // Response with successful message
                return response()->json([
                    "status" => 200,
                    "success" => [
                        "code" => 200,
                        "message" => "Successfully deleted category with ID : ".$categoryId
                    ]
                ]);
            }

            // Request must specify post id
            return response()->json([
                "status" => 202,
                "error" => [
                    "code" => 202,
                    "message" => "Invalid request data"
                ]
            ]);
        }

        return redirect()->back();
    }

    // Remove tag
    public function removeTag(Request $request){

        if($request->ajax()){

            // Check if request had slug field
            if($request->has('slug')){

                try {
                    Tag::where('slug', $request->slug)->firstOrFail()->delete();
                    Tagged::where('tag_slug', $request->slug)->delete();
                } catch (ModelNotFoundException $e) {
                    // No tag found
                    return response()->json([
                        "status" => 404,
                        "error" => [
                            "code" => 404,
                            "message" => "No tag found!"
                        ]
                    ]);
                }

                // Success deleted
                return response()->json([
                    "status" => 200,
                    "success" => [
                        "code" => 200,
                        "message" => "Successfully deleted tag and detached from posts"
                    ]
                ]);
            }

            // Request must specify tag slug field
            return response()->json([
                "status" => 202,
                "error" => [
                    "code" => 202,
                    "message" => "Invalid request data, slug field not found!"
                ]
            ]);
        }
    }

    // Load post {id and title}
    public function loadPostsTitle(Request $request){

        // Allow only ajax request
        if($request->ajax()){
            $query = $request->input('query');
            $tag = $request->input('tag');
            try {
                $posts = Post::withoutTags([$tag])
                        ->where('title','like','%'.$query.'%')
                        ->orderBy('created_at', 'desc')
                        ->take(30)
                        ->pluck('title','id');
            } catch (Exception $e) {
                return response()->json([
                    "status" => 500,
                    "error" => [
                        "code" => 500,
                        "message" => "Oop! Something went wrong!"
                    ]
                ]);
            }

            // Success retrived posts
            return response()->json([
                "status" => 200,
                "data" => $posts,
                "success" => [
                    "code" => 200,
                    "message" => "Successfully query posts"
                ]
            ]);
        }

        return redirect()->back();
    }

    public function removeSerie(Request $request){
        // Allow only ajax request
        if($request->ajax()){

            $serie_id = $request->input('id');
            try {
                $serie = PlaylistSerie::findOrFail($serie_id);
                $serie->delete();
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    "status" => 500,
                    "error" => [
                        "code" => 500,
                        "message" => "Oop! there is no record found!"
                    ]
                ]);
            }

            // Success retrived posts
            return response()->json([
                "status" => 200,
                "success" => [
                    "code" => 200,
                    "message" => "Successfully deleted serie and detached from posts"
                ]
            ]);
        }

        return redirect()->back();
    }

}
