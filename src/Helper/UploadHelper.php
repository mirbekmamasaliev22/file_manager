<?php
namespace App\Helper;

class UploadHelper
{

    public function addThumbnail($pdfFileName, $file) {
        $targetDir = $this->getThumbnailDir($pdfFileName);

        $filename = $this->getFilename($file["name"]);

        if (!$this->isImageFile($filename)) {
            throw new \Exception('File type must be: jpg, jpeg, png, gif, bpm.');
        }

        if ($this->isExist($targetDir, $filename)) {
            $filename = uniqid().'.jpg';
        }

        $filePath = $targetDir . $filename;
        $result = move_uploaded_file($file["tmp_name"], $filePath);

        if ($result) {
            return $filePath;
        } else {
            throw new \Exception('Something goes wrong. Try again');
        }
    }

    public function removePDF($filename) {
        $pdfDir = $this->getPdfDir();
        if ($this->isExist($pdfDir, $filename)) {
            return unlink($pdfDir.$filename);
        }
        return true;
    }

    public function removeThumbnail($pdfFileName, $thumbnailFileName) {
        $path = $this->getThumbnailDir($pdfFileName);
        $thumbnailPath = $path.$thumbnailFileName;
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }
    public function removeThumbnails($filename) {
        $path = $this->getThumbnailDir($filename);
        return $this->recurseRmdir($path);
    }

    public function generatePDFThumbnails($pdfPath) {
        $thumbnails = [];
        $images = new \Imagick($pdfPath);
        $file_name = $this->getFilename($pdfPath);
        $path = $this->getThumbnailDir($file_name);
        foreach($images as $i=>$image) {
            $thumbnail = sprintf("page_%d.jpg", $i);
            $thumbnails[] = $thumbnail;
            $image->setResolution(300,300);
            $image->writeImage($path.$thumbnail);
        }
        return $thumbnails;
    }

    protected function getThumbnailDir($filename) {
        $dir = 'uploads/thumbnails/'.$filename.'/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    public function uploadPDF($file) {
        $filename = $this->getFilename($file["name"]);

        if (!$this->isPDF($filename)) {
            throw new \Exception('File type must be PDF');
        }

        $targetDir = $this->getPdfDir();
        if ($this->isExist($targetDir, $filename)) {
            $filename = uniqid().'.pdf';
        }

        $filePath = $targetDir . $filename;
        $result = move_uploaded_file($file["tmp_name"], $filePath);

        if ($result) {
            return $filePath;
        } else {
            throw new \Exception('Something goes wrong. Try again');
        }
    }

    public function getThumbnailFilePath($pdfFileName, $thumbnailName) {
        return $this->getThumbnailDir($pdfFileName).$thumbnailName;
    }

    public function getPDFFilePath($filename) {
        return $this->getPdfDir().$filename;
    }

    protected function isExist($pdfDir, $filename) {
        return file_exists($pdfDir.$filename);
    }

    public function getFilename($file) {
        return basename($file);
    }

    protected function isPDF($filename) {
        $imageFileType  = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        return $imageFileType == "pdf";
    }

    protected function isImageFile($filename) {
        $imageFileType  = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        return in_array($imageFileType, ["jpg", 'jpeg', "png", "gif", "bpm"]);
    }

    protected function getPdfDir() {
        $dir = 'uploads/pdf/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    function recurseRmdir($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recurseRmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}