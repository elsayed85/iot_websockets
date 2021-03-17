<?php

namespace App\Http\Controllers\Python;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function uploadFile(Request $request)
    {
        $request->validate([
            'files' => ['required']
        ]);

        dd($request->all());

        $file_name = time() . '_' . $request->file->getClientOriginalName();
        $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
        return response()->json(['success' => 'File uploaded successfully.', 'file_path' => $file_path]);
    }
}
