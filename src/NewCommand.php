<?php

namespace Brideo\Installer\Console;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('new')
            ->setDescription('Create a new Magento 2 install on docker.')
            ->addArgument('name', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validate($input);
        $output->writeln('<info>Starting Your New Magento 2 Install...</info>');
        $this->createDockerComposeYml($input);
        $this->createGitIgnore($input);
        $this->runTheInstall($input);
    }


    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getDirectoryPath(InputInterface $input)
    {
        return getcwd() . '/' . $input->getArgument('name');
    }

    /**
     * Run validation checks.
     *
     * @param InputInterface $input
     * @throws RuntimeException
     */
    protected function validate(InputInterface $input)
    {
        if (is_dir($this->getDirectoryPath($input))) {
            throw new RuntimeException("You already have a directory at {$this->getDirectoryPath($input)}");
        }

    }

    /**
     * Worse function in the world, get a yaml file, convert it,
     * update it, convert it back then create it in the new project.
     *
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getDockerComposeFileContents(InputInterface $input)
    {
        $dockerCompose = file_get_contents('src/templates/docker-compose.yml');
        $yaml = new Parser();
        $parsedYaml = $yaml->parse($dockerCompose);

        //pure unadulterated filth, sorry.
        foreach($parsedYaml['app'] as $key => $data) {
            $parsedYaml['app'][$key] = str_replace('mysite', $input->getArgument('name'), $data);
        }
        foreach($parsedYaml['setup']['environment'] as $key => $data) {
            $parsedYaml['setup']['environment'][$key] = str_replace('mysite', $input->getArgument('name'), $data);
        }

        $dumper = new Dumper();
        return $dumper->dump($parsedYaml, 3, false, true);
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    protected function createDockerComposeYml(InputInterface $input)
    {
        mkdir($this->getDirectoryPath($input));

        file_put_contents(
            $this->getDirectoryPath($input). '/docker-compose.yml',
            $this->getDockerComposeFileContents($input)
        );

        return $this;
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    protected function createGitIgnore(InputInterface $input)
    {
        file_put_contents(
            $this->getDirectoryPath($input). '/.gitignore',
            file_get_contents('src/templates/.gitignore')
        );

        return $this;
    }

    /**
     * @param InputInterface $input
     */
    protected function runTheInstall(InputInterface $input)
    {
        exec("cd {$input->getArgument('name')} && docker-compose up setup");
    }

}
