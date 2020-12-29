<?php


namespace LegalWeb\Cloud\Console\Command;


use LegalWeb\Cloud\Model\ConfigLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends Command
{
    /**
     * @var ConfigLoader
     */
    private $configLoader;

    public function __construct(
        ConfigLoader $configLoader,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->configLoader = $configLoader;
    }


    protected function configure()
    {
        $this->setName('legal-web:cloud:refresh');
        $this->setDescription('Refresh all legal web cloud settings');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configLoader->refresh();
    }


}
