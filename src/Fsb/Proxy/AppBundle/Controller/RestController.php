<?php

namespace Fsb\Proxy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use GuzzleHttp\Psr7\Request as Psr7Request;

class RestController extends Controller
{
    public function proxyAction($uri, Request $request)
    {
        $response = new Response();

        $client = $this->get('http.client');

        $proxyRequest = new Psr7Request($request->getMethod(), $uri, array(
            'query' => $request->query->all()
        ));

        $proxyResponse = $client->send($proxyRequest);

        $response->setStatusCode($proxyResponse->getStatusCode());
        $response->setContent($proxyResponse->getBody());

        $response->headers->set('Content-Type', $proxyResponse->getHeader('Content-Type'));

        return $response;
    }
}
