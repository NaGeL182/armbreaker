<?php

/*
 * The MIT License
 *
 * Copyright 2017 sylae and skyyrunner.
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

namespace Armbreaker;

/**
 * Description of PostCollection
 *
 * @author sylae and skyyrunner
 */
class PostCollection implements \Iterator, \Countable, \JsonSerializable
{

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var array
     */
    private $posts = [];

    /**
     * @var CarbonRange
     */
    public $timeRange;

    /**
     * @var CarbonRange
     */
    public $timeRangeChapters;

    /**
     * @var CarbonRange
     */
    public $timeRangeLikes;

    public function __construct()
    {
        $this->timeRange         = new CarbonRange();
        $this->timeRangeChapters = new CarbonRange();
        $this->timeRangeLikes    = new CarbonRange();
    }

    public function addPost(Post $post): void
    {
        $this->posts[] = $post;
        $this->timeRangeChapters->addDate($post->time);
        $this->timeRangeLikes->addRange($post->likes->timeRange);
        $this->timeRange->addRange($this->timeRangeChapters);
        $this->timeRange->addRange($this->timeRangeLikes);
    }

    public function jsonSerialize()
    {
        return [
            'earliest'   => $this->timeRange->atomEarliest(),
            'latest'     => $this->timeRange->atomLatest(),
            'rangeLikes' => [
                'earliest' => $this->timeRangeLikes->atomEarliest(),
                'latest'   => $this->timeRangeLikes->atomLatest(),
            ],
            'rangePosts' => [
                'earliest' => $this->timeRangeChapters->atomEarliest(),
                'latest'   => $this->timeRangeChapters->atomLatest(),
            ],
            'posts'      => $this->posts,
        ];
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current(): Post
    {
        return $this->posts[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->posts[$this->position]);
    }

    public function count(): int
    {
        return count($this->posts);
    }
}
