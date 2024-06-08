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

    public function test_when_upload_has_been_processed_image_contents_are_returned()
    {
        $image_fields = Image::processUpload(
            $file = UploadedFile::fake()->image('test.jpg')
        );

        $this->assertEquals(utf8_encode($file->getContent()), $image_fields['image']);
        $this->assertNotEmpty($image_fields['thumbnail_small']);
        $this->assertNotEmpty($image_fields['thumbnail_large']);
    }

    public function test_when_image_is_created_text_is_automatically_recognized_by_tesseract_OCR()
    {
        $image_model = Image::factory()
            ->forOCR()
            ->create();

        $this->assertEquals('test OCR', $image_model->fresh()->recognized_text);
    }
}
