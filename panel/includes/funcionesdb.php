<?php
session_start();
include 'server_config.php';
include 'lang_config.php';

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    $GLOBALS["APP"] = "//localhost/Websites/InProgress/plusurbano/";
} else {
    $GLOBALS["APP"] = "//" . $_SERVER['SERVER_NAME'] . "/";
}

global $mysqli;
$mysqli =  mysqli_init();
$mysqli->real_connect('p:' . $_server['DB_HOST'], $_server['DB_USER'], $_server['DB_PASS'], $_server['DB_NAME'], $_server['DB_PORT'], null, MYSQLI_CLIENT_COMPRESS);
$mysqli->set_charset('utf8');

global $myview;
$myview =  mysqli_init();
$myview = mysqli_connect($_server['DB_HOST'], $_server['DB_USER'], $_server['DB_PASS'], $_server['DB_NAME']);
$myview->set_charset('utf8');
function ExecuteSql($sQuery, $aParameters, $debug = false)
{
    global $mysqli;
    if ($debug) {
        echo $sQuery;
        echo json_encode($aParameters);
    }

    $stmt = $mysqli->prepare($sQuery);
    if (!$stmt) {
        die("SQL Prepare failed: " . $mysqli->error);
    }

    if (!empty($aParameters) && count($aParameters) > 1) {
        $sTypes = '';
        $bindValues = [];

        for ($i = 1; $i < count($aParameters); $i++) {
            $value = $aParameters[$i];

            if ($value === 'null-value') {
                $value = null;
            }

            if (is_int($value)) {
                $sTypes .= 'i';
            } elseif (is_float($value)) {
                $sTypes .= 'd';
            } else {
                $sTypes .= 's';
            }

            $bindValues[] = $value;
        }

        // Crear array con referencias
        $bindParams = [];
        $bindParams[] = $sTypes;
        foreach ($bindValues as $key => &$val) {
            $bindParams[] = &$val;
        }
        unset($val);

        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
    return $res;
}

function ExecuteViews($sQuery)
{
    global $myview;
    $resultado = mysqli_query($myview, $sQuery);
    return $resultado;
    mysqli_close($resultado);
}


function logAction($log, $log_detail)
{

    if (isset($_SESSION['USER_email'])) {
        $user = $_SESSION['USER_email'];
    } else {
        $user = null;
    }
    $sSql = "INSERT INTO `sys_logs` (`user`, `log`, `log_detail`) VALUES (?,?,?);";

    $result = ExecuteSql($sSql, array(null, $user, $log, get_client_ip() . " - " . $log_detail));
}

function importFile($fileName, $tableName)
{
    global $mysqli;
    $sSql = "LOAD DATA LOCAL INFILE '$fileName'
			 INTO TABLE $tableName
			 FIELDS TERMINATED BY ','
			 OPTIONALLY ENCLOSED BY '\"'
			 LINES TERMINATED BY '\r\n'
			 IGNORE 1 LINES";

    if ($mysqli->query($sSql)) {
        return ($mysqli->affected_rows);
    } else {
        return ($mysqli->error);
    }
}

// Function to get the client IP address
function get_client_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function getMonth($monthId)
{
    $arrMonths = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
    return $arrMonths[$monthId];
}

function getFirstFile($table, $field, $id)
{
    $sSql = "SELECT `fileName` FROM `sys_files` WHERE `tableName` = ? AND `columnName` = ? AND `rowId` = ? AND `publish` = 1 ORDER BY `order`;";
    $result = ExecuteSql($sSql, array(null, $table, $field, $id));
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if (is_null($row)) {
        $file = 'images/no-image.jpg';
    } else {
        $file = 'uploads/' . $row['fileName'];
    }
    return $file;
}

function getSmallFile($table, $field, $id)
{
    $sSql = "SELECT `fileName` FROM `sys_files` WHERE `tableName` = ? AND `columnName` = ? AND `rowId` = ? AND `publish` = 1 ORDER BY `order`;";
    $result = ExecuteSql($sSql, array(null, $table, $field, $id));
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if (is_null($row)) {
        $file = 'images/no-image.jpg';
    } else {
        $filename = explode(".",$row['fileName']);
        $file = 'uploads/' . $filename[0] . '-sm.' .$filename[1];
    }
    return $file;
}

function getScndFile($table, $field, $id) {
    $sSql = "SELECT `fileName` FROM `sys_files` WHERE `tableName` = ? AND `columnName` = ? AND `rowId` = ? AND `publish` = 1 ORDER BY `order` LIMIT 1, 1;";

    $result = ExecuteSql($sSql, array(null, $table, $field, $id));
    $row = $result -> fetch_array(MYSQLI_ASSOC);
    
    if (is_null($row)) {
        $file = 'images/no-image.jpg';
    } else {
        $file = 'uploads/' . $row['fileName'];
    }
    return $file;
}

function getFileName($tableName, $columnName, $rowId)
{
    $sSql = "SELECT `fileName` FROM `sys_files` WHERE `tableName` = ? AND `columnName` = ? AND `rowId` = ? ORDER BY `order` LIMIT 0, 1;";

    $result = ExecuteSql($sSql, array(null, $tableName, $columnName, $rowId));
    $row = $result->fetch_array(MYSQLI_ASSOC);

    return $row['fileName'];
}

function fnc_antihackers($str)
{
    $arr_no_permitidos = array('select', 'insert', 'update', 'delete', 'create', 'script', '>', '<', '#60', '#62', 'gt;', 'lt;', '&', 'url', '=', 'http');
    $intento_hack = false;

    foreach ($arr_no_permitidos as $i => $value) {
        if (stripos($str, $arr_no_permitidos[$i]) !== false) {
            $intento_hack = true;
            $str_detectado = $arr_no_permitidos[$i];
        }
    }

    if ($intento_hack == true) {

        //$cabeceras = "From: Editorial Atlante Webmaster <martin@simonyan.com.ar>\r\nContent-Type: text/html; charset=UTF-8\nX-Mailer: PHP/" . phpversion(); 
        //mail("martin@simonyan.com.ar", "Editorial Atlante - Intento de Hack", $str, $cabeceras);

        die("<p style='background-color: #3374b4; display: block;	text-align:center;	color:#FFF;	font-size: 18px;'>Ha ocurrido un ERROR<br />
			Este mensaje no será enviado porque contiene texto no permitido: <strong style='font-size: 24px;'>$str_detectado</strong></p>
			<p style='background-color: #3374b4; display: block; text-align:center;	color:#FFF;	font-size: 16px;'>Por favor, vuelva a enviar su mensaje.</p>
			<p style='background-color: #3374b4; display: block; text-align:center;	color:#FFF;	font-size: 16px;'><a style='text-align:center;	color:#FFF;	font-size: 16px;' href='javascript:history.back(1)'>VOLVER</a></p>");
    }

    return $str;
}
function AgregaSaltos($string)
{
    $string = str_replace(chr(13), "<br>", $string);
    return $string;
}
function ValidaMail($pMail)
{
    if (preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$/", $pMail)) {
        return true;
    } else {
        return false;
    }
}
function urls_amigables($url)
{

    // Tranformamos todo a minusculas
    $url = strtolower($url);

    //Rememplazamos caracteres especiales latinos		
    $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
    $repl = array('a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'ñ');
    $url = str_replace($find, $repl, $url);

    // Añadimos los guiones
    $find = array(' ', '&', '\r\n', '\n', '+');
    $url = str_replace($find, '-', $url);

    // Eliminamos y Reemplazamos demás caracteres especiales
    $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
    $repl = array('', '-', '');
    $url = preg_replace($find, $repl, $url);

    return $url;
}

function nombres_json($url)
{

    // Tranformamos todo a minusculas
    $url = strtolower($url);

    //Rememplazamos caracteres especiales latinos

    $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');

    $repl = array('a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'n');

    //$find = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');

    //$repl = array('á', 'é', 'í', 'ó', 'ú', 'ñ');

    $url = str_replace($find, $repl, $url);

    // Añaadimos los guiones

    $find = array('&', '\r\n', '\n', '+');
    $url = str_replace($find, '', $url);

    // Eliminamos y Reemplazamos demás caracteres especiales

    //$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
    $find = array('/[^a-z0-9\-<>.]/', '/<[^>]*>/');

    $repl = array(' ', ' ');

    $url = preg_replace($find, $repl, $url);

    $url = ucfirst($url);

    return $url;
}

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function resize_image_webp($source_file, $destination_file, $width, $height, $quality, $crop = FALSE) {
    // Check that the file exists and is readable
    if (!file_exists($source_file) || !is_readable($source_file)) {
        throw new Exception("Source file not found or not readable: $source_file");
    }

    // Get original image dimensions
    $image_info = @getimagesize($source_file);
    if (!$image_info) {
        throw new Exception("Could not read image info for: $source_file");
    }

    list($current_width, $current_height) = $image_info;

    // Prevent division by zero
    if ($current_width == 0 || $current_height == 0) {
        throw new Exception("Invalid image dimensions (width=$current_width, height=$current_height) for $source_file");
    }

    $rate = $current_width / $current_height;

    // Calculate new size
    if ($crop) {
        if ($current_width > $current_height) {
            $current_width = ceil($current_width - ($current_width * abs($rate - $width / $height)));
        } else {
            $current_height = ceil($current_height - ($current_height * abs($rate - $width / $height)));
        }
        $newwidth = $width;
        $newheight = $height;
    } else {
        if ($width / $height > $rate) {
            $newwidth = $height * $rate;
            $newheight = $height;
        } else {
            $newheight = $width / $rate;
            $newwidth = $width;
        }
    }

    // Create image resources
    $src_file = @imagecreatefromwebp($source_file);
    if (!$src_file) {
        throw new Exception("Failed to create image from source: $source_file");
    }

    $dst_file = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst_file, $src_file, 0, 0, 0, 0, $newwidth, $newheight, $current_width, $current_height);

    // Save to WebP
    imagewebp($dst_file, $destination_file, $quality);

    // Finish and clean up
    imagedestroy($src_file);
    imagedestroy($dst_file);
}