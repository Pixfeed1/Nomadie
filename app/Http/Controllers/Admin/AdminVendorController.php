<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminVendorController extends Controller
{
    public function index() { return view('admin.vendors.index'); }
    public function pending() { return view('admin.vendors.pending'); }
    public function approved() { return view('admin.vendors.approved'); }
    public function rejected() { return view('admin.vendors.rejected'); }
    public function suspended() { return view('admin.vendors.suspended'); }
    public function show($id) { return view('admin.vendors.show'); }
    public function approve($id) { return redirect()->back(); }
    public function reject($id) { return redirect()->back(); }
    public function suspend($id) { return redirect()->back(); }
    public function activate($id) { return redirect()->back(); }
    public function destroy($id) { return redirect()->back(); }
}