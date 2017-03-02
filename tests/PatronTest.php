<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Patron.php";
    require_once "src/Author.php";
    require_once "src/Book.php";

    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class PatronTest extends PHPUnit_Framework_TestCase
    {
        protected function TearDown()
        {
            Patron::deleteAll();
            Book::deleteAll();
            Patron::deleteAll();
        }

        function test_save()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_patron = new Patron($name);

            //Act
            $new_patron->save();
            $result = Patron::getAll();

            //Assert
            $this->assertEquals($result, [$new_patron]);
        }

        function test_find()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_patron = new Patron($name);
            $new_patron->save();

            //Act
            $result = Patron::find($new_patron->getId());

            //Assert
            $this->assertEquals($result, $new_patron);
        }

        function test_update()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_patron = new Patron($name);
            $new_patron->save();

            //Act
            $new_name = "Georgie 'rail-road' Martinizer";
            $new_patron->update($new_name);
            $result = $new_patron->getName();

            //Assert
            $this->assertEquals($result, "Georgie 'rail-road' Martinizer");
        }

        function test_getOverdue()
        {
            //Arrange
            $name = "Shaun Piiterson";
            $new_patron = new Patron($name);
            $new_patron->save();

            $title = "Crime and Punishment 4 PHP Developers";
            $total_copies = 10;
            $copies_in = 10;
            $copies_out = 0;
            $new_book = new Book($title, $total_copies, $copies_in, $copies_out);
            $new_book->save();

            $new_book->addOverdue($new_patron->getId());

            //Act
            $result = $new_patron->getOverdue();

            //Assert
            $this->assertEquals([$new_book], $result);
        }

        function test_delete()
        {
            //Arrange
            $name = "George R.R. Martin";
            $new_patron = new Patron($name);
            $new_patron->save();

            $name2 = "The PHP Adventure of Dylan";
            $new_patron2 = new Patron($name2);
            $new_patron2->save();

            //Act
            $new_patron->delete();
            $result = Patron::getAll();

            //Assert
            $this->assertEquals($result, [$new_patron2]);

        }

    }

?>
