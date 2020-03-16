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

    public function upload(string $path, string $name, string $suffix, int $width = 400): array
    {
        $file = $this->request->files->get('picture');        

        $errors = array();
        $success = array();

        if ($file === null) {
            $errors['file'] = 'Invalid file name.';
            
            return $errors;
        }

        if ($file->getError() > 0)
            $errors['upload'] = 'An error occurred while uploading the file.';

        if ($file->getSize() >= 1000000)
            $errors['size'] = 'Invalid file size.';

        if (!in_array($file->guessExtension(), self::EXTENSIONS))
            $errors['extension'] = 'Invalid extension. You can only upload .jpg, .jpeg and .png files.';

        if (count($errors) > 0) {
            $errors['status'] = false;
            
            return $errors;
        }

        $pathname = $file->getPathname();
        $filename = $name . $suffix . '.' . $file->guessExtension();
        $directory = __DIR__ . '/../../public/images/' . $path;

        $image = Image::make($pathname);
        
        $image->fit($width);
        $image->save($directory . '/' . $filename);

        $success['picture'] = $filename;
        $success['status'] = true;

        return $success;
    }
}
