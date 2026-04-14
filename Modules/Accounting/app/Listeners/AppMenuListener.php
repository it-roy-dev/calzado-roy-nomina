<?php

namespace Modules\Accounting\Listeners;

use App\Events\AppMenuEvent;

class AppMenuListener
{
    public function __invoke(AppMenuEvent $event): void
    {
        // Manejado desde AppMenuListener principal
    }
}