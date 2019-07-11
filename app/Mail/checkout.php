<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class checkout extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $pdf_path;
    public $day;
    public $price;

    public function __construct(User $user, String $path, String $day, String $price)
    {
        //
        $this->user = $user;
        $this->pdf_path = $path;
        $this->day = $day;
        $this->price = $price;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('checkout')->attach($this->pdf_path);
    }
}
