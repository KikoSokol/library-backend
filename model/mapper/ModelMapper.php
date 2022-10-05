<?php

require_once "Repository.php";
require_once "Database.php";
require_once "model/NewBook.php";
require_once "model/Author.php";
require_once "model/Category.php";
require_once "model/dto/BookDto.php";

class ModelMapper
{
    private Repository $repository;

    public function __construct()
    {
        $this->repository = new Repository();
    }

    public function toBookDto($book)
    {
        $category = $this->repository->getCategoryById($book->category);
        $author = $this->repository->getAutorById($book->author);

        $bookDto = new BookDto();
        $bookDto->setId($book->id);
        $bookDto->setTitle($book->title);
        $bookDto->setIsbn($book->isbn);
        $bookDto->setPrice($book->price);
        $bookDto->setCategory($category);
        $bookDto->setAuthor($author);

        return $bookDto;
    }

}