<?php

namespace App\Services;

use App\Model\Request as TeamRequest;
use App\Model\Tag;
use App\Model\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface TeamService
{
    public function create(Request $input): Team;

    public function createRequest(Request $input): TeamRequest;

    public function getUserTeams(Request $input): AnonymousResourceCollection;

    public function changeLeader(Request $input): Tag;

    public function acceptRequest(Request $input): Tag;

    public function getRequests(Request $input): AnonymousResourceCollection;

    public function leave(Request $input): Team;

    public function deleteAdmin(Request $input): Team;

    public function deleteLeader(Request $input): Team;

    public function deleteRequestLeader(Request $input): Tag;

    public function deleteRequestPlayer(Request $input): Team;
}