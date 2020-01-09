<?php

namespace  Makm\FiasBundle\Component\Unpacker\Tests;

use InvalidArgumentException;
use Liquetsoft\Fias\Component\Exception\UnpackerException;
use Makm\FiasBundle\Component\Unpacker\RarArchiverUnpacker;
use SplFileInfo;

/**
 * Тест для объекта, который распаковывает данные из rar архива.
 */
class RarArchiverUnpackerTest extends BaseCase
{
    /**
     * Проверяет, что объект выбросит исключение, если файла с архивом не
     * существует.
     * @throws UnpackerException
     */
    public function testUnpackUnexistedSourceException()
    {
        $source = new SplFileInfo(__DIR__ . '/empty.rar');
        $destination = new SplFileInfo($this->getPathToTestDir());

        $unpacker = new RarArchiverUnpacker();

        $this->expectException(InvalidArgumentException::class);
        $unpacker->unpack($source, $destination);
    }

    /**
     * Проверяет, что объект выбросит исключение, если папки, в которую должен
     * быть распакован архив, не существует.
     * @throws UnpackerException
     */
    public function testUnpackUnexistedDestinationException()
    {
        $source = new SplFileInfo($this->getPathToTestFile('testUnpackUnexistedDestinationException.rar'));
        $destination = new SplFileInfo('/unexisted/destination');

        $unpacker = new RarArchiverUnpacker;

        $this->expectException(InvalidArgumentException::class);
        $unpacker->unpack($source, $destination);
    }

    /**
     * Проверяет распаковку архива.
     * @throws UnpackerException
     */
    public function testUnpack()
    {
        $sourcePath = __DIR__ . '/_fixtures/test-arch.rar';  // file without extension
        $source = new SplFileInfo($sourcePath);

        $destinationPath = $this->getPathToTestDir('testUnpack');
        $destination = new SplFileInfo($destinationPath);

        $unpacker = new RarArchiverUnpacker;
        $unpacker->unpack($source, $destination);

        $this->assertFileExists($destinationPath . '/xkey.cfg');
        $this->assertSame(2387, filesize($destinationPath . '/xkey.cfg'));
    }

    /**
     * Проверяет , что объект верно обработает битый архив.
     * @throws UnpackerException
     */
    public function testUnpackBrokenArchiveException(): void
    {
        $unpacker = new RarArchiverUnpacker;

        $source = new SplFileInfo($this->getPathToTestFile('broken.rar'));
        $destination = new SplFileInfo($this->getPathToTestDir('testUnpack'));

        $this->expectException(UnpackerException::class);
        $unpacker->unpack($source, $destination);
    }
}
