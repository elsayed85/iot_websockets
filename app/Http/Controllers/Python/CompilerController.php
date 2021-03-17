<?php

namespace App\Http\Controllers\Python;

use App\Http\Controllers\Controller;
use App\python\LaravelPython;
use Debugbar;
use Illuminate\Http\Request;

class CompilerController extends Controller
{
    public function run($file, $prameters)
    {
        $service = new LaravelPython();
        try {
            return $service->run(public_path($file), $prameters);
        } catch (\Throwable $th) {
            Debugbar::addThrowable($th);
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function htmlResponse($response)
    {
        echo "<pre>";
        print_r($response);
        echo "</pre>";
    }

    public function NeedlemanWunsch(Request $request)
    {
        $request->validate([
            'NeedlemanWunsch_Input' => ['required'],
            'pam250' => ['required']
        ]);

        $result = $this->run('storage/python_scripts/NeedlemanWunsch.py', [
            public_path("storage/uploads/{$request->NeedlemanWunsch_Input}"),
            public_path("storage/uploads/{$request->pam250}"),
        ]);

        return $this->htmlResponse($result);
    }

    public function Nussinov(Request $request)
    {
        $request->validate([
            'Nussinov_Input' => ['required'],
        ]);

        $result = $this->run('storage/python_scripts/Nussinov.py', [
            public_path("storage/uploads/{$request->Nussinov_Input}"),
        ]);

        return $this->htmlResponse($result);
    }
}
