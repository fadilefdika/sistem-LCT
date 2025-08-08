<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Routing\Controller;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('updated_at', 'desc')->get();
        return view('pages.admin.master-data.email.index', compact('templates'));
    }


    public function create()
    {
        return view('pages.admin.master-data.email.edit');
    }
    
    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('pages.admin.master-data.email.edit', compact('template'));
    }
    

    public function store(Request $request)
    {
        dd($request->all());
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:EmailTemplates,slug',
            'content' => 'required|string',
        ]);

        EmailTemplate::create($request->only(['name', 'slug', 'content']));

        return redirect()->route('ehs.master-data.email.index')->with('success', 'Template berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:EmailTemplates,slug,' . $id,
            'content' => 'required|string',
        ]);

        $template->update($request->only(['name', 'slug', 'content']));

        return redirect()->route('ehs.master-data.email.index')->with('success', 'Template berhasil diperbarui.');
    }


    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('ehs.master-data.email.index')->with('success', 'Template berhasil dihapus.');
    }
}

