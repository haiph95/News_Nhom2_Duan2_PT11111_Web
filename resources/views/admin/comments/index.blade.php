@extends('layouts.admin')

@section('title', trans('admin.comments.index'))

@section('styles')
    {!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}
@endsection

@section('scripts')
    {!! Html::script('plugins/datatables/jquery.dataTables.min.js') !!}
    {!! Html::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
    <script type="text/javascript">
        $('.btn-status').click(function (e) {
            e.preventDefault();
            var btn = $(this);
            $.post(btn.attr('href'), {
                _token: window.Laravel.csrfToken,
                status: btn.attr('status'),
            }, function (data) {
                if (data.status) {
                    alert(data.messages);
                    window.location.reload();
                }
            });
        })
        /*$('.modal-dialog').on('click', '.btn-send-comment', function () {
            var content,
                parent_id,
                post_id,
                form = $(this).closest('.form'),
                send = function () {
                    $.post(form.find('#reply_url').val(), {
                        _token: window.Laravel.csrfToken,
                        post_id: post_id,
                        parent_id: parent_id,
                        content: content.replace(/\r\n|\r|\n/g, "<br/>"),
                    }, function (data) {
                        console.log(data);
                    });
                };
            return (content = form.find('textarea').val(), post_id = form.find('#user_id').val(), parent_id = form.find('#parent_id').val(), content.length == 0) ? (form.find(".form-group").addClass("has-error").find("label").html("Bạn chưa nhập nội dung ý kiến !"), !1) : content.length < 10 ? (form.find(".form-group").addClass("has-error").find("label").html("Nội dung ý kiến quá ngắn !"), !1) : content.length > 1e3 ? (form.find(".form-group").addClass("has-error").find("label").html("Nội dung ý kiến quá dài !"), !1) : (send(), !1)
        });*/
    </script>
@endsection

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('admin.comments.index')</h3>
        </div>
        <div class="box-body">
            <table class="table table-striped" id="list">
                <thead>
                    <tr>
                        <th>@lang('admin.comments.name')</th>
                        <th>@lang('admin.comments.content')</th>
                        <th>@lang('admin.comments.parent')</th>
                        <th>@lang('admin.comments.updatedat')</th>
                        <th>@lang('admin.comments.tool')</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($comments) > 0)
                        @foreach($comments as $comment)
                            @if($comment->parent_id == 0)
                                <tr id="{!! $comment->id !!}">
                                    <td>{!! $comment->name !!}</td>
                                    <td>
                                        {!! $comment->content !!}
                                        <p>
                                            <button class="btn btn-xs" data-toggle="modal" data-target="#reply-{!! $comment->id !!}">Trả lời</button>
                                        </p>
                                        <div id="reply-{!! $comment->id !!}" class="modal fade reply" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">Trả lời bình luận {!! str_limit($comment->content, 20) !!}</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form">
                                                            <input type="hidden" id="reply_url" value="{!! route('comments.store') !!}">
                                                            <input type="hidden" id="post_id" value="{!! $comment->post_id !!}">
                                                            <input type="hidden" id="parent_id" value="{!! $comment->id !!}">
                                                            <div class="form-group" style="width: 100%;">
                                                                <textarea name="content" id="content" cols="30"
                                                                          rows="5" class="content form-control" style="width: 100%;"></textarea>
                                                                <label for="content" class="control-label help-block">Nội dung</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                                                        <button type="button" class="btn-send-comment btn btn-info">Gửi</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{!! route('posts.edit', ['slug' => $comment->post_id]) !!}">{!! $comment->parent !!}</a>
                                    </td>
                                    <td><a href="{!! route('posts.getPost', ['slug' => $comment->post()->first()->slug]) !!}#{!! $comment->id !!}">{!! $comment->created_at !!}</a></td>
                                    <td>
                                        @if($comment->status)
                                            <a href="{!! route('comments.setstatus', ['id' => $comment->id]) !!}" status="0" class="btn-status">Ẩn</a>
                                        @else
                                            <a href="{!! route('comments.setstatus', ['id' => $comment->id]) !!}" status="1" class="btn-status">Hiện</a>
                                        @endif
                                        {!! link_to_route('comments.destroy', 'Xóa', ['comment' => $comment->id], ['class' => 'delete']) !!}
                                    </td>
                                </tr>
                                @foreach($comments as $sub)
                                    @if($sub->parent_id == $comment->id)
                                        <tr id="{!! $sub->id !!}">
                                            <td>{!! $sub->name !!}</td>
                                            <td>
                                                <p><a href="#{!! $sub->parent_id !!}">Trả lời tới {!! $comment->name !!}</a></p>
                                                <p>{!! $sub->content !!}</p>
                                            </td>
                                            <td>
                                                <a href="{!! route('posts.edit', ['slug' => $sub->post_id]) !!}">{!! $sub->parent !!}</a>
                                            </td>
                                            <td><a href="{!! route('posts.getPost', ['slug' => $sub->post()->first()->slug]) !!}#{!! $sub->id !!}">{!! $sub->created_at !!}</a></td>
                                            <td>
                                                @if($sub->status)
                                                    <a href="{!! route('comments.setstatus', ['id' => $sub->id]) !!}" status="0" class="btn-status">Ẩn</a>
                                                @else
                                                    <a href="{!! route('comments.setstatus', ['id' => $sub->id]) !!}" status="1" class="btn-status">Hiện</a>
                                                @endif
                                                {!! link_to_route('comments.destroy', 'Xóa', ['$sub' => $sub->id], ['class' => 'delete']) !!}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection