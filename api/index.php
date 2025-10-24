<?php
include '../panel/_includes.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// PDO database connection
$pdo = new PDO("mysql:host=" . $_server['DB_HOST'] . ";dbname=" . $_server['DB_NAME'] . ";charset=utf8", $_server['DB_USER'], $_server['DB_PASS']);
$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

// Check request method
$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD === 'OPTIONS') {
    http_response_code(200);
    die;
}

$tableSelected = $_REQUEST['t'] ?? "";  // Get the selected table
$rowsArray = [];

// Query mapping
$queries = [
    'wines' => "SELECT T1.*
                    FROM wines AS T1 WHERE T1.active = 1 ORDER BY T1.order DESC;",

    'oils' => "SELECT T1.*
                    FROM oils AS T1 WHERE T1.active = 1 ORDER BY T1.order DESC;",

    'distillates' => "SELECT T1.*
                    FROM distillates AS T1 WHERE T1.active = 1 ORDER BY T1.order DESC;",
];

function toSlug($string, $id = null)
{
    if (!is_string($string) || trim($string) === '') {
        return '';
    }

    $string = mb_strtolower($string, 'UTF-8');

    // Normalize and remove accents using intl extension if available
    if (class_exists('Normalizer')) {
        $string = Normalizer::normalize($string, Normalizer::FORM_D);
        $string = preg_replace('/\p{Mn}/u', '', $string); // Remove diacritics
    } else {
        // Fallback replacement for common characters
        $accents = [
            'á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            'ü' => 'u'
        ];
        $string = strtr($string, $accents);
    }

    // Remove all non-alphanumeric characters except dashes
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = trim($string, '-');

    if ($id !== null) {
        return $string . '-' . $id;
    }
    return $string;
}

if (isset($queries[$tableSelected])) {
    $result = $pdo->query($queries[$tableSelected]);
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    // Images SKU
    if ($tableSelected === 'wines' || $tableSelected === 'oils' || $tableSelected === 'distillates') {
        foreach ($rows as &$row) {
            $row['sku_image'] = $row['sku'] . '-image' ;
            $row['slug'] = toSlug($row['name']);
        }
    }
    /*// Content
    if ($tableSelected === 'blogs') {
        foreach ($rows as &$row) {
            if (!is_null($row['content_block'])) {
                $row['content_block'] = is_string($row['content_block']) ? json_decode($row['content_block'], true) : null;
                if (is_array($row['content_block'] && !is_null($row['content_block']))) {
                    foreach ($row['content_block'] as &$contentBlock) {
                        if($contentBlock['type'] == 'image' && !is_null($contentBlock['image'])) {
                            if (is_string($contentBlock['image'])) {
                                $contentBlock['image'] = explode(',', $contentBlock['image']);
                            }
                        }
                    }
                }
            }
            $row['notasRecomendadas'] = is_string($row['notasRecomendadas']) ? json_decode($row['notasRecomendadas'], true) : null;
            if (is_array($row['notasRecomendadas'])) {
                foreach ($row['notasRecomendadas'] as &$recommendedBlog) {
                    $recommendedBlog['slug'] = toSlug($recommendedBlog['name']);
                }
            }
        }
    }*/

    // Output response in JSON format
    echo json_encode($rows, JSON_NUMERIC_CHECK);
} else {
    // Handle invalid table selection
    echo json_encode(['error' => 'Invalid table selected']);
}
