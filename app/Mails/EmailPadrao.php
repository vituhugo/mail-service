<?php
namespace Mails;


use Illuminate\Mail\Mailable;

class EmailPadrao extends Mailable
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->data['remetente']) $this->from($this->data['remetente']);

        if ($this->data['anexos']) {
            foreach ($this->data['anexos'] as $anexo) {
                if (is_array($anexo)) {
                    $this->attachFromStorage(...$anexo);
                    continue;
                }
                $this->attachFromStorage($anexo);
            }
        }

        return $this
            ->to($this->data['para'])
            ->subject($this->data['assunto'])
            ->view($this->data['tipo'])
            ->with($this->data['contexto']);
    }
}
