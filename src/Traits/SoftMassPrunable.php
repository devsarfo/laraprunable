<?php

namespace DevSarfo\LaraPrunable\Traits;

use Illuminate\Database\Events\ModelsPruned;
use LogicException;

trait SoftMassPrunable
{
    /**
     * Prune all prunable models in the database.
     *
     * @return int
     */
    public function pruneAll(int $chunkSize = 1000)
    {
        $query = tap($this->prunable(), function ($query) use ($chunkSize) {
            $query->when(! $query->getQuery()->limit, function ($query) use ($chunkSize) {
                $query->limit($chunkSize);
            });
        });

        $total = 0;

        do {
            $total += $count = $query->delete();

            if ($count > 0) {
                event(new ModelsPruned(static::class, $total));
            }
        } while ($count > 0);

        return $total;
    }

    /**
     * Get the prunable model query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        throw new LogicException('Please implement the prunable method on your model.');
    }
}
