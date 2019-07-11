<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Post;
use App\postImage;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PostController extends Controller
{
    //

    public function addImageToUser(Request $request){
//        return $request;
        $user_id = $request->id;
        $post_image = new postImage();

        if($request->file('inputImg')!=null){
            $post_image->id = Uuid::uuid4()->toString();
//            return $post_image->id;
//            $post_image->save();

            $image = $request->file('inputImg');
            $extension = $image->clientExtension();
            $name = $post_image->id .'.'. $extension;
            $banner_path = public_path().'/images/userpost/';//.'/storage/banner';
            $image->move($banner_path, $name);
            $post_image->user_id = $user_id;
            $post_image->name = $name;
            $post_image->save();
            return "success";
        }

        return "error";
    }

    public function getMyImages(Request $request){
        $user_id = $request->id;

        $arrImages = postImage::where('user_id', $user_id)->orderByDesc('created_at')->get();
        return $arrImages;
    }

    public function goPost(PostRequest $request){
//        return $request;
        $user_id = $request->id;
        $title = $request->title;
        $content = $request->contents;

        $post='';
        if($request->post_id!=""){
            $post = Post::find($request->post_id);
        }else{
            $post = new Post();
            $post->id = Uuid::uuid4();
        }

        if($request->file('thumbnail')!=null){
            $image = $request->file('thumbnail');
            $extension = $image->clientExtension();
            $name = $post->id .'.'. $extension;
            $banner_path = public_path().'/images/thumbnail/';//.'/storage/banner';
            $image->move($banner_path, $name);
            $post->thumbnail = $name;
        }else if($post->thumbnail==''){
            return "thumbnail must be filled";
        }

        $tag="";
        foreach ($request->tag as $t){
            if($tag==''){
                $tag=$t;
            }
            else{
                $tag = $tag.'-'.$t;
            }
        }


        if($request->visibility!=null) {
            $visibility="";
            foreach($request->visibility as $v){
                $visibility = $visibility.$v;
                $post->visibility = $visibility;
            }
        }

        $post->title = $title;
        $post->content = $content;
        $post->user_id = $user_id;
        $post->tag = $tag;
        $post->save();

        return "success";
    }

    public function getMyPost(Request $request){
        $user_id = $request->id;
        return Post::where('user_id', $user_id)->orderByDesc('created_at')->paginate(8);
    }

    public function deletePost(Request $request){
        $post_id = $request->post_id;
        $post = Post::where('id', $post_id)->get()[0];
//        $post->deleted_at = now();
//        $post->save();
        $post->delete();

        return "post deleted successfully";
    }

    public function getAllPost(Request $request){
        return Post::orderByDesc('created_at')->paginate(8);
    }

    public function search(Request $request){
        $key = $request->keyword;
        $page = $request->paginate;

//        return $request;
//
//        if($page!=null){
//            return Post::where('title', 'like', '%'.$key.'%')->
//            orWhere('tag', 'like', '%'.$key.'%')->
//            orderByDesc('created_at')->paginate($page);
//        }

        return Post::where('title', 'like', '%'.$key.'%')->
            orWhere('tag', 'like', '%'.$key.'%')->
            orderByDesc('created_at')->paginate(8);
    }

    public function getPostById(Request $request){
        $id = $request->id;

        return Post::where('id', $id)->get();


    }
}
