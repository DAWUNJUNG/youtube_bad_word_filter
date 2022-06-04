<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YoutubeApiController extends Controller
{
    private $is_first_auth = false;
    private $google_code = "";
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
        $this->google_code = $request->input('code');
        $video_id = $request->input('video_id');
        if (!empty($this->google_code)) {
            $this->is_first_auth = true;
        }
        if ($this->is_first_auth && !empty($this->google_code)) {
            $body = array(
                "code" => $this->google_code,
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
                'video_id' => "-8vCDXmyqYY"
            ]);
        }
        return view('Developer.video', [
            'channel_id' => $this->channel_id,
            'api_key' => $this->api_key,
            'google_client_id' => $this->google_client_id,
            'video_id' => (empty($video_id)) ? "" : $video_id
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
