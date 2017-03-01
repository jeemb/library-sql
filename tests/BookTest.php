<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Book.php";
    require_once "src/Author.php";

    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BookTest extends PHPUnit_Framework_TestCase
    {
        protected function TearDown()
        {
            Book::deleteAll();
            Author::deleteAll();
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

        function test_find()
        {
            //Arrange
            $title = "Crime and Punishment 4 PHP Developers";
            $total_copies = 10;
            $copies_in = 10;
            $copies_out = 0;
            $new_book = new Book($title, $total_copies, $copies_in, $copies_out);
            $new_book->save();

            //Act
            $result = Book::find($new_book->getId());

            //Assert
            $this->assertEquals($result, $new_book);
        }

        function test_update()
        {
            //Arrange
            $title = "Crime and Punishment 4 PHP Developers";
            $total_copies = 10;
            $copies_in = 10;
            $copies_out = 0;
            $new_book = new Book($title, $total_copies, $copies_in, $copies_out);
            $new_book->save();

            //Act
            $new_title = "We really love learning PHP";
            $new_book->update($new_title, $total_copies, $copies_in, $copies_out);
            $result = $new_book->getTitle();

            //Assert
            $this->assertEquals($result, 'We really love learning PHP');
        }

        function delete()
        {
            //Arrange
            $title = "Crime and Punishment 4 PHP Developers";
            $total_copies = 10;
            $copies_in = 10;
            $copies_out = 0;
            $new_book = new Book($title, $total_copies, $copies_in, $copies_out);
            $new_book->save();

            $title2 = "The PHP Adventure of Dylan";
            $total_copies2 = 10;
            $copies_in2 = 9;
            $copies_out2 = 1;
            $new_book2 = new Book($title2, $total_copies2, $copies_in2, $copies_out2);
            $new_book2->save();

            //Act
            $new_book->delete();
            $result = Book::getAll();

            //Assert
            $this->assertEquals($result, [$new_book2]);

        }

        function test_addAuthor()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_author = new Author($name);
            $new_author->save();

            $title = "Crime and Punishment 4 PHP Developers";
            $total_copies = 10;
            $copies_in = 10;
            $copies_out = 0;
            $new_book = new Book($title, $total_copies, $copies_in, $copies_out);
            $new_book->save();

            //Act
            $new_book->addAuthor($new_author);
            $result = $new_book->getAuthors();

            //Assert
            $this->assertEquals([$new_author], $result);
        }
    }

?>
