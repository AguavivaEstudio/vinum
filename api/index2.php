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
    'contact' => "SELECT T1.*,
                    COALESCE(NULLIF(file_image.fileName, ''), 'noimage.png') AS image,
                    COALESCE(NULLIF(file_imageBanner.fileName, ''), 'noimage.png') AS banner
                    FROM contact AS T1 
                        LEFT JOIN (
                            SELECT fileName, rowId 
                            FROM sys_files 
                            WHERE tableName = 'contact' 
                            AND columnName = 'file_imagen' 
                            AND publish = 1
                        ) AS file_image ON file_image.rowId = T1.id
                        LEFT JOIN (
                            SELECT fileName, rowId 
                            FROM sys_files 
                            WHERE tableName = 'contact' 
                            AND columnName = 'file_banner' 
                            AND publish = 1
                        ) AS file_imageBanner ON file_imageBanner.rowId = T1.id
                    LIMIT 1;",

    'landing' => "SELECT T1.*
                    FROM landing AS T1;",

    'slider_home' => "SELECT T1.*,
                    REPLACE(REPLACE(REPLACE(REPLACE(T1.name, '|azul-', '<span class=\"highlight-blue\">'), '-azul|', '</span>'), '|celeste-', '<span class=\"highlight-lightblue\">'), '-celeste|', '</span>')
                        AS title,
                    REPLACE(REPLACE(REPLACE(REPLACE(T1.name_en, '|azul-', '<span class=\"highlight-blue\">'), '-azul|', '</span>'), '|celeste-', '<span class=\"highlight-lightblue\">'), '-celeste|', '</span>')
                        AS title_en,
                    COALESCE(NULLIF(thumbnail_file.fileName, ''), 'noimage.png') AS image
                    FROM slider_home AS T1
                        LEFT JOIN (
                            SELECT fileName, rowId 
                            FROM sys_files 
                            WHERE tableName = 'slider_home' 
                            AND columnName = 'file_thumbnail' 
                            AND publish = 1
                        ) AS thumbnail_file ON thumbnail_file.rowId = T1.id
                    ORDER BY T1.order ASC;",

    'blogs' => "SELECT
                    P.id,
                    REPLACE(REPLACE(REPLACE(REPLACE(P.name, '|azul-', '<span class=\"highlight-blue\">'), '-azul|', '</span>'), '|celeste-', '<span class=\"highlight-lightblue\">'), '-celeste|', '</span>')
                        AS name,
                    REPLACE(REPLACE(REPLACE(REPLACE(P.name_en, '|azul-', '<span class=\"highlight-blue\">'), '-azul|', '</span>'), '|celeste-', '<span class=\"highlight-lightblue\">'), '-celeste|', '</span>')
                        AS name_en,
                    P.slugName,
                    REPLACE(REPLACE(REPLACE(REPLACE(P.name_en, '|azul-', ''), '-azul|', ''), '|celeste-', ''), '-celeste|', '')
                        AS slugName_en,
                    P.text,
                    P.featured,
                    P.text_en,
                    P.category,
                    P.date,
                    blogs_categories.name AS catName,
                    blogs_categories.name_en AS catName_en,
                    COALESCE(NULLIF(thumbnail_file.fileName, ''), 'noimage.png') AS thumbnail,
                    
                    JSON_ARRAYAGG(bloque_json ORDER BY blocks.order ASC) AS content_block,

                    (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', R.id,
                                'name', R.slugName,
                                'name_en', R.slugName_en,
                                'text', R.text,
                                'text_en', R.text_en,
                                'thumbnail', COALESCE(NULLIF(thumbnail.fileName, ''), 'noimage.png')
                            )
                        )
                        FROM blogs R
                        LEFT JOIN (
                            SELECT fileName, rowId
                            FROM sys_files
                            WHERE tableName = 'blogs'
                            AND columnName = 'file_thumbnail'
                            AND publish = 1
                        ) AS thumbnail ON thumbnail.rowId = R.id
                        WHERE R.category = P.category
                        AND R.id != P.id
                        AND R.active = 1
                        ORDER BY R.date DESC
                        LIMIT 3
                    ) AS notasRecomendadas
                    FROM blogs P
                    LEFT JOIN blogs_categories
                        ON P.category = blogs_categories.id
                    LEFT JOIN (
                        SELECT 
                            PC.blog, 
                            PC.order,
                            JSON_OBJECT(
                                'id', IF(PC.type = 'video',
                                    (SELECT MIN(id)
                                    FROM blog_content v
                                    WHERE v.blog = PC.blog 
                                    AND v.order = PC.order 
                                    AND v.type = 'video'),
                                    PC.id),
                                'name', PC.name,
                                'type', PC.type,
                                'text', IF(PC.type = 'text', PC.text, NULL),
                                'text_en', IF(PC.type = 'text', PC.text_en, NULL),
                                'video', IF(PC.type = 'video', PC.video, NULL),
                                'video_en', IF(PC.type = 'video', PC.video_en, NULL),
                                'image', (
                                    SELECT JSON_ARRAYAGG(COALESCE(SF.fileName, 'noimage.jpg'))
                                    FROM sys_files SF
                                    WHERE SF.tableName = 'blog_content'
                                    AND SF.columnName = 'file_images'
                                    AND SF.publish = 1
                                    AND SF.rowId = PC.id
                                ),
                                'image_en', (
                                    SELECT JSON_ARRAYAGG(COALESCE(SF.fileName, 'noimage.jpg'))
                                    FROM sys_files SF
                                    WHERE SF.tableName = 'blog_content'
                                    AND SF.columnName = 'file_images_en'
                                    AND SF.publish = 1
                                    AND SF.rowId = PC.id
                                ),
                                'carrusel', PC.carrusel
                            ) AS bloque_json
                        FROM blog_content PC
                        WHERE PC.active = 1
                        GROUP BY PC.blog, PC.type, PC.order
                    ) AS blocks ON P.id = blocks.blog
                    LEFT JOIN (
                        SELECT fileName, rowId 
                        FROM sys_files 
                        WHERE tableName = 'blogs' 
                        AND columnName = 'file_thumbnail' 
                        AND publish = 1
                    ) AS thumbnail_file ON thumbnail_file.rowId = P.id
                    WHERE P.active = 1
                    GROUP BY P.id
                    ORDER BY P.order ASC;
                    ",
    'blogs_featured' => "SELECT
                    P.id,
                    P.name,
                    P.name_en,
                    P.slugName,
                    REPLACE(REPLACE(REPLACE(REPLACE(P.name_en, '|azul-', ''), '-azul|', ''), '|celeste-', ''), '-celeste|', '')
                        AS slugName_en,
                    P.text,
                    P.text_en,
                    P.category,
                    P.date,
                    blogs_categories.name AS catName,
                    blogs_categories.name_en AS catName_en,
                    COALESCE(NULLIF(thumbnail_file.fileName, ''), 'noimage.png') AS thumbnail
                    FROM blogs P
                    LEFT JOIN blogs_categories
                        ON P.category = blogs_categories.id
                    LEFT JOIN (
                        SELECT fileName, rowId 
                        FROM sys_files 
                        WHERE tableName = 'blogs' 
                        AND columnName = 'file_thumbnail' 
                        AND publish = 1
                    ) AS thumbnail_file ON thumbnail_file.rowId = P.id
                    WHERE P.active = 1 AND P.featured = 1
                    GROUP BY P.id
                    ORDER BY P.order ASC
                    ",

    'blogs_categories' => "SELECT T1.*
                    FROM blogs_categories AS T1
                    WHERE T1.active = 1
                    ORDER BY T1.order ASC;",

    'services' => "SELECT T1.*,
                    services_categories.name as catName,
                    services_categories.name_en as catName_en
                    FROM services AS T1
                    LEFT JOIN services_categories
                        ON T1.category = services_categories.id
                    ORDER BY T1.order DESC;",

    'services_featured' => "SELECT T1.*,
                    services_categories.name as catName,
                    services_categories.name_en as catName_en
                    FROM services AS T1
                    LEFT JOIN services_categories
                        ON T1.category = services_categories.id
                    WHERE T1.active = 1
                    AND T1.featured = 1
                    ORDER BY T1.order DESC;",

    'services_categories' => "SELECT T1.*,
                    COALESCE(NULLIF(icon_file.fileName, ''), 'noimage.png') AS icon
                    FROM services_categories AS T1
                    LEFT JOIN (
                        SELECT fileName, rowId 
                        FROM sys_files 
                        WHERE tableName = 'services_categories' 
                        AND columnName = 'file_icono' 
                        AND publish = 1
                    ) AS icon_file ON icon_file.rowId = T1.id
                    ORDER BY T1.order ASC;",

    'reviews' => "SELECT T1.*
                    FROM reviews AS T1
                    ORDER BY T1.order ASC;",

    'aboutus' => "SELECT T1.*
                    FROM aboutus AS T1 LIMIT 1;",

    'privacy_policy' => "SELECT T1.*
                    FROM privacy_policy AS T1 LIMIT 1;",

    'hubspot_landings' => "SELECT T1.*, T1.name_en as slugName
                    FROM hubspot_landings AS T1;",

    'work_travel' => "SELECT T1.*,
                    COALESCE(NULLIF(file_image.fileName, ''), 'noimage.png') AS banner
                    FROM work_travel AS T1
                        LEFT JOIN (
                            SELECT fileName, rowId 
                            FROM sys_files 
                            WHERE tableName = 'work_travel' 
                            AND columnName = 'file_imagen' 
                            AND publish = 1
                        ) AS file_image ON file_image.rowId = T1.id
                    LIMIT 1;",

    'states' => "SELECT T1.*
                    FROM states AS T1
                    ORDER BY T1.name DESC;",

    'work_travel_faqs' => "SELECT T1.*
                    FROM work_travel_faqs AS T1
                    WHERE T1.active = 1
                    ORDER BY T1.order DESC;",

    'work_travel_testimonials' => "SELECT T1.*
                    FROM work_travel_testimonials AS T1
                    WHERE T1.active = 1
                    ORDER BY T1.order ASC;",

    'work_travel_steps' => "SELECT T1.*
                    FROM work_travel_steps AS T1
                    WHERE T1.active = 1
                    ORDER BY T1.order ASC;",
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
    if ($tableSelected === 'blogs' || $tableSelected === 'blogs_featured') {
        foreach ($rows as &$row) {
            $row['slug'] = toSlug($row['slugName']);
        }
    }


    
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
    }
    if ($tableSelected === 'services' || $tableSelected === 'services_featured') {
        foreach ($rows as &$row) {
            if (str_contains(' - ', $row['catName'])) {
                $parentname = explode(' - ', $row['catName'])[0];
                $row['catSlug'] = toSlug($parentname);
            } else {
                $row['catSlug'] = toSlug($row['catName']);
            }
            $row['slug'] = toSlug($row['name']);
        }
    }

    if ($tableSelected === 'services_categories') {
        foreach ($rows as &$row) {
            if (str_contains(' - ', $row['name'])) {
                $parentname = explode(' - ', $row['name'])[0];
                $row['slug'] = toSlug($parentname);
            } else {
                $row['slug'] = toSlug($row['name']);
            }
        }
    }
    // Output response in JSON format
    echo json_encode($rows, JSON_NUMERIC_CHECK);
} else {
    // Handle invalid table selection
    echo json_encode(['error' => 'Invalid table selected']);
}
