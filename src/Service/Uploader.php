<?php

namespace App\Service;

use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\RequestStack;

class Uploader
{
    const EXTENSIONS = ['jpg', 'jpeg', 'png'];

    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }
    
    public function upload(string $path, string $name, int $height = 200, int $width = 200): bool
    {
        $file = $this->request->files->get('image');
        $tmp = $file->getPathname();

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

        $image = Image::make($tmp);
        $image->fit($width, $height);
        $image->save($directory . '/' . $filename);

        return true;
    }
}
