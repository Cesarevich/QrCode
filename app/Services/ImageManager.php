<?php

namespace App\Services\ImageManager;

use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageManager
{
    /**
     * The quality the image should be saved in.
     *
     * @var int
     */
    protected $quality = 85;

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Создание объекта ImageManager.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return void
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem;
    }

    /**
     * Генерация имени файла.
     *
     * @return string
     */
    protected function generateFilename($randomStr, $extension, $width = null, $height = null, $solt = null)
    {
        $filename = $randomStr;

        if (! is_null($width) && ! is_null($height)) {
            $filename = $width.'x'.$height.'-'.$filename;
        }

        if (! is_null($solt)) {
            $filename = $prefix.$delimiter.$filename;
        }

        return $filename.'.'.$extension;
    }

    /**
     * Удаление картинки и всех превьюшек
     *
     * @param  string $filename
     * @param  array $config
     * @return void
     */
    public function delete($filename, $config)
    {
        list($first, $second) = make_parts($filename);
        $path = implode('/', [$config['path'], $first, $second, '']);

        $files[] = $path.$filename;

        foreach ($config['thumbs'] as $thumb) {
            list($width, $height) = $thumb;
            $files[] = sprintf('%s%dx%d-%s', $path, $width, $height, $filename);
        }

        $this->filesystem->delete($files);
    }

    /**
     * Загрузка катинки и создание превьюшек
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $file
     * @param  array $config
     * @param  string $salt
     * @return string|bool
     */
    public function upload(UploadedFile $file, $config, $salt = '')
    {
        $createdFiles = [];
        $extension = $file->getClientOriginalExtension();
        $randomStr = strtolower(str_random(32 - strlen($salt)).$salt);

        list($first, $second) = make_parts($randomStr);
        $path = implode('/', [$config['path'], $first, $second, '']);

        try {
            $original = sprintf('%s.%s', $randomStr, $extension);
            $file->move($path, $original);
            $createdFiles[] = $path.$original;

            foreach ($config['thumbs'] as $thumb) {
                list($width, $height) = $thumb;
                $filename = sprintf('%dx%d-%s.%s', $width, $height, $randomStr, $extension);
                $success = Image::make($path.$original)
                    ->fit($width, $height)
                    ->save($path.$filename, $this->quality);

                if (! $success) {
                    throw new \Exception;
                }

                $createdFiles[] = $path.$filename;
            }

            return $original;
        } catch (\Exception $e) {
            //@todo Upload image Exception
            $this->filesystem->delete($createdFiles);

            return false;
        }
    }
}