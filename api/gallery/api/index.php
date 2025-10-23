<?php
include_once 'folder.php';
ini_set("zlib.output_compression", 4096);
ini_set("allow_url_fopen", 1);
$ts = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: $ts");
header("Last-Modified: $ts");
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, x-Authorization, X-Requested-With, bearer, email");
header("Access-Control-Max-Age: 3600");
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    die;
}

$uri =  $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER["SCRIPT_NAME"];
$scriptName = str_replace("index.php", "", $scriptName);
$uri = str_replace($scriptName, "", $uri);
$uriParams = explode('/', $uri);
$body = json_decode(file_get_contents("php://input"));

$rootFolder = '../../../uploads';
$folder = new Folder($rootFolder, '');

switch ($method) {
    case 'DELETE':
        $responseCode = 404;
        if (isset($_GET['file'])) {
            $responseCode = $folder->deleteFile($_GET['file']) ? 200 : 404;
        } else if (isset($_GET['folder'])) {
            $responseCode = $folder->deleteFolder($_GET['folder']) ? 200 : 404;
        }
        http_response_code($responseCode);
        break;
    case 'POST':
        $isFile = isset($_FILES) && isset($_FILES['fileKey']) && isset($_FILES['fileKey']['name']);
        if ($isFile) {
            $responseCode = $folder->uploadFile();
            $responseCode = 200;
        } else if (isset($body->folder)) {
            $responseCode = $folder->createFolder($body->folder);
        }
        http_response_code($responseCode);
        break;
    default:
        http_response_code(501);
}
