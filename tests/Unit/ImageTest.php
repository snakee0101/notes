<?php

namespace Tests\Unit;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        Storage::disk('public')->makeDirectory('images');
        Storage::disk('public')->makeDirectory('thumbnails_large');
        Storage::disk('public')->makeDirectory('thumbnails_small');
    }

    public function test_when_upload_has_been_processed_valid_paths_are_returned()
    {
        $paths = Image::processUpload(
            UploadedFile::fake()
            ->image('test.jpg', 1000, 1000)
        );

        Storage::disk('public')->assertExists($paths['image_path']);
        Storage::disk('public')->assertExists($paths['thumbnail_small_path']);
        Storage::disk('public')->assertExists($paths['thumbnail_large_path']);
    }

    public function test_when_image_is_created_text_is_automatically_recognized_by_tesseract_OCR()
    {
        $image = imagecreate(200, 200);
        $color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $font_path = 'storage/app/Roboto-Light.ttf';

        imagefttext($image, 20, 0, 40,40, $text_color, $font_path,'test OCR');
        imagejpeg($image, Storage::disk('public')->path('test_OCR.jpg'));

        $image_model = Image::create([
            'note_id' => Note::factory()->create()->id,
            'image_path' => 'test_OCR.jpg',
            'thumbnail_small_path' => 'fake',
            'thumbnail_large_path' => 'fake'
        ]);

        imagedestroy($image);

        $this->assertEquals('test OCR', $image_model->fresh()->recognized_text);
    }
}
