<?php

namespace MultihandED\Overload\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use MultihandED\Overload\Rules\OverloadRule;
use MultihandED\Overload\Overload;


class OverloadCreate extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overload:create 
                            {method : The name of the overloaded method}
                            {namespace : The namespace to be created for the overloaded method. The last segment means the name of the trait}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create overload';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $data = Overload::prepareData($this->arguments());

        $validator = Validator::make($data, [
            'method' => 'regex:' . OverloadRule::FUNCTION_NAME,
            'namespace' => [new OverloadRule]
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());
        }
        else
        {
            //* Создаем папки по неймспейсу
            $params = Overload::prepareFolders($data['namespace']);
            $params['method'] = $data['method'];

            Overload::createFiles($params);
            $this->info('Success');
        }
    }


}
