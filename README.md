#用法：
======================

你先需要在你的模板里这样设置：

layout.html

```

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>{{ $page_title }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.pjax/1.9.6/jquery.pjax.min.js"></script>
  </head>
  <body>
    <div class="main-content" id="pjax-container">
      <!-- 内容主体区域 -->
      @yield('main-content')
    </div>

    <!-- JavaScripts -->
    <script>
        $(document).pjax('a', '#pjax-container', {
            type: 'post'
        });
        $(document).on("pjax:timeout", function(event) {
            // 阻止超时导致链接跳转事件发生
            event.preventDefault()
        });
    </script>
  </body>
</html>


```

index.html

```
@extends('front.layout')

@section('main-content')
<a data-pjax href="/test">test</a>
@endsection

```

然后在你的/test路由处加上pjax中间件即可:

```
front.test:
  path: '/test'
  defaults:
    _controller: '\Hunter\front\Controller\FrontController::test'
    _title: 'test'
  requirements:
    _permission: 'pjax'

```

更多高级设置请参看：https://github.com/defunkt/jquery-pjax
