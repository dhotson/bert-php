BERT
====

A BERT (Binary ERlang Term) serialization library for PHP based on
[Tom Preston-Werner's Ruby implementation](http://github.com/mojombo/bert).

It can encode PHP objects into BERT format and decode BERT binaries into PHP
objects.

See the BERT specification at [bert-rpc.org](http://bert-rpc.org).

To designate an atom, use the Bert::a() helper or the Bert_Atom class.
To designate tuples, use the Bert::t() helper or the Bert_Tuple class:

    Bert::t(Bert::a('foo'), array(1, 2, 3))
    new Bert_Tuple(array(new Bert_Atom('foo'), array(1, 2, 3)))


These will both be converted to (in Erlang syntax):

    {foo, [1, 2, 3]}

Usage
-----

    require_once 'classes/Bert.php'

    $bert = Bert::encode(
      Bert::t(
        Bert::a('user'),
        array('name' => 'TPW', 'nick' => 'mojombo')
      )
    );
    # => string(82) "#hduserhdbertddictllmnamemTPWjlmnickmmojombojj"

    Bert::decode($bert);
    # => Bert_Tuple (
           Bert_Atom ( 'user' ),
           Array (
             'name' => 'TPW',
             'nick' => 'mojombo'
           )
         )

Tests
-----

To run the test suite:

    $ ./runtests.php
    runtests.php
    OK
    Test cases run: 2/2, Passes: 21, Failures: 0, Exceptions: 0
