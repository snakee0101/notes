<?php

namespace Tests\Unit;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImageTest extends TestCase
{
    public function test_when_upload_has_been_processed_valid_paths_are_returned()
    {
        Storage::fake();

        Storage::makeDirectory('images');
        Storage::makeDirectory('thumbnails_large');
        Storage::makeDirectory('thumbnails_small');

        $file = UploadedFile::fake()
            ->image('test.jpg', 1000, 1000);

        $paths = Image::processUpload($file);

        Storage::assertExists( Str::after($paths['image_path'], '/storage/') );
        Storage::assertExists( Str::after($paths['thumbnail_small_path'], '/storage/') );
        Storage::assertExists( Str::after($paths['thumbnail_large_path'], '/storage/') );
    }
}
