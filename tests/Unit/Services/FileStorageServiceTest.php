<?php

use App\Services\FileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->fileStorageService = new FileStorageService();
    Storage::fake('public');
});

test('storeFile correctly stores file and returns path', function () {
    $file = UploadedFile::fake()->image('test.jpg');
    $path = 'test-dir';

    $filePath = $this->fileStorageService->storeFile($file, $path);

    expect($filePath)->toContain($path)
                     ->and(Storage::disk('public')->exists($filePath))->toBeTrue();
});

test('deleteFile removes existing file and returns true', function () {
    $file = UploadedFile::fake()->image('test.jpg');
    $filePath = Storage::disk('public')->putFile('test-dir', $file);

    expect(Storage::disk('public')->exists($filePath))->toBeTrue();

    $result = $this->fileStorageService->deleteFile($filePath);

    expect($result)->toBeTrue()
                   ->and(Storage::disk('public')->exists($filePath))->toBeFalse();
});

test('deleteFile returns false for non-existent file', function () {
    $result = $this->fileStorageService->deleteFile('non-existent-file.jpg');

    expect($result)->toBeFalse();
});

test('fileExists returns correct boolean status', function () {
    $file = UploadedFile::fake()->image('test.jpg');
    $filePath = Storage::disk('public')->putFile('test-dir', $file);

    expect($this->fileStorageService->fileExists($filePath))->toBeTrue()
                                                            ->and($this->fileStorageService->fileExists('non-existent.jpg'))->toBeFalse();
});

test('getFileSizeInKB returns correct file size', function () {
    $file = UploadedFile::fake()->image('test.jpg')->size(200); // 200 KB

    $sizeInKB = $this->fileStorageService->getFileSizeInKB($file);

    expect($sizeInKB)->toBe(200);
});

test('isFileSizeExceeded returns correct boolean for file size limit', function () {
    $smallFile = UploadedFile::fake()->image('small.jpg')->size(100); // 100 KB
    $largeFile = UploadedFile::fake()->image('large.jpg')->size(600); // 600 KB

    expect($this->fileStorageService->isFileSizeExceeded($smallFile, 512))->toBeFalse()
                                                                          ->and($this->fileStorageService->isFileSizeExceeded($largeFile, 512))->toBeTrue();
});
