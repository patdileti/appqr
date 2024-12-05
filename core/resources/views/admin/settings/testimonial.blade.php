<div class="tab-pane" id="quick_testimonial">
    <form class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" method="POST">
        <div class="card">
            <div class="card-header">
                <h5>{{ ___('Testimonials Settings') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    {{quick_switch(___('Testimonials'), 'testimonials_enable', @$settings->testimonials_enable == '1')}}
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            {{quick_switch(___('Show On Home Page'), 'show_testimonials_home', @$settings->show_testimonials_home == '1')}}
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            {{quick_switch(___('Show On Blog Page'), 'show_testimonials_blog', @$settings->show_testimonials_blog == '1')}}
                        </div>
                    </div>
                </div>


            </div>
            <div class="card-footer">
                <input type="hidden" name="testimonial_settings" value="1">
                <button type="submit" class="btn btn-primary">{{ ___('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
