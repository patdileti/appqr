@extends('admin.layouts.main')
@section('title', ___('Add New Blog'))
@section('content')
    <form id="quick-submitted-form" action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Title') }} *</label>
                            <input type="text" name="title" id="create_slug" class="form-control" value="{{ old('title') }}" required autofocus />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Slug') }}</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" />
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
                                <img src="" alt=""
                                     class="d-block rounded" width="150" id="uploadedImage">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">{{ ___('Content') }} *</label>
                            <textarea name="description" rows="10" class="form-control tiny-editor">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Status') }}</label>
                            <select name="status" id="status" class="form-control">
                                <option value="publish" @if (old('status') == "publish") selected @endif>{{ ___('Publish') }}</option>
                                <option value="pending" @if (old('status') == "pending") selected @endif>{{ ___('Pending') }}</option>
                            </select>
                            <span class="form-text text-muted">{{ ___('Select Pending if you want to hide this from the frontend') }}</span>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ ___('Category') }} *</label>
                            <select name="category[]" class="form-control select2" multiple>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">{{ ___('Tags') }}</label>
                            <textarea name="tags" rows="2" class="form-control"
                                      placeholder="{{ ___('Enter tags separated by comma') }}" required>{{ old('tags') }}</textarea>
                        </div>
                        <button class="btn btn-primary">{{ ___('Submit') }}</button>
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
