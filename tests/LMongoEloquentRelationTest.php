<?php

use Mockery as m;
use LMongo\Eloquent\Relations\HasOne;

class LMongoEloquentRelationTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testWhereClausesCanBeRemoved()
	{
		// For this test it doesn't matter what type of relationship we have, so we'll just use HasOne
		$builder = new LMongoRelationResetStub;
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn(1);
		$relation = new HasOne($builder, $parent, 'foreign_key');
		$relation->where('foo', 'bar');
		$wheres = $relation->getAndResetWheres();
		$this->assertEquals('Basic', $wheres[0]['type']);
		$this->assertEquals('foo', $wheres[0]['column']);
		$this->assertEquals('bar', $wheres[0]['value']);
	}

	public function testTouchMethodUpdatesRelatedTimestamps()
	{
		$builder = m::mock('LMongo\Eloquent\Builder');
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn(1);
		$builder->shouldReceive('getModel')->andReturn($related = m::mock('StdClass'));
		$builder->shouldReceive('where');
		$relation = new HasOne($builder, $parent, 'foreign_key');
		$related->shouldReceive('getTable')->andReturn('table');
		$related->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');
		$builder->shouldReceive('update')->once()->with(array('updated_at' => new MongoDate));

		$relation->touch();
	}

}


class LMongoRelationResetStub extends LMongo\Eloquent\Builder {
	public function __construct() { $this->query = new LMongoRelationQueryStub; }
	public function getModel() {}
}


class LMongoRelationQueryStub extends LMongo\Query\Builder {
	public function __construct() {}
}