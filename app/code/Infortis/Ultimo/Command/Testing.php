<?php
namespace Infortis\Ultimo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Testing extends Command
{
    protected $storeManager;
    protected $getWebsiteCode;

    public function __construct(
        \Infortis\Ultimo\Helper\GetWebsiteCode $getWebsiteCode,
        \Infortis\Ultimo\Helper\GetNowBasedOnLocale $getNowBasedOnLocale,
        $name = null
    ) {
        $this->getNowBasedOnLocale                  = $getNowBasedOnLocale;
        $this->getWebsiteCode                       = $getWebsiteCode;
        parent::__construct($name);
    }
    
    protected function configure()
    {
        $this->setName("infortis:testing");
        $this->setDescription("Command for internal dev tests");
        parent::configure();
    }

    protected function testGetWebsiteCode($output)
    {        
        $scope_id   = 1;
        $code       = $this->getWebsiteCode->getCodeByScope($scope_id);
        $output->writeln('Code:' . $code);
    }

    public function testGetNowBasedOnLocale($output)
    {
        $now = $this->getNowBasedOnLocale->getNow();
        $output->writeln('Now:' . $now);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->testGetWebsiteCode($output);
        $this->testGetNowBasedOnLocale($output);
        $output->writeln("Hello World");  
    }
}
