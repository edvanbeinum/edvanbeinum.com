---
layout: post
title: "NodeJS Introduction: Part 1"
date: 2011-05-29 16:04

---
A few weeks ago I attended [Remy Sharp](http://remysharp.com/)’s NodeJS Workshop in Nottingham which was a day of hanging out with some awesome people and getting stuck in to NodeJS. This is my attempt to write up what I learnt in the hope that some of it actually sticks in my head, so apologies for any poor grammar; this really is more a note-to-self rather than something other humans should be reading.

### Installation on Ubuntu 10.10 Server Edition
You can do 

    sudo apt-get install node

But where is the fun in that right? (Also you probably won’t be getting the latest version) Let’s build it ourselves.

Make sure dependencies are covered:

    sudo apt-get install libssl-dev

Node also requires Python but that should already be installed by default with Ubuntu

The installation instructions on the [Node Wiki](https://github.com/joyent/node/wiki/Installation) are good but we are going to differ slightly to where we install node. Follow along with me:
from your home dir 

    mkdir src/ 
    cd src
    git clone https://github.com/joyent/node.git
    cd node
    ./configure --prefix=$HOME/local/node
    make && make install

This was as per the wiki. Now here we go our own way. Ubuntu automatically adds our ~/bin folder to the $PATH so if we install node there then we won’t have to bother about setting the Path variables later. 

    mkdir ~/bin
    cd bin
    ln -s ~/local/node/bin/node

For those of you who like to know WHY this works, have a look at

    cat ~/.profile 

where you’ll see that ~/bin path being added.

Now

    node -v

should tell you the version. If you get a program not installed message try:

    source ~/.profile 

and then try again.

### Install NPM

Because [Node Package Manager](https://github.com/isaacs/npm) installs stuff on your machine, it is recommended to run it under sudo so that NPM can change the user to ‘nobody’ before running any build commands. As the installation docs say: “npm will downgrade permissions if it's root before running any build scripts that package authors specified.”


    sudo curl http://npmjs.org/install.sh | clean=yes sh

Now doing

    npm -v

should give you the version number

### Our first script

Yes, we are going to do a Hello World example, but this will show the event-driven non-blocking nature of NodeJS

    console.log('Hello');
    setTimeout(function(){
           console.log('World') 
    }, 2000);
    console.log('JavaScript');

Because NodeJS is build on top of Google’s V8 Javascript Engine which is used in Chrome amongst many other places, the calls are much the same as client-side JavaScript. So console.log will output stuff to the screen, in this case since NodeJS is run from the command line it is STDOUT.
Run the script 

    node hello-world.js

which will output

    Hello
    JavaScript
    World

Look at the setTimeout function; this says in 2000 milliseconds time (2 seconds to you and me) call console.log(). If you were to write something like this in PHP


    echo ‘Hello’;
    sleep(2000);
    echo ‘PHP’;
    echo ‘world’;

So PHP is not event-driven, you’ll see that while the sleep function is invoked, nothing happens; no processing occurs. Compare that with JavaScript where the console.log('JavaScript'); is executed even while the console.log('World'); function is waiting to be executed.

There is a util module that adds more logging options

    var util = require('util');
    //util log adds a timestamp
     util.log('foo');
     // util.print doesnt add line breaks
     util.print('foo');
     console.log('foo');
    // console.dir can output object memebers
     console.dir(global);
    // can direct errors to STDERR
     console.error('echoed on STDERR');


### More command line utilities

Node’s Process global object is useful for command line tools, and does things such as give us access to the arguments passed via the command line

    process.stdout.write('hello world!\n');
    console.log(process.argv[2]);

So try that out with

    node command-line.js Hi

Process has access to environment vars which can be set when calling a script. This provides a nice way to start a server based on whether we want it set up for production or development. e.g:

     $ NODE_ENV=production node lanch-server.js

And in launch-server.js:

    var port = 8000;
    if(process.env.NODE_ENV == 'production'){ port = 80;}
    server.listen(port);

Here is a [list of functions available via Process](http://nodejs.org/docs/v0.3.1/api/all.html#process.stdout)

### Loading modules

Node loads modules using the require(‘module-name) function. To load the path module
we can 

    var path = require(‘path’);

The argument we pass to the require function is either the name of the module that Node has already been installed and knows about. Or we can pass in a path for a module of our own. Node looks in folders in a [certain order](http://nodejs.org/docs/v0.4.2/api/modules.html#loading_from_node_modules_Folders)

#### Path Module

The [Path](http://nodejs.org/docs/v0.3.1/api/path.html#path) module is used for dealing with file paths. This example will demonstrate using a callback function to alert us as to whether a file exists or not.

    var path = require('path');
    
    console.log('path exists?');
    path.exists(process.argv[2], function(exists){
            console.log('files exists: ' + (exists ? 'yes' : 'no'));
    });
    
    console.log('waiting...');

From the command line:

    $ node callback-path.js test.txt


### Webservers

So that is all well and good but the fun really starts when we use NodeJS for web servers.
Node has an [http module](http://nodejs.org/docs/v0.3.1/api/all.html#hTTP) 

Here is out hello world server example

    var http = require('http'),
    var server = http.createServer(function(req, res){
        res.writeHead(200, {'content-type' : 'text/html'});
        //res.write is optional
        res.write('<p>Hello</p>');
        res.end('<p> World</p>');
    });
    
    server.listen(8000);

The salient parts of that example are:
http module has a createServer() function that accepts an annoymous function with params request and respsonse. We can use the response object to write the HTTP headers with a response code (200 OK) and passin an object literal with other options, in this case setting the mime type. There is a write function that writes the output but keeps the connection alive. The end function outputs whatever is passed to it and then closes the connection. For more on how NodeJS keeps connections open look at [chunked encoding](http://en.wikipedia.org/wiki/Chunked_transfer_encoding).
We then start the server listening on port 8000. As we know, higher port numbers can be opened by any user, ports lower than 1023 can only be opened by the root user.

start the script 

    $ node simple.js

which will lock your command prompt. Stop the server by ctrl + c. 

Because the application code is in the same file as the server code, if you want to change the code then you edit the file and then you have stop the server and then restart it. This is tiresome. Use [nodemon](https://github.com/remy/nodemon) which will restart NodeJS when it detects a change in the files.

Install nodemon:

    $ npm install nodemon -g

The -g flag tells npm to install it globally which is necessary as nodemon is command line utility.

Now start Node by

    $ nodemon simple.js

You’ll see your page as before at localhost:8000, then go and edit the simple.js file and look at your terminal and you’ll see nodemon detect the change and then restart. Boom!
Nodemon has lots of awesome features such as being able to ignore changes in certain file types; read more about it [here](http://remysharp.com/2010/10/12/nodejs-rapid-development-nodemon/)

### ConnectJS

So we have got a basic server working, what other cool shit can we do? [ConnectJS](https://github.com/senchalabs/connect) is a middleware library for NodeJS. 

What the hell is Middleware? From my Java days I thought of Corba and promptly broke out in hives. Luckily, it’s not that. 

This is a better explanation of [middleware](http://tjholowaychuk.com/post/664516126/connect-middleware-for-nodejs)

Connect allows you to register certain actions withNodeJS and then based on the type of request Node will respond appropriately. OK, that is not a great explanation so let’s try and example.

    var connect = require('connect'),
    path = require('path'),
    parse = require('url').parse,
    querystring = require('querystring');
    
    var server = connect.createServer(
        function(req, res, next){
            if(req.url.indexOf('favicon') === -1){
                count++;
                req.count = count;
            }
            next();
        },
        connect.bodyParser(),
        connect.router(route),
        connect.static(__dirname + '/public')
    
    );
    server.listen(8000);

The thing to note here is that instead of using the http module to create the server we use connect. We can then pass in any number of functions that will get called between the request being cand the response returned. 
In this case there is a function that counts the number of times the page has been loaded, bodyParser which gives us access to the querystring (CHECK), then we set up routes that match a requested URL and perform a specific action and finally we tell Connect which folder to use to serve out static files form.  If one function in the middleware has finished processing, or we want to force control back to connnect, we can use the next() method which will return control to Connect.

Coming up in part 2, just as soon as I've typed it: ExpressJS, Jade and EJS, Websockets and event-source...