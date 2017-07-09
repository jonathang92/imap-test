<?php
require_once('class/parser.php');
$email = new Parser();
$data = array(
	'email' => array(
		'hostname' => '{imap.gmail.com:993/imap/ssl}INBOX',
		'username' => 'correo',
		'password' => 'aqui va la contraseña'
	),
	'pagination' => array(
		'sort' => 'DESC',
		'limit' => 10,
		'offset' => 0
	)
);

$result = $email->inbox($data);
   //echo '<pre>';
   //print_r($result);
   //echo '</pre>';
if ($result!=false) {
  foreach ($result as $id => $value) {
    // $datos[$id] = $id;
    foreach ($value as $key => $value) {
      $datos[$id] = $value['numero de cuenta'].",";
      $datos[$id] .= $value['kit a instalar'].",";
      $datos[$id] .= $value['nombre del cliente'].",";
      $datos[$id] .= $value['direccion'].",";
      $datos[$id] .= $value['referencias domicilio'].",";
      $datos[$id] .= $value['telefono'].",";
      $datos[$id] .= $value['e-mail'].",";
      $datos[$id] .= $value['observaciones'];
    }
  }

  $archivo = "csv/parametros.csv";
  if(!file_exists($archivo)){
    mkdir("csv", 0777, true);
    file_put_contents($archivo,"Numero de Cuenta, Kit a instalar, Nombre del cliente, Direccion, Referencias Domicilio, Telefono, E-mail, Observaciones");
  }
  foreach ($datos as $id => $value) {
        file_put_contents($archivo,"\n".$value,FILE_APPEND);
  }
  if(count($datos)==1){
    echo count($datos)." Correo importado";
  }else{
    echo count($datos)." Correos importados";
  }
  // $datos[] = array("Jorge,Gómez,20,8");
  // $datos[] = array("Mariel,Ramirez,30,10");
  // $datos[] = array("Isabela,Contreras,18,9");
  // foreach($datos as $d)
  //     file_put_contents($archivo,"\n".join(",",$d),FILE_APPEND);
  // echo "Archivo creado con exito";
} else{
  echo "No hay correos por importar";
}
echo "</br></br><a href='csv/parametros.csv'> DESCARGAR CSV</a>";



?>
