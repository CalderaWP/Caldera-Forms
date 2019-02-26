<?php


namespace calderawp\calderaforms\Tests\Util\Traits;

/**

use calderawp\calderaforms\Tests\Util\Traits\TestsWpMail;

class ImageTest extends TestCase
{


    use TestsImages;
    public function tearDown()
    {
        $this->deleteTestCatFile();
        parent::tearDown();
    }

    public function testSomething()
    {
         $file = $this->createSmallCat();
        $this->assertTrue(file_exists($file['tmp_name']));
    }
}

 */
trait TestsImages
{

    protected $tmpPath = '/tmp/cats/small-cat.jpeg';
    protected $tmpPathTiny = '/tmp/cats/tiny-cat.jpeg';

    /**
     * Gets a small cat
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function getSmallCat()
    {
        return file_get_contents($this->getSmallCatPath());
    }


    /**
     * Get file path for small cat
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function getSmallCatPath()
    {
        return __DIR__ . '/images/small-cat.jpeg';
    }

    /**
     * Create a tmp copy of small cat and return its file array
     *
     * @since 1.8.0
     *
     * @return array
     */
    public function createSmallCat()
    {
		$this->makeCatsDir();
		copy($this->getSmallCatPath(), $this->tmpPath);
        return [
            'file' => file_get_contents($this->tmpPath),
            'name' => 'small-cat.jpeg',
            'size' => filesize($this->tmpPath),
            'tmp_name' => $this->tmpPath,
        ];
    }
    /**
     * Gets a Tiny cat
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function getTinyCat()
    {
        return file_get_contents($this->getTinyCatPath());
    }


    /**
     * Get file path for tiny cat
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function getTinyCatPath()
    {
        return __DIR__ . '/images/tiny-cat.jpeg';
    }

    /**
     * Create a tmp copy of tiny cat and return its file array
     *
     * @since 1.8.0
     *
     * @return array
     */
    public function createTinyCat()
    {
		$this->makeCatsDir();
        copy($this->getTinyCatPath(), $this->tmpPathTiny);
        return [
            'file' => file_get_contents($this->tmpPathTiny),
            'name' => 'tiny-cat.jpeg',
            'size' => filesize($this->tmpPathTiny),
            'tmp_name' => $this->tmpPathTiny,
        ];
    }

    /**
     * Delete the test cats
     *
     * @since 1.8.0
     */
    public function deleteTestCatFile()
    {
        if (file_exists($this->tmpPath)) {
            unlink($this->tmpPath);
        }
        if (file_exists($this->tmpPathTiny)) {
            unlink($this->tmpPathTiny);
        }
		if (file_exists('/tmp/cats')) {
			rmdir('/tmp/cats');
		}
	}

	/**
	 * Make directory for cats to be stored in
	 *
	 * @since 1.8.0
	 */
	protected function makeCatsDir()
	{
		if ( !file_exists('/tmp/cats') ) {
			mkdir('/tmp/cats');
		}
	}
}
