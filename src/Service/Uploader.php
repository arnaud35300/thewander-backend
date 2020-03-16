<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class Uploader
{
    const EXTENSIONS = ['jpg', 'jpeg', 'png'];

    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }
    public function upload(string $path, string $name) //: bool
    {
        $file = $this->request->files->get('image');

        if ($file === null)
            return false;

        if ($file->getError() > 0)
            return false;

        if ($file->getSize() >= 1000000)
            return false;

        if (!in_array($file->guessExtension(), self::EXTENSIONS))
            return false;

        $filename = $name . '.' . $file->guessExtension();
        $directory = __DIR__ . '/../../public/images/' . $path;
        $file->move($directory, $filename);

        // $this->resize($directory, 100, 100);
    }

    public function resize($file, $w, $h, $crop = false)
    {
        list($width, $height) = getimagesize($file);

        $r = $width / $height;

        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    }
}
