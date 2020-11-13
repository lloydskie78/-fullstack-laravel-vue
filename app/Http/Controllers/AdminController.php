<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function addTag(Request $request)
    {   
        $this->validate($request, [
            'tagName' => 'required',
        ]);
        return Tag::create([
            'tagName' => $request->tagName
        ]);
    }
    public function editTag(Request $request)
    {   
        $this->validate($request, [
            'tagName' => 'required',
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->update([
            'tagName' => $request->tagName
        ]);
    }
     public function deleteTag(Request $request)
    {   
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->delete();
    }

    public function getTag()
    {
        return Tag::orderBy('id', 'desc')->get();
    }
    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png',
        ]);
        $imgName = time().'.'.$request->file->extension();
        $request->file->move(public_path('uploads'),$imgName);
        return $imgName;
    }
    public function deleteImage(Request $request)
    {
        $fileName = $request->imageName;
        $this->deleteFIleFromServer($fileName, false);
        return 'done';
    }
    public function deleteFIleFromServer($fileName, $hasFullPath=false)
    {
        if(!$hasFullPath){
            $filePath = public_path().'/uploads/'.$fileName;
        }
        
        if(file_exists($filePath)){
            @unlink($filePath);
        }
        return;
    }
    public function addCategory(Request $request)
    {
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required'
        ]);
        return Category::create([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage
        ]);
    }
    public function getCategory()
    {
        return Category::orderBy('id', 'desc')->get();
    }
    public function editCategory(Request $request)
    {
        $this->validate($request, [
             'categoryName' => 'required',
            'iconImage' => 'required'
        ]);
        return Category::where('id', $request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage
        ]);
    }
    public function deleteCategory(Request $request)
    {
        //!Delete the original file from the server first
        
        $this->deleteFIleFromServer($request->iconImage);
        
        $this->validate($request, [
            'id' => 'required',
        ]);

        return Category::where('id', $request->id)->delete();
    }
}
