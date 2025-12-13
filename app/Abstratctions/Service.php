<?php


namespace App\Abstratctions;


use App\Mail\MailService;
use Illuminate\Http\Request;

abstract class Service
{

    public function clean_request(Request $request): void
    {
        if ($request->has('_method'))
            $request->request->remove('_method');
        if ($request->has('_token'))
            $request->request->remove('_token');
    }

    /**
     * @param $user
     * @param $request
     * @param $queue
     * @return void
     */
    public function sendMail(
        string $email,
        string $subject,
        string $template,
        array $data,
        bool|int $isMarkDown = false
    ): void
    {
        $mailService = new MailService(
            subject: $subject,
            template: $template,
            data: $data,
            isMarkDown: $isMarkDown
        );
        $mailService->sendMail($email, false);
    }

}
