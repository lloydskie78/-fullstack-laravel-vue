<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::prefix('app')->middleware(['admincheck'])->group(function () {
    Route::post('/create_tag', 'AdminController@addTag');
    Route::get('/get_tags', 'AdminController@getTag');
    Route::post('/edit_tag', 'AdminController@editTag');
    Route::post('/delete_tag', 'AdminController@deleteTag');
    Route::post('/upload', 'AdminController@upload');
    Route::post('/delete_image', 'AdminController@deleteImage');
    Route::post('/create_category', 'AdminController@addCategory');
    Route::get('/get_category', 'AdminController@getCategory');
    Route::post('/edit_category', 'AdminController@editCategory');
    Route::post('/delete_category', 'AdminController@deleteCategory');
    Route::post('/create_user', 'AdminController@createUser');
    Route::get('/get_users', 'AdminController@getUser');
    Route::post('/edit_user', 'AdminController@editUser');
    Route::post('/user_login', 'AdminController@loginUser');


    // ?Roles routes
    Route::post('create_role', 'AdminController@addRole');
    Route::get('get_roles', 'AdminController@getRole');
    Route::post('edit_role', 'AdminController@editRole');
    Route::post('delete_role', 'AdminController@deleteRole');
    Route::post('assign_roles', 'AdminController@assignRole');


    //? BLOG
    Route::post('create_blog', 'AdminController@createBlog');
    Route::get('blogsdata', 'AdminController@blogdata'); //? Getting the blogs data
    Route::post('delete_blog', 'AdminController@deleteBlog'); //? For deleting the blogs data
    Route::get('blog_single/{id}', 'AdminController@singleBlogItem'); //? For showing the single blog data
    Route::post('update_blog/{id}', 'AdminController@updateBlog'); //? For editing the blogs data

});

Route::post('createBlog', 'AdminController@uploadEditorImage');
Route::get('slug', 'AdminController@slug');
Route::get('blogdata', 'AdminController@blogdata');



Route::get('/logout', 'AdminController@logout');
Route::get('/', 'AdminController@index');
Route::any('{slug}', 'AdminController@index')->where('slug', '([A-z\d-\/_.]+)?');
