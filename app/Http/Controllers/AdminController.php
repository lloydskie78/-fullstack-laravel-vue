<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Blog;
use App\Role;
use App\User;
use App\Category;
use App\Blogcategory;
use App\Blogtag;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function index(Request $request)
    {   //? check first if user is admin or not and if the path is login
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }

        if (!Auth::check() && $request->path() == 'login') {
            return view('welcome');
        }

        $user = Auth::user();
        if ($user->userType == 'User') {
            return redirect('/login');
        }

        if ($request->path() == 'login') {
            return redirect('/');
        }

        return $this->checkForPermission($user, $request);
        return view('notfound');
        return view('welcome');
    }

    public function checkForPermission($user, $request)
    {
        $permission = json_decode($user->role->permission);
        $hasPermission = false;
        if (!$permission) return view('welcome');

        foreach ($permission as $p) {
            if ($p->name == $request->path()) {
                if ($p->read) {
                    $hasPermission = true;
                }
            }
        }

        if ($hasPermission) return view('welcome');
        return view('notfound');
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
        $imgName = time() . '.' . $request->file->extension();
        $request->file->move(public_path('uploads'), $imgName);
        return $imgName;
    }

    //? FOR EDITOR JS IMAGE UPLOAD
    public function uploadEditorImage(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);

        $imgName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads'), $imgName);
        return \response()->json([
            'success' => 1,
            'file' => [
                'url' => "http://localhost:8000/uploads/$imgName"
            ]
        ]);
        return $imgName;
    }

    public function deleteImage(Request $request)
    {
        $fileName = $request->imageName;
        $this->deleteFIleFromServer($fileName, false);
        return 'done';
    }

    public function deleteFIleFromServer($fileName, $hasFullPath = false)
    {
        if (!$hasFullPath) {
            $filePath = public_path() . '/uploads/' . $fileName;
        }

        if (file_exists($filePath)) {
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
            'role_id' => 'required'
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            'role_id' => $request->role_id,
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

        if ($request->password) {
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }

        $user = User::where('id', $request->id)->update($data);
        return $user;
    }

    public function getUser()
    {
        return User::get();
    }

    public function loginUser(Request $request)
    {
        //? request validation

        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:8',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->role->isAdmin == 0) {
                Auth::logout();
                return response()->json([
                    'msg' => 'Not an Admin',
                ], 401);
            }

            return response()->json([
                'msg' => 'You are logged in!',
            ]);
        } else {
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

    public function assignRole(Request $request)
    {
        $this->validate($request, [
            'permission' => 'required',
            'id' => 'required',
        ]);

        return Role::where('id', $request->id)->update([
            'permission' => $request->permission
        ]);
    }

    public function slug()
    {
        $title = 'This is a nice title changed';

        return Blog::create([
            'title' => $title,
            'post' => 'some post',
            'post_excerpt' => 'aead',
            'user_id' => 1,
            'metaDescription' => 'aead',
        ]);
        return $title;
    }

    public function createBlog(Request $request)
    {
        $categories = $request->category_id;
        $tags = $request->tag_id;

        $blogCat = [];
        $blogTag = [];



        $blog =  Blog::create([
            'title' => $request->title,
            'post' => $request->post,
            'post_excerpt' => $request->post_excerpt,
            'user_id' => Auth::user()->id,
            'metaDescription' => $request->metaDescription,
            'jsonData' => $request->jsonData,
        ]);

        //? Insertion to Blogcategory table
        foreach ($categories as $c) {
            array_push($blogCat, [
                'category_id' => $c,
                'blog_id' => $blog->id 
            ]);
        }
        Blogcategory::insert($blogCat);

        //? Insertion to BlogTag table
        foreach ($tags as $t) {
            array_push($blogTag, [
                'tag_id' => $t,
                'blog_id' => $blog->id
            ]);
        }
        Blogtag::insert($blogTag);



        return 'done';
    }
}
