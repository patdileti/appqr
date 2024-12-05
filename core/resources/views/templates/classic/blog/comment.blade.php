<li id="li-comment-{{$blogComment->id}}"
    @if($level > 1) class="children-{{$level}}" @endif>
    <div class="comments-box" id="comment-{{$blogComment->id}}">
        <div class="comments-avatar">
            <img
                src="{{ asset('storage/profile/'.($blogComment->user ? $blogComment->user->image : 'default_user.png')) }}"
                alt="{{$blogComment->name}}">
        </div>
        <div class="comments-text">
            <div class="avatar-name">
                <h5>{{$blogComment->name}}</h5>
                <span>{{ date_formating($blogComment->created_at) }}</span>
                @if($level < 3)
                    <a class="reply comments-reply comment-reply-link"
                       href="javascript:void(0)"
                       data-commentid="{{$blogComment->id}}"
                       data-postid="{{ $blog_id }}"
                       data-belowelement="comment-{{$blogComment->id}}"
                       data-respondelement="respond"><i
                            class="fa fa-reply"></i>{{ ___('Reply') }}</a>
                @endif
            </div>
            <p>{{$blogComment->comment}}</p>
        </div>
    </div>
</li>
@if($blogComment->replies)
    @foreach($blogComment->replies as $reply)
        @if($reply->active == 1)
            @include($activeTheme.'blog.comment', [
                'blog_id' => $blog_id,
                'blogComment' => $reply,
                'level' => $level + 1
            ])
        @endif
    @endforeach
@endif
