<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Book.php";

    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BookTest extends PHPUnit_Framework_TestCase
    {
        protected function TearDown()
        {
            Book::deleteAll();
        }

        function test_save()
        {
            //Arrange
            $title = "Crime and Punishment 4 PHP Developers";
            $total_copies = 10;
            $copies_in = 10;
            $copies_out = 0;
            $new_book = new Book($title, $total_copies, $copies_in, $copies_out);

            //Act
            $new_book->save();
            $result = Book::getAll();

            //Assert
            $this->assertEquals($result, [$new_book]);
        }
    }

?>
