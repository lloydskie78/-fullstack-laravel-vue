<?php

namespace App\Http\Controllers;

use Auth;
use App\Tag;
use App\User;
use App\Role;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    public function index(Request $request)
    {   //? check first if user is admin or not and if the path is login
        if(!Auth::check() && $request->path() != 'login'){
            return redirect('/login');
        }else{
            return view('welcome');
        }
        
        $user = Auth::user();
        if($user->userType == 'User'){
            return redirect('/login');
        }

        if($request->path() == 'login'){
            return redirect('/');
        }
        
        return view('welcome');
    }

    public function logout()
    {   
        Auth::logout();
        return \redirect('login');
    }

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

    public function createUser(Request $request)
    {
        //?validating request
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users', //? Unique from users table
            'password' => 'bail|required|min:8',
            'userType' => 'required'
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            'userType' => $request->userType,
        ]);

        return $user;
    }

    public function editUSer(Request $request)
    {
        //?validating request
        $this->validate($request, [
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id", //? Unique from users table
            'password' => 'min:8',
            'userType' => 'required'
        ]);
        $data = [
            'fullName' => $request->fullName,
            'email' => $request->email,
            'userType' => $request->userType,
        ];

        if($request->password){
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }

        $user = User::where('id', $request->id)->update($data);
        return $user;
    }

    public function getUser()
    {
        return User::where('userType', '!=', 'User')->get();
    }

    public function loginUser(Request $request)
    {
        //? request validation
        
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:8',
        ]);
        
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();

            if($user->userType == 'User'){
                Auth::logout();
                return response()->json([
                    'msg' => 'Not an Admin',
                    'user' =>  $user,
                ], 401);
            }
            
            return response()->json([
                'msg' => 'You are logged in!',
            ]);
        }else{
            return response()->json([
                'msg' => 'Incorrect login credentials!',
            ], 401); //? The number is to change the status in order to generate error message on the frontend
        }
    }

    public function addRole(Request $request)
    {
        $this->validate($request, [
            'roleName' => 'required',
        ]);

        return Role::create([
            'roleName' => $request->roleName,
        ]);

    }

    public function getRole()
    {
        return Role::all();
    }

    public function editRole(Request $request)
    {
        $this->validate($request, [
            'roleName' => 'required',
        ]);

        return Role::where('id', $request->id)->update([
            'roleName' => $request->roleName
        ]);

    }

    public function deleteRole(Request $request)
    {   
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Role::where('id', $request->id)->delete();
    }
 }
 