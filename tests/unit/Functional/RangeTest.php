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

class RangeTest extends AbstractTestCase
{
    function setUp()
    {
        parent::setUp();
    }

    function testIntegerRange()
    {
        $it = range(0, 100, 1);
        $this->assertSame(0, $it->getLeftBound());
        $this->assertSame(100, $it->getRightBound());
        $this->assertSame(1, $it->getStep());
    }

    function testFloatRange()
    {
        $it = range(0, 1, 0.1);
        $this->assertSame(0.0, $it->getLeftBound());
        $this->assertSame(1.0, $it->getRightBound());
        $this->assertSame(0.1, $it->getStep());
    }

    function testPassingTolerance()
    {
        $it = range(0, 1, 0.1, 0.001);
        $this->assertSame(0.001, $it->getTolerance());
    }

    function testDefaultTolerance()
    {
        $it = range(0, 1, 0.1);
        $this->assertSame(0.0000000001, $it->getTolerance());
    }

    function testOneIsDefaultStep()
    {
        $it = range(0, 100);
        $this->assertSame(1, $it->getStep());
    }
}
