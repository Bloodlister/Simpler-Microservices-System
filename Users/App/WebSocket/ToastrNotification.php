<?php declare(strict_types=1);

namespace App\WebSocket;

class ToastrNotification extends Message
{
    public const TYPE = 'toastr_notification';

    public const STATUS_SUCCESS = 'success';
    public const STATUS_INFO = 'info';
    public const STATUS_ERROR = 'error';
    public const STATUS_WARNING = 'warning';

    protected string $status;

    protected string $title;

    protected string $desc;

    public function __construct(string $status, string $title, string $desc = '')
    {
        $this->status = $status;
        $this->title = $title;
        $this->desc = $desc;
    }

    public function unpack(): array
    {
        return [
            'action' => static::TYPE,
            'data' => [
                'status' => $this->status,
                'title' => $this->title,
                'desc' => $this->desc,
            ]
        ];
    }
}
