<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $articles = Article::all();
        $articles = Article::with(['user', 'tags'])->latest()->simplePaginate(); ///USE EAGER LOADING. HIGHLY RECOMENDED

        return view('articles.index', ['articles' => $articles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // $categories = Category::pluck('name', 'id');
        // $tags = Tag::pluck('name', 'id');
        return view('articles.create', $this->getFormData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        // dd($request);
        $article = Article::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'description' => $request->description,
            'status' => $request->status === "on",
            'user_id' => auth()->id(),
            'category_id' => $request->category_id
        ]);

        //ANOTHER PATTERN TO STORE

        // $article = Article::create([
        //     'slug' => Str::slug($request->title),
        //     'status' => $request->status === "on",
        //     'user_id' => auth()->id(),
        // ] + $request->validated());


        $article->tags()->attach($request->tags); ///THIS LINE IS FOR THE PAVA TABLE
        
        return redirect()->route('articles.index')->with('message', 'Article Has Successfully been Created!');

    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        return view('articles.edit', array_merge(compact('article'), $this->getFormData()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        // dd('test');

        $article->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'description' => $request->description,
            'status' => $request->status === "on",
            'user_id' => auth()->id(),
            'category_id' => $request->category_id
        ]);
        

        $article->tags()->sync($request->tags);

        return redirect()->route('dashboard')->with('message', 'Article has been updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('dashboard')->with('message', 'Article has been deleted successfully!');
    }


    private function getFormData()
    {
        $categories = Category::pluck('name', 'id');
        $tags = Tag::pluck('name', 'id');

        return compact('categories', 'tags');
    }
}
