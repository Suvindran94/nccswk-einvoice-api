<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service class (e.g., EInvoice/SubmitDocumentService)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class, supporting nested directories via namespace.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Normalize the name to use directory separators and get parts
        $name = str_replace('\\', '/', $name); // Convert backslashes to forward slashes for path handling
        $parts = explode('/', $name);

        // The actual class name is the last part
        $className = array_pop($parts);

        // The namespace/subdirectory path is the remaining parts
        $subDirectory = implode('/', $parts);
        $namespaceSuffix = ! empty($subDirectory) ? '\\'.str_replace('/', '\\', $subDirectory) : '';

        // Define the base path for services
        $baseServicePath = app_path('Services');

        // Construct the full directory path for the service class
        $serviceDirectory = ! empty($subDirectory) ? "{$baseServicePath}/{$subDirectory}" : $baseServicePath;

        // Ensure the directory structure exists
        if (! $this->files->isDirectory($serviceDirectory)) {
            $this->files->makeDirectory($serviceDirectory, 0755, true, true); // Recursive and force
            $this->info("Created directory: {$serviceDirectory}");
        }

        // Construct the full file path
        $filePath = "{$serviceDirectory}/{$className}.php";

        // Check if the service class already exists
        if ($this->files->exists($filePath)) {
            $this->error("Service class [{$className}] already exists at {$filePath}!");

            return parent::FAILURE; // Use parent::FAILURE for static analysis tools
        }

        // Define the basic stub for the service class, passing the namespace suffix
        $stub = $this->getServiceStub($className, $namespaceSuffix);

        // Write the stub content to the file
        $this->files->put($filePath, $stub);

        $this->info("Service class [{$className}] created successfully at {$filePath}");

        return parent::SUCCESS; // Use parent::SUCCESS for static analysis tools
    }

    /**
     * Get the service class stub.
     */
    protected function getServiceStub(string $className, string $namespaceSuffix): string
    {
        $stub = <<<EOT
            <?php

            namespace App\Services{$namespaceSuffix};

            class DummyClass
            {
                /**
                 * Create a new service instance.
                 *
                 * @return void
                 */
                public function __construct()
                {
                    //
                }

                /**
                 * Example method for the service.
                 *
                 * @param  string \$message
                 * @return string
                 */
                public function doSomething(string \$message): string
                {
                    return "Service {$className} received: " . \$message;
                }
            }
        EOT;

        return str_replace('DummyClass', $className, $stub);
    }
}
