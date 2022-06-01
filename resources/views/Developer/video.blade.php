<?php if(empty($video_id)) {header('Location: /'); exit();} ?>
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
@if(isset($token))
<script>
    $("#google_login").hide();
    $.ajax({
        type : "GET",
        dataType : "json",
        url : "https://www.googleapis.com/youtube/v3/channels?part=id&mine=true&access_token={{$token}}&key={{$api_key}}",
        success : function(data) {
    $.ajax({
        type : "GET",
        dataType : "json",
        url : "https://www.googleapis.com/youtube/v3/search?part=id&channelId="+data.items[0].id+
            "&maxResults=4&order=date&type=video&key={{$api_key}}",
        success : function(data) {
            data.items.forEach(function (element, index) {
                $('body').append('<div id="'+element.id.videoId+'" style="margin: 30px 30px;">' +
                    '<p align="middle"><iframe width="560" height="315" src="https://www.youtube.com/embed/' +
                    element.id.videoId +
                    '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; ' +
                    'encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></p>' +
                    '<table id="'+element.id.videoId+'_comment" class="table table-bordered">' +
                    '<tr>' +
                    '<th>댓글 ID</th>' +
                    '<th>닉네임</th>' +
                    '<th>댓글 내용</th>' +
                    '</tr>'+
                    '</table>' +
                    '</p>');
                var videoId = element.id.videoId;
                $.ajax({
                    type : "GET",
                    dataType : "json",
                    url : "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&key={{$api_key}}"+
                        "&videoId="+videoId+"&maxResults=100",
                    success : function(data) {
                        data.items.forEach(function (element, index) {
                            $('#'+videoId+'_comment').append(
                                '<tr>' +
                                '<td>'+element.etag+'</td>' +
                                '<td>'+element.snippet.topLevelComment.snippet.authorDisplayName+'</td>' +
                                '<td>'+element.snippet.topLevelComment.snippet.textOriginal+'</td>' +
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
        },
        complete : function(data) {},
        error : function(xhr, status, error) {}
    });
</script>
@else
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
            url : "https://www.googleapis.com/youtube/v3/search?part=id&channelId={{$channel_id}}"+
                "&maxResults=4&order=date&type=video&key={{$api_key}}",
            success : function(data) {
                $('body').append('<div id="{{$video_id}}" style="margin: 30px 30px;">' +
                    '<p align="middle"><iframe width="560" height="315" src="https://www.youtube.com/embed/{{$video_id}}"' +
                    '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; ' +
                    'encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></p>' +
                    '<table id="{{$video_id}}_comment" class="table table-bordered"' +
                    '<tr>' +
                    '<th>댓글 ID</th>' +
                    '<th>닉네임</th>' +
                    '<th style="width:50%;">댓글 내용</th>' +
                    '</tr>'+
                    '</table>' +
                    '</div>');
                $.ajax({
                    type : "GET",
                    dataType : "json",
                    url : "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&key={{$api_key}}"+
                        "&videoId={{$video_id}}&maxResults=20",
                    success : function(data) {
                        data.items.forEach(function (element, index) {
                            $('#{{$video_id}}_comment').append(
                                '<tr>' +
                                '<td>'+element.etag+'</td>' +
                                '<td>'+element.snippet.topLevelComment.snippet.authorDisplayName+'</td>' +
                                '<td>'+element.snippet.topLevelComment.snippet.textOriginal+'</td>' +
                                '</tr>');
                        });
                    },
                    complete : function(data) {},
                    error : function(xhr, status, error) {}
                });
            },
            complete : function(data) {},
            error : function(xhr, status, error) {}
        });
    </script>
@endif
</html>
