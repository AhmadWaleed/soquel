<?php

namespace AhmadWaleed\Soquel\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeObjectCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:object';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new salesforce object class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Object';

    protected function getStub(): string
    {
        return $this->option('type') === 'standard'
            ? $this->resolveStubPath('/stubs/standard-object.stub')
            : $this->resolveStubPath('/stubs/custom-object.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        return __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\'.config('soquel.app_path', 'Objects');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['type', 't', InputOption::VALUE_OPTIONAL, 'The salesforce object types: (custom, standard)'],
        ];
    }
}
