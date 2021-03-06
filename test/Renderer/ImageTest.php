<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Barcode\Renderer;

use Zend\Barcode;
use Zend\Barcode\Object\Code39;
use Zend\Barcode\Renderer as RendererNS;

/**
 * @group      Zend_Barcode
 */
class ImageTest extends TestCommon
{
    public function setUp()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('\ZendTest\Barcode\Renderer\ImageTest requires the GD extension');
        }
        parent::setUp();
    }

    protected function getRendererObject($options = null)
    {
        return new RendererNS\Image($options);
    }

    public function testType()
    {
        $this->assertSame('image', $this->renderer->getType());
    }

    public function testGoodImageResource()
    {
        $imageResource = imagecreatetruecolor(1, 1);
        $this->renderer->setResource($imageResource);
    }

    public function testObjectImageResource()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        $imageResource = new \stdClass();
        $this->renderer->setResource($imageResource);
    }

    public function testGoodHeight()
    {
        $this->assertSame(0, $this->renderer->getHeight());
        $this->renderer->setHeight(123);
        $this->assertSame(123, $this->renderer->getHeight());
        $this->renderer->setHeight(0);
        $this->assertSame(0, $this->renderer->getHeight());
    }

    public function testBadHeight()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        $this->renderer->setHeight(- 1);
    }

    public function testGoodWidth()
    {
        $this->assertSame(0, $this->renderer->getWidth());
        $this->renderer->setWidth(123);
        $this->assertSame(123, $this->renderer->getWidth());
        $this->renderer->setWidth(0);
        $this->assertSame(0, $this->renderer->getWidth());
    }

    public function testBadWidth()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        $this->renderer->setWidth(- 1);
    }

    public function testAllowedImageType()
    {
        $types = ['gif' => 'gif' , 'jpg' => 'jpeg' , 'jpeg' => 'jpeg' ,
                       'png' => 'png'];
        foreach ($types as $type => $expectedType) {
            $this->renderer->setImageType($type);
            $this->assertSame(
                $expectedType,
                $this->renderer->getImageType()
            );
        }
    }

    public function testNonAllowedImageType()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        $this->renderer->setImageType('other');
    }

    public function testDrawReturnResource()
    {
        $this->checkTTFRequirement();
        $barcode = new Code39(['text' => '0123456789']);
        $this->renderer->setBarcode($barcode);
        $resource = $this->renderer->draw();
        $this->assertInternalType('resource', $resource, 'Image must be a resource');
        $this->assertEquals('gd', get_resource_type($resource), 'Image must be a GD resource');
    }

    public function testDrawWithExistantResourceReturnResource()
    {
        $this->checkTTFRequirement();
        $barcode = new Code39(['text' => '0123456789']);
        $this->renderer->setBarcode($barcode);
        $imageResource = imagecreatetruecolor(500, 500);
        $this->renderer->setResource($imageResource);
        $resource = $this->renderer->draw();
        $this->assertInternalType('resource', $resource, 'Image must be a resource');
        $this->assertEquals('gd', get_resource_type($resource), 'Image must be a GD resource');
        $this->assertSame($resource, $imageResource);
    }

    public function testGoodUserHeight()
    {
        $barcode = new Code39(['text' => '0123456789']);
        $this->assertEquals(62, $barcode->getHeight());
        $this->renderer->setBarcode($barcode);
        $this->renderer->setHeight(62);
        $this->assertTrue($this->renderer->checkParams());
    }

    public function testBadUserHeightLessThanBarcodeHeight()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        $barcode = new Code39(['text' => '0123456789']);
        $this->assertEquals(62, $barcode->getHeight());
        $this->renderer->setBarcode($barcode);
        $this->renderer->setHeight(61);
        $this->renderer->checkParams();
    }

    public function testGoodUserWidth()
    {
        $barcode = new Code39(['text' => '0123456789']);
        $this->assertEquals(211, $barcode->getWidth());
        $this->renderer->setBarcode($barcode);
        $this->renderer->setWidth(211);
        $this->assertTrue($this->renderer->checkParams());
    }

    public function testBadUserWidthLessThanBarcodeWidth()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        $barcode = new Code39(['text' => '0123456789']);
        $this->assertEquals(211, $barcode->getWidth());
        $this->renderer->setBarcode($barcode);
        $this->renderer->setWidth(210);
        $this->renderer->checkParams();
    }

    public function testGoodHeightOfUserResource()
    {
        $barcode = new Code39(['text' => '0123456789']);
        $this->assertEquals(62, $barcode->getHeight());
        $imageResource = imagecreatetruecolor(500, 62);
        $this->renderer->setResource($imageResource);
        $this->renderer->setBarcode($barcode);
        $this->assertTrue($this->renderer->checkParams());
    }

    public function testBadHeightOfUserResource()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        $barcode = new Code39(['text' => '0123456789']);
        $this->assertEquals(62, $barcode->getHeight());
        $this->renderer->setBarcode($barcode);
        $imageResource = imagecreatetruecolor(500, 61);
        $this->renderer->setResource($imageResource);
        $this->renderer->checkParams();
    }

    public function testGoodWidthOfUserResource()
    {
        $barcode = new Code39(['text' => '0123456789']);
        $this->assertEquals(211, $barcode->getWidth());
        $imageResource = imagecreatetruecolor(211, 500);
        $this->renderer->setResource($imageResource);
        $this->renderer->setBarcode($barcode);
        $this->assertTrue($this->renderer->checkParams());
    }

    public function testBadWidthOfUserResource()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        $barcode = new Code39(['text' => '0123456789']);
        $this->assertEquals(211, $barcode->getWidth());
        $this->renderer->setBarcode($barcode);
        $imageResource = imagecreatetruecolor(210, 500);
        $this->renderer->setResource($imageResource);
        $this->renderer->checkParams();
    }

    public function testNoFontWithOrientation()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        Barcode\Barcode::setBarcodeFont(null);
        $barcode = new Code39(['text' => '0123456789']);
        $barcode->setOrientation(1);
        $this->renderer->setBarcode($barcode);
        $this->renderer->draw();
    }

    protected function getRendererWithWidth500AndHeight300()
    {
        return $this->renderer->setHeight(300)->setWidth(500);
    }

    public function testRendererWithUnkownInstructionProvideByObject()
    {
        $this->expectException('\Zend\Barcode\Renderer\Exception\ExceptionInterface');
        parent::testRendererWithUnkownInstructionProvideByObject();
    }

    public function testHorizontalPositionToLeft()
    {
        $this->checkTTFRequirement();

        parent::testHorizontalPositionToLeft();
    }

    public function testHorizontalPositionToCenter()
    {
        $this->checkTTFRequirement();

        parent::testHorizontalPositionToCenter();
    }

    public function testHorizontalPositionToRight()
    {
        $this->checkTTFRequirement();

        parent::testHorizontalPositionToRight();
    }

    public function testVerticalPositionToTop()
    {
        $this->checkTTFRequirement();

        parent::testVerticalPositionToTop();
    }

    public function testVerticalPositionToMiddle()
    {
        $this->checkTTFRequirement();

        parent::testVerticalPositionToMiddle();
    }

    public function testVerticalPositionToBottom()
    {
        $this->checkTTFRequirement();

        parent::testVerticalPositionToBottom();
    }

    public function testLeftOffsetOverrideHorizontalPosition()
    {
        $this->checkTTFRequirement();

        parent::testLeftOffsetOverrideHorizontalPosition();
    }

    public function testTopOffsetOverrideVerticalPosition()
    {
        $this->checkTTFRequirement();

        parent::testTopOffsetOverrideVerticalPosition();
    }

    /**
     * @group 4708
     */
    public function testImageGifWithNoTransparency()
    {
        $barcode = new Code39(['text' => '0123456789']);
        $this->renderer->setBarcode($barcode);

        $this->renderer->setTransparentBackground(false);
        $this->assertFalse($this->renderer->getTransparentBackground());

        //Test Gif output
        $this->renderer->setImageType('gif');
        $image = $this->renderer->draw();
        $index = imagecolortransparent($image);
        $this->assertEquals($index, -1);
    }

    /**
     * @group 4708
     */
    public function testImagePngWithNoTransparency()
    {
        $barcode = new Code39(['text' => '0123456789']);
        $this->renderer->setBarcode($barcode);

        $this->renderer->setTransparentBackground(false);
        $this->assertFalse($this->renderer->getTransparentBackground());

        //Test PNG output
        $this->renderer->setImageType('png');
        $image = $this->renderer->draw();
        $index = imagecolortransparent($image);
        $this->assertEquals($index, -1);
    }

    /**
     * @group 4708
     */
    public function testImageGifWithTransparency()
    {
        $barcode = new Code39(['text' => '0123456789']);
        $this->renderer->setBarcode($barcode);

        $this->renderer->setTransparentBackground(true);
        $this->assertTrue($this->renderer->getTransparentBackground());

        //Test Gif output
        $this->renderer->setImageType('gif');
        $image = $this->renderer->draw();
        $index = imagecolortransparent($image);
        $this->assertNotEquals(-1, $index);
    }

    /**
     * @group 4708
     */
    public function testImagePngWithTransparency()
    {
        $barcode = new Code39(['text' => '0123456789']);
        $this->renderer->setBarcode($barcode);

        $this->renderer->setTransparentBackground(true);
        $this->assertTrue($this->renderer->getTransparentBackground());

        //Test PNG output
        $this->renderer->setImageType('png');
        $image = $this->renderer->draw();
        $index = imagecolortransparent($image);
        $this->assertNotEquals(-1, $index);
    }

    protected function checkTTFRequirement()
    {
        if (! function_exists('imagettfbbox')) {
            $this->markTestSkipped('TTF (FreeType) support is required in order to run this test');
        }
    }
}
