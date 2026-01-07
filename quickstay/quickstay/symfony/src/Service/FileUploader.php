<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger
    ) {
    }

    public function upload(UploadedFile $file, ?string $subdirectory = null): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $targetDir = $subdirectory
            ? $this->targetDirectory . '/' . $subdirectory
            : $this->targetDirectory;

        try {
            $file->move($targetDir, $fileName);
        } catch (FileException $e) {
            throw new \Exception('Erreur lors de l\'upload du fichier: ' . $e->getMessage());
        }

        return 'uploads/properties/' . $fileName;
    }

    public function remove(string $filename): bool
    {
        $filePath = $this->targetDirectory . '/' . $filename;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
