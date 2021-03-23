<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $data = Contact::orderBy('name', 'ASC')->get();
        return respondWithData(true, 200, 'Daftar Kontak', $data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|string',
        ]);


        $save = Contact::create([
            'name'  => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            ]);

        if ($save) {
            return respondWithMessage("Data berhasil disimpan....", true, 200);
        }else{
            return respondWithMessage("Data gagal disimpan....", false, 409);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Contact::find($id);
        
        return respondWithData(true, 200, 'Data Kontak', $data);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [ 
            'name'  => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);
      
        if ($validator->fails()) {
          return response()->json($validator->errors(), 400);
        }

        $data = Contact::find($id);
        $data->name  = $request->input('name');
        $data->email = $request->input('email');
        $data->phone = $request->input('phone');
        $data->save();

        if ($data) {
            return respondWithMessage("Data berhasil diperbaharui....", true, 200);
        }else{
            return respondWithMessage("Data gagal diperbaharui....", false, 409);
        }

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Contact::find($id);
        $data->delete();

        if ($data) {
            return respondWithMessage("Data berhasil dihapus....", true, 200);
        }else{
            return respondWithMessage("Data gagal dihapus....", false, 409);
        }
    }
}