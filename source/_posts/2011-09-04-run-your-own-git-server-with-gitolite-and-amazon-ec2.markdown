---
layout: post
title: "Run your own Git server with Gitolite and Amazon EC2"
date: 2011-09-04 18:34

---
First things first, set up a Micro instance on EC2. Actually getting an EC2 instance is a blog entry for another time. In the meantime you can follow this rather good guide from [.net mag](http://www.netmagazine.com/features/web-dev-s-intro-cloud-part-1). There are a few of things to note when following that guide:

Firstly, remember where you save your AWS .pem key file to! Secondly, if you mess up then you can easily terminate the instance and spin up a new one and try again.

We are going to use Ubuntu for the OS and choosing the right AMI is a bit baffling. Luckily Ubuntu provide a list of [official AMIs](https://help.ubuntu.com/community/EC2StartersGuide#Official%20Ubuntu%20Amazon%20Machine%20Images%20).
After you click 'Launch Instance' from the AWS console, click the 'community AMIs' tab and then search for 'ami-379ea943'. That AMI is a 64-bit 11.04 release in the EU_West region - and, best of all, qualifies for the 'micro' instance which is free for the first year.

I often have trouble SSHing into an instance when I use the 'default' security group - I think it comes with port 22 locked down so I create a new security group and select the SSH port (22) to be open - ideally you would want to specify the IP addresses that can connect but leave it as 0.0.0.0/0 which will permit any IP address to be allowed to try to authenticate.

Also note that the official Ubuntu AMI's come with the default user as 'ubuntu' NOT 'ec2-user' as mentioned in the .net guide. 

## Installing Gitolite
You'll need a public SSH key, if you don't have one

	ssh-keygen -t rsa

should do the trick. 
So yes, you'll have two keys: one you downloaded from Amazon when you created the EC2 instance which is used to connect to the instance as the 'ubuntu' user, and an SSH key pair used to access the gitolite-admin repository.
	
Then, SSH into your new EC2 instance. Assuming you are in the same folder as where you saved your AWS .pem key:

	ssh -i YOURKEYPAIR.pem ubuntu@PUBLIC-AWS-DNS.amazonaws.com

(you can find the public DNS for your Instance by clicking on your Instance in the EC2 dashboard and look at the 'Description tab')

Then put your public SSH key into /tmp/gitolite-admin-user.pub on your EC2 instance, we'll be needing that in a bit.


Since we have just installed 11.04, it has gitolite as an apt package. so:

	sudo apt-get install gitolite

(The default EC2 user has password-less sudo priviliges so you shouldn't be asked for a password)
The installation process will create a new user 'gitolite' and we need switch to that user

	sudo su - gitolite

Then run the Gitolite setup script, passing in the path to the .pub file you saved earlier:

	gl-setup /tmp/gitolite-admin-user.pub

That should then tell you that Gitolite has initialised a new repo at /var/lib/gitolite/repositories/gitolite-admin.git/. you can now logout of your EC2 instance.


## Creating a new repo and adding users

All management of the Git server can be done from your computer by editing the config settings in the gitolite-admin repo and then pushing the changes up to the EC2 Instance. From your computer, run 

	git clone gitolite@PUBLIC-AWS-DNS.amazonaws.com:gitolite-admin.git

In /gitolite-admin open conf/gitolite.conf and add the name of your new repo and the usernames you want to allow access to. Something like:
	
	repo	super-project
			RW+ = ed

which gives the user 'ed' read, write and rewind permissions to the super-project repository. Then ask Ed to send you his public key and add it to the /gitolite-admin/keydir directory with the filename ed.pub. Make sure the NAME.pub matches the NAME you've used in gitolite.conf

Commit and push the gitolite-admin repo. Now the new user will be able to access the repository. Note that they will have to connect using gitolite@SERVER and Gitolite takes care of checking their authentication - Gitolite does some fancy SSH magic that will check that the user has the correct keypair when connecting even as the gitolite user. You can read more details of exactly what goes on in the manual, [How Gitolite uses SSH](http://sitaramc.github.com/gitolite/doc/gitolite-and-ssh.html)

Futher reading:

[Gitolite Installation](http://sitaramc.github.com/gitolite/doc/1-INSTALL.html)  
[Setup Gitolite on Ubuntu](http://www.silassewell.com/blog/2011/01/08/setup-gitolite-on-ubuntu/)    

