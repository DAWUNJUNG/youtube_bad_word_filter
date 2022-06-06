<?php

namespace App\Exports;

use App\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $datas = DB::table('comment')->select('*')->get();
        $convert_data = [];
        array_push($convert_data, [
            'c_video_id' => '영상 ID',
            'c_comment_id' => '댓글 ID',
            'c_comment_usernick' => '글쓴이',
            'c_comment' => '댓글 내용',
            'c_comment_published_at' => '최초 일자',
            'c_comment_updated_at' => '마지막 수정 일자'
        ]);
        foreach ($datas as $index => $val) {
            array_push($convert_data, [
                'c_video_id' => $val->c_video_id,
                'c_comment_id' => $val->c_comment_id,
                'c_comment_usernick' => $val->c_comment_usernick,
                'c_comment' => $val->c_comment,
                'c_comment_published_at' => $val->c_comment_published_at,
                'c_comment_updated_at' => $val->c_comment_updated_at,
            ]);
        }
        return collect($convert_data);
    }
}
