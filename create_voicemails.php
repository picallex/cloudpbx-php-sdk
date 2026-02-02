<?php

require __DIR__ . '/vendor/autoload.php';

// ---- Configuracion ----
$api_base    = ''; 
$api_key     = '';                           
$customer_id = 1387;                                  
$csv_file    = __DIR__ . '/userTest.csv'; 
$error_file  = __DIR__ . '/voicemails_fallidosTest.csv';
// ------------------------

$client = \Cloudpbx\Sdk::createDefaultClient($api_base, $api_key);

// 1. Obtener todos los usuarios y construir mapa name -> id
echo "Obteniendo usuarios del customer $customer_id...\n";

try {
    $users = $client->users->all($customer_id);

} catch (\Exception $e) {
    echo "ERROR: No se pudieron obtener los usuarios: " . $e->getMessage() . "\n";
    exit(1);
}

$userMap = [];
foreach ($users as $user) {
    $userMap[$user->name] = $user->id;
}

echo "Se encontraron " . count($userMap) . " usuarios.\n\n";

// 2. Leer el CSV
if (!file_exists($csv_file)) {
    echo "ERROR: No se encontro el archivo CSV: $csv_file\n";
    exit(1);
}

$handle = fopen($csv_file, 'r');
if ($handle === false) {
    echo "ERROR: No se pudo abrir el archivo CSV.\n";
    exit(1);
}

// Leer header
$header = fgetcsv($handle);

$created  = 0;
$skipped  = 0;
$errors   = 0;
$line     = 1;
$fallidos = [];

while (($row = fgetcsv($handle)) !== false) {
    $line++;

    // Saltar filas vacias
    if (empty(array_filter($row))) {
        continue;
    }

    $nombre    = trim($row[0] ?? '');
    $email     = trim($row[1] ?? '');
    $extension = trim($row[7] ?? '');

    // Validar que tenga extension y email
    if ($extension === '' || $email === '') {
        $razon = 'Falta extension o email';
        echo "[LINEA $line] SKIP: $razon (nombre: $nombre)\n";
        $fallidos[] = [$line, $nombre, $email, $extension, $razon];
        $skipped++;
        continue;
    }

    // Validar que la extension sea numerica
    if (!is_numeric($extension)) {
        $razon = "Extension no numerica: $extension";
        echo "[LINEA $line] SKIP: $razon (nombre: $nombre)\n";
        $fallidos[] = [$line, $nombre, $email, $extension, $razon];
        $skipped++;
        continue;
    }

    // Buscar el user_id por name (el name del usuario es la extension)
    if (!isset($userMap[$extension])) {
        $razon = "No se encontro usuario con name=$extension";
        echo "[LINEA $line] SKIP: $razon (nombre: $nombre)\n";
        $fallidos[] = [$line, $nombre, $email, $extension, $razon];
        $skipped++;
        continue;
    }

    $user_id     = $userMap[$extension];
    $description = "Voicemail $extension - $nombre";

    echo "[LINEA $line] Creando voicemail para extension $extension (user_id=$user_id, email=$email)... ";

    try {
        $voicemail = $client->voicemails->create(
            $customer_id,
            $user_id,
            $description,
            $email,
            ['password' => '123456789', 'skip_greeting' => false, 'skip_instructions' => true]
        );
        echo "OK (voicemail_id=" . $voicemail->id . ")\n";
        $created++;
    } catch (\Exception $e) {
        $razon = $e->getMessage();
        echo "ERROR: $razon\n";
        $fallidos[] = [$line, $nombre, $email, $extension, $razon];
    }

    sleep(2);
        $errors++;
    }
}

fclose($handle);

// 3. Guardar fallidos en CSV
if (!empty($fallidos)) {
    $errorHandle = fopen($error_file, 'w');
    fputcsv($errorHandle, ['Linea', 'Nombre', 'Email', 'Extension', 'Razon']);
    foreach ($fallidos as $f) {
        fputcsv($errorHandle, $f);
    }
    fclose($errorHandle);
    echo "\nSe guardo el detalle de fallidos en: $error_file\n";
}

// 4. Resumen
echo "\n--- Resumen ---\n";
echo "Creados:  $created\n";
echo "Saltados: $skipped\n";
echo "Errores:  $errors\n";
echo "Total fallidos: " . count($fallidos) . "\n";
