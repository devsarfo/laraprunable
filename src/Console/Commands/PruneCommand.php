<?php

namespace DevSarfo\LaraPrunable\Console\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;

class PruneCommand extends \Illuminate\Database\Console\PruneCommand
{
    /**
     * Determine the models that should be pruned.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function models()
    {
        $models = $this->option('model');
        $except = $this->option('except');

        if ($models && $except) {
            throw new InvalidArgumentException('The --models and --except options cannot be combined.');
        }

        if ($models) {
            return (new Collection($models))
                ->filter(static fn (string $model) => class_exists($model))
                ->values();
        }

        return (new Collection(Finder::create()->in($this->getPath())->files()->name('*.php')))
            ->map(function ($model) {
                $namespace = $this->laravel->getNamespace();

                return $namespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($model->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
                );
            })
            ->when(! empty($except), fn ($models) => $models->reject(fn ($model) => in_array($model, $except)))
            ->filter(fn ($model) => $this->isPrunable($model))
            ->values();
    }

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
            \Illuminate\Database\Eloquent\Prunable::class,
            \Illuminate\Database\Eloquent\MassPrunable::class,
            \DevSarfo\LaraPrunable\Traits\SoftPrunable::class,
            \DevSarfo\LaraPrunable\Traits\SoftMassPrunable::class,
        ]));
    }
}
