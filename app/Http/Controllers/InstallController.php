<?php
/**
 * Created by PhpStorm.
 * User: osein
 * Date: 21.08.2018
 * Time: 18:30
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use PDO;

class InstallController extends Controller
{

    public function getIndex()
    {
        if(file_exists(base_path('.env'))) {
            return redirect(url('/'));
        }

        //$dsn = $dsn = "mysql:host=mysql";
        //$pdo = new PDO($dsn,"root","");

        //Creation of user "user_name"
        //$createUserStatement = $pdo->query("CREATE USER 'htest212'@'%' IDENTIFIED BY 'pass_word';");

        //var_dump($createUserStatement);

        //if(!$createUserStatement) {
            //var_dump($pdo->errorCode());
            //var_dump($pdo->errorInfo());
        //}

        //dd(1);
        //Creation of database "new_db"
        //$pdo->query("CREATE DATABASE `new_db`;");
        //Adding all privileges on our newly created database
        //$pdo->query("GRANT ALL PRIVILEGES on `new_db`.* TO 'user_name'@'%';");

        $exampleEnvContents = file_get_contents(base_path('.env.example'));
        $exampleEnvLines = explode(PHP_EOL, $exampleEnvContents);
        $formInputs = [];
        $currentConfigHeader = '';

        foreach ($exampleEnvLines as $line)
        {
            if(empty($line)) {
                continue;
            }

            if(starts_with($line, '# ')) {
                if(strpos($line, 'start') !== false) {
                    $currentConfigHeader = explode(' start', explode('# ', $line)[1])[0];
                }

                continue;
            }

            $lineWithComment = explode(' # ', trim($line));
            $variableAndDefault = explode('=', $lineWithComment[0]);

            $variableName = $variableAndDefault[0];
            $variableDefaultValue = $variableAndDefault[1];

            // Skip some variables that end users dont need to change.
            if(
                $variableName == 'APP_ENV' ||
                $variableName == 'APP_KEY' ||
                $variableName == 'APP_DEBUG' ||
                $variableName == 'APP_LOG_LEVEL' ||
                $variableName == 'VERSION' ||
                $variableName == 'DB_CONNECTION'
            ) {
                continue;
            }

            if(!isset($formInputs[$currentConfigHeader])) {
                $formInputs[$currentConfigHeader] = [];
            }

            $formInputs[$currentConfigHeader][] = [
                'name' => $variableName,
                'default' => $variableDefaultValue,
                'info' => isset($lineWithComment[1]) ? $lineWithComment[1] : ''
            ];
        }

        return view('install/index', ['formInputs' => $formInputs]);
    }

    public function postInstall()
    {
        $dsn = "mysql:host=" . $_POST['env']['DB_HOST'] . ";port=" . $_POST['env']['DB_PORT'] . ";dbname=" . $_POST['env']['DB_DATABASE'] . ";charset=utf8";
        new PDO($dsn, $_POST['env']['DB_USERNAME'], $_POST['env']['DB_PASSWORD']);

        $envFileContents = '';

        foreach ($_POST['env'] as $envVar => $envVal) {
            $envFileContents .= $envVar . '=' . $envVal . PHP_EOL;
        }

        $envFileContents .= 'CHECK_INSTALL=false'.PHP_EOL;

        file_put_contents(base_path('.env'), $envFileContents);

        Artisan::call('key:generate');

        $kernelFileContents = file_get_contents(app_path('Http/Kernel.php'));
        $kernelFileContents = str_replace('CheckInstall::class,', '//CheckInstall::class,', $kernelFileContents);
        file_put_contents(app_path('Http/Kernel.php'), $kernelFileContents);

        return redirect(url('/'));
    }

}