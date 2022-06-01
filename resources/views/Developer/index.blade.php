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

    {{-- google --}}
    <link href="{{ asset('css/google/my_uploads.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
</body>
{{--    js     --}}
{{-- jquery --}}
<script src="{{ asset('js/jquery/jquery-3.6.0.min.js') }}" type="text/javascript"></script>

{{-- bootstrap --}}
<script src="{{ asset('js/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

{{-- google --}}
<script src="https://apis.google.com/js/client.js?onload=googleApiClientReady"></script>
<script src="{{ asset('js/google/auth.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/google/my_uploads.js') }}" type="text/javascript"></script>
<script>
    var channelId = 'UC2kEAAev6IVlucMA9qSzXQQ';
    var key = 'AIzaSyAey59IvPKoBXWgfAsE44CUrIgNirxG9xE';
    $.ajax({
        type : "GET",
        dataType : "json",
        url : "https://www.googleapis.com/youtube/v3/search?part=id&channelId="+channelId+
            "&maxResults=4&order=date&type=video&key="+key,
        success : function(data) {
            data.items.forEach(function (element, index) {
                $('body').append('<div id="'+element.id.videoId+'">' +
                    '<iframe width="560" height="315" src="https://www.youtube.com/embed/' +
                    element.id.videoId +
                    '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; ' +
                    'encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
                    '<table id="'+element.id.videoId+'_comment" class="table table-bordered">' +
                    '<tr>' +
                    '<th>댓글 ID</th>' +
                    '<th>닉네임</th>' +
                    '<th>댓글 내용</th>' +
                    '<th>영상 바로가기</th>' +
                    '</tr>'+
                    '</table>' +
                    '</div>');
                var videoId = element.id.videoId;
                $.ajax({
                    type : "GET",
                    dataType : "json",
                    url : "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&key="+key+
                        "&videoId="+videoId+"&maxResults=100",
                    success : function(data) {
                        data.items.forEach(function (element, index) {
                            $('#'+videoId+'_comment').append(
                                '<tr>' +
                                '<td>'+element.etag+'</td>' +
                                '<td>'+element.snippet.topLevelComment.snippet.authorDisplayName+'</td>' +
                                '<td>'+element.snippet.topLevelComment.snippet.textOriginal+'</td>' +
                                '<td><a href="https://www.youtube.com/watch?v='+videoId+'">바로가기</a></td>' +
                                '</tr>');
                        });
                    },
                    complete : function(data) {},
                    error : function(xhr, status, error) {}
                });
            });
        },
        complete : function(data) {},
        error : function(xhr, status, error) {}
    });
</script>
</html>
