<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use App\Models\Unit;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProfileController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $request->getAttribute('user', []);
        $id = (int) ($user['id'] ?? 0);
        $profile = $user;
        if ($id) {
            $unit = Unit::find($id);
            if ($unit) {
                $profile = $unit->toArray();
            }
        }
        $response->getBody()->write(json_encode(json_success([
            'profile' => $profile,
            'message' => '单位信息',
        ])));
        return $response;
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $request->getAttribute('user', []);
        $id = (int) ($user['id'] ?? 0);
        if (!$id) {
            $response->getBody()->write(json_encode(json_error(401, '未登录')));
            return $response->withStatus(401);
        }
        $unit = Unit::find($id);
        if (!$unit) {
            $response->getBody()->write(json_encode(json_error(404, '单位不存在')));
            return $response->withStatus(404);
        }
        $data = $request->getParsedBody() ?? [];
        $name = isset($data['name']) ? trim((string) $data['name']) : null;
        if ($name !== null) {
            $unit->name = $name;
        }
        $contact = isset($data['contact']) ? trim((string) $data['contact']) : null;
        if ($contact !== null) {
            $unit->contact = $contact;
        }
        $phone = isset($data['phone']) ? trim((string) $data['phone']) : null;
        if ($phone !== null) {
            $unit->phone = $phone;
        }
        $unit->save();
        $response->getBody()->write(json_encode(json_success([
            'profile' => $unit->toArray(),
            'message' => '更新成功',
        ])));
        return $response;
    }
}
