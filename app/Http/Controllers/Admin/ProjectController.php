<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Type;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = Type::all();

        return view('admin.projects.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        $new_project = new Project();
        $new_project->fill($data);
        $new_project->slug = Str::slug($new_project->title, '-');
        // controllo che crea la proprietà picture solo se è settata
        if (isset($data['picture'])) {
            $new_project->picture = Storage::disk('public')->put('uploads', $data['picture']);
        }
        $new_project->save();

        return redirect()->route('admin.projects.index')->with('message', "Il progetto $new_project->title è stato creato con successo");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        $old_title = $project->title;

        $project->slug = Str::slug($data['title'], '-');

        // controllo che verifica se è presente l'immagine e la cancella di default se già inserita
        if (isset($data['picture'])) {
            if ($project->picture) {
                Storage::disk('public')->delete($project->picture);
            }
            $data['picture'] = Storage::disk('public')->put('uploads', $data['picture']);
        }

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('message', "Il progetto $old_title è stato aggiornato!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $old_title = $project->title;

        if ($project->picture) {
            Storage::disk('public')->delete($project->picture);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('message', "Il progetto $old_title è stato cancellato!");
    }
}
