<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bar;
use Illuminate\Http\Request;

class BarController extends Controller
{
    public function index(){
        $bars = Bar::all();
        return view('admin.bars.index', compact('bars'))->with(['catName'=>'bars']);
    }

    public function create(){
        return view('admin.bars.create')->with(['catName'=>'bars']);
    }

    public function store(Request $request){
        $request->validate(['name'=>'required|unique:bars,name']);
        $bar = Bar::create($request->all());
        return redirect()->route('bars.index')->with('success','Bar created');
    }

    public function edit(Bar $bar){
        return view('admin.bars.edit', compact('bar'))->with(['catName'=>'bars']);
    }

    public function update(Request $request, Bar $bar){
        $request->validate(['name'=>'required|unique:bars,name,'.$bar->id]);
        $bar->update($request->all());
        return redirect()->route('bars.index')->with('success','Bar updated');
    }

    public function destroy(Bar $bar){
        $bar->delete();
        return redirect()->route('bars.index')->with('success','Bar deleted');
    }
}
