<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeControllerNice extends Command
{
    protected $signature = 'make:controller:nice {name}';
    protected $description = 'Create controller with clickable path';

    public function handle()
    {
        $name = $this->argument('name');

        $this->call('make:controller', [
            'name' => $name
        ]);

        $path = "app/Http/Controllers/{$name}.php";

        $this->info("Controller created: {$path}");
    }
}
