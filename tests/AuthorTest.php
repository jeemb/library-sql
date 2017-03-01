<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Author.php";
    require_once "src/Book.php";

    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class AuthorTest extends PHPUnit_Framework_TestCase
    {
        protected function TearDown()
        {
            Author::deleteAll();
            Book::deleteAll();
        }

        function test_save()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_author = new Author($name);

            //Act
            $new_author->save();
            $result = Author::getAll();

            //Assert
            $this->assertEquals($result, [$new_author]);
        }

        function test_find()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_author = new Author($name);
            $new_author->save();

            //Act
            $result = Author::find($new_author->getId());

            //Assert
            $this->assertEquals($result, $new_author);
        }

        function test_update()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_author = new Author($name);
            $new_author->save();

            //Act
            $new_name = "Georgie 'rail-road' Martinizer";
            $new_author->update($new_name);
            $result = $new_author->getName();

            //Assert
            $this->assertEquals($result, "Georgie 'rail-road' Martinizer");
        }

        function test_delete()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_author = new Author($name);
            $new_author->save();

            $name2 = "The PHP Adventure of Dylan";
            $new_author2 = new Author($name2);
            $new_author2->save();

            //Act
            $new_author->delete();
            $result = Author::getAll();

            //Assert
            $this->assertEquals($result, [$new_author2]);

        }

        function test_addBook()
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
            $new_author->addBook($new_book);
            $result = $new_author->getBooks();

            //Assert
            $this->assertEquals([$new_book], $result);
        }
    }

?>
