<?php
require 'funcoes.php'; proteger();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }
verificar_token(); $_SESSION = []; session_destroy(); header('Location: login.php'); exit;
