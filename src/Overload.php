<?php

namespace MultihandED\Overload;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Touhidurabir\StubGenerator\Facades\StubGenerator;

class Overload
{
    const TRAITS_DIRECTORY = 'Traits\Overloads';
    
    const OVERLOAD_TEMPLATE = 'Overload';
    const MAIN_TEMPLATE = 'OverloadMain';

    /**
     * Rewrite main trait
     *
     * @return void
     */
    public static function rewriteMainTrait($params) : void
    {
        $files = File::files($params['path']);

        $list = "";

        foreach($files as $file)
        {
            $fileName = $file->getFilename();

            if($fileName != $params['mainName']. '.php')
            {
                $fileName = explode('.', $fileName)[0];

                $list .= "\tuse $fileName;\n";
            }
        }

        $params['listOfTraits'] = "\t" . trim($list);
        
        self::generateFromStub(self::MAIN_TEMPLATE, $params['mainName'], $params);
    }

    /**
     * Create overload trait
     *
     * @return void
     */
    public static function createFiles($params) : void
    {
        //* Generate overload trait
        $params['mainName'] .= 'Main';

        if($params['className'] != $params['mainName'])
            self::generateFromStub(self::OVERLOAD_TEMPLATE, $params['className'], $params);

        self::rewriteMainTrait($params);
    }

    /**
     * Generate file from stub
     *
     * @return void
     */
    public static function generateFromStub($template, $as, $params) : void
    {
        $stubGenerator = StubGenerator::from(__DIR__ . '/Stubs/' . $template . '.stub', true)
        ->to($params['path'])
        ->as($as)
        ->withReplacers($params)
        ->replace(true);
        
        try
        {
            $stubGenerator->save();
        }
        catch(\Exception $e)
        {
            File::put($params['path']. '\\'. $as . '.php', $stubGenerator->toString());
        }
    }

    /**
     * Prepare folders and extract classname
     *
     * @return array
     */
    public static function prepareFolders(string $namespace): array
    {
        $folders =  explode('\\', $namespace);
        $traitsDirectory = explode('\\', self::getMainDirectory(false));

        $className = (count($folders) == 1) ? $folders[0] : array_pop($folders);
        $mainName = end($folders);
        
        $path = app_path() . '\\' . implode('\\', array_merge($traitsDirectory, $folders));
        $namespace = implode('\\', $folders);

        File::makeDirectory($path, 0777, true, true);

        return ['className' => $className, 'path' => $path, 'mainName' => $mainName, 'namespace' => self::getFullNamespace($namespace)];
    }

    /**
     * Remove start back slash
     * 
     * @return string
     */
    public static function removeStartBackSlash($string) : string
    {
        if(mb_substr($string, 0, 1) == '\\')
        {
            $string = Str::replaceFirst('\\', '', $string);
            return self::removeStartBackSlash($string);
        }

        return $string;
    }

    /**
     * Get full directory path
     * 
     * @return string
     */
    public static function getMainDirectory($slashes = true) : string
    {
        $mainDirectory = trim(env('TRAITS_DIRECTORY', self::TRAITS_DIRECTORY), '\\');
        return ($slashes) ? "\\$mainDirectory\\" : $mainDirectory;
    }

    /**
     * Get full namespace
     * 
     * @return string
     */
    public static function getFullNamespace($namespace) : string
    {
        return 'App' . self::getMainDirectory() . $namespace;
    }

    /**
     * Prepare data array for validating
     *
     * @return array
     */
    public static function prepareData($arguments)  : array
    {
        $result = [];
        foreach ($arguments as $key => $val) 
        {
            $result[$key] = self::removeStartBackSlash($val);
        }

        return $result;
    }

    /**
     * Check trait exist by namespace
     *
     * @return bool
     */
    public static function checkTraitExist($namespace) : bool
    {
        $path = app_path() . self::getMainDirectory() . $namespace . '.php';
        return File::exists($path);
    }
}