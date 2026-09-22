<?php
// Helper de CORS - configura los headers para que Angular se pueda comunicar con el backend
// Sin esto el navegador no deja hacer peticiones desde el frontend al backend

// Permitimos que solo el frontend de angular (localhost:4200) pueda hacer peticiones
header('Access-Control-Allow-Origin: http://localhost:4200');
// Permitimos estos metodos HTTP
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
// Permitimos estos headers en las peticiones
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Fo-Usuario');
// Permitimos que se envien cookies y sesiones
header('Access-Control-Allow-Credentials: true');
// Decimos que la respuesta siempre va a ser JSON
header('Content-Type: application/json; charset=UTF-8');

// Si el navegador manda una peticion OPTIONS (pre-flight) respondemos 200 y listo
// Esto pasa cuando Angular hace una peticion compleja
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}
