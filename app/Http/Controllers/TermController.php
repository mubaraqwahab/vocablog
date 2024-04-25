<?php

namespace App\Http\Controllers;

use App\Models\Lang;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var User */
        $user = $request->user();
        $terms = Term::with("lang")
            ->withCount("definitions")
            ->whereBelongsTo($user)
            ->paginate();

        return view("terms.index", ["terms" => $terms]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $langs = Lang::query()->orderBy("name", "asc")->get();
        return view("terms.create", ["langs" => $langs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Term $term)
    {
        return view("terms.show", ["term" => $term]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Term $term)
    {
        return view("terms.edit", ["term" => $term]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Term $term)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Term $term)
    {
        //
    }
}
