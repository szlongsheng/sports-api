<?php

declare(strict_types=1);

namespace App\Controllers\Common;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PageController
{
    public function adminLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('admin/login', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function adminDashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('admin/dashboard', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function adminUsers(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('admin/users', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function adminMembers(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('admin/members', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function adminUnits(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('admin/units', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function unitLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('unit/login', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function unitDashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('unit/dashboard', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function unitProfile(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('unit/profile', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function unitCampaign(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('unit/campaign', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function unitUsers(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('unit/users', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function unitTasks(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('unit/tasks', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function unitCheckins(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('unit/checkins', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function unitPrizes(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $html = view('unit/prizes', []);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
