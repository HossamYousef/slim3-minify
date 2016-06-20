<?php
/*
The MIT License (MIT)

Copyright (c) 2016 Hossam Youssef

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
namespace Slim\Middleware;

class Minify
{
    public function __invoke($request, $response, $next)
    {
        // call next middleware
        $response = $next($request, $response);
        $content = (string)$response->getBody();

        $search = array(
            // remove tabs before and after HTML tags
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            // remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
            '/\>[\r\n\t ]+\</s',
            // shorten multiple whitespace sequences
            '/(\s)+/s',
            // replace end of line by a space
            '/\n/',
            // Remove any HTML comments, except MSIE conditional comments
            '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s',
        );
        $replace = array(
            '>',
            '<',
            '><',
            '\\1',
            ' ',
            ''
        );
        $newContent = preg_replace($search, $replace, $content);
        $newBody = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        $newBody->write($newContent);
        return $response->withBody($newBody);
    }
}
