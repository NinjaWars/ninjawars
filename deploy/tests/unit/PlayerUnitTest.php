<?php
use NinjaWars\core\data\PlayerVO;

class PlayerUnitTest extends PHPUnit_Framework_TestCase {
    private $player;
    private $data;

    public function __construct() {
        $this->data = new PlayerVO();
        $this->data->uname = 'User1';
        $this->data->player_id = 1;
        $this->data->level = 20;
        $this->data->health = 20;
    }

	protected function setUp() {
        $this->player = new Player();
        $this->player->vo = $this->data;
    }

	protected function tearDown() {
    }

    public function testPlayerConstructor() {
        $this->assertInstanceOf('\Player', $this->player);
    }

    public function testToString() {
        $this->assertEquals((string)$this->player, $this->data->uname);
    }

    public function testAccessor_magic() {
        $this->assertEquals($this->player->level, $this->data->level);
    }

    public function testAccessor_id() {
        $this->assertEquals($this->player->id(), $this->data->player_id);
    }

    public function testAccessor_level() {
        $this->assertEquals($this->player->level, $this->data->level);
    }

    public function testIsHurtBy() {
        $this->markTestIncomplete('Player::health() currently hits DB');
        //$this->assertGreaterThanOrEqual(0, $this->player->is_hurt_by());
    }

    public function testHealthPercent() {
        $this->markTestIncomplete('Player::health() currently hits DB');
    }

    public function testHealAPlayer(){
        $this->markTestIncomplete('Player::heal() currently hits DB');
        $this->player->heal(20);
        $this->assertEquals(40, $this->player->health());
    }

    public function testHarmAPlayer(){
        $this->markTestIncomplete('Player::harm() currently hits DB');
        $this->player->harm(7);
        $this->assertEquals(13, $this->player->health());
    }

    public function testAsArray() {
        $this->assertInternalType('array', $this->player->as_array());
        $this->assertNotEmpty($this->player->as_array());
    }
}
