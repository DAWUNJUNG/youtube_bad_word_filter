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
<h3 style="text-align: center;">필터링 댓글 목록</h3>
<div id="del_comment_list" style="margin: 30px 30px;">
    <div id="comment_btn_list" style="text-align: right; padding-bottom: 14px;">
        <button type="button" id="file_make_btn" class="btn btn-outline-success">엑셀 파일</button>
        <button type="button" id="delete_btn" class="btn btn-outline-danger">삭제</button>
    </div>
    <table class="table table-bordered">
        <tr>
            <th></th>
            <th>동영상 ID</th>
            <th>댓글 ID</th>
            <th>닉네임</th>
            <th style="width:50%;">댓글 내용</th>
            <th>일자</th>
        </tr>
        <tbody id="comment_list">
        <?php foreach ($del_comment as $index => $value) {?>
        <tr>
            <td><input type="checkbox" name="del_comment_check" value="{{$value->c_video_id."|".$value->c_comment_id}}"></td>
            <td>{{$value->c_video_id}}</td>
            <td>{{$value->c_comment_id}}</td>
            <td>{{$value->c_comment_usernick}}</td>
            <td>{{$value->c_comment}}</td>
            <td>{{$value->c_comment_published_at}}</td>
        </tr>
        <?php }?>
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

        $("#file_make_btn").on("click",function(){
            $.ajax({
                type : "PUT",
                dataType : "json",
                url : "/api/file_make",
                success : function(data) {
                    if(data.result === false) {
                        alert("오류가 발생하였습니다.");
                        return false;
                    } else {
                        if(confirm("엑셀 파일이 생성 되었습니다.\n다운로드 센터로 이동하시겠습니까?")){
                            location.href="/download";
                        } else {
                            return false;
                        }
                    }
                },
                complete : function(data) {},
                error : function(xhr, status, error) {}
            });
        });

        $("#delete_btn").on("click",function(){
            var delete_comment_datas = [];
            if($("input[name=del_comment_check]:checked").length < 1) return false;
            $("input[name=del_comment_check]:checked").each(function(i){
                delete_comment_datas.push($(this).val());
            });
            $.ajax({
                type : "PUT",
                data : JSON.stringify(delete_comment_datas),
                dataType : "json",
                url : "/api/comment_del",
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
