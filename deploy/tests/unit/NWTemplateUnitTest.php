<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\NWTemplate;

class NWTemplateUnitTest extends NWTest {
    public function setUp() {
      parent::setUp();
		  SessionFactory::init(new MockArraySessionStorage());
    }

    public function tearDown() {
      parent::tearDown();
		  SessionFactory::annihilate();
    }

    public function testCustomConstructor() {
        $view = new NWTemplate();
        $this->assertInstanceOf('NinjaWars\core\extensions\NWTemplate', $view);
    }
}
