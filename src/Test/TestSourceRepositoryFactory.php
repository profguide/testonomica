<?php

namespace App\Test;

use App\Entity\Test;
use App\Repository\DBSourceRepository;
use App\Repository\SourceRepositoryInterface;
use App\Repository\XmlSourceRepository;

class TestSourceRepositoryFactory
{
    private XmlSourceRepository $xmlRepository;

    private DBSourceRepository $dbSourceRepository;

    public function __construct(XmlSourceRepository $xmlRepository, DBSourceRepository $dbSourceRepository)
    {
        $this->xmlRepository = $xmlRepository;
        $this->dbSourceRepository = $dbSourceRepository;
    }

    public function createSource(Test $test): SourceRepositoryInterface
    {
        if ($test->isXmlSource()) {
            return $this->xmlRepository;
        } else {
            return $this->dbSourceRepository;
        }
    }
}