<?php
// Config
$TO      = 'info@transcorp.com.ar';
$FROM    = 'no-reply@transcorp.com.ar';
$SUBJECT = 'Nueva solicitud de cotización - Transcorp';
date_default_timezone_set('America/Argentina/Buenos_Aires');

function respond($ok, $message){
  header('Content-Type: application/json; charset=utf-8');
  http_response_code($ok ? 200 : 400);
  echo json_encode(['ok'=>$ok,'message'=>$message]); exit;
}

// Honeypot
if (!empty($_POST['website'])) respond(true, 'ok');

// Tomar datos
$empresa   = trim($_POST['empresa']   ?? '');
$nombre    = trim($_POST['nombre']    ?? '');
$email     = trim($_POST['email']     ?? '');
$telefono  = trim($_POST['telefono']  ?? '');
$origen    = trim($_POST['origen']    ?? '');
$destino   = trim($_POST['destino']   ?? '');
$fecha     = trim($_POST['fecha']     ?? '');
$hora      = trim($_POST['hora']      ?? '');
$pasajeros = trim($_POST['pasajeros'] ?? '');
$coment    = trim($_POST['comentarios'] ?? ''); // OPCIONAL

// Validación mínima (sin "comentarios")
$missing = [];
foreach (['empresa','nombre','email','telefono','origen','destino','fecha','hora','pasajeros'] as $k) {
  if ($$k === '') $missing[] = $k;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $missing[] = 'email';
if ($missing) respond(false, 'Datos incompletos: ' . implode(', ', $missing));

// Mensaje
$body = "Nueva solicitud de cotización\n\n"
      . "Empresa: $empresa\n"
      . "Nombre y apellido: $nombre\n"
      . "Email: $email\n"
      . "Teléfono: $telefono\n"
      . "Origen: $origen\n"
      . "Destino: $destino\n"
      . "Fecha: $fecha\n"
      . "Hora: $hora\n"
      . "Pasajeros: $pasajeros\n"
      . "Comentario: $coment\n";

// Cabeceras
$headers  = "From: Transcorp Web <{$FROM}>\r\n";
$headers .= "Reply-To: {$nombre} <{$email}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Enviar
$sent = @mail($TO, $SUBJECT, $body, $headers);
if ($sent) respond(true, 'Mensaje enviado');
respond(false, 'No se pudo enviar el email');
