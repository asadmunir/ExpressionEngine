<?php

namespace ExpressionEngine\Cli\Commands;

use ExpressionEngine\Cli\Cli;
use ExpressionEngine\Service\Generator\ProletGenerator;

/**
 * Command to clear selected caches
 */
class CommandMakeProlet extends Cli
{
    /**
     * name of command
     * @var string
     */
    public $name = 'Prolet Generator';

    /**
     * signature of command
     * @var string
     */
    public $signature = 'make:prolet';

    /**
     * How to use command
     * @var string
     */
    public $usage = 'php eecli.php make:prolet MyNewProlet --addon=my_existing_addon';

    /**
     * options available for use in command
     * @var array
     */
    public $commandOptions = [
        'addon,a:'       => 'command_make_prolet_option_addon',
        'description,d:' => 'command_make_prolet_option_description',
    ];

    /**
     * Command can run without EE Core
     * @var boolean
     */
    public $standalone = true;

    protected $data = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Run the command
     * @return mixed
     */
    public function handle()
    {
        $this->info('command_make_command_lets_build_command');
        echo "<pre>";
        var_dump('');
        exit;
        // Gather alll the command information
        $this->data['name'] =  $this->getFirstUnnamedArgument("command_make_command_ask_command_name", null, true);
        $this->data['addon'] = $this->getOptionOrAsk('--addon', "command_make_command_ask_addon", null, true);
        $this->data['description'] = $this->getOptionOrAsk('--description', "command_make_command_ask_description");
        $this->data['signature'] = $this->getOptionOrAsk('--signature', "command_make_command_ask_signature", null, true);

        if (substr($this->data['signature'], 0, strlen($this->data['addon'] . ":")) == $this->data['addon'] . ":") {
            $this->data['signature'] = substr($this->data['signature'], strlen($this->data['addon'] . ":"));
        }
        // Lets prefix with the addon name
        $this->data['signature'] = $this->data['addon'] . ":" . $this->data['signature'];

        $this->info('command_make_command_lets_build');

        try {
            // Build the command
            $service = ee('CommandGenerator', $this->data);
            $service->build();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->info('command_make_command_created_successfully');
    }
}
