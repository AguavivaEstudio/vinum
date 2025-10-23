<?php
include '../panel/_includes.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');


$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD === 'OPTIONS') {
    http_response_code(200);
    die;
}

if (isset($_REQUEST['t'])) {
    $tableSelected = $_REQUEST['t'];
} else {
    $tableSelected = "";
}

// autoplay es para tipo de contenido de carrusel
//VIDEO es un modelo aparte que trae varios datos


//TABLAS
if ($tableSelected === 'blogs' || (strpos($tableSelected, 'blog:') !== false)) {
    $sSql = "SELECT * FROM blogs WHERE activo = 1 ORDER BY 'order' DESC;";
    if ($tableSelected !== 'blogs') {
        $id = explode(':', $tableSelected);
        $id = end($id);
        $sSql = "SELECT * FROM blogs WHERE activo = 1 AND id = $id LIMIT 1;";
    }

    $result = ExecuteSql($sSql, null);
    $rowsArray = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $x = new stdClass();
        $x->id          = $row['id'];
        $x->fecha_pub   = $row['publicacion'];
        $x->titulo      = $row['name'];
        $x->categoria   = $row['categoria'];
        $x->bajada      = $row['bajada'];
        $x->descripcion = $row['descripcion'];
        $x->portada     = getFile('blogs', 'file_imagen', $row['id'], 0);
        $x->apect_ratio = $row['aspecto'];
        $x->btn_txt     = $row['btn_txt'];
        $x->slug        = createSlug($row['name'], $row['id']);
        $x->destacado   = $row['destacado'];
        $x->publicado   = $row['activo'];

        if (empty($row['rel_blogs'])) {
            $relacionadosSql  = "SELECT GROUP_CONCAT(id ORDER BY publicacion DESC) AS id FROM blogs WHERE activo = 1 AND categoria = 1 ORDER BY publicacion DESC LIMIT 3;";
            $relacionadosData = ExecuteSql($relacionadosSql, null);
            $x->pub_relac     = $relacionadosData;
        } else {
            $x->pub_relac     = $row['rel_blogs'];
        }

        $contenidosSql  = "SELECT * FROM blog_contenido WHERE activo = 1 AND blog = " . $row['id'] . " ORDER BY orden DESC;";
        $contenidosData = ExecuteSql($contenidosSql, null);
        $x->contenidos  = array();
        while ($contenidoRow = $contenidosData->fetch_array(MYSQLI_ASSOC)) {
            $y = new stdClass();
            $y->id           = $contenidoRow['id'];
            $y->tipo         = $contenidoRow['tipo'];
            $y->nombre       = $contenidoRow['name'];
            $y->texto        = $contenidoRow['texto'];
            $y->imagen       = getFile('blog_contenido', 'file_img', $contenidoRow['id'], 0);
            $y->apect_ratio  = $contenidoRow['aspecto'];
            $y->autoplay     = $contenidoRow['autoplay'];
            $y->video        = getVideo($contenidoRow['video'], $contenidoRow['autoplay'], $controls['controls']);
            $y->destacado    = $contenidoRow['destacado'];
            $y->btn_txt      = $contenidoRow['url_txt'];
            $y->btn_link     = $contenidoRow['url_link'];
            $x->contenidos[] = $y;
        }

        $autorSql  = "SELECT * FROM autores WHERE id = " . $row['autor'] . ";";
        $autorData = ExecuteSql($autorSql, null);
        $x->autor  = array();
        while ($autorRow = $autorData->fetch_array(MYSQLI_ASSOC)) {
            $z = new stdClass();
            $z->nombre       = $autorRow['name'];
            $z->apellido     = $autorRow['last_name'];
            $z->rol          = $autorRow['rol'];
            $x->autor[] = $z;
        }
        $rowsArray[] = $x;
    }

    echo json_encode($rowsArray);
}

if ($tableSelected === 'contenido_tipo') {
    $rowsArray = array();
    $sSql = "SELECT * FROM contenido_tipo ORDER BY id DESC";
    $result = ExecuteSql($sSql, null);

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $x = new stdClass();
        $x->id       = $row['id'];
        $x->tipo     = $row['name'];
        $rowsArray[] = $x;
    }

    echo json_encode($rowsArray);
}

