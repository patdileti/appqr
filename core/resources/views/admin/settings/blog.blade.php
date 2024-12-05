<div class="tab-pane" id="quick_blog">
    <form class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" method="POST">
        <div class="card">
            <div class="card-header">
                <h5>{{ ___('Blog Settings') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            {{quick_switch(___('Blog'), 'blog_enable', @$settings->blog_enable == '1')}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            {{quick_switch(___('Show Blog On Home Page'), 'show_blog_home', @$settings->show_blog_home == '1')}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            {{ quick_switch(___('Blog Commenting'), 'blog_comment_enable', @$settings->blog_comment_enable == '1')}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            {{ quick_switch(___('Blog Banner Image'), 'blog_banner', @$settings->blog_banner == '1')}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label" for="blog_page_limit">{{___('Number of Blogs on blog page')}}</label>
                            <input name="blog_page_limit" id="blog_page_limit" type="number" class="form-control" value="{{$settings->blog_page_limit ?? 8}}">
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label" for="blog_comment_approval">{{ ___('Comment Approval') }} *</label>
                        <select name="blog_comment_approval" id="blog_comment_approval" class="form-control">
                            <option value="1" {{ ($settings->blog_comment_approval == "1")? "selected" : "" }}>{{ ___('Disable Auto Approve Comments') }}</option>
                            <option value="2" {{ ($settings->blog_comment_approval == "2")? "selected" : "" }}>{{ ___('Auto Approve Login Users Comments') }}</option>
                            <option value="3" {{ ($settings->blog_comment_approval == "3")? "selected" : "" }}>{{ ___('Auto Approve All Comments') }}</option>
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label" for="blog_comment_user">{{ ___('Who Can Comment') }} *</label>
                        <select name="blog_comment_user" id="blog_comment_user" class="form-control">
                            <option value="1" {{ ($settings->blog_comment_user == "1")? "selected" : "" }}>{{ ___('Everyone') }}</option>
                            <option value="0" {{ ($settings->blog_comment_user == "0")? "selected" : "" }}>{{ ___('Only Login Users') }}</option>
                        </select>
                        <small class="form-text">{{ ___('Non-login users have to enter their name and email address.') }}</small>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="blog_settings" value="1">
                <button type="submit" class="btn btn-primary">{{ ___('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
