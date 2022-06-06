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
<h3 style="text-align: center;">단어 설정</h3>
<div id="del_comment_list" style="margin: 30px 30px;">
    <table class="table table-bordered">
        <tr>
            <th></th>
            <th>필터 ID</th>
            <th>필터 멘트</th>
        </tr>
        <tbody id="comment_list">
        <?php
            if (empty(count($filter_comment))) {
        ?>
        <tr>
            <td><input type="checkbox" name="del_filter_check" value="" disabled></td>
            <td colspan="2" style="text-align: center;">필터를 할 수 있는 멘트가 없습니다. 추가해주세요</td>
        </tr>
        <?php
            } else {
                foreach ($filter_comment as $index => $value) {
        ?>
        <tr>
            <td><input type="checkbox" name="del_filter_check" value="{{$value->fc_seq}}"></td>
            <td>{{$value->fc_seq}}</td>
            <td>{{$value->fc_comment}}</td>
        </tr>
        <?php
                }
            }
        ?>
        </tbody>
    </table>
    <div id="insert_filter" class="container">
        <div class="mb-3 row">
            <div class="col-sm-4"></div>
            <div class="col-sm-6">
                <input type="text" class="form-control" id="filter_ment">
            </div>
            <button type="button" id="insert_btn" class="col-sm-1 col-form-label btn btn-outline-info">추가</button>
            <button type="button" id="delete_btn" class="col-sm-1 col-form-label btn btn-outline-danger">삭제</button>
        </div>
    </div>
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

        $("#insert_btn").on("click",function(){
            if($("#filter_ment").val() == ''){
                return false;
            } else {
                $.ajax({
                    type: "PUT",
                    data: JSON.stringify($("#filter_ment").val()),
                    dataType: "json",
                    url: "/api/filter_update",
                    success: function (data) {
                        if (data.result === false) {
                            alert("오류가 발생하였습니다.");
                        } else {
                            alert("저장되었습니다.");
                            $("#filter_ment").val("");
                            location.reload();
                        }
                    },
                    complete: function (data) {
                    },
                    error: function (xhr, status, error) {
                    }
                });
            }
        });

        $("#delete_btn").on("click",function(){
            var delete_comment_datas = [];
            if($("input[name=del_filter_check]:checked").length < 1) return false;
            $("input[name=del_filter_check]:checked").each(function(i){
                delete_comment_datas.push($(this).val());
            });
            $.ajax({
                type : "PUT",
                data : JSON.stringify(delete_comment_datas),
                dataType : "json",
                url : "/api/filter_del",
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
