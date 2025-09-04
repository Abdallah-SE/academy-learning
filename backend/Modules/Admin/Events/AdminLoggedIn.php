<?php

namespace Modules\Admin\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Admin\Models\Admin;

class AdminLoggedIn
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Admin $admin,
        public string $ip,
        public string $userAgent
    ) {}
}
