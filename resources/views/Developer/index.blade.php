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
                    "redirect_uri=http://localhost:8000/video&" +
                    "response_type=code&client_id={{$google_client_id}}&" +
                    "scope=https://www.googleapis.com/auth/youtube&access_type=offline");
            });
        });

        $.ajax({
            type : "GET",
            dataType : "json",
            url : "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={{$channel_id}}"+
                "&maxResults=4&order=date&type=video&maxResults=1&key={{$api_key}}",
            success : function(data) {
                $('body').append('<div id="{{$channel_id}}" class="row row-cols-1 row-cols-lg-3 align-items-stretch g-4 py-5" style="margin: 30px 30px;"></div>');
                var counting = 0;
                data.items.forEach(function (element, index) {
                    $('#{{$channel_id}}').append(
                        '<div class="col" style="text-align: center;">'+
                        '<div id="counting_'+counting+'" class="card card-cover overflow-hidden text-white bg-dark rounded-5 shadow-lg" style="width: 480px; height: 340x; margin: auto;"> '+
                        '<div class="d-flex flex-column p-5 pb-3 text-shadow-1" style="width: 480px; height: 340px;" >'+
                        '</div>'+
                        '</div>'+
                        '<a href="/video?video_id='+element.id.videoId+'" style="color: black; text-decoration:none;">'+element.snippet.title+'</a>'+
                        '</div>'
                    );
                    $("#counting_"+counting).css({"background-image": "url("+element.snippet.thumbnails.high.url+")"});
                    counting++;
                });
            },
            complete : function(data) {},
            error : function(xhr, status, error) {}
        });
    </script>
</html>
