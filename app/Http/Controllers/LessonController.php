<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Category;
use App\Lesson;
use App\Comment;

use Cloudder;

class LessonController extends Controller
{
    public function getList()
    {
    	$less = Lesson::orderBy('id','DESC')->get();
    	return view('cate.lesson.list',['less'=>$less]);
    }

    public function getAdd()
    {
    	$cate = Category::all();
    	return view('cate.lesson.add', ['cate'=>$cate]);
    }
    public function postAdd(Request $request)
    {
       $this->validate($request,
            [
                'category' => 'required',
                'tieude' => 'required|min:3|unique:lessons,title',
               
            ],
            [
                'category.required'=>' Bạn chưa chọn Category',
                'tieude.required'=>'Bạn chưa nhập tiêu đề',
                'tieude.min'=>'Tiêu đề ít nhất 3 kí tự',
                'tieude.unique'=> 'Tiêu đề đã tồn tại',
               
            ]
            );
            
        $less = new Lesson;
        $less->cate_id = $request->category;
        $less->title = $request->tieude;
        $less->tomtat = $request->tomtat;
        $less->noidung = $request->noidung;
        $less->luotxem = 0;
        $less->noibat = $request->noibat;        

        if($request->hasFile('media'))
        {
            $file = $request->media;
            Cloudder::uploadVideo($file, 'khoaluan/' . $less->title);
            $less->media = Cloudder::getResult()['url'];
        }
        

        $less->save();
        return redirect('lesson/list')->with('thongbao', 'Thêm lesson thành công');
    }
    
    public function getEdit($id)
    {
        $cate = Category::all();
       
        $less = Lesson::find($id);
        
        return view('cate.lesson.edit',['less'=>$less, 'cate'=>$cate]);
    }
    public function postEdit(Request $request, $id)
    {
        $cate = Category::all();
        $less = Lesson::find($id);

        $less->cate_id = $request->category;
        $less->title = $request->tieude;
        $less->tomtat = $request->tomtat;
        $less->noidung = $request->noidung;
        $less->noibat = $request->noibat;  

        if($request->hasFile('media'))
        {
            
            $file = $request->media;
            Cloudder::uploadVideo($file, 'khoaluan/' . $less->title);
            $less->media = Cloudder::getResult()['url'];
        }
       
        $less->save();
        return redirect('/lesson/list')->with('thongbao', 'Sửa lesson thành công');
    }
    
    public function getDelete($id)
    {
        $less = Lesson::find($id);
        $less->delete();
        return redirect('/lesson/list')->with('thongbao', 'Xóa thành công');
    }
    
}
