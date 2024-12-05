<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Language;
use Illuminate\Http\Request;
use Validator;

class FaqController extends Controller
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

            $faqs = Faq::with('language')->where('translation_lang', $getlang->code)
                ->OrWhere('translation_lang', null)
                ->get();
            $current_language = $getlang->code;

            return view('admin.faqs.index', compact('faqs', 'current_language'));
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
        return view('admin.faqs.create');
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
            'faq_title' => ['required', 'string', 'max:255'],
            'faq_content' => ['required', 'string'],
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

        $faq = Faq::create([
            'faq_title' => $request->faq_title,
            'faq_content' => $request->faq_content,
            'translation_lang' => $request->translation_lang,
            'active' => $request->active
        ]);
        if ($faq) {
            $result = array('success' => true, 'message' => ___('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Faq  $faq
     */
    public function show(Faq $faq)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faq $faq)
    {
        $validator = Validator::make($request->all(), [
            'faq_title' => ['required', 'string', 'max:255'],
            'faq_content' => ['required', 'string'],
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

        $response = $faq->update([
            'faq_title' => $request->faq_title,
            'faq_content' => $request->faq_content,
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
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();
        quick_alert_success(___('Deleted Successfully'));
        return back();
    }
}
