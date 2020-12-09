<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Controller;

use App\Test\CrawlerUtil;
use App\Test\Proforientation\Profession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dev", name="dev.")
 * Class TestsController
 * @package App\Controller
 */
class DevelopController extends AbstractController
{
    const COMBS_POSSIBLE = [
        'art',
        'body',
        'boss',
        'com',
        'craft',
        'hoz',
        'human',
        'it',
        'math',
        'tech',
        'war',
        'art,body',
        'art,boss',
        'art,com',
        'art,craft',
        'art,hoz',
        'art,human',
        'art,it',
        'art,math',
        'art,natural',
        'art,tech',
        'art,war',
        'body,boss',
        'body,com',
        'body,craft',
        'body,hoz',
        'body,human',
        'body,it',
        'body,math',
        'body,natural',
        'body,tech',
        'body,war',
        'boss,com',
        'boss,craft',
        'boss,hoz',
        'boss,human',
        'boss,it',
        'boss,math',
        'boss,natural',
        'boss,tech',
        'boss,war',
        'com,craft',
        'com,hoz',
        'com,human',
        'com,it',
        'com,math',
        'com,natural',
        'com,tech',
        'com,war',
        'craft,hoz',
        'craft,human',
        'craft,it',
        'craft,math',
        'craft,natural',
        'craft,tech',
        'craft,war',
        'hoz,human',
        'hoz,it',
        'hoz,math',
        'hoz,natural',
        'hoz,tech',
        'hoz,war',
        'human,it',
        'human,math',
        'human,natural',
        'human,tech',
        'human,war',
        'it,math',
        'it,natural',
        'it,tech',
        'it,war',
        'math,natural',
        'math,tech',
        'math,war',
        'natural,tech',
        'natural,war',
        'tech,war',
        'art,body,boss',
        'art,body,com',
        'art,body,craft',
        'art,body,hoz',
        'art,body,human',
        'art,body,it',
        'art,body,math',
        'art,body,natural',
        'art,body,tech',
        'art,body,war',
        'art,boss,com',
        'art,boss,craft',
        'art,boss,hoz',
        'art,boss,human',
        'art,boss,it',
        'art,boss,math',
        'art,boss,natural',
        'art,boss,tech',
        'art,boss,war',
        'art,com,craft',
        'art,com,hoz',
        'art,com,human',
        'art,com,it',
        'art,com,math',
        'art,com,natural',
        'art,com,tech',
        'art,com,war',
        'art,craft,hoz',
        'art,craft,human',
        'art,craft,it',
        'art,craft,math',
        'art,craft,natural',
        'art,craft,tech',
        'art,craft,war',
        'art,hoz,human',
        'art,hoz,it',
        'art,hoz,math',
        'art,hoz,natural',
        'art,hoz,tech',
        'art,hoz,war',
        'art,human,it',
        'art,human,math',
        'art,human,natural',
        'art,human,tech',
        'art,human,war',
        'art,it,math',
        'art,it,natural',
        'art,it,tech',
        'art,it,war',
        'art,math,natural',
        'art,math,tech',
        'art,math,war',
        'art,natural,tech',
        'art,natural,war',
        'art,tech,war',
        'body,boss,com',
        'body,boss,craft',
        'body,boss,hoz',
        'body,boss,human',
        'body,boss,it',
        'body,boss,math',
        'body,boss,natural',
        'body,boss,tech',
        'body,boss,war',
        'body,com,craft',
        'body,com,hoz',
        'body,com,human',
        'body,com,it',
        'body,com,math',
        'body,com,natural',
        'body,com,tech',
        'body,com,war',
        'body,craft,hoz',
        'body,craft,human',
        'body,craft,it',
        'body,craft,math',
        'body,craft,natural',
        'body,craft,tech',
        'body,craft,war',
        'body,hoz,human',
        'body,hoz,it',
        'body,hoz,math',
        'body,hoz,natural',
        'body,hoz,tech',
        'body,hoz,war',
        'body,human,it',
        'body,human,math',
        'body,human,natural',
        'body,human,tech',
        'body,human,war',
        'body,it,math',
        'body,it,natural',
        'body,it,tech',
        'body,it,war',
        'body,math,natural',
        'body,math,tech',
        'body,math,war',
        'body,natural,tech',
        'body,natural,war',
        'body,tech,war',
        'boss,com,craft',
        'boss,com,hoz',
        'boss,com,human',
        'boss,com,it',
        'boss,com,math',
        'boss,com,natural',
        'boss,com,tech',
        'boss,com,war',
        'boss,craft,hoz',
        'boss,craft,human',
        'boss,craft,it',
        'boss,craft,math',
        'boss,craft,natural',
        'boss,craft,tech',
        'boss,craft,war',
        'boss,hoz,human',
        'boss,hoz,it',
        'boss,hoz,math',
        'boss,hoz,natural',
        'boss,hoz,tech',
        'boss,hoz,war',
        'boss,human,it',
        'boss,human,math',
        'boss,human,natural',
        'boss,human,tech',
        'boss,human,war',
        'boss,it,math',
        'boss,it,natural',
        'boss,it,tech',
        'boss,it,war',
        'boss,math,natural',
        'boss,math,tech',
        'boss,math,war',
        'boss,natural,tech',
        'boss,natural,war',
        'boss,tech,war',
        'com,craft,hoz',
        'com,craft,human',
        'com,craft,it',
        'com,craft,math',
        'com,craft,natural',
        'com,craft,tech',
        'com,craft,war',
        'com,hoz,human',
        'com,hoz,it',
        'com,hoz,math',
        'com,hoz,natural',
        'com,hoz,tech',
        'com,hoz,war',
        'com,human,it',
        'com,human,math',
        'com,human,natural',
        'com,human,tech',
        'com,human,war',
        'com,it,math',
        'com,it,natural',
        'com,it,tech',
        'com,it,war',
        'com,math,natural',
        'com,math,tech',
        'com,math,war',
        'com,natural,tech',
        'com,natural,war',
        'com,tech,war',
        'craft,hoz,human',
        'craft,hoz,it',
        'craft,hoz,math',
        'craft,hoz,natural',
        'craft,hoz,tech',
        'craft,hoz,war',
        'craft,human,it',
        'craft,human,math',
        'craft,human,natural',
        'craft,human,tech',
        'craft,human,war',
        'craft,it,math',
        'craft,it,natural',
        'craft,it,tech',
        'craft,it,war',
        'craft,math,natural',
        'craft,math,tech',
        'craft,math,war',
        'craft,natural,tech',
        'craft,natural,war',
        'craft,tech,war',
        'hoz,human,it',
        'hoz,human,math',
        'hoz,human,natural',
        'hoz,human,tech',
        'hoz,human,war',
        'hoz,it,math',
        'hoz,it,natural',
        'hoz,it,tech',
        'hoz,it,war',
        'hoz,math,natural',
        'hoz,math,tech',
        'hoz,math,war',
        'hoz,natural,tech',
        'hoz,natural,war',
        'hoz,tech,war',
        'human,it,math',
        'human,it,natural',
        'human,it,tech',
        'human,it,war',
        'human,math,natural',
        'human,math,tech',
        'human,math,war',
        'human,natural,tech',
        'human,natural,war',
        'human,tech,war',
        'it,math,natural',
        'it,math,tech',
        'it,math,war',
        'it,natural,tech',
        'it,natural,war',
        'it,tech,war',
        'math,natural,tech',
        'math,natural,war',
        'math,tech,war',
        'natural,tech,war',
//        'art,body,boss,com',
//        'art,body,boss,craft',
//        'art,body,boss,hoz',
//        'art,body,boss,human',
//        'art,body,boss,it',
//        'art,body,boss,math',
//        'art,body,boss,natural',
//        'art,body,boss,tech',
//        'art,body,boss,war',
//        'art,body,com,craft',
//        'art,body,com,hoz',
//        'art,body,com,human',
//        'art,body,com,it',
//        'art,body,com,math',
//        'art,body,com,natural',
//        'art,body,com,tech',
//        'art,body,com,war',
//        'art,body,craft,hoz',
//        'art,body,craft,human',
//        'art,body,craft,it',
//        'art,body,craft,math',
//        'art,body,craft,natural',
//        'art,body,craft,tech',
//        'art,body,craft,war',
//        'art,body,hoz,human',
//        'art,body,hoz,it',
//        'art,body,hoz,math',
//        'art,body,hoz,natural',
//        'art,body,hoz,tech',
//        'art,body,hoz,war',
//        'art,body,human,it',
//        'art,body,human,math',
//        'art,body,human,natural',
//        'art,body,human,tech',
//        'art,body,human,war',
//        'art,body,it,math',
//        'art,body,it,natural',
//        'art,body,it,tech',
//        'art,body,it,war',
//        'art,body,math,natural',
//        'art,body,math,tech',
//        'art,body,math,war',
//        'art,body,natural,tech',
//        'art,body,natural,war',
//        'art,body,tech,war',
//        'art,boss,com,craft',
//        'art,boss,com,hoz',
//        'art,boss,com,human',
//        'art,boss,com,it',
//        'art,boss,com,math',
//        'art,boss,com,natural',
//        'art,boss,com,tech',
//        'art,boss,com,war',
//        'art,boss,craft,hoz',
//        'art,boss,craft,human',
//        'art,boss,craft,it',
//        'art,boss,craft,math',
//        'art,boss,craft,natural',
//        'art,boss,craft,tech',
//        'art,boss,craft,war',
//        'art,boss,hoz,human',
//        'art,boss,hoz,it',
//        'art,boss,hoz,math',
//        'art,boss,hoz,natural',
//        'art,boss,hoz,tech',
//        'art,boss,hoz,war',
//        'art,boss,human,it',
//        'art,boss,human,math',
//        'art,boss,human,natural',
//        'art,boss,human,tech',
//        'art,boss,human,war',
//        'art,boss,it,math',
//        'art,boss,it,natural',
//        'art,boss,it,tech',
//        'art,boss,it,war',
//        'art,boss,math,natural',
//        'art,boss,math,tech',
//        'art,boss,math,war',
//        'art,boss,natural,tech',
//        'art,boss,natural,war',
//        'art,boss,tech,war',
//        'art,com,craft,hoz',
//        'art,com,craft,human',
//        'art,com,craft,it',
//        'art,com,craft,math',
//        'art,com,craft,natural',
//        'art,com,craft,tech',
//        'art,com,craft,war',
//        'art,com,hoz,human',
//        'art,com,hoz,it',
//        'art,com,hoz,math',
//        'art,com,hoz,natural',
//        'art,com,hoz,tech',
//        'art,com,hoz,war',
//        'art,com,human,it',
//        'art,com,human,math',
//        'art,com,human,natural',
//        'art,com,human,tech',
//        'art,com,human,war',
//        'art,com,it,math',
//        'art,com,it,natural',
//        'art,com,it,tech',
//        'art,com,it,war',
//        'art,com,math,natural',
//        'art,com,math,tech',
//        'art,com,math,war',
//        'art,com,natural,tech',
//        'art,com,natural,war',
//        'art,com,tech,war',
//        'art,craft,hoz,human',
//        'art,craft,hoz,it',
//        'art,craft,hoz,math',
//        'art,craft,hoz,natural',
//        'art,craft,hoz,tech',
//        'art,craft,hoz,war',
//        'art,craft,human,it',
//        'art,craft,human,math',
//        'art,craft,human,natural',
//        'art,craft,human,tech',
//        'art,craft,human,war',
//        'art,craft,it,math',
//        'art,craft,it,natural',
//        'art,craft,it,tech',
//        'art,craft,it,war',
//        'art,craft,math,natural',
//        'art,craft,math,tech',
//        'art,craft,math,war',
//        'art,craft,natural,tech',
//        'art,craft,natural,war',
//        'art,craft,tech,war',
//        'art,hoz,human,it',
//        'art,hoz,human,math',
//        'art,hoz,human,natural',
//        'art,hoz,human,tech',
//        'art,hoz,human,war',
//        'art,hoz,it,math',
//        'art,hoz,it,natural',
//        'art,hoz,it,tech',
//        'art,hoz,it,war',
//        'art,hoz,math,natural',
//        'art,hoz,math,tech',
//        'art,hoz,math,war',
//        'art,hoz,natural,tech',
//        'art,hoz,natural,war',
//        'art,hoz,tech,war',
//        'art,human,it,math',
//        'art,human,it,natural',
//        'art,human,it,tech',
//        'art,human,it,war',
//        'art,human,math,natural',
//        'art,human,math,tech',
//        'art,human,math,war',
//        'art,human,natural,tech',
//        'art,human,natural,war',
//        'art,human,tech,war',
//        'art,it,math,natural',
//        'art,it,math,tech',
//        'art,it,math,war',
//        'art,it,natural,tech',
//        'art,it,natural,war',
//        'art,it,tech,war',
//        'art,math,natural,tech',
//        'art,math,natural,war',
//        'art,math,tech,war',
//        'art,natural,tech,war',
//        'body,boss,com,craft',
//        'body,boss,com,hoz',
//        'body,boss,com,human',
//        'body,boss,com,it',
//        'body,boss,com,math',
//        'body,boss,com,natural',
//        'body,boss,com,tech',
//        'body,boss,com,war',
//        'body,boss,craft,hoz',
//        'body,boss,craft,human',
//        'body,boss,craft,it',
//        'body,boss,craft,math',
//        'body,boss,craft,natural',
//        'body,boss,craft,tech',
//        'body,boss,craft,war',
//        'body,boss,hoz,human',
//        'body,boss,hoz,it',
//        'body,boss,hoz,math',
//        'body,boss,hoz,natural',
//        'body,boss,hoz,tech',
//        'body,boss,hoz,war',
//        'body,boss,human,it',
//        'body,boss,human,math',
//        'body,boss,human,natural',
//        'body,boss,human,tech',
//        'body,boss,human,war',
//        'body,boss,it,math',
//        'body,boss,it,natural',
//        'body,boss,it,tech',
//        'body,boss,it,war',
//        'body,boss,math,natural',
//        'body,boss,math,tech',
//        'body,boss,math,war',
//        'body,boss,natural,tech',
//        'body,boss,natural,war',
//        'body,boss,tech,war',
//        'body,com,craft,hoz',
//        'body,com,craft,human',
//        'body,com,craft,it',
//        'body,com,craft,math',
//        'body,com,craft,natural',
//        'body,com,craft,tech',
//        'body,com,craft,war',
//        'body,com,hoz,human',
//        'body,com,hoz,it',
//        'body,com,hoz,math',
//        'body,com,hoz,natural',
//        'body,com,hoz,tech',
//        'body,com,hoz,war',
//        'body,com,human,it',
//        'body,com,human,math',
//        'body,com,human,natural',
//        'body,com,human,tech',
//        'body,com,human,war',
//        'body,com,it,math',
//        'body,com,it,natural',
//        'body,com,it,tech',
//        'body,com,it,war',
//        'body,com,math,natural',
//        'body,com,math,tech',
//        'body,com,math,war',
//        'body,com,natural,tech',
//        'body,com,natural,war',
//        'body,com,tech,war',
//        'body,craft,hoz,human',
//        'body,craft,hoz,it',
//        'body,craft,hoz,math',
//        'body,craft,hoz,natural',
//        'body,craft,hoz,tech',
//        'body,craft,hoz,war',
//        'body,craft,human,it',
//        'body,craft,human,math',
//        'body,craft,human,natural',
//        'body,craft,human,tech',
//        'body,craft,human,war',
//        'body,craft,it,math',
//        'body,craft,it,natural',
//        'body,craft,it,tech',
//        'body,craft,it,war',
//        'body,craft,math,natural',
//        'body,craft,math,tech',
//        'body,craft,math,war',
//        'body,craft,natural,tech',
//        'body,craft,natural,war',
//        'body,craft,tech,war',
//        'body,hoz,human,it',
//        'body,hoz,human,math',
//        'body,hoz,human,natural',
//        'body,hoz,human,tech',
//        'body,hoz,human,war',
//        'body,hoz,it,math',
//        'body,hoz,it,natural',
//        'body,hoz,it,tech',
//        'body,hoz,it,war',
//        'body,hoz,math,natural',
//        'body,hoz,math,tech',
//        'body,hoz,math,war',
//        'body,hoz,natural,tech',
//        'body,hoz,natural,war',
//        'body,hoz,tech,war',
//        'body,human,it,math',
//        'body,human,it,natural',
//        'body,human,it,tech',
//        'body,human,it,war',
//        'body,human,math,natural',
//        'body,human,math,tech',
//        'body,human,math,war',
//        'body,human,natural,tech',
//        'body,human,natural,war',
//        'body,human,tech,war',
//        'body,it,math,natural',
//        'body,it,math,tech',
//        'body,it,math,war',
//        'body,it,natural,tech',
//        'body,it,natural,war',
//        'body,it,tech,war',
//        'body,math,natural,tech',
//        'body,math,natural,war',
//        'body,math,tech,war',
//        'body,natural,tech,war',
//        'boss,com,craft,hoz',
//        'boss,com,craft,human',
//        'boss,com,craft,it',
//        'boss,com,craft,math',
//        'boss,com,craft,natural',
//        'boss,com,craft,tech',
//        'boss,com,craft,war',
//        'boss,com,hoz,human',
//        'boss,com,hoz,it',
//        'boss,com,hoz,math',
//        'boss,com,hoz,natural',
//        'boss,com,hoz,tech',
//        'boss,com,hoz,war',
//        'boss,com,human,it',
//        'boss,com,human,math',
//        'boss,com,human,natural',
//        'boss,com,human,tech',
//        'boss,com,human,war',
//        'boss,com,it,math',
//        'boss,com,it,natural',
//        'boss,com,it,tech',
//        'boss,com,it,war',
//        'boss,com,math,natural',
//        'boss,com,math,tech',
//        'boss,com,math,war',
//        'boss,com,natural,tech',
//        'boss,com,natural,war',
//        'boss,com,tech,war',
//        'boss,craft,hoz,human',
//        'boss,craft,hoz,it',
//        'boss,craft,hoz,math',
//        'boss,craft,hoz,natural',
//        'boss,craft,hoz,tech',
//        'boss,craft,hoz,war',
//        'boss,craft,human,it',
//        'boss,craft,human,math',
//        'boss,craft,human,natural',
//        'boss,craft,human,tech',
//        'boss,craft,human,war',
//        'boss,craft,it,math',
//        'boss,craft,it,natural',
//        'boss,craft,it,tech',
//        'boss,craft,it,war',
//        'boss,craft,math,natural',
//        'boss,craft,math,tech',
//        'boss,craft,math,war',
//        'boss,craft,natural,tech',
//        'boss,craft,natural,war',
//        'boss,craft,tech,war',
//        'boss,hoz,human,it',
//        'boss,hoz,human,math',
//        'boss,hoz,human,natural',
//        'boss,hoz,human,tech',
//        'boss,hoz,human,war',
//        'boss,hoz,it,math',
//        'boss,hoz,it,natural',
//        'boss,hoz,it,tech',
//        'boss,hoz,it,war',
//        'boss,hoz,math,natural',
//        'boss,hoz,math,tech',
//        'boss,hoz,math,war',
//        'boss,hoz,natural,tech',
//        'boss,hoz,natural,war',
//        'boss,hoz,tech,war',
//        'boss,human,it,math',
//        'boss,human,it,natural',
//        'boss,human,it,tech',
//        'boss,human,it,war',
//        'boss,human,math,natural',
//        'boss,human,math,tech',
//        'boss,human,math,war',
//        'boss,human,natural,tech',
//        'boss,human,natural,war',
//        'boss,human,tech,war',
//        'boss,it,math,natural',
//        'boss,it,math,tech',
//        'boss,it,math,war',
//        'boss,it,natural,tech',
//        'boss,it,natural,war',
//        'boss,it,tech,war',
//        'boss,math,natural,tech',
//        'boss,math,natural,war',
//        'boss,math,tech,war',
//        'boss,natural,tech,war',
//        'com,craft,hoz,human',
//        'com,craft,hoz,it',
//        'com,craft,hoz,math',
//        'com,craft,hoz,natural',
//        'com,craft,hoz,tech',
//        'com,craft,hoz,war',
//        'com,craft,human,it',
//        'com,craft,human,math',
//        'com,craft,human,natural',
//        'com,craft,human,tech',
//        'com,craft,human,war',
//        'com,craft,it,math',
//        'com,craft,it,natural',
//        'com,craft,it,tech',
//        'com,craft,it,war',
//        'com,craft,math,natural',
//        'com,craft,math,tech',
//        'com,craft,math,war',
//        'com,craft,natural,tech',
//        'com,craft,natural,war',
//        'com,craft,tech,war',
//        'com,hoz,human,it',
//        'com,hoz,human,math',
//        'com,hoz,human,natural',
//        'com,hoz,human,tech',
//        'com,hoz,human,war',
//        'com,hoz,it,math',
//        'com,hoz,it,natural',
//        'com,hoz,it,tech',
//        'com,hoz,it,war',
//        'com,hoz,math,natural',
//        'com,hoz,math,tech',
//        'com,hoz,math,war',
//        'com,hoz,natural,tech',
//        'com,hoz,natural,war',
//        'com,hoz,tech,war',
//        'com,human,it,math',
//        'com,human,it,natural',
//        'com,human,it,tech',
//        'com,human,it,war',
//        'com,human,math,natural',
//        'com,human,math,tech',
//        'com,human,math,war',
//        'com,human,natural,tech',
//        'com,human,natural,war',
//        'com,human,tech,war',
//        'com,it,math,natural',
//        'com,it,math,tech',
//        'com,it,math,war',
//        'com,it,natural,tech',
//        'com,it,natural,war',
//        'com,it,tech,war',
//        'com,math,natural,tech',
//        'com,math,natural,war',
//        'com,math,tech,war',
//        'com,natural,tech,war',
//        'craft,hoz,human,it',
//        'craft,hoz,human,math',
//        'craft,hoz,human,natural',
//        'craft,hoz,human,tech',
//        'craft,hoz,human,war',
//        'craft,hoz,it,math',
//        'craft,hoz,it,natural',
//        'craft,hoz,it,tech',
//        'craft,hoz,it,war',
//        'craft,hoz,math,natural',
//        'craft,hoz,math,tech',
//        'craft,hoz,math,war',
//        'craft,hoz,natural,tech',
//        'craft,hoz,natural,war',
//        'craft,hoz,tech,war',
//        'craft,human,it,math',
//        'craft,human,it,natural',
//        'craft,human,it,tech',
//        'craft,human,it,war',
//        'craft,human,math,natural',
//        'craft,human,math,tech',
//        'craft,human,math,war',
//        'craft,human,natural,tech',
//        'craft,human,natural,war',
//        'craft,human,tech,war',
//        'craft,it,math,natural',
//        'craft,it,math,tech',
//        'craft,it,math,war',
//        'craft,it,natural,tech',
//        'craft,it,natural,war',
//        'craft,it,tech,war',
//        'craft,math,natural,tech',
//        'craft,math,natural,war',
//        'craft,math,tech,war',
//        'craft,natural,tech,war',
//        'hoz,human,it,math',
//        'hoz,human,it,natural',
//        'hoz,human,it,tech',
//        'hoz,human,it,war',
//        'hoz,human,math,natural',
//        'hoz,human,math,tech',
//        'hoz,human,math,war',
//        'hoz,human,natural,tech',
//        'hoz,human,natural,war',
//        'hoz,human,tech,war',
//        'hoz,it,math,natural',
//        'hoz,it,math,tech',
//        'hoz,it,math,war',
//        'hoz,it,natural,tech',
//        'hoz,it,natural,war',
//        'hoz,it,tech,war',
//        'hoz,math,natural,tech',
//        'hoz,math,natural,war',
//        'hoz,math,tech,war',
//        'hoz,natural,tech,war',
//        'human,it,math,natural',
//        'human,it,math,tech',
//        'human,it,math,war',
//        'human,it,natural,tech',
//        'human,it,natural,war',
//        'human,it,tech,war',
//        'human,math,natural,tech',
//        'human,math,natural,war',
//        'human,math,tech,war',
//        'human,natural,tech,war',
//        'it,math,natural,tech',
//        'it,math,natural,war',
//        'it,math,tech,war',
//        'it,natural,tech,war',
//        'math,natural,tech,war',
    ];

