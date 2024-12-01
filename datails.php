<?php

require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($id == '' || $token == ''){
    echo 'ERROR AL CARGAR LA PAGINA';
    exit;
} else{

    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if ($token == $token_tmp){

$sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo=1");
$sql->execute([$id]);
if ($sql->fetchColumn() > 0) {

$sql = $con->prepare("SELECT nombre, descripcion, precio,descuento FROM productos WHERE id=? AND activo=1 LIMIT 1");
$sql->execute([$id]);
$row = $sql-> fetch(PDO::FETCH_ASSOC);
$nombre = $row['nombre'];
$descripcion = $row['descripcion'];
$precio = $row['precio'];
$descuento = $row['descuento'];
$precio_desc = $precio - (($precio * $descuento) / 100);
$dir_images = 'images/productos/'. $id . '/';

$rutaimg = $dir_images . 'principal.jpg';

if(!file_exists($rutaimg)){
    $rutaimg = 'images/no.photo.jpg';
}

$imagenes = array();
if(file_exists($dir_images)){

$dir = dir($dir_images);

while(($archivo = $dir->read()) != false){
    if($archivo != 'principal.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))){
        $imagenes[] = $dir_images . $archivo;
    }

    }
    $dir->close();
    }
    }else {
            echo 'Error al procesar la peticion';
            exit;
        }
    }
}
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
          </li>
        </ul>
        <a href="checkout.php" class="btn btn-primary">
        Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
        </a> 
    </div>
  </div>
</header>
<!--descripcion-->
<main>
  <div class="container">
    <div class="row">
        <div class="col-md-6 order.md-1">
        <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="<?php echo $rutaimg;?>" class="d-block w-100">
    </div>
    <?php foreach($imagenes as $img) { ?>
    <div class="carousel-item active">
    <img src="<?php echo $img;?>" class="d-block w-100">
    </div>
    <?php } ?>
  
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="carouselImages" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="carouselImages" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>




        </div>
        <div class="col-md-6 order.md-2">
            <h2><?php echo $nombre; ?></h2>

            <?php if($descuento > 0) { ?>
                <p><del><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></del></p>
            <h2>                
                <?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?>
                <small class="text-success"><?php echo $descuento; ?>% descuento</small>
            </h2>

            <?php } else { ?>

            

            <h2><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></h2>

            <?php } ?>

            <p class="lead">
                <?php echo $descripcion ?>
            </p>

            <div class="d-grip gap-3 col-10 mx-auto">
                <button class="btn btn-primary" type="button">COMPRAR AHORA</button>
                <button class="btn outline-primary" type="button" onclick="addProducto(<?php echo $id; ?>, '<?php echo $token_tmp; ?>')">AÑADIR AL CARRITO</button>

            </div>
        </div>
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
    formData.append('id', id)
    formData.append('token', token)

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