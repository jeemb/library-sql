<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Author.php";

    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class AuthorTest extends PHPUnit_Framework_TestCase
    {
        protected function TearDown()
        {
            Author::deleteAll();
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

        function delete()
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
    }

?>
