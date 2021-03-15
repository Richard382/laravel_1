<?php

namespace App\Http\Controllers;

use App\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('pages.template', [
            'title' => $page->title,
            'content' => $page->content,
        ]);
    }
}
