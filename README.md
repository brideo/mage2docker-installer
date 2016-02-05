#Magento 2 Installer

![Installer Example](https://dl.dropboxusercontent.com/u/42656369/Screen%20Shot%202016-02-05%20at%2009.15.05.png)

I have read a lot of people are having issues installing Magento 2 within the community, the point of this tool is to make it quick and painless to quickly get a new Magento 2 project going. This is currently in the BETA phase and I will be releasing a new version which accepts more parameters so you can change your database credentials and stuff.

So this is currently a wrapper around [MageInferno's docker setup](https://mageinferno.com/blog/magento-2-development-docker-os-x) for OSX.

This uses `docker`, `docker-compose`, `virtualbox`, `vagrant` and `dinghy`.

To install these requirements:

    brew update
    brew install docker
    brew install docker-compose
    brew cask install virtualbox
    brew cask install vagrant
    brew install https://github.com/codekitchen/dinghy/raw/latest/dinghy.rb

I am using MageInferno's setup with the [Meanbee Docker Nginx Proxy](https://github.com/meanbee/docker-nginx-proxy).

##Installation

    wget https://www.dropbox.com/s/kpsb3ai50rmc7oo/mage2docker-installer.phar
    chmod +x ./mage2docker-installer.phar
    sudo cp ./mage2docker-installer.phar /usr/local/bin/mage2docker-installer.phar

##How to use

First you should make sure Dinghy is running by running `dinghy up`. To create a new project you can run the following command.

    mage2docker-installer.phar new project-name
    
This will create a project in your current directory within a `project-name`.

Once the install has run you should be able to access your new Magento install at [http://project-name.docker](#).

Sometimes I have had issues with docker images not booting properly, like I said this is still in BETA. If you're magento 2 install isn't running try the following:
 
    cd project-nam
    docker up -d
