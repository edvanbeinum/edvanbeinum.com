---
layout: post
title: "Use Ant and HTML5 Boilerplate to build and deploy to Amazon S3"
date: 2012-01-22 17:08

---
__Disclaimer!__ I'm really no kind of Java/Ant guy. So please do correct me [@edvanbeinum](http://twitter.com/edvanbeinum). I thank you.

This article will show you how to use Ant to run the HTML5BP build scripts and then upload the optimised site to Amazon S3 with just one command (or two if you like).

You'll need these things:

* [HTML5 Boilerplate](http://html5boilerplate.com/)
* [awstasks](http://code.google.com/p/awstasks/downloads/list)

### Dick around with JARs

You'll need to compile the latest version of awstasks from SVN

    svn checkout http://awstasks.googlecode.com/svn/trunk/

or, if like me you have banished SVN from your short-term memory:

    git svn clone http://awstasks.googlecode.com/svn/trunk/

The awstasks library is rather old so it depends on some old versions of other JARs to work which take a bit of hunting down (especially for a Java n00b like me)  
The JARs that awstasks need are:

 * The current version of [commons-codec](http://commons.apache.org/codec/download_codec.cgi)
 * An old version of [commons-httpclient (v3.0)](http://archive.apache.org/dist/httpcomponents/commons-httpclient/3.0/binary/commons-httpclient-3.0.zip)
 * An old version of [Jets3t (v.0.61)](https://bitbucket.org/jmurty/jets3t/get/Release-0_6_1.zip)

Once you have Jets3t are you are going to have to compile it. Extract it into a directory and then run 

    ant 

from there and it create its JARs in dist/jets3t-0.6.1/jars

Yeah, that all seems massively confusing, here is the list of Jars that I ended up with:

 * awstasks-0.3.jar (but since we compiled our own version, I guess it would be v0.4)
 * cockpit-0.6.1.jar
 * cockpitlite-0.6.1.jar
 * commons-codec-1.4.jar
 * commons-httpclient-3.0.jar
 * commons-logging-1.1.1.jar
 * httpclient-4.1.2.jar
 * httpclient-cache-4.1.2.jar
 * httpcore-4.1.2.jar
 * httpmime-4.1.2.jar
 * jets3t-0.6.1.jar
 * jets3t-gui-0.6.1.jar
 * synchronize-0.6.1.jar
 * uploader-0.6.1.jar

I've bundled all these into a zip and put them in a [Github repo](https://github.com/edvanbeinum/awstasks-dependencies) if hunting each of those down sounds like a chore (which it was).

These JARs need to be available to Ant's classpath - one way to do this is to put them in $HOME/.ant/lib which is where Ant will automatically look for JARs.  
To check than Ant knows about these new JARs:

    ant -diagnostics

which will spew out a lot of data, including loaded JARs.

### Create a properties and build file 

Going over creating a bucket on S3 and how to get your security credentials is beyond the scope of this article. The [S3 documentation](http://docs.amazonwebservices.com/AmazonS3/latest/gsg/CreatingABucket.html) is a good place to start.

To play nicely with the HTML5BP build script we are going to keep as much of our files separate from HTML5BP as possible. This will make it easier for us when we want to upgrade to the next version of HTML5BP.

Firstly, we are going to add some files to HTML5BP. In /build we are going to add our new 'deploy' targets (don't worry if you don't know what that means, all shall hopefully become clear)

Create a 'deploy.properties' file in the /build folder of HTML5BP, and paste in the following (add your own security credentials instead of my placeholders)

    aws.accessId=<ACCESS_ID>
    aws.secretKey=<SECRET_KEY>
    aws.bucket=<BUCKET_NAME>


Now, awstasks uses a rather limited mimetype library that doesn't recognise CSS and JS files so as a result it will upload CSS as the application/octet-stream type to S3. Luckily there is a way around this in the latest version of awstasks (which is why we compiled it from SVN earlier).

Create another file in /build called deploy.mime.types and add

    text/css css
    application/x-javascript js

we will use that file a little later to tell awstasks about these types it doesn't recognise.

The third file we are going to add to /build is deploy-targets.xml. This file is where the task that actually deploys to S3 will live.
Put the following into that file:

    <?xml version="1.0"?>
    <project name="YOUR_PROJECT_NAME">
      <property file="${basedir}/build/deploy.properties"/>

      <taskdef name="S3Upload" classname="dak.ant.taskdefs.S3Upload" />

          <target name="deploy-to-s3">
            <echo message="Uploading folder: ${dir.publish} to bucket: ${aws.bucket}" />
           <S3Upload verbose="true"
            accessId="${aws.accessId}"
            secretKey="${aws.secretKey}"
            bucket="${aws.bucket}"
            publicRead="true"
            mimeTypesFile="${basedir}/build/deploy.mime.types">
            <fileset dir="${dir.publish}" />
          </S3Upload>
        </target>
    </project>

Add the name of your project in place of 'YOUR\_PROJECT\_NAME'. You'll see that the script gets the deploy.properties file and then create a new tasks called 'S3Upload'. The S3Upload task has the 'mimeTypesFiles' attribute where we give it the path to the mime types file we made earlier.

You may be wondering where 'dir.publish' property comes from. Good question. This build script is going to be included into HTML5BP's build script so our script will have access to the properties from HMTL5BP - here we tell the S3Upload task to copy the publish directory to our S3 bucket.

There is the one edit we are going to make to the HTML5BP build script: open up build.xml and at the bottom, just before the closing </project> tag include our new deploy-targets.xml file:

    <include file="deploy-targets.xml" />

Zing! and you're done. Let's try it out. From the /build directory:

    ant build
    ant YOUR_PROJECT_NAME.deploy-to-s3

By default 'ant build' will build a production system, minifying and compressing the site and writing the output to the publish/ folder. We then call our new deploy script to upload the site to our S3 bucket.

### Finally...

But wait! That's not all!

You lead a busy jet-set lifestyle, you have no time to type in two commands when one will do. OK, I hear you. 
Open up build.xml and we can add our deploy target to to end of the build task so we only need to type 'ant build' and HTML%BP will build the production site AND then upload to S3.
Take a look around line 352 and add our deploy-to-s3 target as a dependency:

    <target name="-build.production"
            depends="-rev,
                     -usemin,
                     -js.all.minify,
                     -js.main.concat,
                     -js.mylibs.concat,
                     -js.scripts.concat,
                     -css,
                     -manifest,
                     -htmlclean,
                     -imagespng,
                     -imagesjpg,
                     -copy,
                     YOUR_PROJECT_NAME.deploy-to-s3"/>

Bear in mind that you'll need to re-add that line when you upgrade HTML5BP, but you can now build and deploy in one command.

### Further reading

Ant is fun right? Here are the best of the docs that I read while I was figuring out how to do this:

<http://wiki.apache.org/ant/AntNewbies>  
<http://ant.apache.org/manual/develop.html>  
<http://ant.apache.org/manual/Tasks/include.html>  
<http://docs.oracle.com/javase/6/docs/api/javax/activation/MimetypesFileTypeMap.html>
