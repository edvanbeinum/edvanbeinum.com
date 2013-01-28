---
layout: post
title: "Unit Test File Systems with vfsStream in PHP"
date: 2011-09-11 17:38

---
[vfsStream](https://github.com/mikey179/vfsStream/wiki) is a framework for unit testing file systems in PHP. Use it with PHPUnit for all kinds of testing fun. The docs are fairly good but I felt like I wanted a slightly better example code. So here is my attempt at doing that.

## Installing

Nice and easy with Pear:

	$ pear channel-discover pear.php-tools.net
	$ pear install pat/vfsStream-beta

The latest version is 0.10.1 at the time of writing.

## The Code we are testing

Here is the code we will be testing; this is a rather simple helper class that deals with creating files and directories.

	class FileSystem
	{

	    /**
	     * Writes a given string to a file.
	     * Will try to create the file if it doesn't exist
	     *
	     * @static
	     * @param string $filePath
	     * @param string $content
	     * @return void
	     */
	    public static function createFile($filePath, $content)
	    {
	        $directoryPath = pathinfo($filePath, PATHINFO_DIRNAME);

	        if (self::createDirectory($directoryPath)) {
	            file_put_contents($filePath, $content);
	        }

	    }

	    /**
	     * Creates a given directory if it doesn't already exist
	     *
	     * @static
	     * @throws \Exception
	     * @param string $directoryPath
	     * @return bool
	     */
	    public static function createDirectory($directoryPath)
	    {
	        if (is_writeable($directoryPath)) {
	            return TRUE;
	        }
	        else {

	            // suppress errors on mkdir: we'll throw our own exception if it fails
	            if (@mkdir($directoryPath, 0777)) {
	                return TRUE;
	            }
	            
	            $processUser = posix_getpwuid(posix_geteuid());
	            throw new \Exception(
	                "$directoryPath is not writable and couldn't be created. \n" . 
	                "Make sure the user {$processUser['name']} " .
	                "has write permissions on the directory: $directoryPath"
	            );
	        }
	        return FALSE;
	    }
	}


## The Test Case

Here is the first part of the FileSystemTest file we will use to test the Filesystem class:

	require_once '../Filesystem.php';
	require_once 'vfsStream/vfsStream.php';

	/**
	 * Test class for FileSystem.
	 */
	class FileSystemTest extends PHPUnit_Framework_TestCase
	{
	    /**
	     * @var FileSystem
	     */
	    protected $_fileSystem;

	    /**
	     * Sets up the fixture.
	     */
	    protected function setUp()
	    {
	        $this->_fileSystem = new FileSystem;

	        vfsStreamWrapper::register();
	        vfsStreamWrapper::setRoot(new vfsStreamDirectory('testDir'));
	    }

	    /**
	     * Tears down the fixture.
	     */
	    protected function tearDown()
	    {
	        unset($this->_fileSystem);
	    }
	}

vfsStream operates under the singleton 'design pattern' (yep, those quotes were deliberate) so many of its calls are static. In the above code, you'll see we register a new instance of vfsStreamWrapper and create a root directory called 'testDir'.

##How does vfsStream work?

I'm still getting my head around how that works. I think it something like: vfsStream creates a [stream](http://uk3.php.net/manual/en/intro.stream.php) of a custom type called vfs:// which it does by implementing its own [Stream Wrapper class](http://uk3.php.net/manual/en/class.streamwrapper.php) called  [vfsStreamWrapper](https://github.com/mikey179/vfsStream/blob/master/src/main/php/org/bovigo/vfs/vfsStreamWrapper.php). Since PHP treats the filesystem like any other stream then using a custom stream will work. This is the reason you can do things like:

	file_get_contents('http://edvanbeinum.com');

(provided the php.ini setting _allow\_url\_fopen_ is TRUE), the file\_get\_contents function will work with any stream be it a filesystem , http, or anything else.

But if someone wants to be a dear and explain all that to me properly, that'd be grand.   


## Testing directories 

We are first going to write the tests for the createDirectory() function. And the first test we want is that passing a  name as a string parameter will create a new directory with that name. Take a look at this:

    /**
     * createDirectory() creates a new directory
     *
     * @test
     */
    public function createDirectory_creates_new_directory()
    {
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('newDir'));

        $this->assertTrue(
	        $this->_fileSystem->createDirectory(vfsStream::url('testDir/newDir'))
	    );
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('newDir'));
    }