    /**@var KernelInterface */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Route("/xml/")
     */
    public function xml()
    {
        $questions = '';
        $crawler = CrawlerUtil::load($this->kernel->getProjectDir() . "/xml/proforientation2.xml");
        $items = $crawler->children('item');
        foreach ($items as $itemElement) {
            $itemCrawler = new Crawler($itemElement);
            $questions
                .= '<div style="margin-bottom: 10px">'
                . '<span style="color: #888">' . $itemCrawler->attr('group') . '</span><br> '
                . $itemCrawler->children('name')->text()
                . '</div>';
        }
        return new Response($questions);
    }

    /**
     * For development
     * @Route("/table/", name=".table")
     */
    public function table()
    {
        $professions = $this->loadProfessions();

        return $this->render('dev/table.html.twig', [
            'professions' => $professions,
            'combs_possible' => self::COMBS_POSSIBLE
        ]);
    }

    private function loadProfessions()
    {
        $professions = [];
        $crawler = CrawlerUtil::load($this->kernel->getProjectDir() . "/xml/proforientation2Professions.xml");
        foreach ($crawler as $professionNode) {
            $crawler = new Crawler($professionNode);
            $professions[] = new Profession(
                $crawler->children('name')->text(),
                $this->parseCombs($crawler->children('combs')),
                $this->parseProfessionNot($crawler));
        }
        return $professions;
    }

    private function parseCombs(Crawler $combs): array
    {
        $arr = [];
        /**@var \DOMElement $comb */
        foreach ($combs->children() as $comb) {
//            echo trim($comb->getAttribute('comb')) . "<br/>";
            $types = explode(",", trim($comb->getAttribute('comb')));
            asort($types);
            $arr[] = $types;
        }
        return $arr;
    }

    private function parseProfessionNot(Crawler $crawler)
    {
        $arr = [];
        $not = $crawler->attr('not');
        if (!empty($not)) {
            foreach (explode(",", $not) as $word) {
                $arr[] = trim($word);
            }
        }
        return $arr;
    }
}