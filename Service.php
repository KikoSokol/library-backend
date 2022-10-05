<?php
require_once "Repository.php";
require_once "model/mapper/ModelMapper.php";
require_once "utils/Validator.php";

class Service
{
    private $repository;
    private $modelMapper;
    private $validator;

    public function __construct()
    {
        $this->repository = new Repository();
        $this->modelMapper = new ModelMapper();
        $this->validator = new Validator();
    }

    public function addNewBook(NewBook $newBook)
    {
        $validationResult = $this->validator->validateNewBook($newBook);

        if(!is_bool($validationResult))
        {
            $result = array();
            $result["typeError"] = "Wrong inputs";
            $result["type"] = "objectWithInputsError";
            $result["result"] = $validationResult;
            return $result;
        }

        if(!is_null($newBook->getAuthor()))
        {
            $author = $this->repository->getAutorById($newBook->getAuthor());
            if(!$author)
            {
                $result = array();
                $result["typeError"] = "existsProblem";
                $result["type"] = "string";
                $result["result"] = "Author does not exists";
                return $result;
            }
        }


        if($this->repository->getBookByIsbn($newBook->getIsbn()))
        {
            $result = array();
            $result["typeError"] = "existsProblem";
            $result["type"] = "string";
            $result["result"] = "Book with isbn: " . $newBook->getIsbn() . " already exists";
            return $result;
        }

        if(!$this->repository->getCategoryById($newBook->getCategory()))
        {
            $result = array();
            $result["typeError"] = "existsProblem";
            $result["type"] = "string";
            $result["result"] = "Category with id: " . $newBook->getCategory() . " does not exists";
            return $result;
        }

        $addedBookId = $this->repository->addNewBook($newBook);

        if($addedBookId == -1)
        {
            $result = array();
            $result["typeError"] = "createProblem";
            $result["type"] = "string";
            $result["result"] = "The book could not be added";
            return $result;
        }


        $book = $this->repository->getBookById($addedBookId);

        return $this->modelMapper->toBookDto($book);

    }

    public function getAllBooks()
    {
//        return $this->repository->getAllBooks();
        return $this->fromBookListToBookDtoList($this->repository->getAllBooks());
    }

    public function getAllBooksSorted($what, $sort)
    {
//        return
        return $this->fromBookListToBookDtoList($this->repository->getAllBooksSorted($what, $sort));
    }

    public function getAllCategory()
    {
        return $this->repository->getAllCategory();
    }

    public function getAuthorByStartMatchFullName($name)
    {
        return $this->repository->getAutorByStartFullName($name);
    }

    private function fromBookListToBookDtoList($books)
    {
        if(!$books)
        {
            return [];
        }

        $list = array();

        foreach($books as $book)
        {
            $list[] = $this->modelMapper->toBookDto($book);
        }

        return $list;

    }



}