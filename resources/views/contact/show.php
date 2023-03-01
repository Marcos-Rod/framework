<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de contacto</title>
</head>

<body>
    <h1>Detalle de contacto</h1>

    <a href="/contact/<?= $contact['id'] ?>/edit">Editar</a>

    <p>Nombre: <?= $contact['name'] ?></p>
    <p>Correo: <?= $contact['email'] ?></p>
    <p>Telefono: <?= $contact['phone'] ?></p>

    <form action="/contact/<?= $contact['id'] ?>/delete" method="post">
        <button>Eliminar</button>
    </form>

</body>

</html>