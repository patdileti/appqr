<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Validator;

class BlogController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        abort_if(!config('settings.blog_enable'), 404);
        $this->activeTheme = active_theme();
    }

    /**
     * Display the page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $page_limit = config('settings.blog_page_limit', 8);

        if ($search = request()->input('search')) {
            $blogs = Blog::with('user')->where('status', 'publish')
                ->where('title', 'like', '%' . $search . '%')
                ->OrWhere('tags', 'like', '%' . $search . '%')
                ->orderbyDesc('id')
                ->paginate($page_limit);

            $blogs->appends(['search' => $search]);
        } else {
            $blogs = Blog::with('user')->where('status', 'publish')->orderbyDesc('id')->paginate($page_limit);
        }

        return view($this->activeTheme . '.blog.index', compact('blogs'));
    }

    /**
     * Display single blog page
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function single($id)
    {
        $blog = Blog::where('status', 'publish')->findOrFail($id);
        $blog->tags = explode(',', $blog->tags);

        $blogComments = BlogComment::with(['replies' => function ($q) {
            $q->where('active', '1')
                ->orderbyDesc('created_at');
        }])
            ->where('blog_id', $blog->id)
            ->where('active', '1')
            ->where('parent', 0)
            ->orderbyDesc('created_at')
            ->paginate(20);

        return view($this->activeTheme . '.blog.single', compact(
            'blog',
            'blogComments',
        ));
    }

    /**
     * Display the tag page
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function tag($slug)
    {
        $page_limit = config('settings.blog_page_limit', 8);

        $blogs = Blog::with('user')
            ->where('tags', 'like', '%' . $slug . '%')
            ->orderbyDesc('id')
            ->paginate($page_limit);
        $title = ___('Tag: :tag_name', ['tag_name' => $slug]);

        return view($this->activeTheme . '.blog.index', compact(
            'title',
            'blogs'
        ));
    }

    /**
     * Display the category page
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function category($slug)
    {
        $page_limit = config('settings.blog_page_limit', 8);
        $category = BlogCategory::where('slug', $slug)->first();
        if ($category) {
            $blogs = $category->blogs()->with('user')->orderbyDesc('id')->paginate($page_limit);
            $title = ___('Category: :category_name', ['category_name' => $category->title]);

            return view($this->activeTheme . '.blog.index', compact(
                'title',
                'blogs'
            ));
        } else {
            abort(404);
        }
    }

    /**
     * Handle comment
     *
     * @param Request $request
     * @param $slug
     */
    public function comment(Request $request, $id)
    {
        /* Check if comment enabled */
        if (config('settings.blog_comment_enable')) {

            $rules = [
                'comment' => ['required', 'string'],
            ];
            if (!auth()->check()) {
                $rules['user_name'] = ['required', 'string'];
                $rules['user_email'] = ['required', 'email'];
            }

            $request->validate($rules + validate_recaptcha());

            $blog = Blog::where('status', 'publish')->findOrFail($id);

            if (auth()->check()) {
                $user_id = $request->user()->id;
                $name = $request->user()->name;
                $email = $request->user()->email;
            } else {
                $user_id = null;
                $name = $request->user_name;
                $email = $request->user_email;
            }

            if ($user_id && $request->user()->isAdmin()) {
                $approve = '1';
            } else {
                if (config('settings.blog_comment_approval') == 1) {
                    $approve = '0';
                } else if (config('settings.blog_comment_approval') == 2) {
                    if ($user_id) {
                        $approve = '1';
                    } else {
                        $approve = '0';
                    }
                } else {
                    $approve = '1';
                }
            }

            $create = BlogComment::create([
                'blog_id' => $blog->id,
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email,
                'comment' => $request->comment,
                'active' => $approve,
                'parent' => $request->comment_parent
            ]);

            if ($create) {

                /* add admin notification */
                create_notification(___('New comment received'), 'new_comment', route('admin.blog.comments.index'));

                $comment_success = $approve ? ___("Comment is posted.") : ___("Comment is posted, wait for the reviewer to approve.");
                quick_alert_success($comment_success);
                return back();
            }
        }

        quick_alert_error(___('Unexpected error'));
        return back();
    }
}
