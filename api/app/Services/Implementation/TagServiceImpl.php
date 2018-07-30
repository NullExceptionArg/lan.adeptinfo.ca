<?php

namespace App\Services\Implementation;


use App\Model\Tag;
use App\Repositories\Implementation\TagRepositoryImpl;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TagServiceImpl implements TagService
{
    protected $tagRepository;

    /**
     * LanServiceImpl constructor.
     * @param TagRepositoryImpl $tagRepositoryImpl
     */
    public function __construct(TagRepositoryImpl $tagRepositoryImpl)
    {
        $this->tagRepository = $tagRepositoryImpl;
    }

    public function create(Request $input): Tag
    {
        $tournamentValidator = Validator::make([
            'name' => $input->input('name'),
        ], [
            'name' => 'required|string|max:5|unique:tag,name',
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $user = Auth::user();
        return $this->tagRepository->create(
            $user,
            $input->input('name')
        );
    }
}