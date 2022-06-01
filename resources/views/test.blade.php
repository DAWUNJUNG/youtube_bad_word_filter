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
<button type="button" id="oauth">로그인</button>
</body>
{{--    js     --}}
{{-- jquery --}}
<script src="{{ asset('js/jquery/jquery-3.6.0.min.js') }}" type="text/javascript"></script>

{{-- bootstrap --}}
<script src="{{ asset('js/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

{{-- google --}}
<script src="https://apis.google.com/js/client.js?onload=googleApiClientReady"></script>
<script>
    const oauth2Client = new google.auth.OAuth2(
      "3147257063-aukh6rntabnh969v4k35lc9ni2bt31fc.apps.googleusercontent.com",
      "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",
      "http://localhost:8000/test"
    );
    const scopes = [
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.force.ssl',
        'https://www.googleapis.com/auth/youtube.readonly',
        'https://www.googleapis.com/auth/youtubepartner'
    ];

    async function authenticate(){
        return new Promise((resolve, reject) => {
            const authorizeUrl = oauth2Client.generateAuthUrl({
                access_type:'offline',
                scope:scopes
            });
            const server = http.createServer(async (req,res)=>{
                try {
                    if (req.url.indexOf('/api/oauth2callback') > -1){
                        const qs = new url.URL(req.url, 'http://localhost:8000').searchParams;
                        server.destroy();

                        resolve(qs.get('code'));
                    }
                } catch (e) {
                    reject(e);
                }
            }).listen(8000, () => {
                opn(authorizeUrl, {
                    wait: false
                }).then(cp => cp.unref());
            });
            destroyer(server);
        });
    }
    async function runSample(code){
        console.log(code);
    }
</script>
</html>
