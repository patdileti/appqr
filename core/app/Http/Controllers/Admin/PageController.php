<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Validator;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('lang')) {
            $getlang = Language::where('code', $request->lang)->firstOrFail();
            $current_language = $getlang->code;

            $pages = Page::with('language')->where('translation_lang', $getlang->code)
                ->OrWhere('translation_lang', null)
                ->get();

            return view('admin.pages.index', compact('pages', 'current_language'));
        } else {
            return redirect(url()->current() . '?lang=' . env('DEFAULT_LANGUAGE'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_title' => ['required', 'min:2', 'max:255'],
            'page_content' => ['required', 'min:5'],
            'slug' => ['nullable', 'unique:pages', 'alpha_dash'],
            'type' => ['required'],
            'translation_lang' => ['nullable', 'string',  'exists:languages,code'],
            'active' => ['required'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $create = Page::create([
            'title' => $request->page_title,
            'content' => $request->page_content,
            'slug' => !empty($request->slug)
                ? $request->slug
                : SlugService::createSlug(Page::class, 'slug', $request->page_title),
            'type' => $request->type,
            'translation_lang' => $request->translation_lang,
            'active' => $request->active
        ]);

        if ($create) {
            $result = array('success' => true, 'message' => ___('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        $validator = Validator::make($request->all(), [
            'page_title' => ['required', 'min:2', 'max:255'],
            'page_content' => ['required', 'min:5'],
            'slug' => ['nullable', 'alpha_dash', 'unique:pages,slug,' . $page->id],
            'type' => ['required'],
            'translation_lang' => ['nullable', 'string',  'exists:languages,code'],
            'active' => ['required'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $response = $page->update([
            'title' => $request->page_title,
            'content' => $request->page_content,
            'slug' => !empty($request->slug)
                ? $request->slug
                : SlugService::createSlug(Page::class, 'slug', $request->page_title),
            'type' => $request->type,
            'translation_lang' => $request->translation_lang,
            'active' => $request->active
        ]);

        if ($response) {
            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->delete();
        quick_alert_success(___('Deleted Successfully'));
        return back();
    }
}
