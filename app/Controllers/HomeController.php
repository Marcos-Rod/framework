<?php

namespace App\Controllers;

use App\Models\Contact;

class HomeController extends Controller
{
    public function index()
    {
        $contactModel = new Contact();

        return $contactModel->where("name", "User")->get();
        
        return $this->view('home', [
            'title' => 'home',
            'description' => 'descripcion de la pag'
        ]);
    }
}
