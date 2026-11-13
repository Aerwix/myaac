<?php
header('Content-Type: application/json; charset=utf-8');

// CONFIG
$files_dir = "/var/www/html/updater/files";
$files_url = "http://aerwix.com/updater/files/";
$files_and_dirs = array("init.lua", "data", "modules", "layouts");
$checksum_file = "checksums.txt";
$checksum_update_interval = 60;

$binaries = array(
    "WIN32-WGL"       => "Naruto.exe",
    "WIN32-EGL"       => "Naruto.exe",
    "WIN32-WGL-GCC"   => "Naruto.exe",
    "WIN32-EGL-GCC"   => "Naruto.exe",
    "X11-GLX"         => "otclient_linux",
    "X11-EGL"         => "otclient_linux",
    "ANDROID-EGL"     => "",
    "ANDROID64-EGL"   => ""
);
// CONFIG END

function sendError($error) {
    echo json_encode(array("error" => $error));
    die();
}

$data = json_decode(file_get_contents("php://input"));
if (!is_object($data)) {
    $data = new stdClass();
}

$version  = isset($data->version)  ? $data->version  : 0;
$build    = isset($data->build)    ? $data->build    : "";
$os       = isset($data->os)       ? $data->os       : "unknown";
$platform = isset($data->platform) ? $data->platform : "";
$args     = isset($data->args)     ? $data->args     : array();
$binary   = isset($binaries[$platform]) ? $binaries[$platform] : "";

$cache = null;
$cache_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $checksum_file;

if (file_exists($cache_file) && (filemtime($cache_file) + $checksum_update_interval > time())) {
    $cache = json_decode(file_get_contents($cache_file), true);
}

if (!$cache) {
    $dir = realpath($files_dir);
    if (!$dir) {
        sendError("files_dir does not exist: " . $files_dir);
    }
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $cache = array();
    foreach ($rii as $file) {
        if (!$file->isFile()) continue;
        $path = str_replace($dir, '', $file->getPathname());
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        // El cliente reporta rutas con "/" inicial
        $path = '/' . ltrim($path, '/');
        $cache[$path] = hash_file("crc32b", $file->getPathname());
    }
    file_put_contents($cache_file . ".tmp", json_encode($cache));
    rename($cache_file . ".tmp", $cache_file);
}

$ret = array(
    "url"       => $files_url,
    "files"     => array(),
    "keepFiles" => false
);

foreach ($cache as $file => $checksum) {
    // Las keys ahora empiezan con "/", quitarlo SOLO para identificar el primer segmento
    $parts = explode("/", ltrim($file, "/"));
    $base  = isset($parts[0]) ? trim($parts[0]) : "";

    if (in_array($base, $files_and_dirs)) {
        $ret["files"][$file] = $checksum;
    }
    if (!empty($binary) && $base === $binary) {
        $ret["binary"] = array("file" => $file, "checksum" => $checksum);
    }
}

echo json_encode($ret);