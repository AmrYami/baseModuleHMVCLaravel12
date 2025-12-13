<?php

namespace App\Services;

//use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Support\Facades\Storage;
use Users\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;


class IdCardService
{
    protected ImageManager $image;

    public function __construct()
    {
        // Use Imagick for widest format support
        $this->image = ImageManager::withDriver(new Driver());
    }

    public function generate(User $user)
    {
        // 1. Load your local template (always local)
        $template = $this->image->read(
            file_get_contents(public_path('assets/images/thumbnail_ids.png'))
        );

        // 2. Grab the user's avatar media
        $photoMedia = $user->getFirstMedia('avatar');
        if (!$photoMedia) {
            try {
                $mailService = new \App\Mail\MailService(
                    subject: 'accounts doesnt have avatar',
                    template: 'list_email',
                    data: ['emails' => [$user->email]]
                );
                $mailService->sendMail('amalhasani@fakeeh.care', false);
            } catch (\Exception $exception) {

            }
            return 0;
        }

        // 3. Fetch raw bytes depending on disk
        $disk = $photoMedia->disk;                // e.g. "s3" or "public"
        $path = $photoMedia->getPath();           // path on that disk

//        if ($disk === 's3') {
//            // Pull down from S3
        $rawPhoto = Storage::disk('s3')->get($path);
//        } else {
        // Local disk, just read it
//            $rawPhoto = file_get_contents(public_path('assets/images/00.JPG'));
//            $rawPhoto = file_get_contents($photoMedia->getPath());
//        }
        $img = $this->image->read($rawPhoto);

// 2) figure out the largest centered square
        $origW = $img->width();
        $origH = $img->height();
        $side = min($origW, $origH);

// 3) pick your vertical offset (e.g. 20% down from the very top)
        $yOffset = intval(($origH - $side) * 0.20);

// 4) crop the exact square, then resize
        $photo = $img
            ->crop($side, $side, 0, $yOffset)  // crop a $side×$side square, offset 0×$yOffset
            ->resize(300, 290);
        // 4. Resize & crop to 300×300
//        $photo = $this->image
//            ->read($rawPhoto)
//            ->cover(300, 290);
// card dimensions
        $cardW = $template->width();
        $cardH = $template->height();

// photo W/H
        $photoW = $photo->width();
        $photoH = $photo->height();

        $x = (int)(($cardW - $photoW) / 2);

        // 5. Composite onto the template
//        $template->place($photo, 'top-left', $x, 150);
// 3. place photo centered 150px down
        $template->place($photo, 'top', 0, 100);
//        $template->place($photo, 'top-left', 50, 150);
        $centerX = $template->width() / 2;

        // 4. draw centered text
        $centerX = $template->width() / 2;
        $template->text($user->first_name . ' ' . $user->second_name . ' ' . $user->last_name . ' ', $centerX, 420,
            fn($f) => $f->file(public_path('fonts/Arial-Bold.ttf'))->align('center')->valign('top')->size(36));
        $template->text($user->speciality, $centerX, 475,
            fn($f) => $f->file(public_path('fonts/Arial-Bold.ttf'))->align('center')->valign('top')->size(28));
        $template->text("ID: {$user->id}", $centerX, 510,
            fn($f) => $f->file(public_path('fonts/Arial-Bold.ttf'))->align('center')->valign('top')->size(24));


        // 4. Generate a 150×150 QR as raw PNG
        $url = config('app.url') . '/user_show/' . $user->id;
        $renderer = new GDLibRenderer(115);       // size = 150px :contentReference[oaicite:0]{index=0}
        $writer = new Writer($renderer);
        $rawQr = $writer->writeString($url);   // PNG binary

        // 5. Composite the QR at bottom-center, 20px up
        $qrImage = $this->image->read($rawQr);
        $template->place($qrImage, 'bottom-center', 0, 0);


        // 7. Save & attach via Spatie Media
        $out = storage_path("app/public/ids/idcard_{$user->id}.png");
        $template->save($out);

        $media = $user
            ->addMedia($out)
            ->usingName("ID Card #{$user->id}")
            ->toMediaCollection('id_cards');

// now remove the local file
        if (file_exists($out)) {
            unlink($out);
        }

        return $media;
//        return $user
//            ->addMedia($out)
//            ->usingName("ID Card #{$user->id}")
//            ->toMediaCollection('id_cards');
    }
}
