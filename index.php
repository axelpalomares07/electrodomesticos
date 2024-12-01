<?php

require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id,nombre,precio FROM productos WHERE activo=1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

//print_r($_SESSION);

//session_destroy();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online.</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" 
    crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
</head>
<body>
<header>
  <div class="collapse bg-dark" id="navbarHeader">
    <div class="container">
      <div class="row">
        <div class="col-sm-4 offset-md-1 py-4">
          <h4 class="text-white">Contactanos</h4>
          <ul class="list-unstyled">
            <li><a href="#" class="text-white">Sigueme en Instagram</a></li>
            <li><a href="#" class="text-white">Sigueme en Facebook</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a href="index.php" class="navbar-brand d-flex align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" aria-hidden="true" class="me-2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        <strong>Tienda Online</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarHeader">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
          <a href="#" class="nav-link active">Catalogo</a>
          </li>

          <li class="nav-item">
          <a href="#" class="nav-link active">Sugerencias</a>
          <li><a href="registro.php" class="btn btn-primary">Registro usuarios</a></li>
          <li><a href="login.php" class="btn btn-primary">Inicio de Sesion</a></li>
          </li>
        </ul>
        <a href="checkout.php" class="btn btn-primary">
        Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
        </a> 
    </div>
  </div>
</header>
<!--productos-->
<main>
  <div class="container" class="d-block w-100">
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <?php foreach($resultado as $row) { ?>
        <div class="col">
          <div class="card shadow-sm">s
            <?php

              $id = $row['id'];
              $imagen = "images/productos/" .$id. "/principal.jpg";

              if(!file_exists($imagen)) {
                $imagen = "images/no-photo.jpg";
              }
            ?>
            <img src="<?php echo $imagen; ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
              <p class="card-text"><?php echo number_format($row['precio'], 2, '.', ',');?></p>
                 <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <a href="datails.php?id=<?php echo $row['id']; ?>&token=<?php echo hash_hmac('sha1',$row['id'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>   
                </div>
                <button class="btn outline-success" type="button" onclick="addProducto(<?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1',$row['id'], KEY_TOKEN); ?>')">AÑADIR AL CARRITO</button>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script>
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  function addProducto(id, token){
    let url='clases/carrito.php'
    let formData = new FormData()
    formData.append('id', id )
    formData.append('token', token )

    fetch(url, {
      method: 'POST',
      body: formData,
      mode: 'cors'
    }).then(response => response.json())
    .then(data => {
      if(data.ok){
        let elemento = document.getElementById("num_cart")
        elemento.innerHTML = data.numero
      }
    })
  }
</script>


</body>
</html>