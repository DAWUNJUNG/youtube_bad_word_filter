<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class YoutubeApiController extends Controller
{
    private $is_first_auth = false;
    private $google_client_id = "3147257063-aukh6rntabnh969v4k35lc9ni2bt31fc.apps.googleusercontent.com";
    private $google_client_secret = "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";
    private $channel_id = "UC1B6SalAoiJD7eHfMUA9QrA";
    private $api_key = "AIzaSyAey59IvPKoBXWgfAsE44CUrIgNirxG9xE";

    public function index(Request $request)
    {
        return view('Developer.index',[
            'channel_id' => $this->channel_id,
            'api_key' => $this->api_key,
            'google_client_id' => $this->google_client_id
        ]);
    }

    public function video(Request $request)
    {
        $google_code = $request->input('code');
        $video_id = $request->input('video_id');

        $del_comment_list = [];
        $del_datas = DB::table('comment')->where('c_video_id','=',$video_id)->select('c_comment_id')->get();

        foreach($del_datas as $index => $val){
            array_push($del_comment_list, $val->c_comment_id);
        }

        if (!empty($google_code)) {
            $this->is_first_auth = true;
        }
        if ($this->is_first_auth && !empty($google_code)) {
            $body = array(
                "code" => $google_code,
                "client_id" => $this->google_client_id,
                "client_secret" => $this->google_client_secret,
                "redirect_uri" => "http://localhost:8000/video?video_id=-8vCDXmyqYY",
                "grant_type" => "authorization_code"
            );

            $post_data = json_encode($body);
            $url = 'https://accounts.google.com/o/oauth2/token';
            $header_data = array(
                'Content-Type: application/json; charset=utf-8'
            );

            $ch = curl_init($url);
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => $header_data,
                CURLOPT_POSTFIELDS => $post_data
            ));

            $response = curl_exec($ch);
            curl_close($ch);
            $object = json_decode($response, true);
            if(isset($object['access_token']) && !empty($object['access_token'])){
                $request->session()->put('token', $object['access_token']);
            }
            $token = $request->session()->get('token');
            return view('Developer.video', [
                'channel_id' => $this->channel_id,
                'api_key' => $this->api_key,
                'google_client_id' => $this->google_client_id,
                'token' => $token,
                'video_id' => "-8vCDXmyqYY",
                'del_comment' => $del_comment_list
            ]);
        }
        return view('Developer.video', [
            'channel_id' => $this->channel_id,
            'api_key' => $this->api_key,
            'google_client_id' => $this->google_client_id,
            'video_id' => (empty($video_id)) ? "" : $video_id,
            'del_comment' => $del_comment_list
        ]);
    }

    public function comment(Request $request)
    {
        $del_datas = DB::table('comment')
            ->select('*')
            ->orderBy('c_comment_published_at','asc')
            ->get();

        return view('Developer.comment',[
            'channel_id' => $this->channel_id,
            'api_key' => $this->api_key,
            'google_client_id' => $this->google_client_id,
            'video_id' => "-8vCDXmyqYY",
            'del_comment' => $del_datas
        ]);
    }

    public function comment_setting(Request $request)
    {
        $filter_data = DB::table('filter_comment')
            ->select('*')
            ->get();

        return view('Developer.comment_setting',[
            'google_client_id' => $this->google_client_id,
            'filter_comment' => $filter_data
        ]);
    }

    public function download(Request $request)
    {
        $file_list = [];
        $file_datas = Storage::disk('s3')->files('/'.$this->channel_id);
        foreach ($file_datas as $data) {
            $client = Storage::disk('s3')->getDriver()->getAdapter()->getClient();
            $expiry = "+10 minutes";

            $command = $client->getCommand('GetObject', [
                'Bucket' => \Config::get('filesystems.disks.s3.bucket'),
                'Key'    => $data
            ]);

            $request = $client->createPresignedRequest($command, $expiry);
            $file_name_info = explode('/',$request->getUri()->getPath());
            array_push($file_list, [
                "file_path" => $request->getUri()->getPath(),
                "channel_id" => $file_name_info[1],
                "file_name" => $file_name_info[2],
                "download_url" => (string) $request->getUri()
            ]);
        }

        return view('Developer.download',[
            'google_client_id' => $this->google_client_id,
            'file_list' => $file_list
        ]);
    }

    public function comment_update(Request $request){
        try {
            $delete_datas = json_decode($request->getContent(), true);

            foreach ($delete_datas as $index => $data) {
                DB::table('comment')->updateOrInsert([
                    'c_video_id' => $data['c_video_id'],
                    'c_comment_id' => $data['c_comment_id']
                ],[
                    'c_video_id' => $data['c_video_id'],
                    'c_comment_id' => $data['c_comment_id'],
                    'c_comment_usernick' => $data['c_comment_usernick'],
                    'c_comment' => $data['c_comment'],
                    'c_comment_published_at' => date('Y-m-d h:i:s', strtotime($data['c_comment_published_at'])),
                    'c_comment_updated_at' => date('Y-m-d h:i:s', strtotime($data['c_comment_updated_at']))
                ]);
            }

            $del_datas = DB::table('comment')->select('c_comment_id')->get();
            $del_comment_list = [];

            foreach($del_datas as $index => $val){
                array_push($del_comment_list, $val->c_comment_id);
            }

            Excel::store(new UsersExport, '/'.$this->channel_id.'/'.date("Y-m-d").'.xlsx', 's3');

            return response()->json([
                'result' => true,
                'del_comment' => $del_comment_list
            ]);
        } catch (QueryException|Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e
            ]);
        }
    }

    public function comment_del(Request $request){
        try {
            $delete_datas = json_decode($request->getContent(), true);

            foreach ($delete_datas as $index => $data) {
                $comment_info = explode('|',$data);
                DB::table('comment')->where([
                    'c_video_id' => $comment_info[0],
                    'c_comment_id' => $comment_info[1]
                ])->delete();
            }

            Excel::store(new UsersExport, '/'.$this->channel_id.'/'.date("Y-m-d").'.xlsx', 's3');

            return response()->json([
                'result' => true
            ]);
        } catch (QueryException|Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e
            ]);
        }
    }

    public function filter_update(Request $request){
        try {
            $filter_datas = json_decode($request->getContent(), true);

            DB::table('filter_comment')->updateOrInsert([
                'fc_comment' => $filter_datas
            ],[
                'fc_comment' => $filter_datas
            ]);

            return response()->json([
                'result' => true
            ]);
        } catch (QueryException|Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e
            ]);
        }
    }

    public function filter_del(Request $request){
        try {
            $delete_datas = json_decode($request->getContent(), true);

            foreach ($delete_datas as $index => $data) {
                DB::table('filter_comment')->where([
                    'fc_seq' => $data
                ])->delete();
            }

            return response()->json([
                'result' => true
            ]);
        } catch (QueryException|Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e
            ]);
        }
    }

    public function file_make(Request $request){
        try {

            Excel::store(new UsersExport, '/'.$this->channel_id.'/'.date("Y-m-d").'.xlsx', 's3');

            return response()->json([
                'result' => true
            ]);
        } catch (QueryException|Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e
            ]);
        }
    }

    public function file_del(Request $request){
        try {
            $delete_datas = json_decode($request->getContent(), true);

            foreach ($delete_datas as $index => $data) {
                Storage::disk('s3')->delete($data);
            }

            return response()->json([
                'result' => true
            ]);
        } catch (QueryException|Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e
            ]);
        }
    }
}
