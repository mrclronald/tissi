<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function uploadSheet()
    {
        $fileSheet = Input::file('transactions');
        $fileSheetName = $this->doSomethingLikeUpload($fileSheet);

        return public_path('transactions') . '/' . $fileSheetName;
    }

    private function doSomethingLikeUpload($fileSheet)
    {
        $fileSheetName = time() . '.' . $fileSheet->getClientOriginalExtension();
        $fileSheet->move(public_path('transactions'), $fileSheetName);

        return public_path('transactions') . '/' . $fileSheetName;
    }

}