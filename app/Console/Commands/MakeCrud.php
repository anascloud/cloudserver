<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrud extends Command
{
    protected $signature = 'make:crud {name : Name, for example Expense or Fm/Expense}
        {--model= : Fully qualified model class}
        {--table= : Database table name}
        {--route= : Route URI}
        {--force : Overwrite existing files}';

    protected $description = 'Generate a CRUD model, controller and route using ApiCrudController';

    public function handle()
    {
        $input = trim($this->argument('name'), '/\\');
        $parts = preg_split('/[\/\\\\]/', $input);
        $baseName = Str::studly(array_pop($parts));
        $subDirectory = implode('/', array_map([Str::class, 'studly'], $parts));
        $subNamespace = str_replace('/', '\\', $subDirectory);
        $controllerNamespace = 'App\\Http\\Controllers' . ($subNamespace ? '\\' . $subNamespace : '');
        $controllerClass = $baseName . 'Controller';
        $controllerPath = app_path(
            'Http/Controllers/' . ($subDirectory ? $subDirectory . '/' : '') . $controllerClass . '.php'
        );

        $modelClass = $this->option('model')
            ?: 'App\\Models' . ($subNamespace ? '\\' . $subNamespace : '') . '\\' . $baseName;
        $modelPath = app_path('Models/' . ($subDirectory ? $subDirectory . '/' : '') . $baseName . '.php');

        $table = $this->option('table') ?: Str::snake(Str::pluralStudly($baseName));
        $route = $this->option('route') ?: Str::kebab(Str::pluralStudly($baseName));
        $resource = Str::headline($baseName);
        $resourcePlural = Str::headline(Str::pluralStudly($baseName));

        if (!File::exists($modelPath) || $this->option('force')) {
            File::ensureDirectoryExists(dirname($modelPath));
            File::put($modelPath, $this->modelStub($modelClass, $table));
            $this->info('Model created: ' . $modelPath);
        } else {
            $this->warn('Model already exists: ' . $modelPath);
        }

        if (!File::exists($controllerPath) || $this->option('force')) {
            File::ensureDirectoryExists(dirname($controllerPath));
            File::put(
                $controllerPath,
                $this->controllerStub($controllerNamespace, $controllerClass, $modelClass, $resource, $resourcePlural)
            );
            $this->info('Controller created: ' . $controllerPath);
        } else {
            $this->warn('Controller already exists: ' . $controllerPath);
        }

        $routesPath = base_path('routes/crud.php');
        $routeLine = "Route::apiCrud('" . $route . "', \\" . $controllerNamespace . '\\' . $controllerClass . '::class);';

        if (!File::exists($routesPath)) {
            File::put($routesPath, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n");
        }

        $routes = File::get($routesPath);

        if (strpos($routes, $routeLine) === false) {
            File::append($routesPath, "\n" . $routeLine . "\n");
            $this->info('Route added: ' . $route);
        } else {
            $this->warn('Route already exists: ' . $route);
        }

        $this->line('Update $fillable and $rules, then run: php artisan make:migration create_' . $table . '_table');

        return 0;
    }

    protected function modelStub($modelClass, $table)
    {
        return "<?php\n\nnamespace " . $this->namespaceOf($modelClass) . ";\n\n"
            . "use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;\n"
            . "use Illuminate\\Database\\Eloquent\\Model;\n\n"
            . 'class ' . class_basename($modelClass) . " extends Model\n{\n"
            . "    use HasFactory;\n\n"
            . "    protected \$table = '" . $table . "';\n\n"
            . "    protected \$fillable = ['name'];\n}\n";
    }

    protected function controllerStub($namespace, $class, $modelClass, $resource, $resourcePlural)
    {
        return "<?php\n\nnamespace " . $namespace . ";\n\n"
            . "use App\\Http\\Controllers\\ApiCrudController;\n\n"
            . 'class ' . $class . " extends ApiCrudController\n{\n"
            . "    protected \$model = \\" . $modelClass . "::class;\n\n"
            . "    protected \$resource = '" . $resource . "';\n\n"
            . "    protected \$resourcePlural = '" . $resourcePlural . "';\n\n"
            . "    protected \$searchable = ['name'];\n\n"
            . "    protected \$rules = [\n"
            . "        'name' => 'required|string|max:255',\n"
            . "    ];\n}\n";
    }

    protected function namespaceOf($class)
    {
        $position = strrpos($class, '\\');

        return $position === false ? '' : substr($class, 0, $position);
    }
}
