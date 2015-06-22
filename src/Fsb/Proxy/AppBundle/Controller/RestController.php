<?php

namespace Fsb\Proxy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
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
		), $request->getContent());

		$promise = $client->sendAsync($proxyRequest)->then(function ($proxyResponse) use ($client, $proxyRequest, $response) {

			$response->setStatusCode($proxyResponse->getStatusCode());
			$response->headers->replace($proxyResponse->getHeaders());

			$dom = new Crawler($proxyResponse->getBody()->getContents());
			$baseUri = (String) $client->getConfig()['base_uri'];

			$dom->filter('link')->each(function (Crawler $node, $i) use ($baseUri) {
				$node->getNode(0)->setAttribute('href', preg_replace("#(.?)/(.+)#", $baseUri . '$2', $node->attr('href')));
			});

			$dom->filter('script')->each(function (Crawler $node, $i) use ($baseUri) {
				$node->getNode(0)->setAttribute('src', preg_replace("#(.?)/(.+)#", $baseUri . '$2', $node->attr('src')));
			});

			$response->setContent($dom->html());

			$response->send();
		});

		$promise->wait();
	}
}