This is pretty much lifted from the [manual](https://github.com/mikey179/vfsStream/wiki/Example). We first assert that the root directory (created in out setUp() method to be called 'testDir') doesn't have any child elements.   
Then we call our createDirectory() function and pass in vfsStream::url('testDir/newDir') instead of a string with a folder path in it. We first assert that calling createDirectory() returns TRUE and we then assert that the root directory now has a child called 'newDir' - which is should if createDirectory() works properly. 

Next we want to test if create directory works with an existing directory:

     /**
     * createDirectory returns true with existing directory
     * 
     * @test
     */
    public function createDirectoryreturns_true_with_existing_directory()
    {
        vfsStream::newDirectory('newDir', 0755)->at(vfsStreamWrapper::getRoot());
        $this->assertTrue(
	        $this->_fileSystem->createDirectory(vfsStream::url('testDir/newDir'))
	    );
    }

You can create folders using vfsStream::newDirectory() and passing in the path and the folder permissions and then specifing the parent folder usingthe at() method. So here we check that createDirectory() returns true when the folder already exists.

Our final test is to check that createDirectory() throws an Exception if the path provided is unwritable. Here is some more code:

    /**
     * createDirectory throws exception if directory is unwritable
     * 
     * @expectedException Exception
     * @test
     */
    public function createDirectory_throws_exception_if_directory_is_unwritable()
    {
        vfsStreamWrapper::getRoot()->chmod(0400);
        $this->_fileSystem->createDirectory(vfsStream::url('testDir/newDir'));
    }

We can use PHPUnit's [@expectedException annotation](http://www.phpunit.de/manual/3.5/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.exceptions) to test that this method throws an exception. We get the root directory as set in setUp() method and set its permissions to be only readable by the owner. Now when we try and create a new sub-folder it should throw an exception. There is lots more good stuff on permissions on the vfsStream wiki page on [Filemodes](https://github.com/mikey179/vfsStream/wiki/Filemode)


## Testing Files

Testing files is just as easy as folders, so this time I'll give you all the tests in one go. 

    /**
     * createFile() creates new file
     *
     * @test
     */
    public function createFile_creates_new_file()
    {
        $this->_fileSystem->createFile(vfsStream::url('testDir/one.html'), 'some content');
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('one.html'));
    }


    /**
     * createFile() overwrites existing file
     *
     * @test
     */
    public function createFile_overwrites_existing_file()
    {
        vfsStream::newFile('one.html')->at(vfsStreamWrapper::getRoot());
        $this->_fileSystem->createFile(vfsStream::url('testDir/one.html'), 'some content');
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('one.html'));
    }

    /**
     * createFile() creates new file in subdir
     *
     * @test
     */
    public function createFile_creates_new_file_in_subdir()
    {
        $this->_fileSystem->createFile(
	        vfsStream::url('testDir/subDir/one.html'), 'some content'
	    );
        $this->assertTrue(
	        vfsStreamWrapper::getroot()->getChild('subDir')->hasChild('one.html')
	    );
    }

That should look pretty familiar, but note we use the vfsStream::newFile() method to mock existing files.

## Testing Complex Folder Structures

We don't need to do this for testing our class but creating complex folder and file structures is pretty easy. You can use an associative array to specifiy the structure and then pass it to vfsStream::create(). Keys with arrays as values are created as folders and keys with strings as values are created as files. The vfsStream docs give you a good example of [How To Create Complex Directory Structures](https://github.com/mikey179/vfsStream/wiki/Createcomplexstructures)

So there you are, go forth and mock your filesystem (and I don't mean laughing at FAT32)