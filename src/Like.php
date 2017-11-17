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
 * Holds a like
 *
 * @author sylae and skyyrunner
 */
class Like {

  /**
   * @var User
   */
  public $user;

  /**
   * @var Post
   */
  public $post;

  /**
   * @var Fic
   */
  public $fic;

  /**
   * @var \Carbon\Carbon
   */
  public $time;

  public function __construct(User $user, Post $post, Fic $fic, \Carbon\Carbon $time) {
    $this->user = $user;
    $this->post = $post;
    $this->fic  = $fic;
    $this->time = $time;
  }

  public function sync() {
    $sql = DatabaseFactory::get()->prepare('INSERT INTO armbreaker_likes (pid, uid, likeTime, lastUpdated) VALUES(?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE likeTime=VALUES(likeTime), lastUpdated=VALUES(lastUpdated);', ['integer', 'string', 'datetime', 'datetime']);
    $sql->bindValue(1, $this->post->id);
    $sql->bindValue(2, $this->user->id);
    $sql->bindValue(3, $this->time);
    $sql->bindValue(4, \Carbon\Carbon::now());
    $sql->execute();
  }

}