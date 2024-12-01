<?php

require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

//print_r($_SESSION);

$lista_carrito = array();

if ($productos != null) {
  foreach ($productos as $clave => $cantidad) {
      $sql = $con->prepare("SELECT id, nombre, precio, descuento, :cantidad AS cantidad 
                            FROM productos 
                            WHERE id=:id AND activo=1");
      $sql->execute(['id' => $clave, 'cantidad' => $cantidad]);
      $resultado = $sql->fetch(PDO::FETCH_ASSOC);

      if ($resultado) {
          $lista_carrito[] = $resultado;
      }
  }
}

if ($lista_carrito == null) {
  echo '<tr><td colspan="5" class="text-center"><b>Lista Vacía</b></td></tr>';
}




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
      <a href="#" class="navbar-brand d-flex align-items-center">
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
        <a href="carrito.php" class="btn btn-primary">
        Carrito <span id="num_cart" class="badge bg-secundary"><?php echo $num_cart; ?></span>
        </a> 
    </div>
  </div>
</header>
<!--productos-->
<main>
  <div class="container" class="d-block w-100">
    <div class= "table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Catidad</th>
            <th>Subtotal</th>
            <th>Metodos de Pago</th>
          </tr>
        </thead>
        <thody>
          <?php if($lista_carrito == null){
            echo '<tr><td colspan="5" class="text-center"><b>Lista Vacia</b></td></tr>';
          } else {
            $total = 0;
            foreach($lista_carrito as $producto){
              $_id = $producto['id'];
              $nombre = $producto['nombre'];
              $precio = $producto['precio'];
              $descuento = $producto['descuento'];
              $cantidad = $producto['cantidad'];
              $precio_desc = $precio - (($precio * $descuento) / 100);
              $subtotal = $cantidad * $precio_desc;
              $total += $subtotal; 
          ?>
          <tr>
            <td><?php echo $nombre; ?></td>
            <td><?php echo MONEDA . number_format($precio_desc,2, '.', ','); ?></td>
            <td>
              <input type="number" min="1" max="10" step="1" value="<?php echo $cantidad ?>" size="5" id="cantidad_<?php echo $_id; ?>" onchange="actualizaCantidad(this.value, <?php echo $_id; ?>)">
            </td>
            <td>
              <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]"><?php echo MONEDA . number_format($subtotal,2, '.', ','); ?></div>
            </td>
            <td><a href="#" id="eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo $_id; ?>" data-bs-toogle="modal" data-bs-target="eliminaModal">Eliminar</a></td>
          </tr>
          <?php } ?>
          <tr>
              <td colspan="3"></td>
              <td colspan="2">
                <p class="h3" id="total"><?php echo MONEDA . number_format($total, 2, '.', ','); ?></p>
              </td>
          </tr>
        </thody>
        <?php } ?>
      </table>
    </div>
    
    <div class="row">
      <div class="col-md-5 offset-md-7 d-grid gap-2">
        <button class="btn btn-primary btn-lg">Realizar pago</button>
      </div>

    </div>


  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script>
  function actualizaCantidad(cantidad, id){
    let url='clases/actualizar_carrito.php'
    let formData = new FormData()
    formData.append('action', 'agregar' )
    formData.append('id', id )
    formData.append('cantidad', cantidad )

    fetch(url, {
      method: 'POST',
      body: formData,
      mode: 'cors'
    }).then(response => response.json())
    .then(data => {
      if(data.ok){


        let divsubtotal = document.getElementById('subtotal_' + id)
        divsubtotal.innerHTML = data.sub

        let total = 0.00
        let list = document.getElementsByName('subtotal[]')

        for(let i = 0; i < list.length; i++){
          total += parseFloat(list[i].innerHTML.replace(/[$,]/g, ''))
        }

        total = new Intl.NumberFormat('en-US', {
          minimumFractionDigits: 2
        }).format(total)
        document.getElementById('total').innerHTML = '<?php echo MONEDA; ?>' + total 
      }
    })
  }
</script>


</body>
</html>