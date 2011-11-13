<?php
/**
 * Copyright (C) 2011 by Lars Strojny <lstrojny@php.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Functional;

use ArrayIterator;

class MagicGetThrowException
{
    public function __get($propertyName)
    {
        throw new \Exception($propertyName);
    }
}

class MagicGet
{
    protected $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function __isset($propertyName)
    {
        return isset($this->properties[$propertyName]);
    }

    public function __get($propertyName)
    {
        return $this->properties[$propertyName];
    }
}

class MagicGetException
{
    protected $throwExceptionInIsset = false;
    protected $throwExceptionInGet = false;

    public function __construct($throwExceptionInIsset, $throwExceptionInGet)
    {
        $this->throwExceptionInIsset = $throwExceptionInIsset;
        $this->throwExceptionInGet = $throwExceptionInGet;
    }

    public function __isset($propertyName)
    {
        if ($this->throwExceptionInIsset) {
            throw new \DomainException('__isset exception: ' . $propertyName);
        }
        return true;
    }

    public function __get($propertyName)
    {
        if ($this->throwExceptionInGet) {
            throw new \DomainException('__get exception: ' . $propertyName);
        }
        return "value";
    }
}


class PluckTest extends AbstractTestCase
{
    function setUp()
    {
        parent::setUp();
        $this->propertyExistsEverywhereArray = array((object)array('property' => 1), (object)array('property' => 2));
        $this->propertyExistsEverywhereIterator = new ArrayIterator($this->propertyExistsEverywhereArray);
        $this->propertyExistsSomewhere = array((object)array('property' => 1), (object)array('otherProperty' => 2));
        $this->propertyMagicGet = array(new MagicGet(array('property' => 1)), new MagicGet(array('property' => 2)));
        $this->mixedCollection = array((object)array('property' => 1), array('key'  => 'value'));
        $this->keyedCollection = array('test' => (object)array('property' => 1), 'test2' => (object)array('property' => 2));
        $this->issetExceptionArray = array((object)array('property' => 1), new MagicGetException(true, false));
        $this->issetExceptionIterator = new ArrayIterator($this->issetExceptionArray);
        $this->getExceptionArray = array((object)array('property' => 1), new MagicGetException(false, true));
        $this->getExceptionIterator = new ArrayIterator($this->getExceptionArray);
    }

    function testPluckPropertyThatExistsEverywhere()
    {
        $this->assertSame(array(1, 2), pluck($this->propertyExistsEverywhereArray, 'property'));
        $this->assertSame(array(1, 2), pluck($this->propertyExistsEverywhereIterator, 'property'));
        $this->assertSame(array(1, 2), pluck($this->propertyMagicGet, 'property'));
    }

    function testPluckPropertyThatExistsSomewhere()
    {
        $this->assertSame(array(1, null), pluck($this->propertyExistsSomewhere, 'property'));
        $this->assertSame(array(null, 2), pluck($this->propertyExistsSomewhere, 'otherProperty'));
    }

    function testPluckPropertyFromMixedCollection()
    {
        $this->assertSame(array(1, null), pluck($this->mixedCollection, 'property'));
    }

    function testPluckProtectedProperty()
    {
        $this->assertSame(array(null, null), pluck(array($this, 'foo'), 'preserveGlobalState'));
    }

    function testPluckPropertyInKeyedCollection()
    {
        $this->assertSame(array('test' => 1, 'test2' => 2), pluck($this->keyedCollection, 'property'));
    }

    function testPassNoCollection()
    {
        $this->expectArgumentError('Functional\pluck() expects parameter 1 to be array or instance of Traversable');
        pluck('invalidCollection', 'property');
    }

    function testPassNoPropertyName()
    {
        $this->expectArgumentError('Functional\pluck() expects parameter 2 to be string, object given');
        pluck($this->propertyExistsSomewhere, new \stdClass());
    }

    function testExceptionThrownInMagicIssetWhileIteratingArray()
    {
        $this->setExpectedException('DomainException', '__isset exception: foobar');
        pluck($this->issetExceptionArray, 'foobar');
    }

    function testExceptionThrownInMagicIssetWhileIteratingIterator()
    {
        $this->setExpectedException('DomainException', '__isset exception: foobar');
        pluck($this->issetExceptionIterator, 'foobar');
    }

    function testExceptionThrownInMagicGetWhileIteratingArray()
    {
        $this->setExpectedException('DomainException', '__get exception: foobar');
        pluck($this->getExceptionArray, 'foobar');
    }

    function testExceptionThrownInMagicGetWhileIteratingIterator()
    {
        $this->setExpectedException('DomainException', '__get exception: foobar');
        pluck($this->getExceptionIterator, 'foobar');
    }
}
