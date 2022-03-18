<?php

namespace App\Http\Controllers;

use App\Models\users;
use App\http\Libraries\BaseApi;
use Illuminate\Http\Request;
use SebastianBergmann\Environment\Console;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //disini hanya perlu menggunakan BaseApi yg sebelumnya dibuat
        //hanya tinggal menambahkan endpoint yg akan digunakan yaitu '/user'
        $api = new BaseApi;
        $users = $api->index('/user');
        $pages = ceil($users['total'] / $users['limit']);

        $datass = [];
        for ($i = 1; $i < $pages; $i++) {
            array_push($datass, $api->index('/user', ['page' => $i])['data']);
        }

        $datas = [];
        for ($i = 0; $i < count($datass); $i++) {
            foreach ($datass[$i] as $value) {
                array_push($datas, $value);
            }
        }

        return view('user.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        for ($i=0; $i < 100 ; $i++) { 
            $request['nama_depan'] = 'firstName ke '.$i;
            $request['nama_belakang'] = 'firstName ke '.$i;
            $request['email'] = 'email_ke_'.$i.'@gmail.com';
        
            $payload = [
                'firstName' => $request->input('nama_depan'),
                'lastName' => $request->input('nama_belakang'),
                'email' => $request->input('email'),
            ];
            $baseApi = new BaseApi;
            $response = $baseApi->create('/user/create', $payload);
        }
				// handle jika request API nya gagal
        // diblade nanti bisa ditambahkan toast alert
        if ($response->failed()) {
            $errors = $response->json('data');
            $message = '';
            foreach($errors as $error) {
                $message.=' '.$error;
            };

            return redirect()->back()->with('messageF', 'Data gagal dibuat, '.$message);
        }

        return redirect()->back()->with('messageS', 'Data berhasil dibuat');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\users  $users
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit(users $users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\users  $users
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
            //kalian bisa coba untuk dd($response) untuk test apakah api nya sudah benar atau belum
        //sesuai documentasi api detail user akan menshow data detail seperti `email` yg tidak dimunculkan di api list index
        $response = (new BaseApi)->detail('/user', $id);
        return view('user.edit')->with([
            'user' => $response->json()
        ]);
    }

    public function update(Request $request, $id)
    {   
        //column yg bisa di update sesuai dengan documentasi dummyapi.io hanyalah
        // `fisrtName`, `lastName`
        $payload = [
            'firstName' => $request->input('nama_depan'),
            'lastName' => $request->input('nama_belakang'),
        ];

        $response = (new BaseApi)->update('/user', $id, $payload);
        if ($response->failed()) {

            return redirect('users')->with('messageF','Data gagal diperbarui');
        }

        return redirect('users')->with('messageS','Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\users  $users
     * @return \Illuminate\Http\Response
     */public function destroy(Request $request, $id)
    {
        
        $response = (new BaseApi)->delete('/user', $id);

        if ($response->failed()) {
            return redirect('users')->with('messageF','Data gagal dihapus');
        }
        return redirect('users')->with('messageS','Data berhasil dihapus');
    }
}
