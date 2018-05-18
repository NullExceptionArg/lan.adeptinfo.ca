<?php


namespace App\Services;


use App\Model\Lan;
use Illuminate\Http\Request;

interface LanService
{
    public function createLan(Request $input): Lan;

    public function updateRules(Request $input, string $lanId): array;

    public function getRules(string $lanId): array;
}