<?php
class Folder
{
    private $root;
    private $name;
    private $files;
    private $folders;
    private $rootPath;

    function __construct($root, $name)
    {
        $this->root = $root;
        $this->name = $name;
        $this->files = [];
        $this->folders = [];
        $this->setRootPath();
    }

    function getStructure()
    {
        $retVal = new stdClass();
        $retVal->name = $this->name;
        $retVal->files = $this->files;
        $retVal->folders = $this->folders;
        return $retVal;
    }

    private function setRootPath()
    {
        $rootPath = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        $routes = explode('/', $rootPath);
        $toReplace = $routes[count($routes) - 2];
        $rootPath = str_replace($toReplace . '/', '/', $rootPath) . $this->root;
        $rootPath = str_replace('/../', '', $rootPath);
        $this->rootPath = $rootPath;
    }

    function getContent()
    {
        $content = scandir($this->root);
        for ($i = 0; $i < count($content); $i++) {
            if (!is_dir($this->root . '/' . $content[$i])) {
                list($width, $height, $type) = [0, 0, 0];
                $file = new stdClass();
                $file->URI = $this->rootPath . '/' . $content[$i];
                $file->width = $width;
                $file->height = $height;
                $file->type = $type;

                $this->files[] = $file;
            } else {
                if ($content[$i] !== '.' && $content[$i] !== '..') {
                    $subDir = new Folder($this->root . '/' . $content[$i], $content[$i]);
                    $subDir->getContent();
                    $this->folders[] = $subDir->getStructure();
                }
            }
        }
    }

    function createFolder($name)
    {
        $path = $this->root . '/' . $name;
        if (file_exists($path)) {
            return 409;
        }
        if (mkdir($path, 0777, true)) {
            return 200;
        } else {
            return 400;
        }
    }

    function uploadFile()
    {
        try {
            $fileName   = $_FILES['fileKey']['name'];
            $temp       = explode('.', $fileName);
            $extension  = end($temp);
            $name       = str_replace('.' . $extension, '', $fileName);
            $name       = str_replace(' ', '_', $name);
            $fileName   = $name . '.' . $extension;

            $uploadFolder = $this->root . '/';

            if (file_exists($uploadFolder . $fileName)) {
                $fileName = $name . '__' . time() . '.' . $extension;
            }
            $_FILES['fileKey']['tmp_name'] = str_replace(' ', '', $_FILES['fileKey']['tmp_name']);
            move_uploaded_file($_FILES['fileKey']['tmp_name'], $uploadFolder . '/' . $fileName);

            $file = new stdClass();
            $file->URI = $fileName;

            echo json_encode($file);
            return $file;
        } catch (Throwable $error) {
            return false;
        }
    }

    function deleteFile($file)
    {
        if (file_exists($this->root . '/' . $file)) {
            return unlink($this->root . '/' . $file);
        }
        return false;
    }

    function deleteFolder($path, $firstCall = true)
    {
        if ($firstCall) {
            $path = $this->root . '/' . $path;
        }

        if (!is_dir($path)) {
            return false;
        }

        $files = scandir($path);
        $files = array_diff($files, ['.', '..']);


        if (count($files) > 0) {
            return false;
        } else {
            return rmdir($path);
        }
        die;

        foreach ($files as $file) {
            $pathFile = $path . '/' . $file;

            if (is_dir($pathFile)) {
                $this->deleteFolder($pathFile, false);
            } else {
                unlink($pathFile);
            }
        }

        return rmdir($path);
    }
}