if ($tableSelected === 'redes') {
    $rowsArray = array();
    $sSql = "SELECT * FROM redes WHERE activo = 1 ORDER BY orden DESC;";
    $result = ExecuteSql($sSql, null);

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $x = new stdClass();
        $x->id       = $row['id'];
        $x->nombre   = $row['name'];
        $x->link     = $row['link'];
        $rowsArray[] = $x;
    }

    echo json_encode($rowsArray);
}

if ($tableSelected === 'autores') {
    $rowsArray = array();
    $sSql = "SELECT * FROM autores WHERE activo = 1 ORDER BY id DESC;";
    $result = ExecuteSql($sSql, null);

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $x = new stdClass();
        $x->id       = $row['id'];
        $x->nombre   = $row['name'];
        $x->apellido   = $row['last_name'];
        $x->rol      = $row['rol'];
        $rowsArray[] = $x;
    }

    echo json_encode($rowsArray);
}


if ($tableSelected === 'aspect_ratio') {
    $rowsArray = array();
    $sSql = "SELECT * FROM aspect_ratio";
    $result = ExecuteSql($sSql, null);

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $x = new stdClass();
        $x->id       = $row['id'];
        $x->aspRat   = $row['aspect'];
        $rowsArray[] = $x;
    }

    echo json_encode($rowsArray);
}

if ($tableSelected === 'tokenig') {
    $rowsArray = array();
    $sSql = "SELECT * FROM aspect_ratio";
    $result = ExecuteSql($sSql, null);

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $x = new stdClass();
        $x->id       = $row['id'];
        $x->token   = $row['token'];
        $rowsArray[] = $x;
    }

    echo json_encode($rowsArray);
}

//FUNCIONES
function getFile($tableName, $columnName, $id, $cant)
{
    if ($cant < 1) {
        $image = 'noimage.jpg';
        $sSql = "SELECT * FROM `sys_files` WHERE `tableName` = '$tableName' AND `columnName` = '$columnName' AND `rowId` = $id LIMIT 1;";
        $result = ExecuteSql($sSql, null);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $image = $row['fileName'];
        }
    } else {
        $image = array();
        $sSql = "SELECT * FROM `sys_files` WHERE `tableName` = '$tableName' AND `columnName` = '$columnName' AND `rowId` = $id;";
        $result = ExecuteSql($sSql, null);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $image[] = $row['fileName'];
        }
        if (!$image[0]) {
            $image[] = 'noimage.jpg';
        }
    }
    return $image;
}

function getArchivos($tableName, $columnName, $id)
{
    $sSql = "SELECT * FROM `sys_files` WHERE `tableName` = '$tableName' AND `columnName` = '$columnName' AND `rowId` = $id AND `publish` = 1 ORDER BY `order`;";
    $result = ExecuteSql($sSql, null);

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $image[] = $row['fileName'];
    }
    return $image;
}

function getVideo($video, $autoplay, $controls)
{
    $result =  new StdClass();
    if (str_contains($video, "youtube")) {
        $result -> src = "youtube";
        $result -> code = str_replace('watch?v=', 'embed/', $video);

    } else if (str_contains($video, "vimeo")) {
        $result -> src = "vimeo";
        $result -> code = $video;
        
    } else {
        $result -> src = "unknown";
        $result -> code = $video;
    }
        $result -> autoplay = $autoplay;
        $result -> controls = $controls;

    return $result;
}

function createSlug($name, $id)
{
    $kebabTitle = preg_replace(['/([áäàâ])/', '/([éëèê])/', '/([íïìî])/', '/([óöòô])/', '/([úüùû])/', '/ñ/', '/\s+/', '/\s/'], ['a', 'e', 'i', 'o', 'u', 'n', ' ', '-'], $name);
    $kebabTitle = preg_replace('/[^\x00-\x7F]/', "", $kebabTitle); //Remove ascii codes
    $kebabTitle = strtolower($kebabTitle);
    $path = '/' . $kebabTitle . '-' . $id;
    return $path;
}

function decodeCustomFormat($string) 
{
    $bold = "::negrita::";
    $main = "::destacado::";
}