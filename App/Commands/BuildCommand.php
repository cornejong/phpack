<?php

namespace App\Commands;

use SouthCoast\Helpers\Str;
use SouthCoast\Helpers\File;
use SouthCoast\Console\Format;
use SouthCoast\Console\Console;
use SouthCoast\Console\Color;
use SouthCoast\Console\Abstracted\Command;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use RecursiveCallbackFilterIterator;
use Phar;
use App\Modules\Project;
use App\Helpers\Locator;

class BuildCommand extends Command
{
    public $name = 'Build';
    public $description = 'Build a phar archive of the current project';
    
    public $accessor = 'build {target?}';
    public $acceptedFlags = [];

    public function boot()
    {
        Console::log('> ' . Color::blue('phpack') . ' => build executable' . PHP_EOL);

        if (in_array(ini_get('phar.readonly'), ['On', '1', 'true'])) {
            print("\033[31mâ\033[0m Please first change your ini directive 'phar.readonly' to 'Off'!" . PHP_EOL .
            "  \033[2m(current: " . ini_get('phar.readonly') . ") (ini path: " . php_ini_loaded_file() . ")\033[0m") . PHP_EOL;
            exit(1);
        }
        
        $this->projectRoot = Locator::find(app()->projectFilename);
        if (!$this->projectRoot) {
            Console::error('Project not initialized');
            Console::log('   Run \'' . Format::bold('$ phpack init') . '\' to initialize a project');
            Console::exit(1);
        }
        
        try {
            $pack = require $this->projectRoot . '/' . app()->projectFilename;
            $this->project = new Project($this->projectRoot);

            if(!$this->project->validate($pack)) {
                Console::error('Validation errors found in your pack.php file!');
                var_dump($this->project->validationErrorAsArray());
                Console::exit(1);
            }
            
        } catch (\Throwable $th) {
            throw $th;
            Console::log(Color::red('â') . ' Could not load active project deployer class!');
            Console::exit(1);
        }
    }

    public function execute()
    {
        $destinationDirectory = $this->project->getBuildDirectory($this->arguments['target'] ?? 'debug');

        /* Create the new Phar Package */
        $phar = new Phar($destinationDirectory . '/' . $this->project->getAccessor() . '.phar');

        /* get all application files */
        $files = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator(
                    $this->project->getRoot(),
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                function ($file, $key, $iterator) {
                    if ($this->project->shouldIgnore($file->getRealPath()) || $file->getFileName() === '.DS_Store') {
                        return false;
                    }
                
                    return true;
                }
            )
        );

        $files = iterator_to_array($files);
        $sortCallback = function ($a, $b) {
            return strlen($b) - strlen($a);
        };
        usort($files, $sortCallback);

        Console::log('- Adding Files to executable...');
        Console::newLine();
        $added = 0;

        /* Loop and inform */
        foreach (array_reverse($files) as $file) {
            $localFile = str_replace($this->project->getRoot() . '/', '', $file);
            Console::log('> Adding: ' . $localFile);

            if($this->project['checkSyntax'] === true) {
                Console::replaceLastLine('- Checking: ' . $localFile);
                
                exec('php -l "' . $file . '"', $output);
                !Str::startsWith('No syntax errors detected', $output[0])
                    ? Console::error(implode(PHP_EOL, $output)) && exit(1)
                    : Console::replaceLastLine('â Checked: ' . $localFile);                
            }
            
            if($this->project->shouldMinify(ltrim($localFile, '/'))) {
                $phar->addFromString($localFile, \php_strip_whitespace($file));
                Console::replaceLastLine('â Minified: ' . $localFile);
            } else {
                $phar->addFile($file, $localFile);
                Console::replaceLastLine('â Added: ' . $localFile);
            }

            if($this->project->compresses()) {
                $phar[$localFile]->compress($this->project->getCompression());

                $phar[$localFile]->isCompressed()
                    ? Console::replaceLastLine('â Compressed: ' . $localFile)
                    : Console::replaceLastLine('â Could not Compress: ' . $localFile);
            }

            $added++;
        }

        Console::replaceLastLine(PHP_EOL . '> Added ' . $added . ' files to ' . $this->project->getAccessor() . '.');
        Console::newLine();

        Console::log('> Creating bootstrap stub to ' . $this->project->getAccessor() . '...');

        Console::replaceLastLine('> Adding Build Identifier to sub');

        $buildLog = json_decode(file_get_contents($this->project->getBuildLog()), true);
        
        $buildNumber = count($buildLog) + 1;
        $buildLog[] = [
            'target' => $this->arguments['target'] ?? 'debug',
            'sequence' => $buildNumber,
            'identifier' => uniqid(),
            'time' => time()
        ];

        $stub = php_strip_whitespace($this->project->getBootstrapAbsolutePath());
        $stub = str_replace('{{build_id}}', $buildLog[$buildNumber - 1]['identifier'], $stub);
        $stub = str_replace('{{build_time}}', $buildLog[$buildNumber - 1]['time'], $stub);
        $stub = str_replace('{{build_number}}', $buildLog[$buildNumber - 1]['sequence'], $stub);
        $stub = str_replace('{{build_target}}', $buildLog[$buildNumber - 1]['target'], $stub);

        Console::replaceLastLine('> Adding bootstrap stub to ' . $this->project->getAccessor() . '...');
        $phar->setStub($stub);
        
        Console::replaceLastLine('â Added bootstrap stub to ' . $this->project->getAccessor());

        if (file_exists($destinationDirectory . '/' . $this->project->getAccessor())) {
            unlink($destinationDirectory . '/' . $this->project->getAccessor());
        }

        /* Renaming */
        File::rename($phar->getPath(), $this->project->getAccessor());

        Console::log('> Changing file permissions');
        /* add execution mode for the archive */
        system('chmod +x ' . $destinationDirectory . '/' . $this->project->getAccessor());
        Console::replaceLastLine('â Changed file permissions');

        file_put_contents($this->project->getBuildLog(), json_encode($buildLog, JSON_PRETTY_PRINT));

        Console::newLine();
        Console::success('Successfully created ' . $this->project->getAccessor() . ' executable');

        return Command::SUCCESS;
    }

    public function shutdown()
    {
        # code...
    }
}
