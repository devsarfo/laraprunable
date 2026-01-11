<?php

namespace DevSarfo\LaraPrunable\Console\Commands;

use DevSarfo\LaraPrunable\Traits\SoftMassPrunable;
use DevSarfo\LaraPrunable\Traits\SoftPrunable;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Prunable;

class PruneCommand extends \Illuminate\Database\Console\PruneCommand
{
    /**
     * Determine if the given model class is prunable.
     *
     * @param  string  $model
     * @return bool
     */
    protected function isPrunable($model)
    {
        $uses = class_uses_recursive($model);

        return ! empty(array_intersect($uses, [
            Prunable::class,
            MassPrunable::class,
            SoftPrunable::class,
            SoftMassPrunable::class,
        ]));
    }
}
