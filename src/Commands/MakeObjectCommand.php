<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Commands;

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
        return $this->option('standard')
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
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace.'\Objects';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['standard', 's', InputOption::VALUE_OPTIONAL, 'The salesforce standard object'],
            ['custom', 'c', InputOption::VALUE_OPTIONAL, 'The salesforce custom object'],
        ];
    }
}
