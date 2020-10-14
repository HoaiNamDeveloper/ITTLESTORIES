<?php

namespace App\Http\Controllers;
use App\Post;
use Illuminate\Http\Request;
use Validator;
use Session;
use App\Category;
use App\Tag;
use App\File;
use Auth;

class NewPostContronller extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('/news/news_create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cates = Category::all();
    	$tags = Tag::all();
        return view('/news/news_create',['cates'=>$cates,'tags'=>$tags]);
    }
    public function createSlug($title, $id = 0)
    {
        // Normalize the title
        $slug = str_slug($title);

        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($slug, $id);

        // If we haven't used it before then we are all good.
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }

        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Post::select('slug')->where('slug', 'like', $slug.'%')
            ->where('id', '<>', $id)
            ->get();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        echo "a";
    	$rules= [
    			'title'=>'required|min:3|max:100',
                'des' =>'required|max:180',
                'author' => 'required|max:15',
    			'category_id'=> 'required| integer',
    			'content'=> 'required',
    			];
    	$msg = [
    			'title.required'=>'Không được bỏ trống tiêu đề.',
    			'title.unique' => 'Tin này đã bị trùng, vui lòng nhập lại!',
    			'title.min'=>'Tên bài viết gồm ít nhất 3 ký tự!',
    			'title.max'=>'Tên bài viết gồm tối đa 100 ký tự!',
                'des.required'=>'Không được bỏ trống tóm tắt.',
                'author.required'=>'Không được bỏ trống tên tác giả.',
                'author.max'=>'Tên tác giả tối đa 15 ký tự!',
    			'content.required'=>'Không được bỏ trống nội dung.',
    			'category_id.required'=> 'Không được bỏ trống chuyên mục.',
    			'category_id.integer'=> 'Chọn sai chuyên mục.',
    			];
		$validator = Validator::make($request->all(), $rules , $msg);
		if ($validator->fails()) {
		    return redirect()->back()
		                ->withErrors($validator)
                        ->withInput();
                      
		} else {
            $post = new Post();
	    	$post->title = $request->input('title');
	    	$post->content = $request->input('content');
	    	$post->description = $request->input('des');
            $post->status = 0;
	    	$post->slug = $this->createSlug($request->input('title')) ;
            $post->user_id = 1;
            $post->owner = $request->input('author');
	    	$post->category_id = $request->input('category_id');
	    	//Upload Image
	    	if($request->hasFile('img_post')){
	    		$file = $request->file('img_post');
	    		$file_extension = $file->getClientOriginalExtension(); // Lấy đuôi của file
	    		if($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'jpeg'){
	    			$post->post_type = 'text';
	    		} else if($file_extension == 'mp4' || $file_extension == '3gp' || $file_extension == 'avi' || $file_extension == 'flv'){
	    			$post->post_type = 'video';
	    		} else return redirect()->back()->with('errfile','Chưa hỗ trợ định dạng file vừa upload.')->withInput();

	    		$file_name = $file->getClientOriginalName();
	    		$random_file_name = str_random(4).'_'.$file_name;
	    		while(file_exists('upload/posts/'.$random_file_name)){
	    			$random_file_name = str_random(4).'_'.$file_name;
	    		}
	    		$file->move('upload/posts',$random_file_name);
	    		// $file_upload = new File();
	    		// $file_upload->name = $random_file_name;
	    		// $file_upload->link = 'upload/posts/'.$random_file_name;
	    		// $file_upload->post_id = $post->id;
	    		// $file_upload->save();
	    		$post->feture = 'upload/posts/'.$random_file_name;
            } else $post->feture='';
	    	$post->save();
    	}
    	Session::flash('flash_success','Thêm bài viết thành công.');
    	return redirect()->action('NewPostContronller@create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        echo('aaaaaaa');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
