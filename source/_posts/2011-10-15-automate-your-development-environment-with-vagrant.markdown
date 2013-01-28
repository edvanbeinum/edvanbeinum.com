---
layout: post
title: "Automate your development environment with Vagrant"
date: 2011-10-15 17:29

---
Are you still downloading ISOs, setting up SSH keys, sorting the host-only networking each time you create a new VM for each new project you do? That is _so_ CD-ROM.  
[Vagrant](http://vagrantup.com/) will automate the creation of all of that for you. It also plays nice with Chef and Puppet so you can automate the installation of your server software. It is pretty freakin' cool.

Vagrant needs [Virtual Box](https://www.virtualbox.org/) and Ruby to be installed to automatically provision development boxes for you.
For the purpose of this article, we'll assume our project is structured:

	public/
	test/
	tools/
		vagrant/
		chef/

where public/ contains our project files, tests/ contain our unit, functional and integration tests and tools/vagrant/ contains the Vagrantfile.

What on earth is a Vagrantfile? Well that is where you tell Vagrant what to do. As the manual says, Vagrantfile to Vagrant is what a Makefile is to Make.

Vagrant can automatically create a Vagrantfile for you

	cd tools/vagrant
	vagrant init

and you'll see a file called Vagrantfile.

### Boxes

You can think of Boxes as disk images. In fact they are wrappers around an .iso file that add some extra information that Vagrant uses to construct the dev environment. Things like RAM, disk space, networking are all handled for you.

There are some pre-made boxes ready for you to use, but you can make your own either by packaging up an existing Virtual Machine or by using a tool called [VeeWee](https://github.com/jedi4ever/veewee)

	sudo gem install veewee

Veewee comes with  'templates' which cover most Linux distros to help build your box from. To see a list of the templates available to you:

	vagrant basebox templates

We'll create a 64-bit Ubuntu box based on version 10.10 called 'ubuntu-10.10-64'

	vagrant basebox define 'ubuntu-10.10-64' 'ubuntu-10.10-server-amd64'

which will create folders definitions/ubuntu-10.10-64/ which contains some Ruby files we can edit to specify the configuration of the box. In our case, we'd like it give the Box 1GB of RAM. Open definitions/ubuntu-10.10-64/definition.rb and edit the memory size parameter to
	
	:memory_size=> '1024'

then we build the box
	
	vagrant basebox build 'ubuntu-10.10-64'

This will download the ISO file from Ubuntu or if you already have one locally, put it in /iso folder. You'll see a parameter called :iso_file in the definition.rb file, make sure your ISO matches the name given there.
This will then run VirtualBox and run through an automated installation routine. You might want to go and put the kettle on at this point, as this will take a wee while.

Then you'll need to verify the box has been created correctly before you go and distribute it to all your amazed friends:

	vagrant basebox validate ubuntu-10.10-64

Which should pass, so then export it!

	vagrant basebox export   ubuntu-10.10-64

This will export the Box to ubuntu-10.10-64.box in the tools/vagrant folder

So we have created a custom Box for Vagrant to use to create a VM. Now we need to tell Vagrant about the new box

	vagrant box add 'ubuntu-10.10-64' 'ubuntu-10.10-64.box'

Now that Vagrant knows about the Box, we need to specify that you we actually to use it. Edit the Vagrantfile:

	config.vm.box = "ubuntu-10.10-64"

Then run Vagrant
	
	vagrant up

Which will trundle through creating the Virtual Machine. Once that has completed then you can SSH into your VM 

	vagrant ssh

There you go. If you open VirtualBox you see a new VM there, with 1024MB of RAM.
The Vagrant commands you'll find yourself using most of the time will be:

	vagrant up

This creates or resumes the VM and boots it

	vagrant suspend

Suspends the currently running VM, saving its state.

	vagrant destroy

will perform a shutdown of the VM

	vagrant provision

runs the Chef or Puppet scripts to provision the VM software but doesn't restart the VM itself

	vagrant reload

restarts the VM and runs provision again.

We now have a VM that can be built and destroyed as we please. The next part is to automate provisioning the server, that is, installing the system software we want and configuring it. We are going to use [Chef](http://opscode.com/chef), but that is for Part 2...