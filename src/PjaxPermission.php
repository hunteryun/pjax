<?php

namespace Hunter\pjax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides pjax module middleware.
 */
class PjaxPermission {

  /**
   * Returns bool value of pjax permission.
   *
   * @return bool
   */
  public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
     $response = $next($request, $response);
     // Only handle non-redirections and must be a pjax-request
     if (!$this->isRedirection($response) && $this->pjax($request)) {
         $crawler = new Crawler($response->getBody()->__toString());
         // Filter to title (in order to update the browser title bar)
         $response_title = $crawler->filter('head > title');
         // Filter to given container
         $response_container = $crawler->filter($request->hasHeader('X-PJAX-CONTAINER'));
         // Container must exist
         if ($response_container->count() != 0) {
             $title = '';
             // If a title-attribute exists
             if ($response_title->count() != 0) {
                 $title = '<title>' . $response_title->html() . '</title>';
             }
             // Set new content for the response
             $response->setContent($title . $response_container->html());
         }
         // Updating address bar with the last URL in case there were redirects
         $response->withHeader('X-PJAX-URL', request_uri());
     }
     return $response;
  }

  /**
   * Is the response a redirect?
   *
   * @final
   */
  public function isRedirection($response) {
      return $response->getStatusCode() >= 300 && $response->getStatusCode() < 400;
  }

  /**
   * Determine if the request is the result of an PJAX call.
   *
   * @return bool
   */
  public function pjax($request) {
      return $request->hasHeader('X-PJAX');
  }

}
