<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function shouldCollect($data): false|Collection
    {
        if (! is_array($data) || count($data) !== 1) {
            return false;
        }

        $collect = $data[array_keys($data)[0]];

        return $collect instanceof Collection ? $collect : collect($collect);
    }

    public function handleResponse(
        mixed     $data,
        string    $component,
        ?callable $apiCallback = null,
    ): Response|JsonResource|AnonymousResourceCollection {
        if (request()->attributes->get('is_inertia')) {
            return Inertia::render($component, $data);
        }

        if (is_callable($apiCallback)) {
            return $apiCallback();
        }

        if ($data instanceof Collection || $collect = $this->shouldCollect($data)) {
            return JsonResource::collection($collect ?? $data);
        }

        return new JsonResource($data);
    }

    public function handleRedirect(
        mixed     $data,
        ?string   $redirect = null,
        ?callable $apiCallback = null
    ): RedirectResponse|JsonResource {
        if (request()->attributes->get('is_inertia')) {
            if (! $redirect) {
                return redirect()->back();
            }

            return redirect($redirect);
        }

        if (is_callable($apiCallback)) {
            return $apiCallback();
        }

        if ($data instanceof Collection || $collect = $this->shouldCollect($data)) {
            return JsonResource::collection($collect ?? $data);
        }

        return new JsonResource($data);
    }
}
