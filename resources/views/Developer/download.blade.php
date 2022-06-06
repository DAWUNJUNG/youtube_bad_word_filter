<html>
<head>
    <title>유튜브 댓글 필터링</title>

    {{--    css    --}}
    {{-- bootstrap --}}
    <link href="{{ asset('css/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap/bootstrap.rtl.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap/bootstrap-grid.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap/bootstrap-grid.rtl.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap/bootstrap-reboot.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap/bootstrap-reboot.rtl.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap/bootstrap-utilities.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap/bootstrap-utilities.rtl.min.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
@include("header")
<h3 style="text-align: center;">다운로드 센터</h3>
<div id="del_comment_list" style="margin: 30px 30px;">
    <div id="comment_btn_list" style="text-align: right; padding-bottom: 14px;">
        <button type="button" id="delete_btn" class="btn btn-outline-danger">삭제</button>
    </div>
    <table class="table table-bordered">
        <tr>
            <th></th>
            <th>채널 ID</th>
            <th>파일 명칭</th>
            <th>파일 경로</th>
        </tr>
        <tbody id="comment_list">
        <?php
            if (empty(count($file_list))) {
        ?>
        <tr>
            <td><input type="checkbox" name="del_file_check" value="" disabled></td>
            <td colspan="3" style="text-align: center;">파일이 없습니다. 파일을 수집하거나 자동 수집을 기다리십시오.</td>
        </tr>
        <?php
            } else {
                foreach ($file_list as $index => $value) {
        ?>
        <tr>
            <td><input type="checkbox" name="del_file_check" value="{{$value['file_path']}}"></td>
            <td>{{$value['channel_id']}}</td>
            <td><a href="{{$value['download_url']}}" download>{{$value['file_name']}}</a></td>
            <td>{{$value['file_path']}}</td>
        </tr>
        <?php
                }
            }
        ?>
        </tbody>
    </table>
</div>
</body>
{{--    js     --}}
{{-- jquery --}}
<script src="{{ asset('js/jquery/jquery-3.6.0.min.js') }}" type="text/javascript"></script>

{{-- bootstrap --}}
<script src="{{ asset('js/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $("#google_login").on("click",function(){
            window.open("https://accounts.google.com/o/oauth2/v2/auth?" +
                "redirect_uri=http://localhost:8000/video?video_id=-8vCDXmyqYY&" +
                "response_type=code&client_id={{$google_client_id}}&" +
                "scope=https://www.googleapis.com/auth/youtube&access_type=offline");
        });

        $("#delete_btn").on("click",function(){
            var delete_file_datas = [];
            if($("input[name=del_file_check]:checked").length < 1) return false;
            $("input[name=del_file_check]:checked").each(function(i){
                delete_file_datas.push($(this).val());
            });
            $.ajax({
                type : "PUT",
                data : JSON.stringify(delete_file_datas),
                dataType : "json",
                url : "/api/file_del",
                success : function(data) {
                    if(data.result === false) {
                        alert("오류가 발생하였습니다.");
                    } else {
                        alert("저장되었습니다.");
                        location.reload();
                    }
                },
                complete : function(data) {},
                error : function(xhr, status, error) {}
            });
        });
    });
</script>
</html>
