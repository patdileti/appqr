@extends('admin.layouts.main')
@section('title', ___('Edit blog'))
@section('content')
    <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card p-2 mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Title') }} *</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ $blog->title }}" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Slug') }}</label>
                            <input type="text" name="slug" class="form-control" value="{{ $blog->slug }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Image') }} *</label>
                            <div class="d-flex align-items-start justify-content-between gap-4">
                                <div>
                                    <label for="upload" class="btn btn-primary mb-2" tabindex="0">
                                        <i class="fas fa-upload"></i>
                                        <span class="d-none d-sm-block ms-2">{{ ___('Upload Image') }}</span>
                                        <input name="image" type="file" id="upload" hidden
                                               onchange="readURL(this,'uploadedImage')"
                                               accept="image/png, image/jpeg">
                                    </label>
                                    <p class="form-text mb-0">{{ ___('Allowed JPG, JPEG or PNG.') }}</p>
                                </div>
                                <img src="{{ asset('storage/blog/'.$blog->image) }}" alt=""
                                     class="d-block rounded" width="150" id="uploadedImage">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">{{ ___('Description') }} *</label>
                            <textarea name="description" rows="10" class="form-control tiny-editor">{{ $blog->description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-2 mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Status') }}</label>
                            <select name="status" id="status" class="form-control">
                                <option value="publish" @selected( $blog->status == "publish" )>{{ ___('Publish') }}</option>
                                <option value="pending" @selected( $blog->status == "pending" )>{{ ___('Pending') }}</option>
                            </select>
                            <span class="form-text text-muted">{{ ___('Select Pending if you want to hide this from the frontend') }}</span>
                        </div>
                        @php
                            $selectcats = [];
                            foreach($blog->categories as $category){
                                $selectcats[] = $category->id;
                            }
                        @endphp
                        <div class="mb-4">
                            <label class="form-label">{{ ___('Category') }} *</label>
                            <select name="category[]" class="form-control select2" multiple>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, $selectcats) ? 'selected="selected"' : '' }}>{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ ___('Tags') }}</label>
                            <textarea name="tags" rows="2" class="form-control"
                                      placeholder="{{ ___('Enter tags separated by comma') }}" required>{{ $blog->tags }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ ___('Submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "blog", "subpage": "blog-post"};
        </script>
    @endpush
    @push('scripts_vendor')
        <script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
    @endpush
@endsection
