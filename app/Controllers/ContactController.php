<?php

namespace App\Controllers;

use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $model = new Contact;
        
        if(isset($_GET['search'])){
            $contacts = $model->where('name', 'LIKE', '%' . $_GET['search'] . '%')->paginate(10);
        } else {
            $contacts = $model->paginate(10);
        }

        return $this->view('contact.index', compact('contacts'));
    }

    public function create()
    {
        return $this->view('contact.create');
    }

    public function store()
    {
        //Retornar parametros
        $data = $_POST;
        $model = new Contact;

        $model->create($data);

        return $this->redirect('/contacts');
    }

    public function show($id)
    {
        $model = new Contact;

        $contact = $model->find($id);

        return $this->view('contact.show', compact('contact'));
    }

    public function edit($id)
    {
        $model = new Contact;

        $contact = $model->find($id);

        return $this->view('contact.edit', compact('contact'));
    }

    public function update($id)
    {
        $data = $_POST;
        $model = new Contact;
        $model->update($id, $data);

        return $this->redirect("/contact/$id");
    }

    public function destroy($id)
    {
        $model = new Contact;
        $model->delete($id);

        return $this->redirect('/contacts');
    }
}
