<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artical;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use App\Models\Category;
use App\Models\User;

class ArticalController extends Controller
{
    public function index()
    {
        $Artical = Artical::latest()->paginate(30);
        return view('admin.articals.index')->with([
            'articals' => $Artical
        ]);
    }
    
    public function create()
    {
         $authors = User::where('role',2)->where('status','active')->get();
        try{
            $categoriesd = Category::where('parent_id',0)->with('subcategories')->get();
            $language=Language::all();
            return response()->json([
                "success" => true,
                "html" => view('admin.articals.ajax.create',compact('language','categoriesd','authors'))->render(),
            ]);
        }
        catch(\Exception $ex){
            return response()->json([
                "success" => false,
                'msgText' =>$ex->getMessage(),
            ]);
        }
    }
    public function store(Request $request)
    {
        $requestData = $request->all();
        $requestData['url'] = Str::slug($request->url, '-');
        $request->replace($requestData);
        $validator = Validator::make($requestData, [
            'title' => 'required|max:255',
            'category' => 'required',
            'url' => 'required|max:255|unique:articals',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'content' => 'required',
            'meta_title' => 'required',
            'meta_keyword' => 'required',
            'meta_description' => 'required',
            'language_id' => 'required',
            'date' => 'required',

        ]);
        if ($validator->passes()) {
            try {

                 //For image
                $file = $request->file('image');
                $path = public_path(). '/storage/articals/';
                $filename = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move($path, $filename);   

               if($request->author_id){
                $authorId = $request->author_id;
               }
               else{
                $authorId = auth()->user()->id;
               }
                Artical::create([
                    'language_id' => $request->language_id,
                    'author_id' => $authorId,
                    'title' => $request->title,
                    'category_id' => $request->category,
                    'subcategory_id' => $request->subcategory,
                    'user_name' => auth()->user()->name,
                    'date' => $request->date,
                    
                    'url' => $request->url,
                    //'image' => $request->image->store('blogs'),
                    'image' => $filename,
                    'content' => $request->content,
                    
                    'meta_title' => $request->meta_title,
                    'meta_keyword' => $request->meta_keyword,
                    'meta_description' => $request->meta_description,
                    'status' => $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'msgText' => 'Created',
                ]);
            } catch(\Exception $ex) {
                return response()->json([
                    'success' => false,
                    'code' => 400,
                    'msgText' => $ex->getMessage(),
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'code' => 422,
                'errors' => $validator->errors(),
            ]);
        }
    }
    public function edit($id)
    {
        $authors = User::where('role',2)->where('status','active')->get();
        try {
            $language=Language::all();
           
            $artical = Artical::findOrFail($id);
            $categoriesd = Category::where('parent_id',0)->with('subcategories')->get();
            if($artical->subcategory_id!==null){
                $subcategory = Category::findOrFail($artical->subcategory_id);
            }
            else {
                $subcategory="None";
            }
            
            return response()->json([
                "success" => true,
                "html" => view('admin.articals.ajax.edit')->with([
                    'artical' => $artical,
                    'language' => $language,
                    'categoriesd'=>$categoriesd,
                    'subcategory'=>$subcategory,
                    'authors' =>$authors,
                ])->render(),
            ]);
        } catch(\Exception $ex){
            return response()->json([
                "success" => false,
                'msgText' =>$ex->getMessage(),
            ]);
        }
    }

    public function update(Request $request , $id)
    {
  
        $requestData = $request->all();
        $requestData['url'] = Str::slug($request->url, '-');

        
        $request->replace($requestData);
        $validator = Validator::make($requestData, [
            'title' => 'required|max:255',
            'category' => 'required',
            'url' => [ "required",Rule::unique('articals')->ignore($id),"max:255"],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'content' => 'required',
            'meta_title' => 'required',
            'meta_keyword' => 'required',
            'meta_description' => 'required',
            'language_id' => 'required',
            'date'=>'required',
        ]);
        if ($validator->passes()) {
            if($request->author_id){
                $authorId = $request->author_id;
               }
               else{
                $authorId = auth()->user()->id;
               }
             
            try {
                $blog = Artical::findOrFail($id);
               if($blog->user_name){
                $userName = $blog->user_name;
               }
               else{
                $userName = auth()->user()->name;
               }
                $data = array(
                    'language_id' => $request->language_id,
                    'title' => $request->title,
                    'category_id' => $request->category,
                    'subcategory_id' =>($request->subcategory!=="null")?$request->subcategory:null,
                    'url' => $request->url,
                    'content' => $request->content,
                    'author_id' => $authorId,
                    'meta_title' => $request->meta_title,
                    'meta_keyword' => $request->meta_keyword,
                    'meta_description' => $request->meta_description,
                    'status' => $request->status,
                    'aproved' => $request->aproved,
                    'user_name' => $userName,
                    'date' => $request->date,
                );

                //For Image
                if($request->hasFile('image')){
                    //For image
                    $file = $request->file('image');
                    $path = public_path(). '/storage/articals/';
                    $file_img='storage/articals/'.$blog->image;
                    
                    if(isset($blog->image) && File::exists($file_img))
                    {File::delete($file_img);}

                    $filename = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move($path, $filename);   
                    $data['image'] = $filename;
                }


                $blog->update($data);
                return response()->json([
                    'success' => true,
                    'msgText' => 'Blog Updated',
                ]);
            } catch(\Exception $ex) {
                return response()->json([
                    'success' => false,
                    'code' => 400,
                    'msgText' => $ex->getMessage(),
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'code' => 422,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $blog = Artical::findOrFail($id);
            
            $file_img='storage/articals/'.$blog->image;
            if(isset($blog->image) && File::exists($file_img))
            {File::delete($file_img);}
             $blog->delete();
            return response()->json([
                'success' => true,
            ]);
        } catch(\Exception $ex) {
            return response()->json([
                'success' => false,
                'msgText' => $ex->getMessage(),
            ]);
        }
    }

    public function Checkstatus($type,$id)
    {
        $model = Artical::find($id);
        $model->aproved = $type;
        $model->save();
        return redirect()->back();
    }
}
