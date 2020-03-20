<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Intervention\Image\ImageManagerStatic as Image;

class Uploader
{
    const EXTENSIONS = ['jpg', 'jpeg', 'png'];
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Uploades an image within the public images folder.
     * 
     * @param string $path The image's path.
     * @param string $name The image's name.
     * @param string $suffix The image's type suffix (e.g. _picture, _avatar).
     * @param string $field The returned array's index name.
     * @param int $width The image's width.
     * 
     * @return array
     */
    public function upload(string $path, string $name, string $suffix, string $field, int $width = 400): array
    {
        $file = $this->request->files->get($field);        

        $errors = array();
        $success = array();

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
        $directory = __DIR__ . '/../../public/assets/images/' . $path;

        $image = Image::make($pathname);
        
        $image->fit($width);
        $image->save($directory . '/' . $filename);

        $success[$field] = $filename;
        $success['status'] = true;

        return $success;
    }
}
