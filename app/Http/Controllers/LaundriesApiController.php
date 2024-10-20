<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Binatu;

class LaundriesApiController extends Controller
{
    public function index(){
        $laundries = Binatu::all();
        return response()->json(['message' =>'Success','data' => $laundries]);
    }

    public function show($id){
        $laundry = Binatu::find($id);
        return response()->json(['message' => 'Success','data' => $laundry]);
    }
    public function store(Request $request){
        
        if (!$request->has('binatus')) {
            return redirect()->back()->with('error', 'Data binatus tidak ditemukan.'); // Kembali ke halaman sebelumnya dengan pesan error
        }
    
        // Ambil data binatus dari request
        $binatusData = $request->input('binatus');
    
        // Pastikan semua data memiliki struktur yang sama
        foreach ($binatusData as $data) {
            // Insert data ke database
            Binatu::create($data);
        }
        $response_data = Binatu::all();
        return response()->json(['message' =>'Success','data'=> $response_data]);
    }
    public function update(Request $request,$id) {
        $laundry = Binatu::find($id);
        $laundry->update($request->all());
        return response()->json(['message' =>'Success','data'=> $laundry]);
    }
    public function destroy($id) {
        $laundry = Binatu::find($id);
        $laundry->delete();
        return response()->json(['message' =>'Success','data'=> null]);
    }
}
